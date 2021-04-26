<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="Mark Otto, Jacob Thornton, and Bootstrap contributors">
    <meta name="generator" content="Hugo 0.82.0">
    <title>E-Commerce</title>

    <link rel="canonical" href="https://getbootstrap.com/docs/5.0/examples/navbar-fixed/">



    <!-- Bootstrap core CSS -->
<link href="<?=base_url('/bootstrap-5.0.0/css/bootstrap.min.css')?>" rel="stylesheet">

    <style>
      .bd-placeholder-img {
        font-size: 1.125rem;
        text-anchor: middle;
        -webkit-user-select: none;
        -moz-user-select: none;
        user-select: none;
      }

      @media (min-width: 768px) {
        .bd-placeholder-img-lg {
          font-size: 3.5rem;
        }
      }
      /* Custom styles for this template */
      body {
        min-height: 75rem;
        padding-top: 4.5rem;
      }
    </style>
  </head>
  <body>

  <?=$this->include('navbar')?>


    <main role="main" class="container">
      <?=$this->renderSection('content');?>
    </main>


    <script src="<?=base_url('/bootstrap-5.0.0/js/bootstrap.bundle.min.js')?>"></script>
    <script src="<?=base_url('jquery-3.6.0.min.js')?>"></script>


  </body>
</html>
