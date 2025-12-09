<?php

class ProblemCreateRequest
{
    public string $title;
    public ?string $description;
    public ?string $responsible_team;

    public function __construct(array $data)
    {
        $this->title = $data['title'] ?? '';
        $this->description = $data['description'] ?? null;
        $this->responsible_team = $data['responsible_team'] ?? null;
    }

    public function validate(): array
    {
        $errors = [];

        if (empty($this->title)) {
            $errors[] = "title zorunludur.";
        }

        return $errors;
    }
}
