<?php
// Página de Misión y Visión con estilos del programa y menú inferior
if (session_status() === PHP_SESSION_NONE) {
  session_start();
}
require_once __DIR__ . '/../../../config/config.php';
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Misión y Visión - Lost & Found</title>
  <link rel="icon" href="<?= ASSETS_URL ?>/images/logo.png">
  <link rel="stylesheet" href="<?= ASSETS_URL ?>/css/bottom-nav.css" />
  <style>
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }

    body {
      font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
      background: #f8f9fa;
      color: #2c3e50;
      line-height: 1.6;
    }

    .container {
      max-width: 1200px;
      margin: 0 auto;
      padding: 60px 30px;
    }

    h1 {
      font-size: 3rem;
      font-weight: 800;
      text-align: center;
      margin-bottom: 15px;
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
      background-clip: text;
    }

    .container > p {
      text-align: center;
      font-size: 1.2rem;
      color: #64748b;
      margin-bottom: 60px;
      max-width: 600px;
      margin-left: auto;
      margin-right: auto;
    }

    .row {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
      gap: 40px;
      margin-bottom: 60px;
    }

    .card {
      background: white;
      padding: 40px 30px;
      border-radius: 16px;
      box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
      border: 1px solid #f0f4ff;
      transition: all 0.4s cubic-bezier(0.25, 0.46, 0.45, 0.94);
      position: relative;
    }

    .card:hover {
      transform: translateY(-8px);
      box-shadow: 0 20px 40px rgba(102, 126, 234, 0.15);
    }

    .card .icon {
      font-size: 60px;
      margin-bottom: 20px;
      display: block;
    }

    .card h2 {
      font-size: 1.8rem;
      color: #1a202c;
      margin-bottom: 15px;
      font-weight: 700;
    }

    .card p {
      font-size: 1rem;
      color: #64748b;
      line-height: 1.8;
    }

    /* Línea decorativa */
    .card::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      right: 0;
      height: 4px;
      background: linear-gradient(90deg, #667eea, #764ba2);
      border-radius: 16px 16px 0 0;
    }

    /* Sección adicional */
    .values-section {
      background: linear-gradient(135deg, #f0f3ff 0%, #fff 100%);
      padding: 60px 30px;
      border-radius: 20px;
      margin-top: 60px;
      border: 1px solid #e8eef7;
    }

    .values-section h2 {
      font-size: 2.5rem;
      text-align: center;
      color: #1a202c;
      margin-bottom: 50px;
      font-weight: 800;
      position: relative;
    }

    .values-section h2::after {
      content: '';
      display: block;
      width: 60px;
      height: 4px;
      background: linear-gradient(135deg, #667eea, #764ba2);
      margin: 15px auto 0;
    }

    .values-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
      gap: 30px;
      max-width: 900px;
      margin: 0 auto;
    }

    .value-item {
      text-align: center;
    }

    .value-item .emoji {
      font-size: 50px;
      margin-bottom: 15px;
      display: block;
    }

    .value-item h3 {
      font-size: 1.3rem;
      color: #667eea;
      margin-bottom: 10px;
      font-weight: 700;
    }

    .value-item p {
      color: #64748b;
      font-size: 0.95rem;
    }

    /* Responsive */
    @media (max-width: 768px) {
      .container {
        padding: 40px 20px;
      }

      h1 {
        font-size: 2.2rem;
      }

      .container > p {
        font-size: 1rem;
      }

      .row {
        grid-template-columns: 1fr;
        gap: 25px;
      }

      .card {
        padding: 30px 20px;
      }

      .card h2 {
        font-size: 1.4rem;
      }

      .card p {
        font-size: 0.95rem;
      }

      .values-section {
        padding: 40px 20px;
        margin-top: 40px;
      }

      .values-section h2 {
        font-size: 1.8rem;
        margin-bottom: 35px;
      }

      .values-grid {
        grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
        gap: 20px;
      }

      .value-item .emoji {
        font-size: 40px;
      }

      .value-item h3 {
        font-size: 1.1rem;
      }

      .value-item p {
        font-size: 0.9rem;
      }
    }

    @media (max-width: 480px) {
      .container {
        padding: 25px 15px;
      }

      h1 {
        font-size: 1.6rem;
        margin-bottom: 10px;
      }

      .container > p {
        font-size: 0.9rem;
        margin-bottom: 35px;
      }

      .row {
        grid-template-columns: 1fr;
        gap: 15px;
        margin-bottom: 40px;
      }

      .card {
        padding: 20px 15px;
      }

      .card .icon {
        font-size: 45px;
        margin-bottom: 12px;
      }

      .card h2 {
        font-size: 1.15rem;
        margin-bottom: 10px;
      }

      .card p {
        font-size: 0.85rem;
        line-height: 1.6;
      }

      .values-section {
        padding: 25px 15px;
        margin-top: 30px;
        border-radius: 16px;
      }

      .values-section h2 {
        font-size: 1.3rem;
        margin-bottom: 25px;
      }

      .values-section h2::after {
        width: 50px;
      }

      .values-grid {
        grid-template-columns: 1fr;
        gap: 15px;
      }

      .value-item .emoji {
        font-size: 35px;
        margin-bottom: 10px;
      }

      .value-item h3 {
        font-size: 1rem;
        margin-bottom: 5px;
      }

      .value-item p {
        font-size: 0.8rem;
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
        <span class="icon">🐾</span>
        <h2>Nuestra Misión</h2>
        <p>
          Facilitar el reencuentro entre mascotas perdidas y sus familias mediante
          un sistema accesible, seguro y fácil de usar basado en códigos QR.
          Queremos que ningún amigo peludo se quede sin volver a casa.
        </p>
      </div>

      <!-- Visión -->
      <div class="card">
        <span class="icon">🌍</span>
        <h2>Nuestra Visión</h2>
        <p>
          Soñamos con un mundo donde toda mascota tenga una identificación digital,
          que permita a las personas ayudar a encontrar a sus dueños de manera rápida
          y solidaria. Queremos ser la plataforma de referencia en Latinoamérica para
          la identificación y seguridad de mascotas.
        </p>
      </div>
    </div>

    <!-- Valores -->
    <div class="values-section">
      <h2>Nuestros Valores</h2>
      <div class="values-grid">
        <div class="value-item">
          <span class="emoji">❤️</span>
          <h3>Amor</h3>
          <p>Por los animales y sus familias</p>
        </div>
        <div class="value-item">
          <span class="emoji">🤝</span>
          <h3>Solidaridad</h3>
          <p>Comunidad comprometida ayudando</p>
        </div>
        <div class="value-item">
          <span class="emoji">🔒</span>
          <h3>Seguridad</h3>
          <p>Protección de datos confiable</p>
        </div>
      </div>
    </div>
  </section>

  <?php include ROOT_PATH . '/app/includes/bottom_nav.php'; ?>
</body>
</html>
