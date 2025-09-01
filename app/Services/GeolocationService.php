<?php

namespace App\Services;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

class GeolocationService
{
    private Client $httpClient;

    public function __construct()
    {
        $this->httpClient = new Client([
            'base_uri' => 'https://nominatim.openstreetmap.org/',
            'headers' => [
                'User-Agent' => 'CommunityConnectApp/1.0', // Required by Nominatim
                'Accept' => 'application/json'
            ],
        ]);
    }

    /**
     * Converts a human-readable address to coordinates (geocoding).
     * @param string $address
     * @return array|null An array with latitude, longitude, and display_name, or null.
     */
    public function geocode(string $address): ?array
    {
        try {
            $response = $this->httpClient->get('search', [
                'query' => [
                    'q' => $address,
                    'format' => 'json',
                    'limit' => 1,
                    'addressdetails' => 1
                ]
            ]);

            $data = json_decode($response->getBody()->getContents(), true);
            
            return empty($data) ? null : [
                'latitude' => (float)$data[0]['lat'],
                'longitude' => (float)$data[0]['lon'],
                'display_name' => $data[0]['display_name']
            ];
        } catch (GuzzleException $e) {
            \Log::error('Geocoding API Error: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Converts coordinates to a human-readable address (reverse geocoding).
     * @param float $latitude
     * @param float $longitude
     * @return string|null The address display name or null.
     */
    public function reverseGeocode(float $latitude, float $longitude): ?string
    {
        try {
            $response = $this->httpClient->get('reverse', [
                'query' => [
                    'lat' => $latitude,
                    'lon' => $longitude,
                    'format' => 'json'
                ]
            ]);

            $data = json_decode($response->getBody()->getContents(), true);

            return $data['display_name'] ?? null;
        } catch (GuzzleException $e) {
            \Log::error('Reverse Geocoding API Error: ' . $e->getMessage());
            return null;
        }
    }
}