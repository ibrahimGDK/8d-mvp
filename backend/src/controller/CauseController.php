<?php

class CauseController {

    private CauseService $service;

    public function __construct(CauseService $causeService) {
        $this->service = $causeService;
    }

    /** [GET] /causes?problem_id=5 */
    public function index(): void {
        try {
            $problemId = $_GET['problem_id'] ?? null;

            if (!$problemId) {
                Response::error("problem_id zorunludur.", 422);
            }

            $tree = $this->service->getCauseTree((int)$problemId);
            Response::success($tree, 200);

        } catch (Exception $e) {
            Response::error("Neden listesi alınamadı: " . $e->getMessage(), 500);
        }
    }

    /** [GET] /causes/{id} */
    public function show(int $id): void {
        try {
            $cause = $this->service->getSingleCause($id);

            if (!$cause) {
                Response::error("Cause bulunamadı.", 404);
            }

            Response::success($cause, 200);

        } catch (Exception $e) {
            Response::error("Cause alınırken hata: " . $e->getMessage(), 500);
        }
    }

    /** [POST] /causes */
    public function store(): void {
        $input = json_decode(file_get_contents("php://input"), true);

        if (empty($input['problem_id']) || empty($input['title'])) {
            Response::error("problem_id ve title zorunludur.", 422);
        }

        try {
            $id = $this->service->createCause($input);
            Response::success(['id' => $id], 201);

        } catch (Exception $e) {
            Response::error("Cause oluşturulamadı: " . $e->getMessage(), 500);
        }
    }

    /** [PATCH] /causes/{id} */
    public function update(int $id): void {
        // json_decode null dönebilir; fallback ile boş dizi kullanıyoruz
        $input = json_decode(file_get_contents("php://input"), true) ?? [];

        try {
            $ok = $this->service->updateCause($id, $input);

            if (!$ok) {
                Response::error("Güncellenecek cause bulunamadı.", 404);
            }

            Response::success(["message" => "Cause güncellendi"], 200);

        } catch (Exception $e) {
            Response::error("Cause güncellenirken hata: " . $e->getMessage(), 500);
        }
    }

    /** [DELETE] /causes/{id} */
    public function destroy(int $id): void {
        try {
            $ok = $this->service->deleteCause($id);

            if (!$ok) {
                Response::error("Silinecek cause bulunamadı.", 404);
            }

            Response::success(["message" => "Cause silindi"], 200);

        } catch (Exception $e) {
            Response::error("Cause silinirken hata: " . $e->getMessage(), 500);
        }
    }
}
