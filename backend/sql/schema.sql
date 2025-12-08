-- Eğer '8d_db' adında bir veritabanı kullanacaksanız:
-- CREATE DATABASE IF NOT EXISTS 8d_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
-- USE 8d_db;

-- --------------------------------------------------------
-- 1. PROBLEMS Tablosu (D1-D2: Problem Tanımlama)
-- --------------------------------------------------------

CREATE TABLE IF NOT EXISTS problems (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL COMMENT 'Problemin kısa başlığı',
    description TEXT NULL COMMENT 'D2: Problemin detaylı açıklaması',
    responsible_team VARCHAR(100) NOT NULL COMMENT 'D1: Problemin sorumlusu (Ekip/Kişi)',
    status ENUM('OPEN', 'CLOSED') DEFAULT 'OPEN' NOT NULL COMMENT 'Problemin durumu',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabloların temizlenmesi veya yeniden kurulması gerektiğinde kolaylık sağlar.
TRUNCATE TABLE problems; 


-- --------------------------------------------------------
-- 2. CAUSES Tablosu (D4-D5: Kök Neden Analizi ve Çözüm)
-- --------------------------------------------------------

CREATE TABLE IF NOT EXISTS causes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    
    -- Problem İlişkisi: Hangi probleme bağlı olduğunu gösterir
    problem_id INT NOT NULL,
    
    -- Ağaç İlişkisi: Bu kaydın bir üst (parent) kaydının ID'si. 
    -- NULL ise, doğrudan Problem'in altındaki ilk seviye nedendir.
    parent_id INT NULL, 
    
    title VARCHAR(255) NOT NULL COMMENT 'Neden-Sonuç ağacındaki bir dal/sebep',
    is_root_cause BOOLEAN DEFAULT FALSE COMMENT 'D4: Kök neden olarak işaretlendi mi?',
    action_plan TEXT NULL COMMENT 'D5: Kök neden için belirlenen kalıcı çözüm (Aksiyon)',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    -- YABANCI ANAHTARLAR (FOREIGN KEYS): Veri Bütünlüğünü Sağlar
    
    -- Problem Silinirse, buna bağlı tüm nedenler de silinir. (ON DELETE CASCADE)
    FOREIGN KEY (problem_id) 
        REFERENCES problems(id) 
        ON DELETE CASCADE,
        
    -- Üst Neden (Parent Cause) Silinirse, buna bağlı alt nedenler de silinir. (ON DELETE CASCADE)
    FOREIGN KEY (parent_id) 
        REFERENCES causes(id) 
        ON DELETE CASCADE
        
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

TRUNCATE TABLE causes; 

-- Performans için İndeksleme: 
-- Özellikle ağaç yapısını çekerken parent_id ve problem_id sütunlarında arama yapılacağı için indeksler önemlidir.
CREATE INDEX idx_causes_problem_id ON causes (problem_id);
CREATE INDEX idx_causes_parent_id ON causes (parent_id);

-- --------------------------------------------------------
-- 3. TEST VERİSİ EKLEME (Opsiyonel ama test için gerekli)
-- --------------------------------------------------------

INSERT INTO problems (title, description, responsible_team, status) VALUES
('Üretim Hattı Durması', 'Otomasyon hattı X, son 3 günde 5 kez plansız duruş yaptı.', 'Bakım Ekibi', 'OPEN'), -- ID 1
('Müşteri Şikayeti - Yüzey Hatası', 'Sevkiyatta %10 yüzey çizik hatası tespit edildi.', 'Kalite Ekibi', 'OPEN'); -- ID 2

-- Problem ID 1 için Neden-Sonuç Ağacı (5 Neden Analizi Simülasyonu)

-- Seviye 1 (Ana Nedenler)
INSERT INTO causes (problem_id, parent_id, title) VALUES
(1, NULL, 'Makine A durdu.'); -- ID 10
INSERT INTO causes (problem_id, parent_id, title) VALUES
(1, NULL, 'Operatör hatası.'); -- ID 11

-- Seviye 2 (ID 10'un Altında)
INSERT INTO causes (problem_id, parent_id, title) VALUES
(1, 10, 'Sigorta attı.'); -- ID 12 (Root Cause olabilir)

-- Seviye 3 (ID 12'nin Altında)
INSERT INTO causes (problem_id, parent_id, title) VALUES
(1, 12, 'Aşırı yüklenme oldu.'); -- ID 13

-- Seviye 4 (ID 13'ün Altında - KÖK NEDEN)
INSERT INTO causes (problem_id, parent_id, title, is_root_cause, action_plan) VALUES
(1, 13, 'Koruma rölesi yanlış ayarlanmış.', TRUE, 'Röle ayarı standarda çekildi ve prosedür güncellendi.'); -- ID 14 (D4 ve D5)