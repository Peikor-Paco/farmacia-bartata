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
      <div class="row header mobile">
        <div class="column">
          <a href="https://www.farmaciabarata.es/"><img class="logo" src="<?php echo base_url(); ?>/assets/img/logo-farmaciabarata.svg" /></a>
        </div>
        <div class="column">
          <a class="micuenta" href="https://www.farmaciabarata.es/mi-cuenta">Mi cuenta</a>
        </div>
      </div>
      <div class="content">
        <div>
          <img class="cabecera" src="<?php echo base_url(); ?>/assets/img/tracking/<?php echo $imgcabecera; ?>" />
        </div>
        <div class="text-center">
            <h2 class="title"><?php echo $orderstate; ?></h2>
        </div>
            <p class="subtitle"><?php echo $text; ?>
            </p>
            <?php if (isset($state_text) && $state_text) : ?>
              <p class="subtitle">El transportista nos indica el siguiente estado: <strong><?php echo $state_text; ?></strong></p>
            <?php endif; ?>
        </div>

        <div class="detalle">
                <p class="detalle__title"><strong>Detalle de pedido</strong></p>
                <p class="detalle__body">Número: <strong><?php echo $reference; ?></strong><br />
                Agencia de transporte: <strong><?php echo $carrier; ?></strong><br />
                Número de seguimiento:<strong> <?php echo $shipping_number; ?></strong>
            </p>
        </div>
        <p class="texto">
          💙 Gracias por tu confianza 💙
        </p>
        <div class="row text-center">  
          <?php if(isset($bannerpath) && $bannerpath): ?>
            <a target="_blank" href="https://app.app4less.es/app/farmaciabarata?campaign=tracking">
              <img class="banner" src="<?= $bannerpath; ?>"/>
            </a>
          <?php endif;?>
        </div>
      </div>
    </div>
  </section>
  <footer>
  </footer>
</body>

</html>