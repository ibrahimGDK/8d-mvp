<?php

class Cause {
    public $id;
    public $problem_id;
    public $parent_id; // Ağaç yapısı için kritik
    public $title;
    public $is_root_cause; // Kök neden mi? (boolean)
    public $action_plan; // D5 - Kalıcı çözüm
    public $created_at;

    // Ağaç yapısı oluşturulurken children (çocuk) verileri buraya eklenecek
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