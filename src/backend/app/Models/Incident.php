<?php

namespace App\Models;

use Doctrine\DBAL\Connection;
use Ramsey\Uuid\Uuid;

class Incident
{
    private Connection $db;

    public function __construct(Connection $db)
    {
        $this->db = $db;
    }

    /**
     * Creates a new incident report in the database.
     *
     * @param array $data
     * @return string The UUID of the new incident.
     */
    public function create(array $data): string
    {
        // Generate a new UUID for the incident
        $uuid = Uuid::uuid4()->toString();
        $data['id'] = $uuid;
        $data['created_at'] = date('Y-m-d H:i:s');
        $data['updated_at'] = date('Y-m-d H:i:s');
        
        // Insert data into the 'incidents' table
        $this->db->insert('incidents', $data);
        
        return $uuid;
    }

    /**
     * Finds an incident by its UUID.
     *
     * @param string $uuid
     * @return array|false The incident data or false if not found.
     */
    public function find(string $uuid): array|false
    {
        $sql = "SELECT * FROM incidents WHERE id = :uuid";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue('uuid', $uuid);
        
        $result = $stmt->executeQuery();
        return $result->fetchAssociative();
    }

    /**
     * Finds all incidents, optionally filtered by status.
     *
     * @param string|null $status
     * @return array All incidents.
     */
    public function findAll(?string $status = null): array
    {
        $sql = "SELECT * FROM incidents";
        
        if ($status) {
            $sql .= " WHERE status = :status";
        }
        
        $stmt = $this->db->prepare($sql);
        
        if ($status) {
            $stmt->bindValue('status', $status);
        }
        
        $result = $stmt->executeQuery();
        return $result->fetchAllAssociative();
    }
    
}