<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8">
  <title>Farmacia Barata - Seguimiento de pedidos</title>
  <meta name="description" content="Haz seguimiento de los pedidos realizados en farmaciabarata.es">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="shortcut icon" type="image/png" href="/favicon.ico" />
  <link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>/assets/css/home.css?v291121">
  <link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>/assets/css/alerts.css?v291121">

  <!-- Global site tag (gtag.js) - Google Analytics -->
  <script async src="https://www.googletagmanager.com/gtag/js?id=UA-25620267-1"></script>
  <script>
    window.dataLayer = window.dataLayer || [];
    function gtag(){dataLayer.push(arguments);}
    gtag('js', new Date());

    gtag('config', 'UA-25620267-1');
  </script>
</head>

<body>

  <section id="sec-one">
    <div class="container top-container" role="img" aria-label="Localiza dónde está tu pedido">
      ¿Quieres saber donde esta tu pedido?
    <?php echo form_open('tracking'); ?>
        <div class="row">
          <h1>Localiza donde está tu pedido</h1>
        </div>

        <?php if (isset($error)) : ?>
          <div class="row">
            <div class="alert alert-danger">
              <?= $error; ?>
            </div>
          </div>
        <?php endif; ?>

        <?php if (isset($warning)) : ?>
          <div class="row">
            <div class="alert alert-warning">
              <?= $warning; ?>
            </div>
          </div>
        <?php endif; ?>

      <?php if (!isset($warning) && !isset($error)) : ?>
        <div class="row">
          <p>Introduce la referencia de pedido o tu teléfono para comprobar el estado</p>
        </div>
        <div class="row">
          <?php
          $data = array(
            'type'  => 'text',
            'name'  => 'reference',
            'id'    => 'reference',
            'placeholder' => 'Referencia',
            'class' => 'hiddenemail'
          );

          echo form_input($data);
          ?>
        </div>
        <div class="row">
          <?php
          $data = array(
            'type'  => 'text',
            'name'  => 'phone',
            'id'    => 'phone',
            'placeholder' => 'Teléfono',
            'class' => 'hiddenphone',
            'type' => 'tel',
            'pattern' => "[0-9]{9}"
          );

          echo form_input($data);
          ?>
        </div>
        <div class="row">
          <?php echo form_submit(['id' => 'submit', 'name' => 'submit'], 'Localizar'); ?>
        </div>

      <?php else: ?>
        <a href="/">Consultar otro pedido</a>
      <?php endif; ?>
      <?php echo form_close(); ?>


    </div>
  </section>

  <footer>

  </footer>
</body>

</html>