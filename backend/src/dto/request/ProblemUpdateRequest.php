<?php

class ProblemUpdateRequest
{
    public ?string $title;
    public ?string $description;
    public ?string $responsible_team;

    public function __construct(array $data)
    {
        $this->title = $data['title'] ?? null;
        $this->description = $data['description'] ?? null;
        $this->responsible_team = $data['responsible_team'] ?? null;
    }

    public function validateForPatch(): array
    {
        $errors = [];

        if ($this->title !== null && strlen($this->title) < 3) {
            $errors[] = "title en az 3 karakter olmalıdır.";
        }

        return $errors;
    }
}
