<?php

// Composer olmadığı için şimdilik namespace kullanmıyoruz.

class CauseService {
    
    private CauseRepository $repo;

    public function __construct(CauseRepository $repo) {
        $this->repo = $repo;
    }
    
    /** Ağacı oluşturur */
    public function getCauseTree(int $problemId): array {
        $flat = $this->repo->findAllByProblemId($problemId);
        return $this->buildTree($flat);
    } 

    /**
     * Düz listeyi (flat list) hiyerarşik bir ağaç yapısına dönüştürür.
     * Bu fonksiyon, N+1 Probleminden kaçınmanın en temiz yoludur.
     *
     * @param array $flatList Tüm nedenleri içeren düz liste (array of arrays)
     * @param int|null $parentId Hangi ana kayda bakacağımızı belirtir.
     * @return array Ağaç yapısındaki dallar.
     */
    public function buildTree(array $flatList, $parentId = null): array {
        $branch = [];

        foreach ($flatList as $element) {
            // PHP'de NULL ile 0 karşılaştırması farklı olduğu için kontrol ediyoruz.
            // parent_id DB'de null ise (yani ana kök ise), $parentId da null olmalı.
            $currentParentId = $element['parent_id'] ?? null; 
            
            // Eğer bu elemanın parent_id'si, bizim aradığımız parentId ile eşleşiyorsa
            if ($currentParentId == $parentId) {
                
                // Özyineleme (Recursion): Bu elemanın çocuklarını bulmak için fonksiyonu tekrar çağır.
                // Bu çağrıda, mevcut elemanın ID'sini yeni parentId olarak veriyoruz.
                $children = $this->buildTree($flatList, $element['id']);
                
                // Eğer çocuk bulunduysa, 'children' anahtarı ile diziye ekle.
                if ($children) {
                    $element['children'] = $children;
                }
                
                // Hazırlanan elemanı (varsa çocuklarıyla birlikte) bu dala (branch) ekle.
                $branch[] = $element;
            }
        }

        return $branch;
    }
     /** CRUD */
    public function getSingleCause(int $id): ?array {
        return $this->repo->findById($id);
    }

    public function createCause(array $data): int {
        return $this->repo->create($data);
    }

    public function updateCause(int $id, array $data): bool {
        return $this->repo->update($id, $data);
    }

    public function deleteCause(int $id): bool {
        return $this->repo->delete($id);
    }
}