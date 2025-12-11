<?php


class Response {
    /**
     * JSON formatında HTTP yanıtı oluşturur ve gönderir.
     *
     * @param mixed $data Yanıt olarak gönderilecek veri (dizi veya nesne)
     * @param int $statusCode HTTP durum kodu (varsayılan 200 OK)
     * @return void
     */
    public static function json($data, int $statusCode = 200): void {
        
        // Yanıt başlığını ve durum kodunu ayarla
        http_response_code($statusCode);
        header("Content-Type: application/json; charset=UTF-8");
        
        // Veriyi JSON formatına dönüştür ve ekrana bas
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
        
        // Çıkış yap, böylece index.php'deki diğer kodlar çalışmaz.
        exit();
    }
    
    /**
     * Başarılı bir yanıt döndürmek için.
     * @param mixed $data
     * @param int $statusCode
     * @return void
     */
    public static function success($data, int $statusCode = 200): void {
        self::json([
            "status" => "success",
            "data" => $data
        ], $statusCode);
    }
    
    /**
     * Hata yanıtı döndürmek için.
     * @param string $message
     * @param int $statusCode
     * @return void
     */
    public static function error(string $message, int $statusCode = 400): void {
        self::json([
            "status" => "error",
            "message" => $message
        ], $statusCode);
    }
}