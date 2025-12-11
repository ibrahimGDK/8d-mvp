<?php

class ProblemCreateRequest
{
    private array $raw;
    public ?string $title;
    public $status;
    public ?string $description;
    public ?string $responsible_team;
    public $priority;

    public function __construct(array $data)
    {
        $this->raw = $data;
        $this->title = array_key_exists('title', $data) ? $data['title'] : null;
        $this->description = array_key_exists('description', $data) ? $data['description'] : null;
        $this->responsible_team = array_key_exists('responsible_team', $data) ? $data['responsible_team'] : null;
        $this->priority = $data['priority'] ?? 'medium';
        $this->status = $data['status'] ?? 'open';

    }

    public function has(string $key): bool
    {
        return array_key_exists($key, $this->raw);
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
