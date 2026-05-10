<?php
define('DB_HOST', 'mysqldb');
define('DB_USER', 'cruduser');
define('DB_PASS', 'crudpassword');
define('DB_NAME', 'crudapp');

function getConnection() {
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    if ($conn->connect_error) {
        die(json_encode(['error' => 'Connection failed: ' . $conn->connect_error]));
    }
    $conn->set_charset('utf8mb4');
    return $conn;
}
?>
