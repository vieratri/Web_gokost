# Gokost

Gokost adalah starter project PHP + MySQL untuk layanan pemesanan / kurir sederhana.

## Persiapan
1. Salin project ke folder web server.
2. Edit `config/db.php` untuk konfigurasi database.
3. Jalankan `composer install`.
4. Jalankan `php setup.php` dari CLI untuk mengimport SQL dan membuat folder `tmp/`.
5. Konfig `config/twilio.php` jika ingin SMS OTP (Twilio).
6. Pastikan folder `tmp/` dan `vendor/` dapat ditulis oleh PHP untuk mPDF.

## Library utama
- Twilio SDK (SMS OTP)
- PhpSpreadsheet (Export Excel)
- mPDF (Export/Preview PDF)

## Catatan keamanan
- Simpan kredensial sensitif di environment, jangan commit.
- Gunakan HTTPS, batasi OTP attempts, validasi input.
