<?php
use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(dirname(__DIR__));
$dotenv->load();

$secret_key = $_ENV['SECRET_KEY'];

return ["secret_key" => $secret_key];