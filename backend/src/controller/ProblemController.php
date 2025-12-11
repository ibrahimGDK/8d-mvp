<?php


class ProblemController {
    
    /** @var ProblemService */
    private $problemService;

    // Dependency Injection
    public function __construct(ProblemService $problemService) {
        $this->problemService = $problemService;
    }

    /**
     * [GET] /problems - Tüm problemleri listeler. (index)
     */
    public function index(): void {
        try {
            $problems = $this->problemService->getAllProblems();
            
            // Başarılı sonucu JSON olarak döndür
            Response::success($problems, 200);
            
        } catch (Exception $e) {
            // Service katmanından gelebilecek hataları yakala ve 500 olarak döndür
            Response::error("Problemler listelenirken hata oluştu: " . $e->getMessage(), 500);
        }
    }

    /**
     * [GET] /problems/{id} - Belirli bir problemi ve Neden-Sonuç ağacını gösterir. (show)
     * @param int $id Problem ID
     */
    public function show(int $id): void {
        try {
            $data = $this->problemService->getProblemWithCauses($id);

            if (!$data) {
                Response::error("İstenen Problem kaydı bulunamadı.", 404);
            }
            
            // Başarılı sonucu JSON olarak döndür.
            Response::success($data, 200);

        } catch (Exception $e) {
            Response::error("Problem detayları çekilirken hata oluştu: " . $e->getMessage(), 500);
        }
    }
    

    /**
     * [POST] /problems - Yeni bir problem kaydı oluşturur. (store)
     */
    public function store(): void {
        // Gelen POST verisini al
        $input = json_decode(file_get_contents("php://input"), true);
        

        $dto = new ProblemCreateRequest($input);
        $errors = $dto->validate();
        if (!empty($errors)) {
            Response::error($errors, 422);
        }

        try {
            $created = $this->problemService->createProblem($dto); // returns ProblemResponse
            Response::success($created, 201);

        } catch (Exception $e) {
            Response::error("Problem oluşturulurken hata oluştu: " . $e->getMessage(), 500);
        }
    }
    
    /**
     * [PATCH] /problems/{id} - Problem kaydını günceller. (update)
     */
    public function update(int $id): void {
        $input = json_decode(file_get_contents("php://input"), true) ?? [];

        $dto = new ProblemUpdateRequest($input);
        $errors = $dto->validateForPatch();
        if (!empty($errors)) {
            Response::error($errors, 422);
        }
        try {
            $updated = $this->problemService->updateProblem($id, $dto);

            if (!$updated) {
                Response::error("Güncellenecek Problem bulunamadı.", 404);
            }

            Response::success($updated->toArray(), 200);

        } catch (Exception $e) {
            Response::error("Problem güncellenirken hata oluştu: " . $e->getMessage(), 500);
        }
    }

    /**
     * [DELETE] /problems/{id} - Problem kaydını siler. (destroy)
     */
    public function destroy(int $id): void {
        try {
            $deleted = $this->problemService->deleteProblem($id);

            if (!$deleted) {
                Response::error("Silinecek Problem bulunamadı.", 404);
            }

            Response::success(["message" => "Problem başarıyla silindi."], 200);

        } catch (Exception $e) {
            Response::error("Problem silinirken hata oluştu: " . $e->getMessage(), 500);
        }
    }

}