<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8">
  <title>Farmacia Barata - Seguimiento de pedidos</title>
  <meta name="description" content="Haz seguimiento de los pedidos realizados en farmaciabarata.es">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="shortcut icon" type="image/png" href="/favicon.ico" />
  <link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>/assets/css/tracking.css?v300822">
  <link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>/assets/css/alerts.css?v300822">

  <!-- Global site tag (gtag.js) - Google Analytics -->
  <script async src="https://www.googletagmanager.com/gtag/js?id=UA-25620267-1"></script>
  <script>
    window.dataLayer = window.dataLayer || [];

    function gtag() {
      dataLayer.push(arguments);
    }
    gtag('js', new Date());

    gtag('config', 'UA-25620267-1');
  </script>
</head>

<body>
  <section id="sec-one">
    <div class="container top-container">
      <div class="row header desktop">
        <a href="https://www.farmaciabarata.es/"><img class="logo" src="<?php echo base_url(); ?>/assets/img/logo-farmaciabarata.svg" /></a>
        <a class="micuenta" href="https://www.farmaciabarata.es/mi-cuenta">Mi cuenta</a>
      </div>
      <div class="row header desktop">
      <p class="carrier">
          Pedido <?php echo $reference; ?> --- Transportista <?php echo $carrier; ?>: <?php echo $shipping_number; ?>
        </p>
      </div>
      <div class="row header mobile">
        <div class="column">
          <a href="https://www.farmaciabarata.es/"><img class="logo" src="<?php echo base_url(); ?>/assets/img/logo-farmaciabarata.svg" /></a>
        </div>
        <div class="column">
          <a class="micuenta" href="https://www.farmaciabarata.es/mi-cuenta">Mi cuenta</a>
        </div>
        <div class="column">
          <p class="carrier">
            Pedido <?php echo $reference; ?> --- Transportista <?php echo $carrier; ?>: <?php echo $shipping_number; ?>
          </p>
        </div>
      </div>
      <img class="cabecera" src="<?php echo base_url(); ?>/assets/img/tracking/<?php echo $imgcabecera; ?>" />
      <img class="cuerpo" src="<?php echo base_url(); ?>/assets/img/tracking/<?php echo $imgcuerpo; ?>" />

      <?php if(isset($bannerpath) && $bannerpath): ?>
            <div class="row">
                <img class="banner" src="<?= $bannerpath; ?>"/>
            </div>
        <?php endif;?>

      <p class="texto"><?php echo $text; ?></p>
      <?php if (isset($state_text) && $state_text) : ?>
        <p class="texto">El transportista nos indica el siguiente estado: <?php echo $state_text; ?></p>
      <?php endif; ?>
    </div>
  </section>

  <footer>

  </footer>
</body>

</html>