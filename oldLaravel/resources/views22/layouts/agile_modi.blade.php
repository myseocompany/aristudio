<!DOCTYPE html>
<html lang="en">
<head>

     <script src="https://code.jquery.com/jquery-3.3.1.min.js"></script>
      <script type="text/javascript">
      var jQuery_3_3_1 = $.noConflict(true);
      </script>



    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, minimum-scale=1, minimal-ui, viewport-fit=cover">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="#3a57c4">
    <title>MySEO</title>

    <link rel="stylesheet" href="/css/bootstrap.min.css">

    <link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet" integrity="sha384-wvfXpqpZZVQGK6TAh5PVlGOfQNHSoD2xbE+QkPxCAFlNEevoEH3Sl0sibVcOQVnN" crossorigin="anonymous">

    


    <link rel="stylesheet" href="https://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/themes/smoothness/jquery-ui.css">
    


  <script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1/jquery.min.js"></script>
  <script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jqueryui/1/jquery-ui.min.js"></script>

    <script src="/js/scripts.js?id=<?php echo rand(1,1000) ?>"></script> 
    <!--<script src="https://code.jquery.com/jquery-3.3.1.min.js"></script>-->
    <script src="/js/addInput.js?id=<?php echo rand(1,1000) ?>"></script> 



    <!-- Favicons -->
    <link rel="shortcut icon" type="image/x-icon" href="/images/favicon.ico">

    <!--------------------------------------------------->

    
    <!-- fonts online -->

    <script src="/js/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta/js/bootstrap.min.js" integrity="sha384-h0AbiXch4ZDo7tp9hKZ4TsHbi047NrKGLO3SEJAg45jXxnGIfYzk4Si90RDIqNm1" crossorigin="anonymous"></script>
    <!--
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta/js/bootstrap.min.js" integrity="sha384-h0AbiXch4ZDo7tp9hKZ4TsHbi047NrKGLO3SEJAg45jXxnGIfYzk4Si90RDIqNm1" crossorigin="anonymous"></script>

  -->

   <script src="/js/popper.min.js"></script>
    {{-- <script src="/js/popper.min.js"></script> --}}

    <!-- IE10 viewport hack for Surface/desktop Windows 8 bug -->
    <script src="/js/ie10-viewport-bug-workaround.js"></script>
    {{-- <script src="/js/ie10-viewport-bug-workaround.js"></script> --}}

    

    {{--   <script scr="/js/fontawesome.js"></script> --}}


   
   

  







    
    <!-- Bootstrap CSS -->
    <!--
  <link rel="stylesheet" href="/assets/css/bootstrap.min.css">
-->

  

  <!--

  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta/js/bootstrap.min.js" integrity="sha384-h0AbiXch4ZDo7tp9hKZ4TsHbi047NrKGLO3SEJAg45jXxnGIfYzk4Si90RDIqNm1" crossorigin="anonymous"></script>

-->

  <!--
  <link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet" integrity="sha384-wvfXpqpZZVQGK6TAh5PVlGOfQNHSoD2xbE+QkPxCAFlNEevoEH3Sl0sibVcOQVnN" crossorigin="anonymous"> 

-->



  <!-- Framwork7 CSS -->
  <link rel="stylesheet" href="/assets/css/framework7.bundle.min.css">
  <link rel="stylesheet" href="/assets/css/framework7-icons.css">

  <!-- Fontawesome CSS -->
    <link rel="stylesheet" href="/assets/plugins/fontawesome/css/fontawesome.min.css">
  <link rel="stylesheet" href="/assets/plugins/fontawesome/css/all.min.css">
  <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">

  <!-- Select2 CSS -->
  <link rel="stylesheet" href="/assets/plugins/select2/select2.min.css">

  <!-- Feather CSS -->
  <link rel="stylesheet" href="/assets/css/feather.css">

  <!-- Select2 CSS -->
  <link rel="stylesheet" href="/assets/plugins/select2/select2.min.css">

  <!-- Swiper CSS -->
    <link rel="stylesheet" href="/assets/plugins/swiper/css/swiper.min.css">

  <!-- Main CSS -->
    <link rel="stylesheet" href="/assets/css/custom.css?id=<?php echo rand(1,10000);?>">

<!-- MySEO CSS -->
    <link rel="stylesheet" href="/css/dashboard.css?id=<?php echo rand(1,1000);?>">



</head>

<body class="home">

  <div id="app">
    <div class="statusbar"></div>
    
        <div class="panel panel-left panel-cover">
            <div class="block p-0">
                <div class="side-menu" id="sidebar-menu">
                  <div class="panel-close close-btn"><i class="feather-x"></i></div>
                  <img id="img-logo" src="/images/logo_my_seo_company.png">
                  @if(Auth::user()->role_id == 1)
                    <ul> 
                      <li>
                          <a class="panel-close" href="#" onclick="openView('timer');">
                            <img src="/assets/img/icons/menu2.png" alt="Side Menu Icons" />
                          Timer
                          </a>
                      </li>  
                      <li>
                          <a class="panel-close" href="#" onclick="openView('tasks/schedule');">
                            <img src="/assets/img/icons/menu2.png" alt="Side Menu Icons" />
                          Schedule
                          </a>
                      </li>
                      <li>
                          <a class="panel-close" href="#" onclick="openView('tasks/daily');">
                            <img src="/assets/img/icons/menu2.png" alt="Side Menu Icons" />
                          Daily
                          </a>
                      </li>       
                      <li>
                          <a class="panel-close" href="#" onclick="openView('tasks');">
                            <img src="/assets/img/icons/menu2.png" alt="Side Menu Icons" />
                          Tasks
                          </a>
                      </li>
                      <li>
                          <a class="panel-close" href="#">
                            <img src="/assets/img/icons/menu2.png" alt="Side Menu Icons" />
                          Notifications
                          </a>
                      </li>

                      <li>
                          <a class="panel-close" href="#" onclick="openView('planner');">
                            <img src="/assets/img/icons/menu2.png" alt="Side Menu Icons" />
                          Planner
                          </a>
                      </li>
                      <li>
                          <a class="panel-close" href="#" onclick="openView('knowledge_management');">
                            <img src="/assets/img/icons/menu2.png" alt="Side Menu Icons" />
                          Knowledge Management
                          </a>
                      </li>
                      <li>
                          <a class="panel-close" href="#" onclick="openView('reports/weeks_team);">
                            <img src="/assets/img/icons/menu3.png" alt="Side Menu Icons" />
                          Weeks By Team
                          </a>
                      </li>
                      <li>
                          <a class="panel-close" href="#" onclick="openView('reports/months_user');">
                            <img src="/assets/img/icons/menu3.png" alt="Side Menu Icons" />
                          Months By User
                          </a>
                      </li>


                      <li>
                          <a class="panel-close" href="#"onclick="openView('reports/months_project');">
                            <img src="/assets/img/icons/menu3.png" alt="Side Menu Icons" />
                          Months By Project
                          </a>
                      </li>
                      <li>
                          <a class="panel-close" href="#"onclick="openView('reports/weeks_user');">
                            <img src="/assets/img/icons/menu3.png" alt="Side Menu Icons" />
                          Weeks By User
                          </a>
                      </li>
                      <li>
                          <a class="panel-close" href="#"onclick="openView('reports/days_user');">
                            <img src="/assets/img/icons/menu3.png" alt="Side Menu Icons" />
                          Days By User
                          </a>
                      </li>
                      <li>
                          <a class="panel-close" href="#"onclick="openView('reports/projects_statuses');">
                            <img src="/assets/img/icons/menu3.png" alt="Side Menu Icons" />
                          Project Statuses
                          </a>
                      </li>
                      <li>
                          <a class="panel-close" href="#"onclick="openView('reports/users_statuses');">
                            <img src="/assets/img/icons/menu3.png" alt="Side Menu Icons" />
                          User Statuses
                          </a>
                      </li>
                      <li>
                          <a class="panel-close" href="#"onclick="openView('users');">
                            <img src="/assets/img/icons/menu config.png" alt="Side Menu Icons" />
                          Users
                          </a>
                      </li>
                      <li>
                          <a class="panel-close" href="#"onclick="openView('projects');">
                            <img src="/assets/img/icons/menu config.png" alt="Side Menu Icons" />
                          Projects
                          </a>
                      </li>
                      <li>
                          <a class="panel-close" href="#"onclick="openView('role_modules');">
                            <img src="/assets/img/icons/menu config.png" alt="Side Menu Icons" />
                          Role Modules
                          </a>
                      </li>
                      <li>
                          <a class="panel-close" href="#"onclick="openView('customers');">
                            <img src="/assets/img/icons/menu config.png" alt="Side Menu Icons" />
                          Customers
                          </a>
                      </li>
                      <li class="mt-5 pb-3">
                        <a href="/my-profile/" class="panel-close">
                          <div class="menu-footer">
                            <img src="/assets/img/icons/Usuario.png" alt="user" />
                            <h5>{{Auth::user()->name}}</h5>
                            <h6>{{Auth::user()->email}}</h6>
                          </div>

                          <a class="nav-link" href="{{ route('logout') }}" onclick="event.preventDefault();document.getElementById('logout-form').submit();">Salir
                          </a>
                          <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                              {{ csrf_field() }}
                          </form>
                        </a>
                      </li>
                    </ul>
                  @else
                    <ul>
                      <li>
                          <a class="panel-close" href="#" onclick="openView('dashboard');">
                            <img src="/assets/img/icons/leads-icon.png" alt="Side Menu Icons" />
                          Clientes
                          </a>
                      </li> 


                      <!--      
                      <li>
                          <a class="panel-close" href="#" onclick="openView('emails');">
                            <img src="/assets/img/icons/email-icon.png" alt="Side Menu Icons" />
                          Emails
                          </a>
                      </li>

                      <li>
                          <a class="panel-close" href="#" onclick="openView('actions');">
                            <img src="/assets/img/icons/report-icon.png" alt="Side Menu Icons" />
                          Reporte de acciones
                          </a>
                      </li>
                      <li>
                          <a class="panel-close" href="#" onclick="openView('reports/customers_time');">
                            <img src="/assets/img/icons/report-icon.png" alt="Side Menu Icons" />
                          Reporte de clientes en el tiempo
                          </a>
                      </li>
                      <li>
                          <a class="panel-close" href="#" onclick="openView('reports/users/customer/status');">
                            <img src="/assets/img/icons/report-icon.png" alt="Side Menu Icons" />
                          Reporte de estados por usuario
                          </a>
                      </li>
                      <li>
                          <a class="panel-close" href="#" onclick="openView('reports/users/customer/actions');">
                            <img src="/assets/img/icons/report-icon.png" alt="Side Menu Icons" />
                          Reporte de acciones por usuario
                          </a>
                      </li>


                      <li>
                          <a class="panel-close" href="#"onclick="openView('config');">
                            <img src="/assets/img/icons/settings-icon.png" alt="Side Menu Icons" />
                          Configuración
                          </a>
                      </li>
                      -->
                      <li class="mt-5 pb-3">
                        <a href="/my-profile/" class="panel-close">
                          <div class="menu-footer">
                            <img src="/assets/img/icons/Usuario.png" alt="user" />
                            <h5>{{Auth::user()->name}}</h5>
                            <h6>{{Auth::user()->email}}</h6>
                          </div>

                          <a class="nav-link" href="{{ route('logout') }}" onclick="event.preventDefault();document.getElementById('logout-form').submit();">Salir
                          </a>
                          <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                              {{ csrf_field() }}
                          </form>
                        </a>
                      </li>
                    </ul>
                  @endif


        </div>
            </div>
        </div>


                    
                      
                    

                    

        <div class="view view-main view-init ios-edges" data-url="/">
          <div class="page" data-name="index">
            <div class="top-bg">
              <div class="shape1"></div>
              <div class="shape2"></div>
            </div>

            <!-- Header -->
            <div class="navbar bg-trans">
              <div class="navbar-inner align-items-center">
                <div class="right">
                  <a href="#" class="link panel-open" data-panel=".panel-left">
                    <i class="fas fa-bars"></i>
                  </a>
                </div>
                <!--
                <div class="right">
                  <a href="#" onclick="openView('config');">
                    <img src="/assets/img/icons/settings-icon.png" alt="Menu">
                  </a>
                </div>
                -->

                @yield('content-back-page')
              </div>
            </div>
            <div class="tabs">
                  @yield('content')
            </div>
          </div>
        </div>
        </div>
    <!-- App end -->
  
    <script type="text/javascript">
      function openView(url){
        var url_actual = window.location.host;
        window.location.replace("https://"+url_actual+"/"+url);
      }
    </script>

  <script>
      /* When the user clicks on the button, 
      toggle between hiding and showing the dropdown content */
      function myFunction() {
        document.getElementById("myDropdown").classList.toggle("show");
      }

    // Close the dropdown if the user clicks outside of it
    window.onclick = function(event) {
    if (!event.target.matches('.dropbtn')) {
    var dropdowns = document.getElementsByClassName("dropdown-content");
    var i;
    for (i = 0; i < dropdowns.length; i++) {
      var openDropdown = dropdowns[i];
      if (openDropdown.classList.contains('show')) {
        openDropdown.classList.remove('show');
      }
    }
    }
    }
  </script>

    
    <!-- Bootstrap Core JS -->
  <script src="/assets/js/popper.min.js"></script>

  <!--
  <script src="/assets/js/bootstrap.min.js"></script>
  -->
    <!-- Apex Charts -->
  <script src="/assets/plugins/apexcharts/apexcharts.min.js"></script>

  <!-- Swiper JS -->
    <script src="/assets/plugins/swiper/js/swiper.min.js"></script>

    <!-- Select2 JS -->
    <script src="/assets/plugins/select2/select2.min.js"></script>

    <!-- Framework7 JS -->
    <script src="/assets/js/framework7.bundle.min.js"></script>
    <script src="/assets/js/routes.js?id=<?php echo rand(1,1000) ?>"></script>

    <!-- Custom JS -->
    <script src="/assets/js/app.js"></script>
  
</body>

</html>