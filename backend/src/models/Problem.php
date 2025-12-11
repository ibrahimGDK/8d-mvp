<?php
// Problem model sınıfı
class Problem {

    public $id;
    public $title;
    public $description;
    public $responsible_team;
    public $status; // OPEN / CLOSED
    public $priority;
    public $created_at;
    public $updated_at;


    public function __construct($data = []) {
        $this->id = $data['id'] ?? null;
        $this->title = $data['title'] ?? null;
        $this->description = $data['description'] ?? null;
        $this->responsible_team = $data['responsible_team'] ?? null;
        $this->status = $data['status'] ?? null;
        $this->priority = $data['priority'] ?? null;
        $this->created_at = $data['created_at'] ?? null;
        $this->updated_at = $data['updated_at'] ?? null;
    }
}