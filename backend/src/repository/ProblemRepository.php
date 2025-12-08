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
        $stmt = $this->db->prepare("SELECT * FROM problems ORDER BY created_at DESC");
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
}