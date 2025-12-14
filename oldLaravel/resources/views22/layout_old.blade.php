<!DOCTYPE html>
<html lang="en">
  <head>
    <script src="https://use.fontawesome.com/d1fc24111c.js"></script>
    <!-- taked from 
    http://getbootstrap.com/docs/4.0/examples/justified-nav/
    -->
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


    <!-- Bootstrap core CSS -->
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>

   <script src="/js/custom.js?id=<?php echo rand(1, 10000000);?>"></script>
    <!-- Custom styles for this template -->
    <!-- <link href="/css/justified-nav.css" rel="stylesheet"> -->
    <link rel="stylesheet" href="/css/dashboard.css?id=<?php echo rand(1, 100000000);?>">
    <script src="/js/zingchart.min.js"></script>
  </head>

  <body>
    <div id="left-navigation">
      @include('layouts.left_navigation')
    </div>
    <div class="container">

      @include('layouts.navigation')
      <section class="content" id="main-content">
        @yield('content')
      </section>
      <!-- Site footer -->
      @include('layouts.footer')

    </div> <!-- /container -->


    <!-- Bootstrap core JavaScript
    ================================================== -->
    <!-- Placed at the end of the document so the pages load faster -->
   

<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.3/umd/popper.min.js" integrity="sha384-vFJXuSJphROIrBnz7yo7oB41mKfc8JzQZiCq4NCceLEaO4IHwicKwpJf9c9IpFgh" crossorigin="anonymous"></script>


    <!-- IE10 viewport hack for Surface/desktop Windows 8 bug -->
    <script src="/js/ie10-viewport-bug-workaround.js"></script>
  
    @yield('footerjs')
     <script src="/js/footerScripts.js?id=<?php echo rand(1, 10000000);?>"></script>
  <link rel="stylesheet" type="text/css" href="{{ asset('css/nestable.css') }}">
<script type="text/javascript" src="{{ asset('js/jquery.nestable.js') }}"></script>
<script type="text/javascript" src="{{ asset('js/myseo.js') }}"></script>

  </body>
</html>