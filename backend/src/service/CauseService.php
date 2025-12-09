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
        // Mevcut kaydı al
        $existing = $this->repo->findById($id);
        if (!$existing) {
            return false;
        }

        // PATCH: sadece gönderilen alanları değiştir; gönderilmeyenler korunur.
        $payload = [
            'title'         => array_key_exists('title', $data) ? $data['title'] : $existing['title'],
            'parent_id'     => array_key_exists('parent_id', $data) ? $data['parent_id'] : $existing['parent_id'],
            'is_root_cause' => array_key_exists('is_root_cause', $data) ? $data['is_root_cause'] : $existing['is_root_cause'],
            'action_plan'   => array_key_exists('action_plan', $data) ? $data['action_plan'] : $existing['action_plan'],
        ];

        return $this->repo->update($id, $payload);
    }

    public function deleteCause(int $id): bool {
        return $this->repo->delete($id);
    }
}