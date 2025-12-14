<style type="text/css">
	.navbar{
        color: #ffffff !important;
}
</style>
<nav class="navbar navbar-expand-md navbar-white fixed-top bg-white">
  <div id="nav-container" class="container">
    <a class="navbar-brand" href="#"><img src="/img/logo_enfercol.png" alt="" height="40"></a>
      <button class="navbar-toggler d-lg-none collapsed" type="button" data-toggle="collapse" data-target="#navbarsExampleDefault" aria-controls="navbarsExampleDefault" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>

      <div class="navbar-collapse collapse" id="navbarsExampleDefault" style="">
        <ul class="navbar-nav mr-auto">
          <li class="nav-item active">
            <a class="nav-link" href="/">Inicio <span class="sr-only">(current)</span></a>
          </li>
          
          <!-- Authentication Links -->
                        @if (Auth::guest())
                            
                            
          <!--
          <li class="nav-item"><a class="nav-link" href="{{ route('register') }}">Register</a></li>
          -->
          <li class="nav-item"><a class="nav-link" href="{{ route('login') }}">Iniciar Sesion</a></li>
          @else
          @if (Auth::user()->role_id == 1)
            {{-- expr --}}
            <li class="nav-item">
              <a class="nav-link" href="/customers/phase/1">Contactos</a>
            </li>


            <li class="nav-item">
              <a class="nav-link" href="/customers/phase/1">Ordenes</a>
            </li>

            
            <li class="nav-item">
              <a class="nav-link" href="/customers/phase/2">Mensualidades</a>
            </li>
            
            {{-- <li class="dropdown">
              <a class="navlink" href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">
                Clientes <span class="caret"></span>
              </a>

              <ul class="dropdown-menu" role="menu">        
                  <li>
                      <a href="/customers/phase/1">Contactos</a>
                  </li>
                 
                  <li>
                      <a href="/customers/phase/3">Cartera</a>
                  </li>                  
                  
              </ul>
            </li> --}}
            <!--
            <li class="nav-item">
                <a class="nav-link" href="/products">Inventario</a>
            </li> 
        
            <li class="nav-item">
                <a class="nav-link" href="/orders/sid/1">Cotizaciones</a>
            </li>   
            <li class="nav-item">
                <a class="nav-link" href="/orders/sid/2">Ordenes</a>
            </li> 

        -->      
            <li class="dropdown">
              <a class="nav-link" href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">
                  Reportes <span class="caret"></span>
              </a>

              <ul class="dropdown-menu" role="menu">
                <li class="nav-item">
                  <a class="nav-link" href="{{ route('actions') }}">Acciones</a>
                </li>
                  <li>
                      <a class="nav-link" href="/reports/customers_time">Clientes en el tiempo</a>
                  </li>
                  <li>
                      <a  class="nav-link" href="/reports/users/customer/status">Estados por usuario</a>
                  </li>
                  <li>
                      <a class="nav-link" href="/reports/users/customer/actions">Acciones por usuario</a>
                  </li>                  
                  {{-- <li>
                      <a href="/orders">Ordenes</a>
                  </li> --}}
              </ul>
            </li>
            
          @endif

          @if (Auth::user()->role_id == 2)
            <li class="nav-item">
              <a class="nav-link" href="/customers/phase/1">Contactos</a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="/customers/phase/1">Ordenes</a>
            </li>
            
            <li class="nav-item">
              <a class="nav-link" href="/customers/phase/3">Cartera</a>
            </li>
            
            <li class="nav-item">
            	<a class="nav-link" href="/products">Inventario</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="/orders/sid/1">Cotizaciones</a>
            </li>   
            <li class="nav-item">
                <a class="nav-link" href="/orders/sid/2">Ordenes</a>
            </li>       
            <li class="dropdown">
              <a class="nav-link" href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">
                  Reportes <span class="caret"></span>
              </a>

              <ul class="dropdown-menu" role="menu">
                <li class="nav-item">
                  <a class="nav-link" href="{{ route('actions') }}">Acciones</a>
                </li>
                  <li>
                      <a href="/reports/customers_time">Clientes en el tiempo</a>
                  </li>
                  <li>
                      <a href="/reports/users/customer/status">Estados por usuario</a>
                  </li>
                  <li>
                      <a href="/reports/users/customer/actions">Acciones por usuario</a>
                  </li>                                    
              </ul>
            </li>            
          <!--<li class="nav-item">
            <a class="nav-link" href="{{ route('leads') }}">Prospectos</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="{{ route('customers') }}">Clientes</a>
          </li>
            
            <li class="nav-item">
            <a class="nav-link" href="{{ route('actions') }}">Acciones</a>
          </li>-->
            @endif
                <li class="dropdown">
                    <a class="nav-link" href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">
                        Mi cuenta<span class="caret"></span>
                    </a>

                    <ul class="dropdown-menu" role="menu">
                      @if (Auth::user()->role_id == 1)
                      <li class="nav-item">
                        <a class="nav-link" href="/config">Configuracion</a>
                      </li>
                      @endif
                        <li>
                            <a class="nav-link" href="{{ route('logout') }}"
                                onclick="event.preventDefault();
                                         document.getElementById('logout-form').submit();">
                                Salir
                            </a>

                            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                {{ csrf_field() }}
                            </form>
                        </li>
                    </ul>
                </li>
            @endif
        </ul>

         @if (!Auth::guest())
          <form class="form-inline mt-2 mt-md-0" action="/customers" method="GET">
            <input class="form-control mr-sm-2" type="text" placeholder="Busca o escribe" aria-label="Cliente" id="name" name="search" @if (isset($request->search)) value="{{$request->search}}" @endif>
            <button class="btn btn-primary my-2 my-sm-0" type="submit">Ir</button>
          </form>  
        @endif
        
      </div>   
  </div>
</nav> 