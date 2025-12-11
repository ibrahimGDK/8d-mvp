# 🚀 8D Problem Çözme Uygulaması (MVP)

Bu proje, 8 Boyutlu Problem Çözme (8D) metodolojisini uygulamanıza olanak tanıyan bir Minimum Uygulanabilir Üründür (MVP).

## 💡 8D Problem Çözme Nedir?

8D (Eight Disciplines), bir problemi tanımlamak, kök nedenini bulmak, kalıcı bir çözüm uygulamak ve problem tekrarını önlemek için kullanılan, ekip temelli bir problem çözme metodolojisidir.

---

## 🛠️ Proje Kurulumu ve Çalıştırma

Bu doküman, projeyi **yerel ortamda** çalıştırmak isteyen geliştiricilerin hızlı ve sorunsuz bir kurulum yapabilmesi için hazırlanmıştır.

### 📋 Gereksinimler

Projeyi başarıyla çalıştırmak için sisteminizde aşağıdaki yazılımların kurulu olması gerekmektedir:

| Yazılım             | Önerilen Sürüm                     | Notlar                                                   |
| :------------------ | :--------------------------------- | :------------------------------------------------------- |
| **PHP**             | 8.2 veya üzeri                     | Backend için gereklidir.                                 |
| **Node.js**         | 22 veya üzeri                      | Frontend geliştirme ortamı ve `npm` için gereklidir.     |
| **MySQL / MariaDB** | 10.4 veya üzeri                    | Veritabanı yönetim sistemi. (MariaDB ile tam uyumludur.) |
| **npm**             | Otomatik olarak Node.js ile gelir. | Paket yönetimi için kullanılır.                          |

### ⚙️ Adım Adım Kurulum

Lütfen adımları belirtilen sırayla takip edin.


````bash
git clone https://github.com/ibrahimGDK/8d-mvp.git
cd 8d-mvp

cd backend
copy .env.example .env

cd ..
mysql -u root -p < backend/schema.sql

cd frontend
npm install

cd ../backend
php -S localhost:8000 -t public

Ayrı bir terminal açın ve:
cd 8d-mvp/frontend
npm run dev 


