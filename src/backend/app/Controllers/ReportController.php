<?php

namespace App\Controllers;

use App\Models\Incident;
use Doctrine\DBAL\Connection;

class ReportController
{
    private Incident $incidentModel;

    public function __construct(Connection $db)
    {
        // We inject the database connection and create an instance of our Incident Model.
        $this->incidentModel = new Incident($db);
    }

    /**
     * Handles the creation of a new incident report via a POST request.
     *
     * @param array $requestData
     * @return array
     */
    public function createReport(array $requestData): array
    {
        // 1. Validate the incoming data.
        if (empty($requestData['title']) || empty($requestData['description'])) {
            http_response_code(400); // Bad Request
            return ['error' => 'Title and description are required.'];
        }

        // 2. Prepare the data for the Model.
        $incidentData = [
            'title' => $requestData['title'],
            'description' => $requestData['description'],
            'type' => $requestData['type'] ?? 'general', // Default to 'general' if not provided
            'latitude' => $requestData['latitude'] ?? null,
            'longitude' => $requestData['longitude'] ?? null,
            'photo_url' => $requestData['photo_url'] ?? null,
        ];
        
        // 3. Use the Incident Model to save the report to the database.
        $uuid = $this->incidentModel->create($incidentData);

        // 4. Return a success response.
        http_response_code(201); // Created
        return ['message' => 'Incident report created successfully.', 'uuid' => $uuid];
    }
    
    /**
     * Handles retrieving a list of all incident reports.
     *
     * @return array
     */
    public function getReports(): array
    {
        // Fetch all incidents from the database using the Model.
        $incidents = $this->incidentModel->findAll();

        // Return the data as a JSON response.
        return $incidents;
    }
}