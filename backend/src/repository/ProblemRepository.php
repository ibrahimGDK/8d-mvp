<?php

// Composer olmadığı için şimdilik namespace kullanmıyoruz.

class ProblemRepository {
    
    /** @var PDO */
    private $db;

    // Dependency Injection: PDO bağlantısını dışarıdan alıyoruz.
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
        // Ham veriyi döndür (array of arrays)
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
        
        // Eğer kayıt yoksa null, varsa dizi döndür.
        return $result ?: null; 
    }
    
    // NOT: Store, Update, Delete fonksiyonlarını şimdilik atlıyoruz. 
    // Ana odak noktamız olan Cause ağacına geçelim.

        /**
     * Yeni Problem ekler.
     * @param array $data
     * @return array|null
     */
    public function insert(array $data): ?array {
        $stmt = $this->db->prepare("
            INSERT INTO problems (title, description, responsible_team, created_at, updated_at)
            VALUES (:title, :description, :team, NOW(), NOW())
        ");

        $stmt->execute([
            ':title'       => $data['title'],
            ':description' => $data['description'] ?? null,
            ':team'        => $data['responsible_team'] ?? null,
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
                updated_at = NOW()
            WHERE id = :id
        ");

        $stmt->execute([
            ':id'          => $id,
            ':title'       => $data['title'],
            ':description' => array_key_exists('description', $data) ? $data['description'] : null,
            ':team'        => array_key_exists('responsible_team', $data) ? $data['responsible_team'] : null,
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