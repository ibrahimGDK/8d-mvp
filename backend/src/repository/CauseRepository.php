<?php


class CauseRepository {
    
    /** @var PDO */
    private $db;

    // Dependency Injection
    public function __construct(PDO $db) {
        $this->db = $db;
    }

    /**
     * Belirli bir Problem ID'sine ait TÜM nedenleri TEK bir sorgu ile çeker.
     * @param int $problemId
     * @return array
     */
    public function findAllByProblemId(int $problemId): array {
        $sql = "SELECT id, problem_id, parent_id, title, is_root_cause, action_plan 
                FROM causes 
                WHERE problem_id = :problem_id 
                ORDER BY parent_id ASC, id ASC"; 
        
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':problem_id', $problemId, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll(); 
    }
    

    // ID'ye göre tek bir cause kaydı getirir
    public function findById(int $id): ?array {
        $stmt = $this->db->prepare("SELECT * FROM causes WHERE id = :id");
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        $data = $stmt->fetch();
        return $data ?: null;
    }

    
    // Yeni bir cause kaydı oluşturur ve insert edilen ID'yi döndürür
    public function create(array $data): int {
        $sql = "INSERT INTO causes (problem_id, parent_id, title, is_root_cause, action_plan)
                VALUES (:problem_id, :parent_id, :title, :is_root_cause, :action_plan)";
        $stmt = $this->db->prepare($sql);

        $stmt->execute([
            ':problem_id'   => $data['problem_id'],
            ':parent_id'    => $data['parent_id'] ?? null,
            ':title'        => $data['title'],
            ':is_root_cause'=> $data['is_root_cause'] ?? 0,
            ':action_plan'  => $data['action_plan'] ?? null,
        ]);

        return $this->db->lastInsertId();
    }

    
    // Bir cause kaydını günceller
    public function update(int $id, array $data): bool {
        $sql = "UPDATE causes 
                SET title = :title, parent_id = :parent_id, is_root_cause = :is_root_cause, action_plan = :action_plan
                WHERE id = :id";

        $stmt = $this->db->prepare($sql);

        return $stmt->execute([
            ':id'            => $id,
            ':parent_id'     => array_key_exists('parent_id', $data) ? $data['parent_id'] : null,
            ':title'         => $data['title'],
            ':is_root_cause' => array_key_exists('is_root_cause', $data) ? $data['is_root_cause'] : 0,
            ':action_plan'   => array_key_exists('action_plan', $data) ? $data['action_plan'] : null
        ]);
    }

    
    // ID'ye göre cause kaydını siler
    public function delete(int $id): bool {
        $stmt = $this->db->prepare("DELETE FROM causes WHERE id = :id");
        return $stmt->execute([':id' => $id]);
    }

}