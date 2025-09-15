<?php

namespace App\Http\Controllers;

use App\Models\Conversation;
use App\Models\IncidentReport;
use App\Models\Prompt;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class IncidentReportController extends Controller
{
    /**
     * Display the chat interface for incident reporting.
     *
     * @return \Illuminate\View\View
     */
    public function chat()
    {
        return view('reports.chat');
    }

    /**
     * Store a newly created incident report in storage and initiate an AI conversation.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'location' => 'nullable|string',
            'image' => 'nullable|image|max:5000',
        ]);

        $aiCategory = null;
        $imagePath = null;
        $base64Image = null;
        $promptQuestion = null;

        $apiKey = env('NVIDIA_API_KEY');

        try {
            if ($request->hasFile('image')) {
                $imagePath = $request->file('image')->store('incidents', 'public');
                $base64Image = base64_encode(file_get_contents(Storage::disk('public')->path($imagePath)));
            }

            // Define the prompt for initial AI classification
            $initialPrompt = "Classify the following incident report into one of these categories: 'Infrastructure', 'Safety', 'Waste', or 'Other'. Do not provide any other information or explanation, just the category name as a plain text string.";

            // Prepare the JSON payload for the NVIDIA API
            $payload = [
                'model' => 'microsoft/phi-4-multimodal-instruct',
                'messages' => [
                    [
                        'role' => 'user',
                        'content' => [
                            [
                                'type' => 'text',
                                'text' => $initialPrompt . " Title: {$request->title} Description: {$request->description}"
                            ]
                        ]
                    ]
                ],
                'max_tokens' => 512,
                'temperature' => 0.10,
                'top_p' => 0.70,
                'stream' => false
            ];

            // Add image content to the payload if it exists
            if ($base64Image) {
                $payload['messages'][0]['content'][] = [
                    'type' => 'image_url',
                    'image_url' => [
                        'url' => 'data:image/' . pathinfo($imagePath, PATHINFO_EXTENSION) . ';base64,' . $base64Image
                    ]
                ];
            }

            $client = new Client();
            $response = $client->post("https://integrate.api.nvidia.com/v1/chat/completions", [
                'headers' => [
                    'Authorization' => 'Bearer ' . $apiKey,
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json',
                ],
                'json' => $payload
            ]);

            $result = json_decode($response->getBody()->getContents(), true);

            if (isset($result['choices'][0]['message']['content'])) {
                $aiCategory = trim($result['choices'][0]['message']['content']);
                // Find a relevant question from the prompts table
                $prompt = Prompt::where('incident_category', $aiCategory)
                    ->where('is_active', true)
                    ->inRandomOrder()
                    ->first();

                if ($prompt) {
                    $promptQuestion = $prompt->question;
                } else {
                    $promptQuestion = "Thank you for the report. We will review it shortly.";
                }
            }
        } catch (\Exception $e) {
            Log::error('AI classification failed: ' . $e->getMessage());
            return response()->json(['error' => 'AI classification failed. Please try again later.'], 500);
        }

        // Create the incident report
        $report = IncidentReport::create([
            'title' => $request->title,
            'description' => $request->description,
            'location' => $request->location,
            'image_path' => $imagePath,
            'category' => $aiCategory,
            'severity' => null,
            'user_id' => Auth::id(),
        ]);

        // Save the user's initial message
        Conversation::create([
            'incident_report_id' => $report->id,
            'role' => 'user',
            'message' => "Reported a new incident: {$report->title} - {$report->description}"
        ]);

        // Save the AI's first message
        Conversation::create([
            'incident_report_id' => $report->id,
            'role' => 'assistant',
            'message' => $promptQuestion
        ]);

        return response()->json([
            'success' => true,
            'incident_id' => $report->id,
            'initial_message' => $promptQuestion
        ]);
    }

    /**
     * Handle the continuation of the conversation with the AI.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function continueConversation(Request $request)
    {
        $request->validate([
            'incident_id' => 'required|exists:incident_reports,id',
            'message' => 'required|string',
        ]);

        $incidentId = $request->input('incident_id');
        $userMessage = $request->input('message');

        // Retrieve the conversation history
        $conversationHistory = Conversation::where('incident_report_id', $incidentId)
            ->orderBy('created_at')
            ->get();

        // Save the user's new message to the database
        Conversation::create([
            'incident_report_id' => $incidentId,
            'role' => 'user',
            'message' => $userMessage
        ]);

        $apiKey = env('NVIDIA_API_KEY');

        try {
            $client = new Client();
            $messages = $conversationHistory->map(function ($conv) {
                return ['role' => $conv->role, 'content' => [['type' => 'text', 'text' => $conv->message]]];
            })->toArray();

            // Add the user's new message to the payload
            $messages[] = [
                'role' => 'user',
                'content' => [['type' => 'text', 'text' => $userMessage]]
            ];

            $response = $client->post("https://integrate.api.nvidia.com/v1/chat/completions", [
                'headers' => [
                    'Authorization' => 'Bearer ' . $apiKey,
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json',
                ],
                'json' => [
                    'model' => 'microsoft/phi-4-multimodal-instruct',
                    'messages' => $messages,
                    'max_tokens' => 512,
                    'temperature' => 0.10,
                    'top_p' => 0.70,
                    'stream' => false,
                ]
            ]);

            $result = json_decode($response->getBody()->getContents(), true);
            $aiMessage = 'I am unable to continue the conversation.';

            if (isset($result['choices'][0]['message']['content'])) {
                $aiMessage = trim($result['choices'][0]['message']['content']);
            }

            // Save the AI's response to the database
            Conversation::create([
                'incident_report_id' => $incidentId,
                'role' => 'assistant',
                'message' => $aiMessage,
            ]);

            return response()->json([
                'success' => true,
                'ai_message' => $aiMessage,
            ]);
        } catch (RequestException $e) {
            $errorResponse = $e->getResponse() ? json_decode($e->getResponse()->getBody()->getContents(), true) : null;
            Log::error('AI conversation failed: ' . $e->getMessage());
            return response()->json(['error' => 'AI conversation failed. Please try again later.'], 500);
        }
    }
}
