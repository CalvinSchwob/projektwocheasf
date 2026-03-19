<?php
define('DB_SERVER', 'localhost');
define('DB_USERNAME', 'root');  // Standard-XAMPP
define('DB_PASSWORD', '');      // Standard leer
define('DB_NAME', 'optiker_db');

$link = mysqli_connect(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);
if($link === false) {
    die("Verbindungsfehler: " . mysqli_connect_error());
}
?>