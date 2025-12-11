<?php

class ProblemResponse
{
    public int $id;
    public string $title;
    public ?string $description;
    public ?string $status;
    public ?string $responsible_team;
    public ?string $priority;
    public ?string $created_at;
    public ?string $updated_at;

    public function __construct(array $row)
    {
        $this->id = (int)$row['id'];
        $this->title = $row['title'];
        $this->description = $row['description'] ?? null;
        $this->responsible_team = $row['responsible_team'] ?? null;
        $this->priority = $row['priority'] ?? null;
        $this->status = $row['status'] ?? null;
        $this->created_at = $row['created_at'] ?? null;
        $this->updated_at = $row['updated_at'] ?? null;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'responsible_team' => $this->responsible_team,
            'priority' => $this->priority,
            'status' => $this->status,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at
        ];
    }
}
