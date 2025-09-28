Aşağıda HTML olarak yazılmış README içeriğini, GitHub’ın desteklediği **Markdown** (`.md`) formatına uygun şekilde dönüştürdüm ve düzenledim. GitHub sayfanda bu içeriği doğrudan `README.md` dosyasına yapıştırabilirsin:

---

#  NotifyUser App

##  Uygulama Amacı

Veritabanında bulunan belirli bir müşteri grubuna, yine veritabanında bulunan ve henüz gönderilmemiş olan mesajların Laravel **`Job`** ve **`Queue`** sistemi kullanılarak otomatik olarak gönderilmesini sağlar.

---

##  Uygulama Gereksinimleri

* Laravel 10
* PostgreSQL
* Redis
* PHP 8.2
* Docker

---

##  Uygulamanın Kurulumu ve Kullanımı

1. Öncelikle [Docker Desktop](https://www.docker.com/products/docker-desktop/) uygulaması indirilir.

2. Host makinede projenin kurulacağı dizine terminal üzerinden gidilir ve aşağıdaki komut çalıştırılarak proje dosyaları GitHub üzerinden çekilir:

   ```bash
   git clone https://github.com/dervistprk/notifyuser.git
   ```

   > **Not:** Bu işlem için host makinede [Git](https://git-scm.com/downloads) yüklü olmalıdır.

3. Alternatif olarak, projenin [GitHub repo](https://github.com/dervistprk/notifyuser) adresine gidilip proje manuel olarak da indirilebilir.

4. Proje içindeki `.env.example` dosyasının adı `.env` olarak değiştirilir.

5. Proje root dizininde (Dockerfile’ın bulunduğu yerde) terminal açılarak aşağıdaki komut çalıştırılır:

   ```bash
   docker-compose up -d --build
   ```

   Bu komut, gerekli container’ları indirir ve projeyi başlatır.

6. Aşağıdaki komutla container’ların başarıyla çalışıp çalışmadığı kontrol edilebilir:

   ```bash
   docker ps
   ```

7. Projenin bağımlılıkları yüklenir:
    ```bash
   composer install
   ```
   ```bash
   npm install
   ```

8. Veritabanı bağlantısının doğru yapıldığını test etmek için tarayıcıdan şu adrese gidilir:
   [http://localhost:8000/test-db-connection](http://localhost:8000/test-db-connection)

   > Sayfada **`PostgreSQL bağlantısı başarılı.`** mesajı görünmelidir.

9. Uygulama anahtarını oluşturmak için:

   ```bash
   docker exec -it laravel_app php artisan key:generate
   ```

10. Migration ve seeding işlemleri için:

    ```bash
    docker exec -it laravel_app php artisan migrate:refresh --seed
    ```

11. Eklenen tablo ve verileri görmek için `http://localhost:5050/` adresindeki **PgAdmin** arayüzü kullanılır.

    Giriş bilgileri:

    * **E-posta:** `dervis@admin.com`
    * **Şifre:** `dervis123`

    (Bu bilgiler `docker-compose.yml` dosyasında ayarlanmıştır.)

12. Giriş yaptıktan sonra yeni sunucu eklemek için üst menüden:
    `Object => Register => Server` yolunu izleyin.

13. **General** sekmesinde, sunucu adı olarak istediğiniz ismi verebilirsiniz.
    Örneğin: `notifyuser`

14. **Connection** sekmesindeki alanlara şu bilgiler girilir:

    * **Host name/address:** `postgres`
    * **Username:** `dervis`
    * **Password:** `dervis123`

    Ardından **Save** butonuna tıklayın.

15. Migration ile oluşturulan tablolar şu yoldan görülebilir:
    `Schemas => public => Tables`

16. Tablolardaki verilere erişmek için:

    * İlgili tabloya sağ tıklayıp `View/Edit Data` seçeneğini kullanın
    * veya üst menüden `Tools => Query Tools` yolunu izleyerek sorgu yazın:

    ```sql
    SELECT * FROM public.customers ORDER BY id ASC;
    ```

17. Müşterilere mesaj gönderme işlemini başlatmak için:

    ```bash
    docker exec -it laravel_app php artisan messages:send
    ```

    Bu işlem mesajları kuyruğa alır.

18. Kuyruğu çalıştırmak için:

    ```bash
    docker exec -it laravel_app php artisan queue:work
    ```

    Bu komut, gönderilmeye uygun mesajların (160 karakterden kısa olanlar) gönderilmesini ve gönderim bilgilerinin terminalde görüntülenmesini sağlar.
    Alternatif olarak, `messages` tablosundaki `is_sent` kolonu `true` olan kayıtlar incelenebilir.

19. Manuel çalıştırmak yerine **Laravel Schedule** yapısı kullanarak otomatik gönderim sağlanabilir:

    ```bash
    docker exec laravel_app php artisan schedule:run
    ```
    Bu yapı, sistemde her **2 dakikada bir** mesaj gönderim işlemini otomatik başlatır.

    ##  API
    Projenin API kısmı için `webhook.site` sitesine gönderilen istekleri lokalde çalışan projemize yönlendirmek için **npm** kullanarak **whcli** paketini aşağıdaki komutla yüklememiz gerekiyor:

    ```bash
    npm install -g @webhooksite/cli
    ```
    
    Generate ettiğim **webhook.site** URL adresi: `https://webhook.site/#!/view/932b7ab8-4454-40c9-8d39-6b587aeea9be/50802926-5b7e-426c-b922-058474de0519/1`

    Gelen isteklerin başarılı bir şekilde projemize yönlendirildiği test edilir:
    ```bash
    whcli forward --token=932b7ab8-4454-40c9-8d39-6b587aeea9be --target=http://localhost:8000/api/test-api-connection
    ```
    Gelen mesaj isteği işlenerek ilgili kullanıcıya mesaj gönderim işlemi başlatılır.
    ```bash
    whcli forward --token=932b7ab8-4454-40c9-8d39-6b587aeea9be --target=http://localhost:8000/api/receive-message
    ```
    > **Not:** Yukarıdaki komutlar, generate edilen **webhook.site** adresinde dashboard'da hazır olarak bulunuyor. Hedef kısmını işleme göre değiştirip komutu tekrar çalıştırıyoruz.
    
    API üzerinden gönderilen mesaj isteklerinin queue worker ile işlenmesinin takibi için **docker-compose logs -f worker** komutu kullanılabilir.

    ##  Test
    Projeyi docker ile lokalde ayağa kaldırıp, `webhook.site` yönlendirmesi ile istekleri projemizde karşılamaya başladıktan sonra unit ve integration testler koşulabilir:
    ```bash
    docker exec -it laravel_app php artisan test
    ```