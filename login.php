<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Session nur starten wenn nicht aktiv
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if(isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true) {
    header("location: mitarbeiterbereich.php");
    exit;
}

require_once "config.php";

$email = $passwort = "";
$email_err = $passwort_err = $login_err = "";

if($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sichere POST-Prüfung
    $email = isset($_POST["email"]) ? trim($_POST["email"]) : "";
    $passwort = isset($_POST["passwort"]) ? trim($_POST["passwort"]) : "";
    
    if(empty($email)) {
        $email_err = "E-Mail eingeben.";
    }
    if(empty($passwort)) {
        $passwort_err = "Passwort eingeben.";
    }
    
    if(empty($email_err) && empty($passwort_err)) {
        // $param_email DEFINIEREN! (fehlte)
        $param_email = $email;
        
        $sql = "SELECT id, email, passwort FROM mitarbeiter WHERE email = ?";
        if($stmt = mysqli_prepare($link, $sql
