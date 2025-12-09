<?php

class CauseResponse
{
    public int $id;
    public string $title;
    public ?int $parent_id;
    public bool $is_root_cause;
    public ?string $action_plan;

    public function __construct(array $row)
    {
        $this->id = $row['id'];
        $this->title = $row['title'];
        $this->parent_id = $row['parent_id'];
        $this->is_root_cause = (bool)$row['is_root_cause'];
        $this->action_plan = $row['action_plan'];
    }
}
