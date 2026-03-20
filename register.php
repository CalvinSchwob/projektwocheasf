<?php
session_start();

$db = new mysqli("localhost", "root", "", "optiker_db");
if ($db->connect_error) {
    die("Connection Failed: " . $db->connect_error);
}

$email = $name = "";
$email_err = $name_err = $passwort_err = $success = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST["name"] ?? "");
    $email = trim($_POST["email"] ?? "");
    $passwort = trim($_POST["passwort"] ?? "");
    
    // Validierung
    if (empty($name)) $name_err = "Name eingeben.";
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) $email_err = "Gültige E-Mail eingeben.";
    if (empty($passwort) || strlen($passwort) < 6) $passwort_err = "Passwort mind. 6 Zeichen.";
    
    if (empty($name_err) && empty($email_err) && empty($passwort_err)) {
        // **HASH AUTOMATISCH generieren**
        $hashed_passwort = password_hash($passwort, PASSWORD_DEFAULT);
        
        // Prüfen ob E-Mail existiert
        $check_stmt = $db->prepare("SELECT id FROM mitarbeiter WHERE email = ?");
        $check_stmt->bind_param("s", $email);
        $check_stmt->execute();
        $check_stmt->store_result();
        
        if ($check_stmt->num_rows > 0) {
            $email_err = "E-Mail bereits registriert.";
        } else {
            // Neuen Mitarbeiter einfügen
            $insert_stmt = $db->prepare("INSERT INTO mitarbeiter (email, passwort, name) VALUES (?, ?, ?)");
            $insert_stmt->bind_param("sss", $email, $hashed_passwort, $name);
            
            if ($insert_stmt->execute()) {
                $success = "Mitarbeiter '$name' erfolgreich angelegt! Passwort: $passwort";
            } else {
                $email_err = "Fehler beim Anlegen.";
            }
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
        <p><a href="login.php">→ Zum Login</a></p>
    <?php else: ?>
        <?php if(!empty($email_err)): ?><div class="error"><?php echo $email_err; ?></div><?php endif; ?>
        <?php if(!empty($name_err)): ?><div class="error"><?php echo $name_err; ?></div><?php endif; ?>
        <?php if(!empty($passwort_err)): ?><div class="error"><?php echo $passwort_err; ?></div><?php endif; ?>
        
        <form method="POST">
            <p>
                <label>Name:</label><br>
                <input type="text" name="name" value="<?php echo htmlspecialchars($name); ?>" required>
            </p>
            <p>
                <label>E-Mail:</label><br>
                <input type="email" name="email" value="<?php echo htmlspecialchars($email); ?>" required>
            </p>
            <p>
                <label>Passwort:</label><br>
                <input type="password" name="passwort" required>
            </p>
            <button type="submit">Mitarbeiter anlegen</button>
        </form>
    <?php endif; ?>
</body>
</html>
