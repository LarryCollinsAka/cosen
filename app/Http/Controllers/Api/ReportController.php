<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Incident;
use App\Services\AiCategorizationService;
use App\Services\AiService;
use App\Services\GeolocationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class ReportController extends Controller
{
    /**
     * @var AiCategorizationService
     */
    protected $aiCategorizationService;

    public function __construct(AiCategorizationService $aiCategorizationService)
    {
        $this->aiCategorizationService = $aiCategorizationService;
    }

    /**
     * Submits an incident report and gets a category from the AI service.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function submitReport(Request $request)
    {
        // Validate the request, making sure the required text fields are present.
        $request->validate([
            'title' => 'required|string',
            'description' => 'required|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048', // Image is optional
        ]);

        $base64Image = null;
        // Check if an image was uploaded and convert it to a base64 string
        if ($request->hasFile('image')) {
            $imageFile = $request->file('image');
            $base64Image = base64_encode(file_get_contents($imageFile->getRealPath()));
        }

        // Combine the title and description for the AI model's text input
        $text = $request->input('title') . ' ' . $request->input('description');

        // Call the AI categorization service with both text and the base64 image
        $category = $this->aiCategorizationService->categorize($text, $base64Image);

        // Return the predicted category as a JSON response
        return response()->json([
            'category' => $category,
        ]);
    }
}
