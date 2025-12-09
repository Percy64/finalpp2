<?php
// Página de Misión y Visión con estilos del programa y menú inferior
if (session_status() === PHP_SESSION_NONE) {
  session_start();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Misión y Visión</title>
  <link rel="icon" href="../assets/images/logo.png">
  <link rel="stylesheet" href="../assets/css/mascota03.css" />
  <link rel="stylesheet" href="../assets/css/bottom-nav.css" />
  <style>
    body {
      font-family: 'Segoe UI', sans-serif;
      margin: 0;
      padding: 0;
      background: #f9fbfd;
      color: #333;
    }
    .container {
      max-width: 1100px;
      margin: auto;
      padding: 40px 20px;
      text-align: center;
    }
    h1 {
      font-size: 2.2rem;
      margin-bottom: 10px;
      color: #2c3e50;
    }
    h2 {
      font-size: 1.6rem;
      margin: 20px 0 10px;
      color: #16a085;
    }
    p {
      font-size: 1rem;
      line-height: 1.6;
      color: #555;
    }
    .row {
      display: flex;
      flex-wrap: wrap;
      gap: 30px;
      margin-top: 40px;
      justify-content: center;
    }
    .card {
      flex: 1 1 320px;
      background: white;
      padding: 30px;
      border-radius: 15px;
      box-shadow: 0 4px 10px rgba(0,0,0,0.1);
      transition: transform 0.2s ease-in-out;
      max-width: 520px;
    }
    .card:hover {
      transform: translateY(-5px);
    }
    .icon {
      font-size: 40px;
      margin-bottom: 15px;
    }
    .cta-area {
      margin-top: 40px;
    }
    .btn {
      display: inline-block;
      padding: 12px 25px;
      background: #16a085;
      color: white;
      font-weight: bold;
      border-radius: 25px;
      text-decoration: none;
      transition: background 0.3s;
    }
    .btn:hover {
      background: #138d75;
    }
    @media(max-width: 768px) {
      .row {
        flex-direction: column;
        align-items: center;
      }
      .card {
        width: 100%;
      }
    }
  </style>
</head>
<body>

  <section class="container">
    <h1>¿Quiénes somos?</h1>
    <p>Trabajamos por el bienestar de las mascotas y la tranquilidad de sus dueños</p>

    <div class="row">
      <!-- Misión -->
      <div class="card">
        <div class="icon">🐶</div>
        <h2>Nuestra Misión</h2>
        <p>
          Facilitar el reencuentro entre mascotas perdidas y sus familias mediante
          un sistema accesible, seguro y fácil de usar basado en códigos QR.
          Queremos que ningún amigo peludo se quede sin volver a casa.
        </p>
      </div>

      <!-- Visión -->
      <div class="card">
        <div class="icon">🌍</div>
        <h2>Nuestra Visión</h2>
        <p>
          Soñamos con un mundo donde toda mascota tenga una identificación digital,
          que permita a las personas ayudar a encontrar a sus dueños de manera rápida
          y solidaria. Queremos ser la plataforma de referencia en Latinoamérica para
          la identificación y seguridad de mascotas.
        </p>
      </div>
    </div>
  </section>

  <?php include __DIR__ . '/../includes/bottom_nav.php'; ?>
</body>
</html>
