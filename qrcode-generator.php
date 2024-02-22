<?php
require_once 'vendor/autoload.php';
use chillerlan\QRCode\QRCode;
use chillerlan\QRCode\QROptions;
date_default_timezone_get();
$timestamp = date('m-d-y', time());
$randomString = bin2hex(random_bytes(4));
$qrFileName = "qrcode-110-{$timestamp}-{$randomString}.png";
$link = "?cardid=1000118";
$qrCode = (new QRCode(new QROptions([
    'outputType' => QRCode::OUTPUT_IMAGE_PNG,
    'eccLevel' => QRCode::ECC_L,
    'imageBase64' => false,
])))->render($link);
file_put_contents($qrFileName, $qrCode);
