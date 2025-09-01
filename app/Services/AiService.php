<?php

namespace App\Services;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

class AiService
{
    private Client $httpClient;

    public function __construct()
    {
        $this->httpClient = new Client([
            'base_uri' => 'https://integrate.api.nvidia.com/v1/',
            'headers' => [
                'Authorization' => 'Bearer ' . env('NVIDIA_API_KEY'),
                'Content-Type' => 'application/json',
                'Accept' => 'application/json'
            ],
            'verify' => false // For local development, allows connecting without SSL errors
        ]);
    }

    /**
     * Categorizes a given text string using a text classification model.
     *
     * @param string $text The incident report text.
     * @return string The category predicted by the AI.
     */
    public function categorizeReport(string $text): string
    {
        try {
            $prompt = "You are a helpful assistant that categorizes incident reports. Categorize the following incident report into one of these categories: 'road', 'electricity', 'water', 'health', 'public_safety', 'general'. Provide only the category name as the output.\n\nReport: \"$text\"";

            $response = $this->httpClient->post('chat/completions', [
                'json' => [
                    'model' => 'meta/llama3-8b-instruct',
                    'messages' => [
                        ['role' => 'system', 'content' => 'You are a helpful assistant.'],
                        ['role' => 'user', 'content' => $prompt]
                    ],
                    'temperature' => 0.1,
                    'max_tokens' => 20
                ]
            ]);

            $data = json_decode($response->getBody()->getContents(), true);
            $category = trim($data['choices'][0]['message']['content']);

            // Trim any unwanted characters like backticks or quotes
            $category = str_replace(['`', '"', "'"], '', $category);

            // Simple fallback in case the AI provides a different response
            $validCategories = ['road', 'electricity', 'water', 'health', 'public_safety', 'general'];
            return in_array($category, $validCategories) ? $category : 'general';

        } catch (GuzzleException $e) {
            // Log the error for debugging purposes
            \Log::error('AI Service Error: ' . $e->getMessage());
            return 'general'; // Return a safe fallback category
        }
    }
}