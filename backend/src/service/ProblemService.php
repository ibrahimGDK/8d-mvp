<?php

// Composer olmadığı için şimdilik namespace kullanmıyoruz.

class ProblemService {
    
    /** @var ProblemRepository */
    private $problemRepo;
    
    /** @var CauseRepository */
    private $causeRepo;

    /** @var CauseService */
    private $causeService;

    // Dependency Injection: Gerekli Repository'leri constructor ile alıyoruz.
    public function __construct(ProblemRepository $problemRepo, CauseRepository $causeRepo) {
        $this->problemRepo = $problemRepo;
        $this->causeRepo = $causeRepo;
        $this->causeService = new CauseService(); // CauseService'i burada oluşturabiliriz.
    }

    /**
     * Tüm Problem kayıtlarını çeker ve döndürür.
     * @return array<Problem>
     */
    public function getAllProblems(): array {
        $data = $this->problemRepo->findAll();
        
        // Ham veriyi Problem model nesnelerine dönüştür (Mapping)
        return array_map(function($item) {
            return new Problem($item);
        }, $data);
    }

    /**
     * Belirli bir Problemi ve ona ait tüm Neden-Sonuç ağacını çeker.
     * @param int $problemId
     * @return Problem|null
     * @throws Exception
     */
    public function getProblemWithCauses(int $problemId): ?Problem {
        // 1. Ana Problemi Çek
        $problemData = $this->problemRepo->findById($problemId);

        if (!$problemData) {
            // Problem bulunamazsa null döndür (veya Controller'da 404 fırlatılabilir)
            return null;
        }

        // 2. Problemi Model nesnesine dönüştür
        $problem = new Problem($problemData);

        // 3. İlgili Problemin Tüm Neden-Sonuç verilerini düz liste olarak çek
        $causesFlatList = $this->causeRepo->findAllByProblemId($problemId);
        
        // 4. CauseService'i kullanarak düz listeyi hiyerarşik ağaç yapısına dönüştür
        $causesTree = $this->causeService->buildTree($causesFlatList);
        
        // Opsiyonel: Ağaç yapısını Problem nesnesine özel bir özellikle ekleyebiliriz.
        // Şimdilik sadece Controller'a gönderelim veya basit bir DTO yapısı kullanabiliriz.
        
        // Burada, Problem ve Causes ağacını içeren tek bir yapı döndürmek en iyisidir.
        // Örn: return ['problem' => $problem, 'causes' => $causesTree];
        
        // Basit tutmak adına, direkt verileri döndürelim:
        return [
            'problem' => $problem,
            'causes_tree' => $causesTree
        ];
    }
}