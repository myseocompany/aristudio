<!DOCTYPE html>
<html lang="en">
  <head>
    <script src="https://use.fontawesome.com/d1fc24111c.js"></script>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="icon" href="/images/favicon.ico"/>
    <link rel="shortcut icon" href="/images/favicon.ico" />
    <link href="/css/all.css" rel="stylesheet"> 
    <title>@yield('title')</title>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
    <script src="/js/custom.js?id=<?php echo rand(1, 10000000);?>"></script>
    <link rel="stylesheet" href="/css/dashboard.css?id=<?php echo rand(1, 100000000);?>">
    <script src="/js/zingchart.min.js"></script>
  </head>
  <body>
    <div class="container">
      <section class="content" id="main-content">
        @yield('content')
      </section>
    </div>
  </body>
</html>