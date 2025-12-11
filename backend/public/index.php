<?php

// Hata Raporlamasını Aç
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);




// =======================================================
// A. .env Dosyasını Yükleme (Manuel)
// =======================================================
function loadEnv($filePath) {
    if (!file_exists($filePath)) return;

    $lines = file($filePath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

    foreach ($lines as $line) {
        // Yorumları atla
        if (strpos(trim($line), '#') === 0) continue;

        // KEY=VALUE formatını parçala
        if (strpos($line, '=') !== false) {
            [$key, $value] = explode('=', $line, 2);
            $key = trim($key);
            $value = trim($value);

            if ($key !== '') {
                putenv("$key=$value"); // Ortama değişken ekle
            }
        }
    }
}

//  .env dosyasını yükle
loadEnv(__DIR__ . '/../.env');

// ENV değişkenlerini oku
$DB_HOST = getenv('DB_HOST');
$DB_PORT = getenv('DB_PORT') ?: 3308;
$DB_NAME = getenv('DB_NAME');
$DB_USER = getenv('DB_USER');
$DB_PASS = getenv('DB_PASS');



// =======================================================
// A. Elle Yükleme (Manual Autoloading) - Composer'ı Taklit Ediyoruz
// =======================================================

// Kritik Çekirdek Yapılar
require_once __DIR__ . '/../src/Core/Response.php';
require_once __DIR__ . '/../src/config/db.php';

// Modeller 
require_once __DIR__ . '/../src/models/Problem.php';
require_once __DIR__ . '/../src/models/Cause.php';

// Repository'ler
require_once __DIR__ . '/../src/repository/ProblemRepository.php';
require_once __DIR__ . '/../src/repository/CauseRepository.php';

// Servisler (Repository'lere bağımlı)
require_once __DIR__ . '/../src/service/CauseService.php'; // ÖNCE bu yüklenmeli
require_once __DIR__ . '/../src/service/ProblemService.php';

// Controller'lar (Servislere bağımlı)
require_once __DIR__ . '/../src/controller/ProblemController.php';
require_once __DIR__ . '/../src/controller/CauseController.php';

// DTO'lar
require_once __DIR__ . '/../src/dto/request/ProblemCreateRequest.php';
require_once __DIR__ . '/../src/dto/request/ProblemUpdateRequest.php';
require_once __DIR__ . '/../src/dto/request/CauseCreateRequest.php';
require_once __DIR__ . '/../src/dto/request/CauseUpdateRequest.php';

require_once __DIR__ . '/../src/dto/response/ProblemResponse.php';
require_once __DIR__ . '/../src/dto/response/CauseResponse.php';

// =======================================================
// B. Ortak Ayarlar ve CORS
// =======================================================

header("Access-Control-Allow-Origin: *"); // Her kaynaktan gelen isteklere izin ver
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET, POST, PUT, PATCH, DELETE, OPTIONS");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

// Eğer gelen istek OPTIONS ise (Pre-flight request), sadece başlıkları döndür ve çık
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// =======================================================
// C. Router ve Controller İşlemleri
// =======================================================



// İstek yapılan URI’yi al
$requestUri = $_SERVER['REQUEST_URI'];

// Mevcut dizin yolunu al
$basePath = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/');

// URI'den base path'i çıkar
$cleanedUri = $requestUri;
if ($basePath !== '/') {
    $cleanedUri = preg_replace('#^' . preg_quote($basePath) . '#', '', $cleanedUri);
}

// index.php kısmını da temizle
$cleanedUri = preg_replace('#/index\.php#', '', $cleanedUri);

// Sadece path kısmını çek
$cleanedUri = trim(parse_url($cleanedUri, PHP_URL_PATH), '/');

// Segmentlere ayır
$segments = $cleanedUri === '' ? [] : explode('/', $cleanedUri);

$method = $_SERVER['REQUEST_METHOD'];

// Varsayılan değerler
$controllerName = 'Problem';
$action = 'index';
$id = null; // URI'deki ID parametresi


// 1. Segment: Controller (Örn: problems -> ProblemController)
if (!empty($segments[0])) {
    $controllerName = ucfirst(strtolower($segments[0]));
    // "problems" -> "Problem" Controller
    if (substr($controllerName, -1) === 's') { // Çoğuldan tekile çevir
         $controllerName = rtrim($controllerName, 's');
    }
}
$fullControllerName = $controllerName . 'Controller';

// 2. Segment: ID veya Ek Aksiyon
if (isset($segments[1])) {
    if (is_numeric($segments[1])) {
        $id = (int)$segments[1];
        $action = 'show'; 
    } else {
        $action = strtolower($segments[1]); 
    }
}

// HTTP Metoduna göre aksiyonu belirle 
if ($method === 'GET' && $id === null) $action = 'index';
if ($method === 'GET' && $id !== null) $action = 'show';
if ($method === 'POST') $action = 'store';
if ($method === 'PUT' || $method === 'PATCH') $action = 'update';
if ($method === 'DELETE') $action = 'destroy';


// =======================================================
// D. BAĞIMLILIK ENJEKSİYONU (Senior Yaklaşım) ve Çalıştırma
// =======================================================

try {
    // Repository'leri oluştur
    $problemRepo = new ProblemRepository(Database::getConnection());
    $causeRepo   = new CauseRepository(Database::getConnection());

    // Servisleri oluştur (Repository'leri Enjekte Et)
    $problemService = new ProblemService($problemRepo, $causeRepo);
    $causeService   = new CauseService($causeRepo);

    // Controller yönlendirme – Hangi controller istenmişse onu oluştur
    if ($fullControllerName === 'ProblemController') {
        $controller = new ProblemController($problemService);

    } elseif ($fullControllerName === 'CauseController') {
        $controller = new CauseController($causeService);

    } else {
        Response::json(["message" => "404 Not Found. Controller Not Found."], 404);
        exit();
    }

    // Controller gerçekten var mı ve action mevcut mu kontrol et
    if (!method_exists($controller, $action)) {
        Response::json(["message" => "404 Not Found. Invalid Action."], 404);
        exit();
    }

    // Aksiyonu çalıştır
    if ($id !== null) {
        $controller->$action($id);
    } else {
        $controller->$action();
    }

} catch (Exception $e) {
    Response::json(["error" => $e->getMessage()], 500);
}


?>