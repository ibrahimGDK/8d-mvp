<?php

class ProblemUpdateRequest
{
    private array $raw;
    public ?string $title;
    public ?string $description;
    public ?string $responsible_team;
    public ?string $priority;
    public ?string $status;


    public function __construct(array $data)
    {
        $this->raw = $data;
        $this->title = array_key_exists('title', $data) ? $data['title'] : null;
        $this->description = array_key_exists('description', $data) ? $data['description'] : null;
        $this->responsible_team = array_key_exists('responsible_team', $data) ? $data['responsible_team'] : null;
        $this->priority = array_key_exists('priority', $data) ? $data['priority'] : null;
        $this->status = array_key_exists('status', $data) ? $data['status'] : null;
    }

    public function has(string $key): bool
    {
        return array_key_exists($key, $this->raw);
    }

    public function validateForPatch(): array
    {
        $errors = [];
        if ($this->title !== null && strlen((string)$this->title) < 3) {
            $errors[] = "title en az 3 karakter olmalıdır.";
        }
        return $errors;
    }
}
