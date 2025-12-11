<?php

// Cause model sınıfı
class Cause {
    public $id;
    public $problem_id;
    public $parent_id; 
    public $title;
    public $is_root_cause; 
    public $action_plan; 
    public $created_at;

    public $children = [];

    public function __construct($data = []) {
        $this->id = $data['id'] ?? null;
        $this->problem_id = $data['problem_id'] ?? null;
        $this->parent_id = $data['parent_id'] ?? null;
        $this->title = $data['title'] ?? null;
        $this->is_root_cause = $data['is_root_cause'] ?? 0;
        $this->action_plan = $data['action_plan'] ?? null;
        $this->created_at = $data['created_at'] ?? null;
    }
}