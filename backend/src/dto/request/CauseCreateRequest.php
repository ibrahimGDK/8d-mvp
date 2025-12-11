<?php

class CauseCreateRequest
{
    private array $raw;
    public ?int $problem_id;
    public ?int $parent_id;
    public ?string $title;
    public ?int $is_root_cause;
    public ?string $action_plan;

    public function __construct(array $data)
    {
        $this->raw = $data;
        $this->problem_id = array_key_exists('problem_id', $data) ? (int)$data['problem_id'] : null;
        //$this->parent_id = array_key_exists('parent_id', $data) ? (int)$data['parent_id'] : null;
        $this->parent_id = array_key_exists('parent_id', $data) ? $data['parent_id'] : null;
        $this->title = array_key_exists('title', $data) ? $data['title'] : null;
        $this->is_root_cause = array_key_exists('is_root_cause', $data) ? (int)$data['is_root_cause'] : 0;
        $this->action_plan = array_key_exists('action_plan', $data) ? $data['action_plan'] : null;
    }

    public function has(string $key): bool
    {
        return array_key_exists($key, $this->raw);
    }

    public function validate(): array
    {
        $errors = [];
        if ($this->problem_id === null) $errors[] = "problem_id zorunludur.";
        if (empty($this->title)) $errors[] = "title zorunludur.";
        return $errors;
    }
}
