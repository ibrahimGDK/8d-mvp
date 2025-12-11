<?php


class ProblemService {
    
    /** @var ProblemRepository */
    private $problemRepo;
    
    /** @var CauseRepository */
    private $causeRepo;

    /** @var CauseService */
    private $causeService;

    // Dependency Injection
    public function __construct(ProblemRepository $problemRepo, CauseRepository $causeRepo) {
        $this->problemRepo = $problemRepo;
        $this->causeRepo = $causeRepo;
        $this->causeService = new CauseService($causeRepo);
    }

    /**
     * Tüm Problem kayıtlarını çeker ve döndürür.
     * @return array<Problem>
     */
    // GET ALL -> returns array of ProblemResponse->toArray()
    public function getAllProblems(): array {
        $rows = $this->problemRepo->findAll();
        $out = [];
        foreach ($rows as $r) {
            $dto = new ProblemResponse($r);
            $out[] = $dto->toArray();
        }
        return $out;
    }

    /**
     * Belirli bir Problemi ve ona ait tüm Neden-Sonuç ağacını çeker.
     * @param int $problemId
     * @return Problem|null
     * @throws Exception
     */
    public function getProblemWithCauses(int $problemId) {
        // 1. Ana Problemi Çek
        $problemRow = $this->problemRepo->findById($problemId);
        if (!$problemRow) return null;

        $problemDto = new ProblemResponse($problemRow);

        $causesFlat = $this->causeRepo->findAllByProblemId($problemId);
        
        $causesTreeObjs = $this->causeService->buildTreeAsDtos($causesFlat);
        
        $causesTreeArr = array_map(fn($c) => $c->toArray(), $causesTreeObjs);
        
        return [
            'problem' => $problemDto->toArray(),
            'causes_tree' => $causesTreeArr
        ];
    }

    // CREATE
    public function createProblem(ProblemCreateRequest $dto): ProblemResponse {
        $payload = [
            'title'            => $dto->title,
            'description'      => $dto->description,
            'responsible_team' => $dto->responsible_team,
            'priority'         => $dto->priority ?? 'medium',
            'status'           => $dto->status ?? 'open',
        ];
        $inserted = $this->problemRepo->insert($payload);
        return new ProblemResponse($inserted);
    }


    // UPDATE - PATCH
    public function updateProblem(int $id, ProblemUpdateRequest $dto): ?ProblemResponse {
        $existing = $this->problemRepo->findById($id);
        if (!$existing) return null;

        $payload = [
            'title'             => $dto->has('title') ? $dto->title : $existing['title'],
            'description'       => $dto->has('description') ? $dto->description : $existing['description'],
            'responsible_team'  => $dto->has('responsible_team') ? $dto->responsible_team : $existing['responsible_team'],
            'priority'          => $dto->has('priority') ? $dto->priority : $existing['priority'],
            'status'            => $dto->has('status') ? $dto->status : $existing['status'],
        ];

        $updatedRow = $this->problemRepo->updateRecord($id, $payload);
        return $updatedRow ? new ProblemResponse($updatedRow) : null;
    }

    // DELETE
    public function deleteProblem(int $id): bool {
        return $this->problemRepo->deleteRecord($id);
    }

}