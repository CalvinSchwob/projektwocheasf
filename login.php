<?php
session_start(); // Ganz oben!

// Bereits eingeloggt? Weiterleiten
if(isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true) {
    header("Location: mitarbeiterbereich.php");
    exit;
}

// DB-Verbindung
$db = new mysqli("localhost", "root", "", "optiker_db"); // root/leer für XAMPP!
if ($db->connect_error) {
    die("Connection Failed: " . $db->connect_error);
}

$email = $passwort = "";
$email_err = $passwort_err = $login_err = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST["email"] ?? "");
    $passwort = trim($_POST["passwort"] ?? "");
    
    if (empty($email)) {
        $email_err = "E-Mail eingeben.";
    }
    if (empty($passwort)) {
        $passwort_err = "Passwort eingeben.";
    }
    
    if (empty($email_err) && empty($passwort_err)) {
        // Prepared Statement (sicher!)
        $stmt = $db->prepare("SELECT id, email, passwort FROM mitarbeiter WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();
        
        if ($stmt->num_rows == 1) {
            $stmt->bind_result($id, $email, $hashed_passwort);
            $stmt->fetch();
            
            if (password_verify($passwort, $hashed_passwort)) {
                // Login OK!
                $_SESSION["loggedin"] = true;
                $_SESSION["id"] = $id;
                $_SESSION["email"] = $email;
                
                header("Location: mitarbeiterbereich.php");
                exit;
            } else {
                $login_err = "Falsche E-Mail oder Passwort.";
            }
        } else {
            $login_err = "Falsche E-Mail oder Passwort.";
        }
        $stmt->close();
    }
}
$db->close();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Mitarbeiter Login</title>
    <style>body{font-family:Arial; max-width:400px; margin:100px auto;}</style>
</head>
<body>
    <h2>Optiker Mitarbeiter Login</h2>
    
    <?php if(!empty($login_err)): ?>
        <div style="color:red; padding:10px; border:1px solid red; margin:10px 0;">
            <?php echo $login_err; ?>
        </div>
    <?php endif; ?>
    
    <form method="POST">
        <p>
            <label>E-Mail:</label><br>
            <input type="email" name="email" value="<?php echo htmlspecialchars($email); ?>" 
                   placeholder="admin@optiker.de" style="width:100%; padding:10px;" required>
        </p>
        <p>
            <label>Passwort:</label><br>
            <input type="password" name="passwort" placeholder="password" 
                   style="width:100%; padding:10px;" required>
        </p>
        <button type="submit" style="width:100%; padding:12px; background:#007cba; color:white; border:none; cursor:pointer;">
            Einloggen
        </button>
    </form>
</body>
</html>
