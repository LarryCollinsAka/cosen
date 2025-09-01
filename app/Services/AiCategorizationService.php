<?php

namespace App\Services;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

class AiCategorizationService
{
    private Client $httpClient;
    private string $apiKey;

    public function __construct()
    {
        $this->apiKey = env('NVIDIA_API_KEY');
        $this->httpClient = new Client([
            'base_uri' => 'https://integrate.api.nvidia.com/v1/',
            'headers' => [
                'Authorization' => 'Bearer ' . $this->apiKey,
                'Content-Type' => 'application/json',
                'Accept' => 'application/json'
            ],
        ]);
    }

    /**
     * Categorizes a given incident using text and an optional image with the NVIDIA API.
     *
     * @param string $text The combined title and description of the incident.
     * @param string|null $imageData A base64-encoded image, or null if no image is provided.
     * @return string The predicted category.
     */
    public function categorize(string $text, ?string $imageData = null): string
    {
        try {
            // Build the content payload for the user message
            $userContent = [
                [
                    'type' => 'text',
                    'text' => "You are a specialized AI model for incident report classification. Your task is to analyze the following text and any attached images to classify the incident. Respond with only one of the following category names: 'Road Hazard', 'Crime', 'Public Utility Issue', 'Environmental Concern', 'Health & Safety', or 'Other'. Do not include any additional text, explanation, or punctuation.\n\nText: \"$text\"\nCategory:"
                ]
            ];

            // If image data is provided, add it to the user content payload
            if ($imageData) {
                $userContent[] = [
                    'type' => 'image_url',
                    'image_url' => [
                        // The NVIDIA API expects the data: URI format
                        'url' => "data:image/jpeg;base64,{$imageData}"
                    ]
                ];
            }

            $response = $this->httpClient->post('chat/completions', [
                'json' => [
                    'model' => 'google/gemma-3n-e2b-it',
                    'messages' => [
                        [
                            'role' => 'user',
                            'content' => $userContent
                        ]
                    ],
                    'max_tokens' => 20,
                    'temperature' => 0.2
                ]
            ]);

            $data = json_decode($response->getBody()->getContents(), true);

            // Extract and sanitize the category from the response
            $category = trim($data['choices'][0]['message']['content'] ?? 'Other');
            $category = preg_replace('/[^A-Za-z0-9 ]/', '', $category);
            
            // Simple validation to ensure the category is one of the allowed ones
            $validCategories = ['Road Hazard', 'Crime', 'Public Utility Issue', 'Environmental Concern', 'Health & Safety', 'Other'];
            
            return in_array($category, $validCategories) ? $category : 'Other';

        } catch (GuzzleException $e) {
            // Log the error and return a default category
            return 'Other';
        }
    }
}