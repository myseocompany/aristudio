<!DOCTYPE html>
<html lang="en">
  <head>
     <title>MySEO</title>
    <link rel="icon" href="/images/favicon.ico"/>
    <!-- Bootstrap core CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
    <script type="text/javascript" src="{{ asset('js/jquery.nestable.js') }}"></script>
  </head>
  <body>
     <!-- @include('layouts.navigation_metadata')-->
     @yield('content')
      <center>
      @include('layouts.footer')
      </center>
</body>
   
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.3/umd/popper.min.js" integrity="sha384-vFJXuSJphROIrBnz7yo7oB41mKfc8JzQZiCq4NCceLEaO4IHwicKwpJf9c9IpFgh" crossorigin="anonymous"></script>
<script src="/js/ie10-viewport-bug-workaround.js"></script>
<script src="/js/footerScripts.js?id=<?php echo rand(1, 10000000);?>"></script>
<script type="text/javascript" src="{{ asset('js/myseo.js') }}"></script>
<script src="https://use.fontawesome.com/d1fc24111c.js"></script>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
<script src="/js/custom.js?id=<?php echo rand(1, 10000000);?>"></script>
<script src="/js/zingchart.min.js"></script>
  
</html>