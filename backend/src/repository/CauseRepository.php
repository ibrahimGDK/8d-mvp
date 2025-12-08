<?php

// Composer olmadığı için şimdilik namespace kullanmıyoruz.

class CauseRepository {
    
    /** @var PDO */
    private $db;

    // Dependency Injection: PDO bağlantısını dışarıdan alıyoruz.
    public function __construct(PDO $db) {
        $this->db = $db;
    }

    /**
     * Belirli bir Problem ID'sine ait TÜM nedenleri TEK bir sorgu ile çeker.
     * Bu, N+1 probleminden kaçınmak için kritik!
     * @param int $problemId
     * @return array
     */
    public function findAllByProblemId(int $problemId): array {
        $sql = "SELECT id, problem_id, parent_id, title, is_root_cause, action_plan 
                FROM causes 
                WHERE problem_id = :problem_id 
                ORDER BY parent_id ASC, id ASC"; // Verilerin sıralı gelmesi faydalı
        
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':problem_id', $problemId, PDO::PARAM_INT);
        $stmt->execute();
        
        // Ham ve düz (flat) listeyi döndür
        return $stmt->fetchAll(); 
    }
    
    // NOT: Bu katmanın görevi veriyi çekmek. Ağaç yapısına dönüştürme işi Service katmanında yapılacak.
}