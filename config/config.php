<?php
require_once __DIR__ . '/../vendor/autoload.php';

use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(dirname(__DIR__));
$dotenv->load();

$secret_key = $_ENV['SECRET_KEY'];
$base_url = $_ENV['BASE_URL'] ?? "http://localhost:8000";

return ["secret_key" => $secret_key, "base_url" => $base_url];