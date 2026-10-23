# QRIS Dynamic EMVCo (PHP)

Library PHP mandiri dan ringan tanpa dependensi untuk mengubah **QRIS Statis** (GoPay, BCA, Mandiri, ShopeePay, DANA, OVO, dll) menjadi **QRIS Dinamis** dengan nominal khusus (*amount injection*) dan penghitungan ulang checksum **CRC16-CCITT**.

Cocok digunakan untuk sistem pembayaran otomatis tanpa biaya perantara payment gateway.

## ✨ Fitur
- Standar EMVCo QRIS Bank Indonesia / ASPI.
- Mengubah Point of Initiation Method dari Statis (11) ke Dinamis (12).
- Menyuntikkan tag nominal transaksi Tag 54.
- Menghitung ulang Checksum CRC16-CCITT Tag 63 secara otomatis.
- Mendukung PHP 7.4 hingga PHP 8.x.

## 🚀 Instalasi
`ash
composer require tamfansdanberani/qris-static-to-dynamic-emvco
`

## 📖 Cara Penggunaan

`php
<?php
require 'vendor/autoload.php';

use Tamsfinity\Qris\QrisDynamic;

// String QRIS statis dari Merchant (GoPay / BCA / ShopeePay / dll)
 = '00020101021126610014COM.GO-JEK.WWW...6304F500';

// Nominal yang ingin ditagihkan (misal: Rp 50.134)
 = 50134;

// Dapatkan QRIS Dinamis
 = QrisDynamic::generate(, );

echo ;
`

String $dynamicQris yang dihasilkan bisa langsung dirender menggunakan library QR Code generator apa saja (SVG, PNG, Canvas) atau dikirimkan ke aplikasi client.

## 📄 Lisensi
MIT License.
