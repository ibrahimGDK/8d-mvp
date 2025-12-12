#  8D Problem Çözme Uygulaması (MVP)

Bu proje, bir üretimde veya kurumsal ortamda yaygın olarak kullanılan Kalite Yönetimi aracı olan 8D Problem Çözme Metodolojisini dijitalleştiren Full Stack bir prototiptir.

Projenin temel amacı, karmaşık Kök Neden Analizi (D4/D5) süreçlerini, yüksek performanslı ve hiyerarşik (Ağaç Yapısı) bir API ile yönetme yeteneğimizi göstermektir.

## 🛠️ Proje Kurulumu ve Çalıştırma

Bu doküman, projeyi **yerel ortamda** çalıştırmak isteyen geliştiricilerin hızlı ve sorunsuz bir kurulum yapabilmesi için hazırlanmıştır.

### 📋 Gereksinimler

Projeyi başarıyla çalıştırmak için sisteminizde aşağıdaki yazılımların kurulu olması gerekmektedir:

| Yazılım             | Önerilen Sürüm  | Notlar                                               |
| :------------------ | :-------------- | :--------------------------------------------------- |
| **PHP**             | 8.2 veya üzeri  | Backend için gereklidir.                             |
| **Node.js**         | 22 veya üzeri   | Frontend geliştirme ortamı ve `npm` için gereklidir. |
| **MySQL  | 10.4 veya üzeri | Veritabanı yönetim sistemi.                          |

### ⚙️ Adım Adım Kurulum

Lütfen adımları belirtilen sırayla uygulayın.

```bash
git clone https://github.com/ibrahimGDK/8d-mvp.git
cd 8d-mvp

cd backend
copy .env.example .env
.env dosyasını doldurun.

cd ..
mysql -u root -p < backend/database/schema.sql

cd frontend
npm install

cd ../backend
php -S localhost:8000 -t public

Ayrı bir terminal açın ve:
cd frontend
npm run dev


🌐 Uygulamayı Başlatma

Backend ve Frontend başarıyla çalıştırıldıktan sonra tarayıcınızı açarak şu adresi ziyaret edin:

👉 http://localhost:5173

Frontend burada çalışacak ve uygulamayı tam fonksiyonlu şekilde kullanabileceksiniz.
```
