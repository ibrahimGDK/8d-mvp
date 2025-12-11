<?php


class ProblemRepository {
    
    /** @var PDO */
    private $db;

    // Dependency Injection
    public function __construct(PDO $db) {
        $this->db = $db;
    }

    /**
     * Tüm Problem kayıtlarını veritabanından çeker.
     * @return array
     */
    public function findAll(): array {
        $stmt = $this->db->prepare("SELECT * FROM problems ORDER BY id DESC");
        $stmt->execute();
        return $stmt->fetchAll(); 
    }
    
    /**
     * Belirli bir ID'ye sahip Problem kaydını çeker.
     * @param int $id
     * @return array|null
     */
    public function findById(int $id): ?array {
        $stmt = $this->db->prepare("SELECT * FROM problems WHERE id = :id");
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetch();
        
        return $result ?: null; 
    }
    

        /**
     * Yeni Problem ekler.
     * @param array $data
     * @return array|null
     */
    public function insert(array $data): ?array {
        $stmt = $this->db->prepare("
            INSERT INTO problems 
                (title, description, responsible_team, priority, status, created_at, updated_at)
            VALUES 
                (:title, :description, :team, :priority, :status, NOW(), NOW())
        ");

        $stmt->execute([
            ':title'       => $data['title'],
            ':description' => $data['description'] ?? null,
            ':team'        => $data['responsible_team'] ?? null,
            ':priority'    => $data['priority'] ?? 'medium',
            ':status'      => $data['status'] ?? 'open',
        ]);

        $id = $this->db->lastInsertId();
        return $this->findById($id);
    }

    /**
     * Problem günceller.
     * @param int $id
     * @param array $data
     * @return array|null
     */
    public function updateRecord(int $id, array $data): ?array {
        $stmt = $this->db->prepare("
            UPDATE problems 
            SET title = :title,
                description = :description,
                responsible_team = :team,
                priority = :priority,
                status = :status,
                updated_at = NOW()
            WHERE id = :id
        ");

        $stmt->execute([
            ':id'          => $id,
            ':title'       => $data['title'],
            ':description' => array_key_exists('description', $data) ? $data['description'] : null,
            ':team'        => array_key_exists('responsible_team', $data) ? $data['responsible_team'] : null,
            ':priority'    => array_key_exists('priority', $data) ? $data['priority'] : null,
            ':status'      => array_key_exists('status', $data) ? $data['status'] : null,
        ]);

        return $this->findById($id);
    }

    /**
     * Problem siler.
     * @param int $id
     * @return bool
     */
    public function deleteRecord(int $id): bool {
        $stmt = $this->db->prepare("DELETE FROM problems WHERE id = :id");
        return $stmt->execute([':id' => $id]);
    }

}