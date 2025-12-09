<?php
require_once __DIR__ . '/../includes/conexion.php';
$nombre = '';
$apellido = '';
$codigo_pais = '+54';
$codigo_normalizado = '54';
$telefono = '';
$telefono_normalizado = '';
$email = '';
$contraseña = '';
$contraseña2 = '';

$msg_nombre = '';
$msg_apellido = '';
$msg_codigo_pais = '';
$msg_telefono = '';
$msg_email = '';    
$msg_contraseña = '';
$msg_contraseña2 = '';

$error = false;


// Lista de países y códigos telefónicos (E.164)
$countryOptions = [
    ['code'=>'+93', 'name'=>'Afganistán'],
    ['code'=>'+355', 'name'=>'Albania'],
    ['code'=>'+49', 'name'=>'Alemania'],
    ['code'=>'+376', 'name'=>'Andorra'],
    ['code'=>'+244', 'name'=>'Angola'],
    ['code'=>'+1', 'name'=>'Antigua y Barbuda'],
    ['code'=>'+599', 'name'=>'Antillas Neerlandesas'],
    ['code'=>'+966', 'name'=>'Arabia Saudita'],
    ['code'=>'+213', 'name'=>'Argelia'],
    ['code'=>'+54', 'name'=>'Argentina'],
    ['code'=>'+374', 'name'=>'Armenia'],
    ['code'=>'+297', 'name'=>'Aruba'],
    ['code'=>'+61', 'name'=>'Australia'],
    ['code'=>'+43', 'name'=>'Austria'],
    ['code'=>'+994', 'name'=>'Azerbaiyán'],
    ['code'=>'+1', 'name'=>'Bahamas'],
    ['code'=>'+973', 'name'=>'Baréin'],
    ['code'=>'+880', 'name'=>'Bangladés'],
    ['code'=>'+1', 'name'=>'Barbados'],
    ['code'=>'+375', 'name'=>'Bielorrusia'],
    ['code'=>'+32', 'name'=>'Bélgica'],
    ['code'=>'+501', 'name'=>'Belice'],
    ['code'=>'+229', 'name'=>'Benín'],
    ['code'=>'+975', 'name'=>'Bután'],
    ['code'=>'+591', 'name'=>'Bolivia'],
    ['code'=>'+387', 'name'=>'Bosnia y Herzegovina'],
    ['code'=>'+267', 'name'=>'Botsuana'],
    ['code'=>'+55', 'name'=>'Brasil'],
    ['code'=>'+673', 'name'=>'Brunéi'],
    ['code'=>'+359', 'name'=>'Bulgaria'],
    ['code'=>'+226', 'name'=>'Burkina Faso'],
    ['code'=>'+257', 'name'=>'Burundi'],
    ['code'=>'+238', 'name'=>'Cabo Verde'],
    ['code'=>'+855', 'name'=>'Camboya'],
    ['code'=>'+237', 'name'=>'Camerún'],
    ['code'=>'+1', 'name'=>'Canadá'],
    ['code'=>'+235', 'name'=>'Chad'],
    ['code'=>'+56', 'name'=>'Chile'],
    ['code'=>'+86', 'name'=>'China'],
    ['code'=>'+357', 'name'=>'Chipre'],
    ['code'=>'+57', 'name'=>'Colombia'],
    ['code'=>'+269', 'name'=>'Comoras'],
    ['code'=>'+242', 'name'=>'Congo'],
    ['code'=>'+243', 'name'=>'Congo (RDC)'],
    ['code'=>'+850', 'name'=>'Corea del Norte'],
    ['code'=>'+82', 'name'=>'Corea del Sur'],
    ['code'=>'+506', 'name'=>'Costa Rica'],
    ['code'=>'+385', 'name'=>'Croacia'],
    ['code'=>'+53', 'name'=>'Cuba'],
    ['code'=>'+45', 'name'=>'Dinamarca'],
    ['code'=>'+1', 'name'=>'Dominica'],
    ['code'=>'+593', 'name'=>'Ecuador'],
    ['code'=>'+20', 'name'=>'Egipto'],
    ['code'=>'+503', 'name'=>'El Salvador'],
    ['code'=>'+971', 'name'=>'Emiratos Árabes Unidos'],
    ['code'=>'+221', 'name'=>'Senegal'],
    ['code'=>'+34', 'name'=>'España'],
    ['code'=>'+1', 'name'=>'Estados Unidos'],
    ['code'=>'+372', 'name'=>'Estonia'],
    ['code'=>'+251', 'name'=>'Etiopía'],
    ['code'=>'+63', 'name'=>'Filipinas'],
    ['code'=>'+358', 'name'=>'Finlandia'],
    ['code'=>'+679', 'name'=>'Fiyi'],
    ['code'=>'+33', 'name'=>'Francia'],
    ['code'=>'+241', 'name'=>'Gabón'],
    ['code'=>'+220', 'name'=>'Gambia'],
    ['code'=>'+995', 'name'=>'Georgia'],
    ['code'=>'+233', 'name'=>'Ghana'],
    ['code'=>'+350', 'name'=>'Gibraltar'],
    ['code'=>'+30', 'name'=>'Grecia'],
    ['code'=>'+1', 'name'=>'Granada'],
    ['code'=>'+299', 'name'=>'Groenlandia'],
    ['code'=>'+590', 'name'=>'Guadalupe'],
    ['code'=>'+502', 'name'=>'Guatemala'],
    ['code'=>'+224', 'name'=>'Guinea'],
    ['code'=>'+245', 'name'=>'Guinea-Bisáu'],
    ['code'=>'+240', 'name'=>'Guinea Ecuatorial'],
    ['code'=>'+592', 'name'=>'Guyana'],
    ['code'=>'+509', 'name'=>'Haití'],
    ['code'=>'+504', 'name'=>'Honduras'],
    ['code'=>'+852', 'name'=>'Hong Kong'],
    ['code'=>'+36', 'name'=>'Hungría'],
    ['code'=>'+91', 'name'=>'India'],
    ['code'=>'+62', 'name'=>'Indonesia'],
    ['code'=>'+98', 'name'=>'Irán'],
    ['code'=>'+964', 'name'=>'Irak'],
    ['code'=>'+353', 'name'=>'Irlanda'],
    ['code'=>'+354', 'name'=>'Islandia'],
    ['code'=>'+972', 'name'=>'Israel'],
    ['code'=>'+39', 'name'=>'Italia'],
    ['code'=>'+1', 'name'=>'Jamaica'],
    ['code'=>'+81', 'name'=>'Japón'],
    ['code'=>'+962', 'name'=>'Jordania'],
    ['code'=>'+7', 'name'=>'Kazajistán'],
    ['code'=>'+254', 'name'=>'Kenia'],
    ['code'=>'+996', 'name'=>'Kirguistán'],
    ['code'=>'+686', 'name'=>'Kiribati'],
    ['code'=>'+965', 'name'=>'Kuwait'],
    ['code'=>'+856', 'name'=>'Laos'],
    ['code'=>'+266', 'name'=>'Lesoto'],
    ['code'=>'+371', 'name'=>'Letonia'],
    ['code'=>'+961', 'name'=>'Líbano'],
    ['code'=>'+231', 'name'=>'Liberia'],
    ['code'=>'+218', 'name'=>'Libia'],
    ['code'=>'+423', 'name'=>'Liechtenstein'],
    ['code'=>'+370', 'name'=>'Lituania'],
    ['code'=>'+352', 'name'=>'Luxemburgo'],
    ['code'=>'+853', 'name'=>'Macao'],
    ['code'=>'+389', 'name'=>'Macedonia del Norte'],
    ['code'=>'+261', 'name'=>'Madagascar'],
    ['code'=>'+60', 'name'=>'Malasia'],
    ['code'=>'+265', 'name'=>'Malaui'],
    ['code'=>'+960', 'name'=>'Maldivas'],
    ['code'=>'+223', 'name'=>'Malí'],
    ['code'=>'+356', 'name'=>'Malta'],
    ['code'=>'+212', 'name'=>'Marruecos'],
    ['code'=>'+596', 'name'=>'Martinica'],
    ['code'=>'+230', 'name'=>'Mauricio'],
    ['code'=>'+222', 'name'=>'Mauritania'],
    ['code'=>'+52', 'name'=>'México'],
    ['code'=>'+691', 'name'=>'Micronesia'],
    ['code'=>'+373', 'name'=>'Moldavia'],
    ['code'=>'+377', 'name'=>'Mónaco'],
    ['code'=>'+976', 'name'=>'Mongolia'],
    ['code'=>'+382', 'name'=>'Montenegro'],
    ['code'=>'+1', 'name'=>'Montserrat'],
    ['code'=>'+258', 'name'=>'Mozambique'],
    ['code'=>'+95', 'name'=>'Myanmar'],
    ['code'=>'+264', 'name'=>'Namibia'],
    ['code'=>'+674', 'name'=>'Nauru'],
    ['code'=>'+977', 'name'=>'Nepal'],
    ['code'=>'+505', 'name'=>'Nicaragua'],
    ['code'=>'+227', 'name'=>'Níger'],
    ['code'=>'+234', 'name'=>'Nigeria'],
    ['code'=>'+683', 'name'=>'Niue'],
    ['code'=>'+47', 'name'=>'Noruega'],
    ['code'=>'+687', 'name'=>'Nueva Caledonia'],
    ['code'=>'+64', 'name'=>'Nueva Zelanda'],
    ['code'=>'+968', 'name'=>'Omán'],
    ['code'=>'+92', 'name'=>'Pakistán'],
    ['code'=>'+680', 'name'=>'Palaos'],
    ['code'=>'+970', 'name'=>'Palestina'],
    ['code'=>'+507', 'name'=>'Panamá'],
    ['code'=>'+675', 'name'=>'Papúa Nueva Guinea'],
    ['code'=>'+595', 'name'=>'Paraguay'],
    ['code'=>'+51', 'name'=>'Perú'],
    ['code'=>'+689', 'name'=>'Polinesia Francesa'],
    ['code'=>'+48', 'name'=>'Polonia'],
    ['code'=>'+351', 'name'=>'Portugal'],
    ['code'=>'+1', 'name'=>'Puerto Rico'],
    ['code'=>'+974', 'name'=>'Qatar'],
    ['code'=>'+44', 'name'=>'Reino Unido'],
    ['code'=>'+236', 'name'=>'República Centroafricana'],
    ['code'=>'+420', 'name'=>'República Checa'],
    ['code'=>'+1809', 'name'=>'República Dominicana'],
    ['code'=>'+40', 'name'=>'Rumanía'],
    ['code'=>'+7', 'name'=>'Rusia'],
    ['code'=>'+250', 'name'=>'Ruanda'],
    ['code'=>'+685', 'name'=>'Samoa'],
    ['code'=>'+1', 'name'=>'San Cristóbal y Nieves'],
    ['code'=>'+378', 'name'=>'San Marino'],
    ['code'=>'+1', 'name'=>'Santa Lucía'],
    ['code'=>'+239', 'name'=>'Santo Tomé y Príncipe'],
    ['code'=>'+1', 'name'=>'San Vicente y las Granadinas'],
    ['code'=>'+221', 'name'=>'Senegal'],
    ['code'=>'+381', 'name'=>'Serbia'],
    ['code'=>'+248', 'name'=>'Seychelles'],
    ['code'=>'+232', 'name'=>'Sierra Leona'],
    ['code'=>'+65', 'name'=>'Singapur'],
    ['code'=>'+963', 'name'=>'Siria'],
    ['code'=>'+252', 'name'=>'Somalia'],
    ['code'=>'+94', 'name'=>'Sri Lanka'],
    ['code'=>'+27', 'name'=>'Sudáfrica'],
    ['code'=>'+249', 'name'=>'Sudán'],
    ['code'=>'+211', 'name'=>'Sudán del Sur'],
    ['code'=>'+46', 'name'=>'Suecia'],
    ['code'=>'+41', 'name'=>'Suiza'],
    ['code'=>'+597', 'name'=>'Surinam'],
    ['code'=>'+66', 'name'=>'Tailandia'],
    ['code'=>'+886', 'name'=>'Taiwán'],
    ['code'=>'+255', 'name'=>'Tanzania'],
    ['code'=>'+992', 'name'=>'Tayikistán'],
    ['code'=>'+670', 'name'=>'Timor-Leste'],
    ['code'=>'+228', 'name'=>'Togo'],
    ['code'=>'+676', 'name'=>'Tonga'],
    ['code'=>'+1', 'name'=>'Trinidad y Tobago'],
    ['code'=>'+216', 'name'=>'Túnez'],
    ['code'=>'+90', 'name'=>'Turquía'],
    ['code'=>'+993', 'name'=>'Turkmenistán'],
    ['code'=>'+688', 'name'=>'Tuvalu'],
    ['code'=>'+380', 'name'=>'Ucrania'],
    ['code'=>'+256', 'name'=>'Uganda'],
    ['code'=>'+598', 'name'=>'Uruguay'],
    ['code'=>'+998', 'name'=>'Uzbekistán'],
    ['code'=>'+678', 'name'=>'Vanuatu'],
    ['code'=>'+58', 'name'=>'Venezuela'],
    ['code'=>'+84', 'name'=>'Vietnam'],
    ['code'=>'+681', 'name'=>'Wallis y Futuna'],
    ['code'=>'+212', 'name'=>'Sahara Occidental'],
    ['code'=>'+967', 'name'=>'Yemen'],
    ['code'=>'+260', 'name'=>'Zambia'],
    ['code'=>'+263', 'name'=>'Zimbabue'],
];


if(isset($_POST['env_btn'])){

    if (isset($_POST['nombre'])){
        $nombre=trim($_POST['nombre']);
        if (empty($nombre)) {
            $msg_nombre = 'El campo nombre es obligatorio.';
            $error = true;
        } elseif (strlen($nombre) < 3) {
            $msg_nombre = 'El nombre debe tener al menos 3 caracteres.';
            $error = true;    
        }
    }

    if (isset($_POST['apellido'])){
        $apellido=trim($_POST['apellido']);
        if (empty($apellido)) {
            $msg_apellido = 'El campo apellido es obligatorio.';
            $error = true;
        } elseif (strlen($apellido) < 2) {
            $msg_apellido = 'El apellido debe tener al menos 2 caracteres.';
            $error = true;    
        }
    }

    if (isset($_POST['email'])){
        $email=trim($_POST['email']);
        if (empty($email)) {
            $msg_email = 'El campo email es obligatorio.';
            $error = true;
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $msg_email = 'El email no es válido.';
            $error = true;
        } else {
            // Verificar si el email ya existe
            $sql_check = "SELECT id FROM usuarios WHERE email = ?";
            $stmt_check = $pdo->prepare($sql_check);
            $stmt_check->execute([$email]);
            if($stmt_check->fetch()){
                $msg_email = 'Este email ya está registrado.';
                $error = true;
            }
        }
    }

    $codigo_pais = isset($_POST['codigo_pais']) ? trim($_POST['codigo_pais']) : '+54';
    $codigo_normalizado = preg_replace('/\D+/', '', $codigo_pais);

    if ($codigo_normalizado === '') {
        $codigo_normalizado = '54';
        $codigo_pais = '+54';
    } elseif (strlen($codigo_normalizado) < 1 || strlen($codigo_normalizado) > 4) {
        $msg_codigo_pais = 'El código de país debe tener entre 1 y 4 dígitos.';
        $error = true;
    }

    if (isset($_POST['telefono'])){
        $telefono = trim($_POST['telefono']);
        // Normalizamos para WhatsApp: solo dígitos del número local
        $telefono_sin_formato = preg_replace('/\D+/', '', $telefono);

        if (empty($telefono_sin_formato)) {
            $msg_telefono = 'El campo teléfono es obligatorio.';
            $error = true;
        } elseif (strlen($telefono_sin_formato) < 6 || strlen($telefono_sin_formato) > 11) {
            $msg_telefono = 'El teléfono debe tener entre 6 y 11 dígitos.';
            $error = true;
        }

        if (!$error) {
            $telefono_normalizado = '+' . $codigo_normalizado . $telefono_sin_formato;
            $total_digitos = strlen($codigo_normalizado . $telefono_sin_formato);
            if ($total_digitos < 10 || $total_digitos > 15) {
                $msg_telefono = 'Incluí un teléfono válido con código de país (10 a 15 dígitos en total).';
                $error = true;
            }
        }
    }

    if (isset($_POST['contraseña'])){
        $contraseña=trim($_POST['contraseña']);
        if (empty($contraseña)) {
            $msg_contraseña = 'El campo contraseña es obligatorio.';
            $error = true;
        } elseif (strlen($contraseña) < 6) {
            $msg_contraseña = 'La contraseña debe tener al menos 6 caracteres.';
            $error = true;
        }

}

    if (!$error) {
        try {
            $contraseña_hashed = password_hash($contraseña, PASSWORD_DEFAULT);
            $sql = "INSERT INTO usuarios (nombre, apellido, telefono, email, password) VALUES (?, ?, ?, ?, ?)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$nombre, $apellido, $telefono_normalizado, $email, $contraseña_hashed]);

            // Login automático y redirección al perfil
            $usuario_id = (int)$pdo->lastInsertId();
            if (session_status() === PHP_SESSION_NONE) { session_start(); }
            $_SESSION['usuario_id'] = $usuario_id;
            $_SESSION['usuario_nombre'] = $nombre; // nombre de pila
            $_SESSION['usuario_email'] = $email;

            header('Location: ./perfil_usuario.php');
            exit;
        } catch(PDOException $e) {
            $msg_general = 'Error al registrar el usuario. Inténtelo nuevamente.';
            $error = true;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro de Usuario</title>
    <link rel="stylesheet" href="../assets/css/mascota03.css" />
    <link rel="stylesheet" href="../assets/css/registro-usuario.css" />
</head>
<body>
    <section class="registro-mascota">
        <form class="formulario" action="" method="post">
            <div class="logo-container">
                <img src="../assets/images/logo.png" alt="Logo" class="logo" />
            </div>
            <h2>Registro de Usuario</h2>
            
            <?php if(isset($success_message)): ?>
                <div class="success-message">
                    <?= htmlspecialchars($success_message) ?>
                </div>
            <?php endif; ?>
            
            <div>
                <input type="text" name="nombre" placeholder="Ingresar nombre" value="<?= htmlspecialchars($nombre) ?>">
                <output><?=$msg_nombre?></output>
            </div>
            <div>
                <input type="text" name="apellido" placeholder="Ingresar apellido" value="<?= htmlspecialchars($apellido) ?>">
                <output><?=$msg_apellido?></output>
            </div>
            <div>
                <input type="email" name="email" placeholder="Ingresar email" value="<?= htmlspecialchars($email) ?>">
                <output><?=$msg_email?></output>
            </div>
            <div class="phone-wrapper" style="display:flex; gap:10px;">
                <div style="flex:0 0 180px;">
                    <select name="codigo_pais" style="width:100%;" required>
                        <?php foreach($countryOptions as $opt): ?>
                            <option value="<?= htmlspecialchars($opt['code']) ?>" <?= $opt['code'] === $codigo_pais ? 'selected' : '' ?>><?= htmlspecialchars($opt['name']) ?> (<?= htmlspecialchars($opt['code']) ?>)</option>
                        <?php endforeach; ?>
                    </select>
                    <output><?= $msg_codigo_pais ?></output>
                </div>
                <div style="flex:1;">
                    <input type="text"
                           name="telefono"
                           placeholder="Número (sin código país)"
                           value="<?= htmlspecialchars($telefono) ?>"
                           inputmode="numeric"
                           autocomplete="tel-national"
                           pattern="[0-9]{6,11}"
                           title="Solo dígitos del número local (6 a 11)" />
                    <output><?= $msg_telefono ?></output>
                </div>
            </div>
            <div>
                <input type="password" name="contraseña" placeholder="Ingresar contraseña">
                <output><?=$msg_contraseña?></output>
            </div>

            <button type="submit" name="env_btn" class="btn_enviar">Registrarse</button>
            
            <div class="login-link">
                <p>¿Ya tienes cuenta? <a href="./iniciosesion.php">Inicia sesión</a></p>
            </div>
        </form>
    </section>
</body>
</html>