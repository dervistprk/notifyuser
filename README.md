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

7. Veritabanı bağlantısının doğru yapıldığını test etmek için tarayıcıdan şu adrese gidilir:
   [http://localhost:8000/test-db-connection](http://localhost:8000/test-db-connection)

   > Sayfada **`PostgreSQL bağlantısı başarılı.`** mesajı görünmelidir.

8. Uygulama anahtarını oluşturmak için:

   ```bash
   docker exec -it laravel_app php artisan key:generate
   ```

9. Migration ve seeding işlemleri için:

   ```bash
   docker exec -it laravel_app php artisan migrate:refresh --seed
   ```

10. Eklenen tablo ve verileri görmek için `http://localhost:5050/` adresindeki **PgAdmin** arayüzü kullanılır.

    Giriş bilgileri:

    * **E-posta:** `dervis@admin.com`
    * **Şifre:** `dervis123`

    (Bu bilgiler `docker-compose.yml` dosyasında ayarlanmıştır.)

11. Giriş yaptıktan sonra yeni sunucu eklemek için üst menüden:
    `Object => Register => Server` yolunu izleyin.

12. **General** sekmesinde, sunucu adı olarak istediğiniz ismi verebilirsiniz.
    Örneğin: `notifyuser`

13. **Connection** sekmesindeki alanlara şu bilgiler girilir:

    * **Host name/address:** `postgres`
    * **Username:** `dervis`
    * **Password:** `dervis123`

    Ardından **Save** butonuna tıklayın.

14. Migration ile oluşturulan tablolar şu yoldan görülebilir:
    `Schemas => public => Tables`

15. Tablolardaki verilere erişmek için:

    * İlgili tabloya sağ tıklayıp `View/Edit Data` seçeneğini kullanın
    * veya üst menüden `Tools => Query Tools` yolunu izleyerek sorgu yazın:

    ```sql
    SELECT * FROM public.customers ORDER BY id ASC;
    ```

16. Müşterilere mesaj gönderme işlemini başlatmak için:

    ```bash
    docker exec -it laravel_app php artisan messages:send
    ```

    Bu işlem mesajları kuyruğa alır.

17. Kuyruğu çalıştırmak için:

    ```bash
    docker exec -it laravel_app php artisan queue:work
    ```

    Bu komut, gönderilmeye uygun mesajların (160 karakterden kısa olanlar) terminalde görüntülenmesini sağlar.
    Alternatif olarak, `messages` tablosundaki `is_sent` kolonu `true` olan kayıtlar incelenebilir.

18. Manuel çalıştırmak yerine **Laravel Schedule** yapısı kullanarak otomatik gönderim sağlanabilir:

    ```bash
    docker exec laravel_app php artisan schedule:run
    ```

    Bu yapı, sistemde her **2 dakikada bir** mesaj gönderim işlemini otomatik başlatır.