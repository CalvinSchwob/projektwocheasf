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
        if($stmt = mysqli_prepare($link, $sql)) {
            mysqli_stmt_bind_param($stmt, "s", $param_email);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_store_result($stmt);
            
            if(mysqli_stmt_num_rows($stmt) == 1) {
                mysqli_stmt_bind_result($stmt, $id, $email, $hashed_passwort);
                mysqli_stmt_fetch($stmt);
                
                if(password_verify($passwort, $hashed_passwort)) {
                    session_regenerate_id(); // Sicherheit
                    $_SESSION["loggedin"] = true;
                    $_SESSION["id"] = $id;
                    $_SESSION["email"] = $email;
                    header("location: mitarbeiterbereich.php");
                    exit;
                } else {
                    $login_err = "Falsche E-Mail oder Passwort.";
                }
            } else {
                $login_err = "Falsche E-Mail oder Passwort.";
            }
            mysqli_stmt_close($stmt);
        } else {
            $login_err = "DB-Fehler: " . mysqli_error($link);
        }
    }
    mysqli_close($link);
}
?>
<!DOCTYPE html>
<html>
<head><title>Login</title></head>
<body>
<?php if(!empty($login_err)): ?>
    <div style="color:red"><?php echo $login_err; ?></div>
<?php endif; ?>
<form method="post">
    <input type="email" name="email" value="<?php echo htmlspecialchars($email); ?>" placeholder="admin@optiker.de" required><br>
    <input type="password" name="passwort" placeholder="password" required><br>
    <button type="submit">Login</button>
</form>
</body>
</html>
