<div id="inner-left-navigation">
  <a class="navbar-brand" href="/" id="logo-app"><img src="/images/logo_my_seo_company.png" height="40" ></a>

      <button class="navbar-toggler d-lg-none collapsed" type="button" data-toggle="collapse" data-target="#navbarsExampleDefault" aria-controls="navbarsExampleDefault" aria-expanded="false" aria-label="Toggle navigation">
        <i class="fa fa-bars  " aria-hidden="true"></i>
      </button>      

      <div class="" id="inner-navbar" style="">
        <ul class="navbar-nav mr-auto">
          @if (  Auth::user()->role_id == 2 || Auth::user()->role_id == 1)
            <li>
              <a class="nav-link" href="{{ route('logout') }}"
                  onclick="event.preventDefault();
                           document.getElementById('logout-form').submit();">
                           <svg width="16" height="16" viewBox="0 0 16 16"><path fill="#ffffff" fill-rule="evenodd" d="M8 16A8 8 0 118 0a8 8 0 010 16zm0-3a1 1 0 100-2 1 1 0 000 2zm0-8c.683 0 1.068.57.955 1.1-.077.362-.2.53-.623.908l-.052.046c-.83.741-1.202 1.316-1.278 2.416a1 1 0 101.996.138c.032-.476.135-.634.613-1.061l.053-.048c.711-.634 1.062-1.113 1.247-1.982A2.914 2.914 0 008 3c-1.572 0-2.501.84-3.057 2.145a1 1 0 001.84.784C7.057 5.285 7.373 5 8 5z"></path></svg>
                  Logout
              </a>

            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                  {{ csrf_field() }}
            </form>
            </li>
           @endif            
        </ul>
  
      </div>

</div>