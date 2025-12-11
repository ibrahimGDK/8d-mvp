-- --------------------------------------------------------
-- XRAMP / LOCAL INSTALLATION READY SQL SCHEMA
-- --------------------------------------------------------
DROP DATABASE IF EXISTS 8d_db;

-- 1. DATABASE OLUŞTURMA
CREATE DATABASE IF NOT EXISTS 8d_db 
    CHARACTER SET utf8mb4 
    COLLATE utf8mb4_unicode_ci;

USE 8d_db;

-- --------------------------------------------------------
-- 2. PROBLEMS Tablosu (D1-D2: Problem Tanımlama)
-- --------------------------------------------------------

CREATE TABLE IF NOT EXISTS problems (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL COMMENT 'Problemin kısa başlığı',
    description TEXT NULL COMMENT 'D2: Problemin detaylı açıklaması',
    responsible_team VARCHAR(100) NOT NULL COMMENT 'D1: Problemin sorumlusu (Ekip/Kişi)',
    
    -- Yeni eklenen kolonlar
    priority ENUM('LOW', 'MEDIUM', 'HIGH') NOT NULL DEFAULT 'MEDIUM' COMMENT 'Problemin öncelik seviyesi',
    status ENUM('OPEN', 'CLOSED') DEFAULT 'OPEN' NOT NULL COMMENT 'Problemin durumu',

    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) 
ENGINE=InnoDB 
DEFAULT CHARSET=utf8mb4 
COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- 3. CAUSES Tablosu (D4-D5: Kök Neden Analizi ve Çözüm)
-- --------------------------------------------------------

CREATE TABLE IF NOT EXISTS causes (
    id INT AUTO_INCREMENT PRIMARY KEY,

    problem_id INT NOT NULL,
    parent_id INT NULL,

    title VARCHAR(255) NOT NULL COMMENT 'Neden-Sonuç ağacındaki bir dal/sebep',
    is_root_cause BOOLEAN DEFAULT FALSE COMMENT 'D4: Kök neden olarak işaretlendi mi?',
    action_plan TEXT NULL COMMENT 'D5: Kök neden için belirlenen kalıcı çözüm',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY (problem_id) REFERENCES problems(id) ON DELETE CASCADE,
    FOREIGN KEY (parent_id) REFERENCES causes(id) ON DELETE CASCADE
) 
ENGINE=InnoDB 
DEFAULT CHARSET=utf8mb4 
COLLATE=utf8mb4_unicode_ci;

-- Performans İndeksleri
CREATE INDEX idx_causes_problem_id ON causes (problem_id);
CREATE INDEX idx_causes_parent_id ON causes (parent_id);

