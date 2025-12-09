<?php

class CauseCreateRequest
{
    public string $title;
    public ?int $parent_id;
    public bool $is_root_cause;
    public ?string $action_plan;

    public function __construct(array $data)
    {
        $this->title = $data['title'] ?? '';
        $this->parent_id = $data['parent_id'] ?? null;
        $this->is_root_cause = $data['is_root_cause'] ?? false;
        $this->action_plan = $data['action_plan'] ?? null;
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
