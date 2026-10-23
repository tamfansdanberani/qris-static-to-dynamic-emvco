<div align="center">

# 💳 QRIS Static to Dynamic EMVCo Converter (PHP)

**A high-performance, zero-dependency PHP library for transforming Indonesian Static QRIS into Dynamic QRIS with real-time nominal injection and ASPI/Bank Indonesia-compliant CRC16-CCITT recalculation.**

[![Latest Version](https://img.shields.io/badge/version-1.0.0-blue.svg?style=flat-square)](https://github.com/tamfansdanberani/qris-static-to-dynamic-emvco)
[![PHP Version](https://img.shields.io/badge/PHP-%3E%3D%207.4%20%7C%208.x-8892BF.svg?style=flat-square&logo=php)](https://php.net)
[![License: MIT](https://img.shields.io/badge/License-MIT-green.svg?style=flat-square)](LICENSE)
[![Zero Dependency](https://img.shields.io/badge/dependencies-0-brightgreen.svg?style=flat-square)](composer.json)
[![Standard](https://img.shields.io/badge/Standard-EMVCo%20%2F%20ASPI%20QRIS-orange.svg?style=flat-square)](https://aspi-indonesia.or.id)

[Overview](#-overview) •
[Key Features](#-key-features) •
[Installation](#-installation) •
[Usage Guide](#-usage-guide) •
[EMVCo Specification](#-emvco-specification-breakdown) •
[Framework Integration](#-framework-integration) •
[License](#-license)

</div>

---

## 📌 Overview

Most merchant QRIS issued in Indonesia (GoPay, BCA, Mandiri, ShopeePay, DANA, OVO, LinkAja) are **Static QRIS** (EMVCo Tag `01` = `11`). In standard static mode, customers must manually type the payment amount on their mobile banking or e-wallet application, which introduces human error and complicates automated order matching.

**`qris-static-to-dynamic-emvco`** programmatically converts any merchant static QRIS payload into a **Dynamic QRIS** (EMVCo Tag `01` = `12`), injects a fixed transaction amount (Tag `54`), and computes the required CRC16-CCITT checksum (Tag `63`).

When scanned with any Indonesian banking app or e-wallet (BCA Mobile, Livin by Mandiri, GoPay, OVO, ShopeePay, Dana, SeaBank, etc.), **the payment amount is automatically locked**, enabling zero-fee, automated, self-hosted payment reconciliation.

---

## ⚡ Key Features

- **Standard Compliance**: Fully adheres to Bank Indonesia & ASPI EMVCo QR Code Merchant-Presented Mode specifications.
- **Dynamic Conversion**: Converts Point of Initiation Method from Static (`11`) to Dynamic (`12`).
- **Precision Nominal Injection**: Injects exact amount into Tag `54` with proper 2-digit length padding.
- **Accurate Checksum Recalculation**: Computes CRC16-CCITT (`0xFFFF` polynomial `0x1021`) as mandated by the EMVCo standard.
- **Universal Merchant Support**: Compatible with static QRIS from GoPay, GoBiz, ShopeePay, BCA, BRI, Mandiri, BNI, Dana, and standard MPM QRIS.
- **Zero External Dependencies**: Pure PHP implementation; lightweight and instant execution with zero bloat.
- **Broad Compatibility**: Supports PHP 7.4, 8.0, 8.1, 8.2, and 8.3+.

---

## 📦 Installation

Install via [Composer](https://getcomposer.org/):

```bash
composer require tamfansdanberani/qris-static-to-dynamic-emvco
```

*Or manually clone and include via PSR-4 autoloading:*

```bash
git clone https://github.com/tamfansdanberani/qris-static-to-dynamic-emvco.git
```

---

## 🚀 Usage Guide

### Basic Example

```php
<?php

require 'vendor/autoload.php';

use Tamsfinity\Qris\QrisDynamic;

// 1. Static QRIS string from your Merchant (GoPay, BCA, etc.)
$staticQris = '00020101021126610014COM.GO-JEK.WWW01189360091437094188610210G7094188610303UMI51440014ID.CO.QRIS.WWW0215ID10264783168090303UMI5204899953033605802ID5925TAMSFINITY STUDIO, Digita6008BOYOLALI61055731162070703A016304F500';

// 2. Exact amount to charge (e.g., Rp 50.134 with unique code)
$amount = 50134;

try {
    // 3. Generate dynamic QRIS string
    $dynamicQris = QrisDynamic::generate($staticQris, $amount);

    echo $dynamicQris;
} catch (\InvalidArgumentException $e) {
    echo "Error generating QRIS: " . $e->getMessage();
}
```

The resulting `$dynamicQris` string can be passed directly to any QR rendering library (such as `chillerlan/php-qrcode`, `endroid/qr-code`, or rendered on frontend canvas/SVG).

---

## 🔬 EMVCo Specification Breakdown

| Tag | Name | Static Value | Dynamic Value (Output) | Purpose |
| :---: | :--- | :---: | :---: | :--- |
| **`00`** | Payload Format Indicator | `01` | `01` | Standard EMVCo version |
| **`01`** | Point of Initiation Method | **`11`** (Static) | **`12`** (Dynamic) | Instructs scanner that amount is predefined |
| **`53`** | Transaction Currency | `360` | `360` | ISO 4217 Currency Code for IDR |
| **`54`** | Transaction Amount | *(None)* | **`540550134`** | Injected transaction amount |
| **`58`** | Country Code | `ID` | `ID` | Republic of Indonesia |
| **`63`** | Checksum (CRC16) | `6304F500` | **`6304XXXX`** | Recomputed polynomial `0x1021` checksum |

---

## 🛠 Framework Integration

### Laravel Example

```php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Tamsfinity\Qris\QrisDynamic;

class PaymentController extends Controller
{
    public function createInvoice(Request $request)
    {
        $baseAmount = 50000;
        $uniqueCode = rand(10, 999);
        $totalAmount = $baseAmount + $uniqueCode; // Rp 50,xxx

        $staticQris = config('services.qris.static_string');
        $dynamicQris = QrisDynamic::generate($staticQris, $totalAmount);

        return response()->json([
            'order_id'     => 'INV-' . time(),
            'amount'       => $totalAmount,
            'qr_string'    => $dynamicQris,
            'expires_at'   => now()->addMinutes(15)->toIso8601String(),
        ]);
    }
}
```

---

## 🔒 Security Best Practices

1. **Unique Code Matching**: Combine dynamic amount injection with random 2-to-3 digit unique codes (e.g., Rp 50.123, Rp 50.456) to ensure 100% collision-free transaction reconciliation.
2. **Time Expiration**: Pair generated dynamic QR codes with a 10 to 30-minute expiry window on your server.
3. **Data Integrity**: Always use the built-in CRC16 verification before dispatching QR strings to payment interfaces.

---

## 🤝 Contributing

Contributions, issues, and feature requests are welcome!  
Feel free to check the [issues page](https://github.com/tamfansdanberani/qris-static-to-dynamic-emvco/issues).

---

## 📄 License

This project is open-source software licensed under the **[MIT License](LICENSE)**.

Developed and maintained with ❤️ by [tamfansdanberani](https://github.com/tamfansdanberani) & [Tamsfinity](https://tamsfinity.web.id).
