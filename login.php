<?php
session_start();
if(isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true) {
    header("location: mitarbeiterbereich.php");
    exit;
}
require_once "config.php";

$email = $passwort = "";
$email_err = $passwort_err = $login_err = "";

if($_SERVER["REQUEST_METHOD"] == "POST") {
    if(empty(trim($_POST["email"]))) $email_err = "E-Mail eingeben.";
    else $email = trim($_POST["email"]);
    
    if(empty(trim($_POST["passwort"]))) $passwort_err = "Passwort eingeben.";
    else $passwort = trim($_POST["passwort"]);
    
    if(empty($email_err) && empty($passwort_err)) {
        $sql = "SELECT id, email, passwort FROM mitarbeiter WHERE email = ?";
        if($stmt = mysqli_prepare($link, $sql)) {
            mysqli_stmt_bind_param($stmt, "s", $param_email);
            $param_email = $email;
            mysqli_stmt_execute($stmt);
            mysqli_stmt_store_result($stmt);
            if(mysqli_stmt_num_rows($stmt) == 1) {
                mysqli_stmt_bind_result($stmt, $id, $email, $hashed_passwort);
                if(mysqli_stmt_fetch($stmt)) {
                    if(password_verify($passwort, $hashed_passwort)) {
                        $_SESSION["loggedin"] = true;
                        $_SESSION["id"] = $id;
                        $_SESSION["email"] = $email;
                        header("location: mitarbeiterbereich.php");
                        exit;
                    } else {
                        $login_err = "Falsche E-Mail oder Passwort.";
                    }
                }
            } else {
                $login_err = "Falsche E-Mail oder Passwort.";
            }
            mysqli_stmt_close($stmt);
        }
    }
    mysqli_close($link);
}
?>
<!-- Ihr HTML-Formular hier einfügen, mit Fehlermeldung: <?php if(!empty($login_err)) echo '<div class="error">' . $login_err . '</div>'; ?> -->
