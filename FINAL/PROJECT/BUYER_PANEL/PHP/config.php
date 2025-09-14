<?php

$host = "localhost";
$user = "root";
$pass = "";
$db   = "pc_webstore";

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
$conn->set_charset("utf8mb4");

// SMTP config
$SMTP_CONFIG = [
    'host' => 'mail.smtp2go.com',   
    'port' => 587,                  // 465 (SSL) or 587 (STARTTLS)
    'username' => 'eshopmailer',
    'password' => '207nnncyzmghQ1NH',
    'from_email' => 'bultuyudre@necub.com	',
    'from_name' => 'eShop - AIUB',
    'secure' => 'tls'               // 'tls' or 'ssl'
];
?>