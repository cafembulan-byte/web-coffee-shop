# Deploy ke cPanel Rumahweb

Panduan ini men-deploy Summit Split pada alamat:

```text
https://domain-anda.com/hiking/
```

## Struktur di hosting

Gunakan struktur berikut di File Manager cPanel. `public_html/hiking` adalah satu-satunya folder yang bisa diakses browser.

```text
/home/CPANEL_USERNAME/
├── hiking-private/
│   ├── database/
│   │   └── schema.sql
│   └── src/
│       ├── database.php
│       └── functions.php
└── public_html/
    └── hiking/
        ├── .htaccess
        ├── app-config.php
        ├── admin.php
        ├── index.php
        ├── login.php
        ├── logout.php
        ├── register.php
        └── style.css
```

Jangan upload `tests/`, `DEPLOYMENT.md`, atau database SQLite lama ke `public_html`.

## Cara upload

1. Buka cPanel Rumahweb > **File Manager**.
2. Masuk ke `/home/CPANEL_USERNAME/` dan buat folder `hiking-private`.
3. Upload folder `src/` dan `database/` ke dalam `hiking-private/`.
4. Buka `public_html` dan buat folder `hiking`.
5. Upload **isi folder `public/`** ke `public_html/hiking/`.
6. Salin file `style.css` dari root project ke `public_html/hiking/style.css`.
7. Buka file `public_html/hiking/.htaccess` dan ganti:

```apache
SetEnv SUMMIT_PRIVATE_ROOT /home/CPANEL_USERNAME/hiking-private
```

menjadi username cPanel sebenarnya, contoh:

```apache
SetEnv SUMMIT_PRIVATE_ROOT /home/rumahweb123/hiking-private
```

8. Pastikan permission folder private `755` dan file PHP `644`.
9. Pastikan folder `hiking-private/database` writable oleh PHP, umumnya `755` atau `775`. Jangan jadikan `777` kecuali diminta support Rumahweb.

## Pengaturan PHP cPanel

Di cPanel buka **Select PHP Version** atau **MultiPHP Manager**:

- Pilih PHP 8.2 atau lebih baru.
- Aktifkan `PDO`, `pdo_sqlite`, `SQLite3`, `mbstring`, dan `openssl` jika tersedia.
- Set `display_errors` menjadi `Off` untuk production.
- Set `log_errors` menjadi `On`.

Jika paket hosting tidak menyediakan SQLite, gunakan MySQL Rumahweb dan migrasikan tabel `users` dari `database/schema.sql` sebelum go-live.

## Database pertama kali

Database SQLite akan dibuat otomatis saat halaman pertama dibuka. File akan berada di:

```text
/home/CPANEL_USERNAME/hiking-private/database/summit.sqlite
```

Akun demo dibuat otomatis untuk staging:

```text
admin@example.com / summit-admin
hiker@example.com / summit-user
```

Hapus akun demo atau ganti password-nya sebelum production. Jangan menampilkan password demo ke pengguna nyata.

## Test setelah upload

Buka URL berikut:

```text
https://domain-anda.com/hiking/
https://domain-anda.com/hiking/register.php
https://domain-anda.com/hiking/login.php
```

Hasil yang diharapkan:

- Homepage tampil dengan ranking route dan CSS.
- Registrasi member berhasil dan mengarah ke login.
- Login member mengarah ke homepage.
- `/hiking/admin.php` tanpa login mengarah ke login.
- User biasa mendapat `403` saat membuka admin.
- Admin dapat melihat daftar route dan daftar member.

## HTTPS dan domain

Aktifkan SSL gratis dari **SSL/TLS Status** atau menu SSL Rumahweb. Setelah aktif, gunakan URL `https://` dan aktifkan redirect HTTP ke HTTPS melalui fitur SSL atau `.htaccess` hosting.

## Backup

Backup file berikut secara berkala:

```text
/home/CPANEL_USERNAME/hiking-private/database/summit.sqlite
```

Simpan backup di luar public hosting. Jangan memberikan akses download langsung ke file SQLite.

## Checklist go-live

- [ ] `CPANEL_USERNAME` sudah diganti dengan username sebenarnya.
- [ ] `public_html/hiking` hanya berisi file publik.
- [ ] `src/` dan database berada di luar `public_html`.
- [ ] `pdo_sqlite` tersedia dan aktif.
- [ ] URL HTTPS aktif.
- [ ] Akun demo dihapus atau password diganti.
- [ ] Homepage, register, login, dan admin sudah diuji.
- [ ] Backup SQLite sudah dikonfigurasi.
- [ ] `display_errors=Off` aktif.