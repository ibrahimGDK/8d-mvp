<?php

class CauseUpdateRequest
{
    public ?string $title;
    public ?int $parent_id;
    public ?bool $is_root_cause;
    public ?string $action_plan;

    public function __construct(array $data)
    {
        $this->title = $data['title'] ?? null;
        $this->parent_id = $data['parent_id'] ?? null;
        $this->is_root_cause = $data['is_root_cause'] ?? null;
        $this->action_plan = $data['action_plan'] ?? null;
    }

    public function validateForPatch(): array
    {
        $errors = [];

        if ($this->title !== null && strlen($this->title) < 3) {
            $errors[] = "title en az 3 karakter olmalı.";
        }

        return $errors;
    }
}
