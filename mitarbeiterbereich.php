<?php
session_start();
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: login.php");
    exit;
}
?>
<h1>Mitarbeiterbereich: Willkommen, <?php echo htmlspecialchars($_SESSION["email"]); ?>!</h1>
<!-- Hier Ihren Inhalt -->
<a href="logout.php">Abmelden</a>
