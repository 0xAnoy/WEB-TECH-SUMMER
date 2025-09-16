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
    'username' => 'm46bsv@tiksofi.uk',
    'password' => 'Nr5qB3kxacY7B9gv',
    'from_email' => 'm46bsv@tiksofi.uk',
    'from_name' => 'eShop - AIUB',
    'secure' => 'tls'               // 'tls' or 'ssl'
];
?>