<?php

class Problem {
    // Veritabanı sütunlarıyla eşleşen özellikler
    public $id;
    public $title;
    public $description;
    public $responsible_team;
    public $status; // OPEN / CLOSED
    public $created_at;
    public $updated_at;

    // Basit bir constructor (isteğe bağlı)
    public function __construct($data = []) {
        $this->id = $data['id'] ?? null;
        $this->title = $data['title'] ?? null;
        $this->description = $data['description'] ?? null;
        $this->responsible_team = $data['responsible_team'] ?? null;
        $this->status = $data['status'] ?? 'OPEN';
        $this->created_at = $data['created_at'] ?? null;
        $this->updated_at = $data['updated_at'] ?? null;
    }
}