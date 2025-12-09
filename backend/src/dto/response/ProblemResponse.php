<?php

class ProblemResponse
{
    public int $id;
    public string $title;
    public ?string $description;
    public ?string $responsible_team;
    public string $created_at;
    public string $updated_at;

    public function __construct(array $row)
    {
        $this->id = $row['id'];
        $this->title = $row['title'];
        $this->description = $row['description'];
        $this->responsible_team = $row['responsible_team'];
        $this->created_at = $row['created_at'];
        $this->updated_at = $row['updated_at'];
    }
}
