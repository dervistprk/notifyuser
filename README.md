<!DOCTYPE html>
<html lang="tr">
<head>
  <meta charset="utf-8">
  <title></title>
  <style>
    a {
      color: #007bff;
      text-decoration: underline;
    }
  </style>
</head>
<body style="font-family:'Segoe UI',serif; font-size: 16px;" >
    <h2 style="text-align:center">NotifyUser App</h2>
    <h3>Uygulama Amacı</h3>
    <p>
        Veritabanında bulunan belirli bir müşteri grubuna yine veritabanında bulunan ve henüz gönderilmemiş olan mesajların Laravel <code>Job</code> ve <code>Queue</code> mantığını kullanarak otomatik olarak gönderimini sağlar.
    </p>
    <h3>Uygulama Gereksinimleri</h3>
    <ul>
        <li>Laravel 10</li>
        <li>PostgreSQL</li>
        <li>Redis</li>
        <li>PHP 8.2</li>
        <li>Docker</li>
    </ul>
    <h3>Uygulamanın Kurulumu ve Kullanımı</h3>
    <ol>
        <li>Öncelikle <a href="https://www.docker.com/products/docker-desktop/">şu adresten</a> Docker Desktop uygulaması indirilir.</li>
        <li>
            Host makinede projenin kurulacağı dizine terminal üzerinden gidilir ve <code>git clone https://github.com/dervistprk/notifyuser.git</code> komutu ile proje dosyaları github üzerinden çekilir. <span style="color: #ef4444">Not: Bu işlem için host makinede <a href="https://git-scm.com/downloads">git</a> uygulamasının kurulu olması gerekir.</span>
        </li>
        <li>İkinci maddeye alternatif olarak projenin <a href="https://github.com/dervistprk/notifyuser">repo</a> adresine gidilip proje dosyaları manuel olarak da indirilebilir.</li>
        <li>Proje içindeki <code>.env.example</code> dosyasının adı <code>.env</code> olarak değiştirilir.</li>
        <li>Proje root dizininde (Dockerfile bulunan dizin) terminal açılarak <code>docker-compose up -d --build</code> komutu çalıştırılır ve proje gereksinimleri pull edilip proje ayağa kaldırılır.</li>
        <li>Aynı root dizininde <code>docker ps</code> komutu ile projeye ait container'ların çalıştığını görebilirsiniz.</li>
        <li>Projede veritabanı bağlantısının doğru bir şekilde yapıldığını test etmek için <code>http://localhost:8000/test-db-connection</code> adresine gidip tarayıcıda <code>PostgreSQL bağlantısı başarılı.</code> mesajını görmemiz gerekir.</li>
        <li>İkinci adımda adını değiştirdiğimiz <code>.env</code> dosyası içerisindeki <code>APP_KEY</code> değerini set etmek için <code>docker exec -it laravel_app php artisan key:generate</code> komutu kullanılır.</li>
        <li><code>docker exec -it laravel_app php artisan migrate:refresh --seed</code> komutu ile veritabanında tablolar oluşturulur ve içerisine fake veriler eklenir.</li>
        <li>Eklediğimiz bu tablo ve verileri görebilmek için <code>http://localhost:5050/</code> adresindeki Pgadmin arayüzü kullanılır. Pgadmine giriş yapabilmek için <code>dervis@admin.com</code> e-posta adresi ve <code>dervis123</code> şifresi kullanılır.(Bu değerler docker-compose.yml dosyasında ayarlandı.)</li>
        <li>Pgadmin sistemine giriş yaptıktan sonra sol üst köşedeki menüden <code>Object=>Register=>Server</code> yolu izlenerek yeni sunucu ekleme penceresi açılır.</li>
        <li>Bu penceredki <code>General</code> sekmesindeki name alanına istediğiniz bir isim verebilirsiniz. Örneğin:<code>notifyuser</code></li>
        <li><code>Connection</code> sekmesinde <code>Host name/address</code> alanına <code>postgres</code> değeri girilir. <code>Username</code> alanına <code>dervis</code> değeri girilir ve son olarak <code>Password</code> alanına <code>dervis123</code> değeri girilerek <code>Save</code> butonuna tıklanır.(Bu değerler docker-compose.yml dosyasında ayarlandı.)</li>
        <li>Yukarıda migration ile oluşturduğumuz tablolar, bağlantı kurduğumuz db içerisindeki <code>Schemas=>public=>tables</code> yolu kullanılarak görülebilir.</li>
        <li>Tablodaki verilere ilgili tabloya sağ tıklayıp <code>View/Edit Data</code> yolu ile veya üst menüden <code>Tools=>Query Tools</code> yolu izlenerek açılan sorgu penceresine SQL sorgusu yazılarak erişilebilir. Örneğin: <code>SELECT * FROM public.customers ORDER BY id ASC </code></li>
        <li>Uygulamanın müşterilere mesaj gönderme işlemini başlatmak için <code>docker exec -it laravel_app php artisan messages:send</code> komutu çalıştırılarak işlem kuyruğa alınır.</li>
        <li><code>docker exec -it laravel_app php artisan queue:work</code> komutu ile kuyruk çalıştırılır ve gönderilmeye uygun mesajların(160 karakter veya daha az olanlar) gönderildiği bilgisi terminalden görülebilir. Alternatif olarak <code>messages</code> tablosu içerisindeki <code>is_sent</code> kolonunun <code>true</code> olduğu görülerek de mesajın gönderilmiş olduğu anlaşılabilir.</li>
        <li>Üstteki iki adımlı yapıyı manuel olarak kullanmak yerine Laravel Schedule yapısı kullanılarak gönderilmeyen ve gönderilmeye uygun olarak bekleyen mesajların otomatik olarak gönderilmesi sağlanabilir. Bunun için <code>docker exec laravel_app php artisan schedule:run</code> komutu kullanılır. Bu yapı sistemi 2 dakikada bir otomatik olarak çalıştırır ve mesaj gönderim işlemini sağlar.</li>
    </ol>
</body>
</html>
