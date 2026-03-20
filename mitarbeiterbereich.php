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
    <nav>
        <div class="navbarLeft">
            <!--Dropdown-->
            <!-- From Uiverse.io by gharsh11032000 -->
            <div class="menu">
            <div class="item">
                <a href="#" class="link">
                <span> Our Services </span>
                <svg viewBox="0 0 360 360" xml:space="preserve">
                    <g id="SVGRepo_iconCarrier">
                    <path
                        id="XMLID_225_"
                        d="M325.607,79.393c-5.857-5.857-15.355-5.858-21.213,0.001l-139.39,139.393L25.607,79.393 c-5.857-5.857-15.355-5.858-21.213,0.001c-5.858,5.858-5.858,15.355,0,21.213l150.004,150c2.813,2.813,6.628,4.393,10.606,4.393 s7.794-1.581,10.606-4.394l149.996-150C331.465,94.749,331.465,85.251,325.607,79.393z"
                    ></path>
                    </g>
                </svg>
                </a>
                <div class="submenu">
                <div class="submenu-item">
                    <a href="pages/products" class="submenu-link"> Produkte </a>
                </div>
                <div class="submenu-item">
                    <a href="pages/services.html" class="submenu-link"> Dienstleistungen </a>
                </div>
                <div class="submenu-item">
                    <a href="/pages/contact.html" class="submenu-link"> Kontakt </a>
                </div>
                <div class="submenu-item">
                    <a href="/pages/about-us" class="submenu-link"> Über Uns </a>
                </div>
                <div class="submenu-item">
                    <a href="pages/impressum.html" class="submenu-link"> Impressum </a>
                </div>
                <div class="submenu-item">
                    <a href="pages/login.html" class="submenu-link"> Login </a>
                </div>
                </div>
            </div>
            </div>
        </div>

        <div class="navbarCenter">
            <img src="img/Design ohne Titel.png" alt="Logo der SchönesGlas Optikerkette" width="100px" class="logo">
            <p class="companyName"><a href="index.html">SchönesGlas</a></p>
        </div>
        <div class="navbarRight">
            <button>Unsere Dienstleistungen</button>
            <button class="callToAction">Kontakt</button>
        </div>
    </nav>
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
    <?php endif; ?>

    <footer class="footer">
        <h3><a href="../index.html">SchönesGlas</a></h3>
        <ul>
            <li><a href="../index.html">Startseite</a></li>
            <li><a href="./products.html">Produkte</a></li>
        </ul>
        
        <ul>
            <li><a href="./services.html">Dienstleistungen</a></li>
            <li><a href="./contact.html">Kontakt</a></li>
            <li><a href="./impressum.html">Impressum</a></li>
        </ul>

        <ul>
            <li><a href="./aboutUs.html">Über uns</a></li>
            <li><a href="./login.html">Für Mitarbeiter</a></li>
        </ul>
    </footer>
</body>
</html>

<a href="logout.php">Abmelden</a>
