<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8">
  <title>Farmacia Barata - Seguimiento de pedidos</title>
  <meta name="description" content="Haz seguimiento de los pedidos realizados en farmaciabarata.es">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="shortcut icon" type="image/png" href="/favicon.ico" />
  <link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>/assets/css/admin.css?v291121">
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
    <?php echo form_open_multipart('gestion'); ?>
        <div class="row">
          <h1>Gestión banner informativo</h1>
        </div>
        <?php if(isset($bannerpath) && $bannerpath): ?>
            <div class="row" style="display: block;">
                <p>Banner actual:</p>
                <img class="banner" src="<?= $bannerpath; ?>"/>
            </div>
            <div class="row">
              <a class="delete" href="/gestion/delete">Eliminar banner</a>
            </div>
        <?php endif;?>
        <?php if(isset($errors) && $errors): ?>
            <div class="row" style="display: block;">
                <p>Se produjo un error:</p>
                <?= print_r($errors); ?>
            </div>
        <?php endif;?>
        <p>Subir nuevo banner (archivos JPG):</p>

        <div class="row">
          <?php
          $data = array(
            'type'  => 'file',
            'name'  => 'banner',
            'id'    => 'banner',
            'placeholder' => 'Banner',
          );

          echo form_input($data);
          ?>
        </div>
        <div class="row">
          <?php echo form_submit(['id' => 'submit', 'name' => 'submit'], 'Enviar'); ?>
        </div>
      <?php echo form_close(); ?>


    </div>
  </section>

  <footer>

  </footer>
</body>

</html>