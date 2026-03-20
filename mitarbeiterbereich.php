<?php
session_start();
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: login.php");
    exit;
}
?>
<h1>Mitarbeiterbereich: Willkommen, <?php echo htmlspecialchars($_SESSION["email"]); ?>!</h1>
<!-- Hier Ihren Inhalt -->
 <?php
session_start();

$db = new mysqli("localhost", "root", "", "optiker_db");
if ($db->connect_error) {
    die("Connection Failed: " . $db->connect_error);
}

$email = "";
$email_err = $passwort_err = $success = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST["email"] ?? "");
    $passwort = trim($_POST["passwort"] ?? "");
    
    // Validierung
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $email_err = "Gültige E-Mail eingeben.";
    }
    if (empty($passwort) || strlen($passwort) < 6) {
        $passwort_err = "Passwort mind. 6 Zeichen.";
    }
    
    if (empty($email_err) && empty($passwort_err)) {
        // **AUTOMATISCHER HASH**
        $hashed_passwort = password_hash($passwort, PASSWORD_DEFAULT);
        
        // E-Mail existiert?
        $check_stmt = $db->prepare("SELECT id FROM mitarbeiter WHERE email = ?");
        $check_stmt->bind_param("s", $email);
        $check_stmt->execute();
        $check_stmt->store_result();
        
        if ($check_stmt->num_rows > 0) {
            $email_err = "E-Mail bereits registriert.";
        } else {
            // Neuen Benutzer anlegen
            $insert_stmt = $db->prepare("INSERT INTO mitarbeiter (email, passwort) VALUES (?, ?)");
            $insert_stmt->bind_param("ss", $email, $hashed_passwort);
            
            if ($insert_stmt->execute()) {
                $success = "Mitarbeiter '$email' angelegt! Passwort: <b>$passwort</b> (sichere es!)";
            } else {
                $email_err = "Fehler: " . $insert_stmt->error;
            }
            $insert_stmt->close();
        }
        $check_stmt->close();
    }
}
$db->close();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Mitarbeiter Registrieren</title>
    <style>
        body{font-family:Arial; max-width:400px; margin:100px auto;}
        .error{color:red;}
        .success{color:green; background:#d4edda; padding:10px; border:1px solid green;}
        input{width:100%; padding:10px; margin:5px 0;}
        button{width:100%; padding:12px; background:#28a745; color:white; border:none;}
    </style>
</head>
<body>
    <h2>Neuen Mitarbeiter anlegen</h2>
    
    <?php if($success): ?>
        <div class="success"><?php echo $success; ?></div>
        <p><a href="login.php">→ Login</a> | <a href="register.php">Neuer Benutzer</a></p>
    <?php else: ?>
        <?php if(!empty($email_err)): ?><div class="error"><?php echo $email_err; ?></div><?php endif; ?>
        <?php if(!empty($passwort_err)): ?><div class="error"><?php echo $passwort_err; ?></div><?php endif; ?>
        
        <form method="POST">
            <p>
                <label>E-Mail:</label><br>
                <input type="email" name="email" value="<?php echo htmlspecialchars($email); ?>" required>
            </p>
            <p>
                <label>Passwort:</label><br>
                <input type="password" name="passwort" required>
            </p>
            <button type="submit">Anlegen</button>
        </form>
        <p><a href="login.php">← Zurück zum Login</a></p>
    <?php endif; ?>
</body>
</html>

<a href="logout.php">Abmelden</a>
