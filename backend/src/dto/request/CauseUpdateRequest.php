<?php

// "Cause" güncelleme işlemleri için kullanılan DTO sınıfı

class CauseUpdateRequest
{
    private array $raw;
    public ?int $parent_id;
    public ?string $title;
    public ?int $is_root_cause;
    public ?string $action_plan;

    public function __construct(array $data)
    {
        $this->raw = $data;
        $this->parent_id = array_key_exists('parent_id', $data) ? (int)$data['parent_id'] : null;
        $this->title = array_key_exists('title', $data) ? $data['title'] : null;
        $this->is_root_cause = array_key_exists('is_root_cause', $data) ? (int)$data['is_root_cause'] : null;
        $this->action_plan = array_key_exists('action_plan', $data) ? $data['action_plan'] : null;
    }

    public function has(string $key): bool
    {
        return array_key_exists($key, $this->raw);
    }

    public function validateForPatch(): array
    {
        $errors = [];
        if ($this->title !== null && strlen((string)$this->title) < 3) $errors[] = "title en az 3 karakter olmalı.";
        return $errors;
    }
}
