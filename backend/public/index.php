<?php

// Hata Raporlamasını Aç
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


// Veritabanı ayarlarını burada geçici olarak tanımlıyoruz (Senior pratik, ama Composer olmadığı için geçici çözüm)
putenv("DB_HOST=127.0.0.1");
putenv("DB_PORT=3308"); // Senin düzelttiğin port
putenv("DB_NAME=8d_db"); 
putenv("DB_USER=root"); 
putenv("DB_PASS=123456");



// =======================================================
// A. Elle Yükleme (Manual Autoloading) - Composer'ı Taklit Ediyoruz
// =======================================================

// Kritik Çekirdek Yapılar
require_once __DIR__ . '/../src/Core/Response.php';
require_once __DIR__ . '/../src/config/db.php';

// Modeller (Bağımlı olduğu başka bir model yoksa önce onları yükle)
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


// =======================================================
// B. Ortak Ayarlar ve CORS
// =======================================================

header("Access-Control-Allow-Origin: *"); // Her kaynaktan gelen isteklere izin ver
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
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


/*$requestUri = $_SERVER['REQUEST_URI'];         // Örn: /8d-mvp/backend/public/index.php/problems/1
$scriptName = $_SERVER['SCRIPT_NAME'];         // Örn: /8d-mvp/backend/public/index.php

// Kök yolu (Script Name) kısmını URI'den siliyoruz.
$uri = str_replace($scriptName, '', $requestUri);

// Geriye sadece temiz path kalır: /problems/1
$uri = trim(parse_url($uri, PHP_URL_PATH), '/'); // Sonuç: "problems/1"
$segments = explode('/', $uri);                   // Sonuç: ['problems', '1']
*/

// Örn: /8d-mvp/backend/public/index.php/problems/1
$requestUri = $_SERVER['REQUEST_URI'];

// Örn: /8d-mvp/backend/public
$basePath = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/');

// Base path'i URI'den çıkar
$cleanedUri = $requestUri;

// Base path root değilse temizle
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

// Örnek URI yapısı: /api/problems/123

// URI'nin ilk segmenti "api" veya "backend" olabilir, onu atlayalım.
// BU KISIM ARTIK GEREKSİZ OLABİLİR AMA YİNE DE KALSIN:
// if ($segments[0] === 'api' || $segments[0] === 'backend') {
//     array_shift($segments); 
// }

// 1. Segment: Controller (Örn: problems -> ProblemController)
if (!empty($segments[0])) {
    $controllerName = ucfirst(strtolower($segments[0]));
    // "problems" -> "Problem" Controller
    if (substr($controllerName, -1) === 's') { // Çoğuldan tekile çevir (Basit yaklasim)
         $controllerName = rtrim($controllerName, 's');
    }
}
$fullControllerName = $controllerName . 'Controller';

// 2. Segment: ID veya Ek Aksiyon
if (isset($segments[1])) {
    if (is_numeric($segments[1])) {
        $id = (int)$segments[1];
        // Örn: /problems/1 -> show(1) aksiyonu
        $action = 'show'; 
    } else {
        // Örn: /problems/search -> search() aksiyonu
        $action = strtolower($segments[1]); 
    }
}

// HTTP Metoduna göre aksiyonu belirle (Basit RESTful yaklaşım)
if ($method === 'GET' && $id === null) $action = 'index'; // /problems -> index()
if ($method === 'GET' && $id !== null) $action = 'show';  // /problems/1 -> show(1)
if ($method === 'POST') $action = 'store';              // POST /problems -> store()
if ($method === 'PUT' || $method === 'PATCH') $action = 'update'; // PUT /problems/1 -> update(1)
if ($method === 'DELETE') $action = 'destroy';          // DELETE /problems/1 -> destroy(1)


// =======================================================
// D. BAĞIMLILIK ENJEKSİYONU (Senior Yaklaşım) ve Çalıştırma
// =======================================================

try {
    // 1. Repository'leri oluştur
    $problemRepo = new ProblemRepository(Database::getConnection());
    $causeRepo   = new CauseRepository(Database::getConnection());

    // 2. Servisleri oluştur (Repository'leri Enjekte Et)
    $problemService = new ProblemService($problemRepo, $causeRepo);
    $causeService   = new CauseService($causeRepo);

    // 3. Controller yönlendirme – Hangi controller istenmişse onu oluştur
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