<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once "config.php";

$sql = "SELECT * FROM mitarbeiter";
$result = mysqli_query($link, $sql);

if (mysqli_num_rows($result) > 0) {
    echo "DB OK! Gefundene Mitarbeiter:<br>";
    while($row = mysqli_fetch_assoc($result)) {
        echo "ID: " . $row["id"] . " - " . $row["email"] . "<br>";
    }
} else {
    echo "Keine Daten gefunden oder Tabelle leer.";
}
mysqli_close($link);
?>
