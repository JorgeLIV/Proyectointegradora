<?php
// Include database connection
require 'conexion.php';

// Initialize error variable
$error = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Collect form data
    $correo = trim($_POST['correo']);
    $password = $_POST['password'];

    // Basic validations
    if (empty($correo) || empty($password)) {
        $error = "Todos los campos son obligatorios.";
    } else {
        // Prepare and execute query
        $stmt = $conn->prepare("SELECT idusuario, contraseña, roles FROM USUARIOS WHERE correo = ?");
        $stmt->bind_param("s", $correo);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $stmt->bind_result($idusuario, $hashed_password, $role);
            $stmt->fetch();

            // Verify password
            if (password_verify($password, $hashed_password)) {
                // Start user session
                session_start();
                $_SESSION['idusuario'] = $idusuario;
                $_SESSION['correo'] = $correo;
                $_SESSION['role'] = $role;

                // Redirect based on user role
                if ($role === 'usuario') {
                    header("Location: Alumno_dashboard.php");
                    exit();
                } elseif ($role === 'administrador') {
                    header("Location: Administrativo_dashboard.php");
                    exit();
                }
            } else {
                $error = "Correo o contraseña incorrectos.";
            }
        } else {
            $error = "Correo o contraseña incorrectos.";
        }

        $stmt->close();
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Iniciar sesión</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Mulish&display=swap" rel="stylesheet" type='text/css'>
    <link rel="stylesheet" type="text/css" href="style.css">
</head>
<body>
    <div class="form-wrapper">
        <div class="form-side">
            <a href="#" title="Logo">
                <img src="assets/ofin.png" class='logo' alt="Ofin">
            </a>
            <form class="my-form" method="post" action="">
                <?php if (!empty($error)): ?>
                    <p style="color: red;"><?php echo $error; ?></p>
                <?php endif; ?>
                <div class="form-welcome-row">
                    <h1>Inicia sesión &#x1F44F;</h1>
                </div>
                <div class="text-field">
                    <label for="correo">Correo Electrónico:
                        <input
                            type="email"
                            id="correo"
                            name="correo"
                            autocomplete="off"
                            placeholder="Tu correo electrónico"
                            required
                        >
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" 
                            viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" 
                            fill="none" stroke-linecap="round" stroke-linejoin="round">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                            <path d="M12 12m-4 0a4 4 0 1 0 8 0a4 4 0 1 0 -8 0"></path>
                            <path d="M16 12v1.5a2.5 2.5 0 0 0 5 0v-1.5a9 9 0 1 0 -5.5 8.28"></path>
                        </svg>
                    </label>
                </div>
                <div class="text-field">
                    <label for="password">Contraseña:
                        <input
                            id="password"
                            type="password"
                            name="password"
                            placeholder="Tu contraseña"
                            title="Mínimo 6 caracteres con al menos 1 letra y 1 número"
                            pattern="^(?=.*[A-Za-z])(?=.*\d)[A-Za-z\d]{6,}$"
                            required
                        >
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" 
                            viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" 
                            fill="none" stroke-linecap="round" stroke-linejoin="round">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                            <path d="M5 13a2 2 0 0 1 2 -2h10a2 2 0 0 1 2 2v6a2 2 0 0 1 -2 2h-10a2 2 0 0 1 -2 -2v-6z"></path>
                            <path d="M11 16a1 1 0 1 0 2 0a1 1 0 0 0 -2 0"></path>
                            <path d="M8 11v-4a4 4 0 1 1 8 0v4"></path>
                        </svg>
                    </label>
                </div>
                <button type="submit" class="my-form__button">Iniciar sesión</button>
                <div class="my-form__actions">
                    <a href="#" title="Restablecer contraseña">Restablecer contraseña</a>
                    <a href="#" title="Crear cuenta">¿Ya tienes una cuenta?</a>
                </div>
            </form>
        </div>
        <div class="info-side">
            <img src="assets/mock.png" alt="Mock" class="mockup">
        </div>
    </div>
    <script src="script.js"></script>
</body>
</html>
