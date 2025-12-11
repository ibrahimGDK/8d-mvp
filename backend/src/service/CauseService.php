<?php


class CauseService {
    
    private CauseRepository $repo;

    public function __construct(CauseRepository $repo) {
        $this->repo = $repo;
    }
    
    /** Ağacı oluşturur */
    public function getCauseTree(int $problemId): array {
        $flat = $this->repo->findAllByProblemId($problemId);
        $dtos = $this->buildTreeAsDtos($flat);
        return array_map(fn($c) => $c->toArray(), $dtos);
    }

    /**
     *
     * @param array $flatList Tüm nedenleri içeren düz liste
     * @param int|null $parentId Hangi ana kayda bakacağımızı belirtir.
     * @return array Ağaç yapısındaki dallar.
     */
    public function buildTreeAsDtos(array $flatList, $parentId = null): array {
        $branch = [];
        foreach ($flatList as $element) {
            $currentParentId = isset($element['parent_id']) ? $element['parent_id'] : null;
            if ($currentParentId == $parentId) {
                $node = new CauseResponse($element);
                // find children recursively
                $children = $this->buildTreeAsDtos($flatList, $element['id']);
                if (!empty($children)) {
                    $node->setChildren($children);
                }
                $branch[] = $node;
            }
        }
        return $branch;
    }

     /** CRUD */
    public function getSingleCause(int $id): ?array {
        $row = $this->repo->findById($id);
        if (!$row) return null;
        $dto = new CauseResponse($row);
        return $dto->toArray();
    }

    public function createCause(CauseCreateRequest $dto): int {
        $payload = [
            'problem_id' => $dto->problem_id,
            'parent_id' => $dto->parent_id,
            'title' => $dto->title,
            'is_root_cause' => $dto->is_root_cause ?? 0,
            'action_plan' => $dto->action_plan
        ];
        return $this->repo->create($payload);
    }

    public function updateCause(int $id, CauseUpdateRequest $dto): bool {
        $existing = $this->repo->findById($id);
        if (!$existing) return false;

        $payload = [
            'title' => $dto->has('title') ? $dto->title : $existing['title'],
            'parent_id' => $dto->has('parent_id') ? $dto->parent_id : $existing['parent_id'],
            'is_root_cause' => $dto->has('is_root_cause') ? $dto->is_root_cause : $existing['is_root_cause'],
            'action_plan' => $dto->has('action_plan') ? $dto->action_plan : $existing['action_plan']
        ];

        return $this->repo->update($id, $payload);
    }

    public function deleteCause(int $id): bool {
        return $this->repo->delete($id);
    }
}