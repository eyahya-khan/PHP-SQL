<?php

$host 	  = 'localhost';
$database = 'blog';
$user     = 'root';
$password = 'root';
$charset  = 'utf8mb4';

$dns 	  = "mysql:host={$host};dbname={$database};charset={$charset}";


$options = [
	PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, // Error mode
	PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC, // Fetch style, fetching associative array
];


try {

	$dbconnect = new PDO($dns, $user, $password, $options);
} catch (\PDOException $e) {
	
	throw new \PDOException($e->getMessage(), (int) $e->getCode());
}
