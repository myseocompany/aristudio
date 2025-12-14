<div id="inner-left-navigation">
  <a class="navbar-brand" href="/" id="logo-app"><img src="/images/logo_my_seo_company.png" height="40" ></a>

      <button class="navbar-toggler d-lg-none collapsed" type="button" data-toggle="collapse" data-target="#navbarsExampleDefault" aria-controls="navbarsExampleDefault" aria-expanded="false" aria-label="Toggle navigation">
        <i class="fa fa-bars  " aria-hidden="true"></i>
      </button>      

      <div class="" id="inner-navbar" style="">
        <ul class="navbar-nav mr-auto">
           

          
          <!-- Authentication Links -->
          <?php if(Auth::guest()): ?>
              <li class="nav-item"><a class="nav-link" href="<?php echo e(route('login')); ?>">Login</a></li>
              
              
          <?php else: ?>
          <?php if(  (Auth::user()->role_id == 1) || (Auth::user()->role_id == 2) || (Auth::user()->role_id == 3)  ): ?>
<!--
          <li class="nav-item">
            <a class="nav-link" href="/timer"><svg width="16" height="16" viewBox="0 0 18 18"><path d="M8.001 8.948L8 9c0 .556.448 1 1 1h3c.556 0 1-.448 1-1 0-.556-.448-1-1-1h-2V4.003a1 1 0 10-2 0v4.894l.001.051zM9 18A9 9 0 109 0a9 9 0 000 18z" fill="#ffffff" fill-rule="evenodd"></path></svg>Timer</a>
            
          </li>
          <li class="nav-item">
            <a class="nav-link" href="/tasks/schedule"><svg width="16" height="16" viewBox="0 0 18 18"><path d="M8.001 8.948L8 9c0 .556.448 1 1 1h3c.556 0 1-.448 1-1 0-.556-.448-1-1-1h-2V4.003a1 1 0 10-2 0v4.894l.001.051zM9 18A9 9 0 109 0a9 9 0 000 18z" fill="#ffffff" fill-rule="evenodd"></path></svg>Schedule</a>
            
          </li>
          <li class="nav-item active">
            <a class="nav-link" href="/tasks/daily"><svg width="18" height="14" viewBox="0 0 18 14"><path fill="#ffffff" fill-rule="evenodd" d="M11.572 8.295C11.282 7.678 10.5 7 10.5 6c0-1.5.5-3 2.5-3s2.5 1.5 2.5 3-1 1.5-1 3c0 2 3.5.5 3.5 3v.5c0 .821-.67 1.5-1.499 1.5H12v-2.072c0-1.683 0-2.72-.428-3.633zM4 7c0-1-1.5-1.5-1.5-3.5C2.5 2 3 0 5.5 0s3 2 3 3.5C8.5 5.5 7 6 7 7c0 2.5 4 1 4 5v.5c0 .821-.674 1.5-1.506 1.5H1.506A1.502 1.502 0 010 12.5V12c0-4 4-2.5 4-5z"></path></svg>Daily <span class="sr-only">(current)</span></a>
          </li>
        -->
          <li class="nav-item">
            <a class="nav-link" href="/tasks">
              <svg width="18" height="18" viewBox="0 0 16 18">
                <path d="M0 2.993A2.994 2.994 0 013.01 0h9.98C14.654 0 16 1.353 16 2.993v12.014A2.994 2.994 0 0112.99 18H3.01C1.346 18 0 16.647 0 15.007V2.993zM6.5 9.5c0 1.5-3.5 1-3.5 4 0 1 0 0 0 0A1.5 1.5 0 004.497 15h7.006C12.33 15 13 14.323 13 13.5c0 0 0 1 0 0 0-3-3.5-2.5-3.5-4C9.5 8.5 11 8 11 6c0-1.5-1.343-3-3-3S5 4.5 5 6c0 2 1.5 2.5 1.5 3.5z" fill="#ffffff" fill-rule="evenodd">
                  </path>
                </svg>Tasks
            </a>
          </li>
          
          <!--Links reports -->
            <li class="nav-item">
                <a class="nav-link" href="/reports/users_statuses"><svg width="14" height="16" viewBox="0 0 14 16"><path fill="#ffffff" fill-rule="evenodd" d="M0 1.994A2 2 0 012.006 0h9.988C13.102 0 14 .895 14 1.994v12.012A2 2 0 0111.994 16H2.006A2.001 2.001 0 010 14.006V1.994zM2 3c0 .556.446 1 .995 1h8.01c.54 0 .995-.448.995-1 0-.556-.446-1-.995-1h-8.01C2.455 2 2 2.448 2 3zm0 3c0 .556.446 1 .995 1h8.01c.54 0 .995-.448.995-1 0-.556-.446-1-.995-1h-8.01C2.455 5 2 5.448 2 6zm0 3c0 .556.446 1 .995 1h8.01c.54 0 .995-.448.995-1 0-.556-.446-1-.995-1h-8.01C2.455 8 2 8.448 2 9z"></path></svg>Users</a>
           </li>
           <li class="nav-item">
            <a class="nav-link" href="/reports/months_user">
              <svg width="14" height="16" viewBox="0 0 14 16">
                <path fill="#ffffff" fill-rule="evenodd" d="M0 1.994A2 2 0 012.006 0h9.988C13.102 0 14 .895 14 1.994v12.012A2 2 0 0111.994 16H2.006A2.001 2.001 0 010 14.006V1.994zM2 3c0 .556.446 1 .995 1h8.01c.54 0 .995-.448.995-1 0-.556-.446-1-.995-1h-8.01C2.455 2 2 2.448 2 3zm0 3c0 .556.446 1 .995 1h8.01c.54 0 .995-.448.995-1 0-.556-.446-1-.995-1h-8.01C2.455 5 2 5.448 2 6zm0 3c0 .556.446 1 .995 1h8.01c.54 0 .995-.448.995-1 0-.556-.446-1-.995-1h-8.01C2.455 8 2 8.448 2 9z">
                  </path>
                </svg>Users by Month
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="/reports/weeks_team">
              <svg width="14" height="16" viewBox="0 0 14 16">
                <path fill="#ffffff" fill-rule="evenodd" d="M0 1.994A2 2 0 012.006 0h9.988C13.102 0 14 .895 14 1.994v12.012A2 2 0 0111.994 16H2.006A2.001 2.001 0 010 14.006V1.994zM2 3c0 .556.446 1 .995 1h8.01c.54 0 .995-.448.995-1 0-.556-.446-1-.995-1h-8.01C2.455 2 2 2.448 2 3zm0 3c0 .556.446 1 .995 1h8.01c.54 0 .995-.448.995-1 0-.556-.446-1-.995-1h-8.01C2.455 5 2 5.448 2 6zm0 3c0 .556.446 1 .995 1h8.01c.54 0 .995-.448.995-1 0-.556-.446-1-.995-1h-8.01C2.455 8 2 8.448 2 9z">
                  </path>
                </svg>Team
            </a>
          </li>

           
            <li class="nav-item">
                <a class="nav-link" href="/reports/projects_statuses"><svg width="14" height="16" viewBox="0 0 14 16"><path fill="#ffffff" fill-rule="evenodd" d="M0 1.994A2 2 0 012.006 0h9.988C13.102 0 14 .895 14 1.994v12.012A2 2 0 0111.994 16H2.006A2.001 2.001 0 010 14.006V1.994zM2 3c0 .556.446 1 .995 1h8.01c.54 0 .995-.448.995-1 0-.556-.446-1-.995-1h-8.01C2.455 2 2 2.448 2 3zm0 3c0 .556.446 1 .995 1h8.01c.54 0 .995-.448.995-1 0-.556-.446-1-.995-1h-8.01C2.455 5 2 5.448 2 6zm0 3c0 .556.446 1 .995 1h8.01c.54 0 .995-.448.995-1 0-.556-.446-1-.995-1h-8.01C2.455 8 2 8.448 2 9z"></path></svg>Projects</a>
          </li>
          <!--Links reports -->
          
          <li class="nav-item"><a class="nav-link" href="/customers"><svg width="18" height="18" viewBox="0 0 16 18"><path d="M0 2.993A2.994 2.994 0 013.01 0h9.98C14.654 0 16 1.353 16 2.993v12.014A2.994 2.994 0 0112.99 18H3.01C1.346 18 0 16.647 0 15.007V2.993zM6.5 9.5c0 1.5-3.5 1-3.5 4 0 1 0 0 0 0A1.5 1.5 0 004.497 15h7.006C12.33 15 13 14.323 13 13.5c0 0 0 1 0 0 0-3-3.5-2.5-3.5-4C9.5 8.5 11 8 11 6c0-1.5-1.343-3-3-3S5 4.5 5 6c0 2 1.5 2.5 1.5 3.5z" fill="#ffffff" fill-rule="evenodd"></path>
          </svg>Customers</a></li>
          <li class="nav-item"><a class="nav-link" href="/projects"><svg width="18" height="18" viewBox="0 0 16 18"><path d="M0 2.993A2.994 2.994 0 013.01 0h9.98C14.654 0 16 1.353 16 2.993v12.014A2.994 2.994 0 0112.99 18H3.01C1.346 18 0 16.647 0 15.007V2.993zM6.5 9.5c0 1.5-3.5 1-3.5 4 0 1 0 0 0 0A1.5 1.5 0 004.497 15h7.006C12.33 15 13 14.323 13 13.5c0 0 0 1 0 0 0-3-3.5-2.5-3.5-4C9.5 8.5 11 8 11 6c0-1.5-1.343-3-3-3S5 4.5 5 6c0 2 1.5 2.5 1.5 3.5z" fill="#ffffff" fill-rule="evenodd"></path>
          </svg>Projects</a></li>
          
          <?php $__currentLoopData = Auth::user()->getProjects(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <li class="nav-item list-project-item" style="color:<?php echo e($item->color); ?>">
              <a class="nav-link" href="/tasks?project_id=<?php echo e($item->id); ?>">
                <?php echo e($item->name); ?></a>
            </li>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
         

          <li class="nav-item">
            <a class="nav-link">

              <svg width="18" height="18" viewBox="0 0 16 18"><path d="M0 2.993A2.994 2.994 0 013.01 0h9.98C14.654 0 16 1.353 16 2.993v12.014A2.994 2.994 0 0112.99 18H3.01C1.346 18 0 16.647 0 15.007V2.993zM6.5 9.5c0 1.5-3.5 1-3.5 4 0 1 0 0 0 0A1.5 1.5 0 004.497 15h7.006C12.33 15 13 14.323 13 13.5c0 0 0 1 0 0 0-3-3.5-2.5-3.5-4C9.5 8.5 11 8 11 6c0-1.5-1.343-3-3-3S5 4.5 5 6c0 2 1.5 2.5 1.5 3.5z" fill="#ffffff" fill-rule="evenodd"></path></svg>Notifications<div id="notification" style="float: right;"></div>
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="/planner"><svg width="16" height="16" viewBox="0 0 18 18"><path d="M8.001 8.948L8 9c0 .556.448 1 1 1h3c.556 0 1-.448 1-1 0-.556-.448-1-1-1h-2V4.003a1 1 0 10-2 0v4.894l.001.051zM9 18A9 9 0 109 0a9 9 0 000 18z" fill="#ffffff" fill-rule="evenodd"></path></svg>Planner</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="/knowledge_management"><svg width="14" height="16" viewBox="0 0 14 16"><path fill="#ffffff" fill-rule="evenodd" d="M0 1.994A2 2 0 012.006 0h9.988C13.102 0 14 .895 14 1.994v12.012A2 2 0 0111.994 16H2.006A2.001 2.001 0 010 14.006V1.994zM2 3c0 .556.446 1 .995 1h8.01c.54 0 .995-.448.995-1 0-.556-.446-1-.995-1h-8.01C2.455 2 2 2.448 2 3zm0 3c0 .556.446 1 .995 1h8.01c.54 0 .995-.448.995-1 0-.556-.446-1-.995-1h-8.01C2.455 5 2 5.448 2 6zm0 3c0 .556.446 1 .995 1h8.01c.54 0 .995-.448.995-1 0-.556-.446-1-.995-1h-8.01C2.455 8 2 8.448 2 9z"></path></svg>SOPs</a>
          </li>
          
          
         
          <!--
          <li class="nav-item">
            <a class="nav-link" href="/reports/weeks_team"><svg width="14" height="16" viewBox="0 0 14 16"><path fill="#ffffff" fill-rule="evenodd" d="M0 1.994A2 2 0 012.006 0h9.988C13.102 0 14 .895 14 1.994v12.012A2 2 0 0111.994 16H2.006A2.001 2.001 0 010 14.006V1.994zM2 3c0 .556.446 1 .995 1h8.01c.54 0 .995-.448.995-1 0-.556-.446-1-.995-1h-8.01C2.455 2 2 2.448 2 3zm0 3c0 .556.446 1 .995 1h8.01c.54 0 .995-.448.995-1 0-.556-.446-1-.995-1h-8.01C2.455 5 2 5.448 2 6zm0 3c0 .556.446 1 .995 1h8.01c.54 0 .995-.448.995-1 0-.556-.446-1-.995-1h-8.01C2.455 8 2 8.448 2 9z"></path></svg>Weeks By Team</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="/reports/months_user"><svg width="14" height="16" viewBox="0 0 14 16"><path fill="#ffffff" fill-rule="evenodd" d="M0 1.994A2 2 0 012.006 0h9.988C13.102 0 14 .895 14 1.994v12.012A2 2 0 0111.994 16H2.006A2.001 2.001 0 010 14.006V1.994zM2 3c0 .556.446 1 .995 1h8.01c.54 0 .995-.448.995-1 0-.556-.446-1-.995-1h-8.01C2.455 2 2 2.448 2 3zm0 3c0 .556.446 1 .995 1h8.01c.54 0 .995-.448.995-1 0-.556-.446-1-.995-1h-8.01C2.455 5 2 5.448 2 6zm0 3c0 .556.446 1 .995 1h8.01c.54 0 .995-.448.995-1 0-.556-.446-1-.995-1h-8.01C2.455 8 2 8.448 2 9z"></path></svg>Moths By User</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="/reports/months_project"><svg width="14" height="16" viewBox="0 0 14 16"><path fill="#ffffff" fill-rule="evenodd" d="M0 1.994A2 2 0 012.006 0h9.988C13.102 0 14 .895 14 1.994v12.012A2 2 0 0111.994 16H2.006A2.001 2.001 0 010 14.006V1.994zM2 3c0 .556.446 1 .995 1h8.01c.54 0 .995-.448.995-1 0-.556-.446-1-.995-1h-8.01C2.455 2 2 2.448 2 3zm0 3c0 .556.446 1 .995 1h8.01c.54 0 .995-.448.995-1 0-.556-.446-1-.995-1h-8.01C2.455 5 2 5.448 2 6zm0 3c0 .556.446 1 .995 1h8.01c.54 0 .995-.448.995-1 0-.556-.446-1-.995-1h-8.01C2.455 8 2 8.448 2 9z"></path></svg>Moths By Project</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="/reports/weeks_user"><svg width="14" height="16" viewBox="0 0 14 16"><path fill="#ffffff" fill-rule="evenodd" d="M0 1.994A2 2 0 012.006 0h9.988C13.102 0 14 .895 14 1.994v12.012A2 2 0 0111.994 16H2.006A2.001 2.001 0 010 14.006V1.994zM2 3c0 .556.446 1 .995 1h8.01c.54 0 .995-.448.995-1 0-.556-.446-1-.995-1h-8.01C2.455 2 2 2.448 2 3zm0 3c0 .556.446 1 .995 1h8.01c.54 0 .995-.448.995-1 0-.556-.446-1-.995-1h-8.01C2.455 5 2 5.448 2 6zm0 3c0 .556.446 1 .995 1h8.01c.54 0 .995-.448.995-1 0-.556-.446-1-.995-1h-8.01C2.455 8 2 8.448 2 9z"></path></svg>Weeks By User</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="/reports/days_user"><svg width="14" height="16" viewBox="0 0 14 16"><path fill="#ffffff" fill-rule="evenodd" d="M0 1.994A2 2 0 012.006 0h9.988C13.102 0 14 .895 14 1.994v12.012A2 2 0 0111.994 16H2.006A2.001 2.001 0 010 14.006V1.994zM2 3c0 .556.446 1 .995 1h8.01c.54 0 .995-.448.995-1 0-.556-.446-1-.995-1h-8.01C2.455 2 2 2.448 2 3zm0 3c0 .556.446 1 .995 1h8.01c.54 0 .995-.448.995-1 0-.556-.446-1-.995-1h-8.01C2.455 5 2 5.448 2 6zm0 3c0 .556.446 1 .995 1h8.01c.54 0 .995-.448.995-1 0-.556-.446-1-.995-1h-8.01C2.455 8 2 8.448 2 9z"></path></svg>Days By User</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="/reports/projects_statuses"><svg width="14" height="16" viewBox="0 0 14 16"><path fill="#ffffff" fill-rule="evenodd" d="M0 1.994A2 2 0 012.006 0h9.988C13.102 0 14 .895 14 1.994v12.012A2 2 0 0111.994 16H2.006A2.001 2.001 0 010 14.006V1.994zM2 3c0 .556.446 1 .995 1h8.01c.54 0 .995-.448.995-1 0-.556-.446-1-.995-1h-8.01C2.455 2 2 2.448 2 3zm0 3c0 .556.446 1 .995 1h8.01c.54 0 .995-.448.995-1 0-.556-.446-1-.995-1h-8.01C2.455 5 2 5.448 2 6zm0 3c0 .556.446 1 .995 1h8.01c.54 0 .995-.448.995-1 0-.556-.446-1-.995-1h-8.01C2.455 8 2 8.448 2 9z"></path></svg>
            Project Statuses</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="/reports/users_statuses"><svg width="14" height="16" viewBox="0 0 14 16"><path fill="#ffffff" fill-rule="evenodd" d="M0 1.994A2 2 0 012.006 0h9.988C13.102 0 14 .895 14 1.994v12.012A2 2 0 0111.994 16H2.006A2.001 2.001 0 010 14.006V1.994zM2 3c0 .556.446 1 .995 1h8.01c.54 0 .995-.448.995-1 0-.556-.446-1-.995-1h-8.01C2.455 2 2 2.448 2 3zm0 3c0 .556.446 1 .995 1h8.01c.54 0 .995-.448.995-1 0-.556-.446-1-.995-1h-8.01C2.455 5 2 5.448 2 6zm0 3c0 .556.446 1 .995 1h8.01c.54 0 .995-.448.995-1 0-.556-.446-1-.995-1h-8.01C2.455 8 2 8.448 2 9z"></path></svg>
            User Statuses</a>
          </li>
        -->
      <!--module 4 users-->
       <?php if(  Auth::user()->getRoleModule(Auth::user()->role_id,4) == 4): ?>

          <li class="nav-item">
            <a class="nav-link" href="/users">
              <svg width="14" height="16" viewBox="0 0 14 16">
                <path d="M8 11a3 3 0 100-6 3 3 0 000 6zM5.182 2.702c.337-.18.693-.328 1.065-.442L6.778.666C6.9.299 7.313 0 7.7 0h.6c.387 0 .8.298.922.666l.531 1.594c.372.114.728.262 1.065.442l1.504-.752c.346-.173.85-.093 1.122.18l.425.426c.274.273.354.775.181 1.122l-.752 1.504c.18.337.328.693.442 1.065l1.594.531c.367.122.666.535.666.922v.6c0 .387-.298.8-.666.922l-1.594.531a5.963 5.963 0 01-.442 1.065l.752 1.504c.173.346.093.85-.18 1.122l-.426.425c-.273.274-.775.354-1.122.181l-1.504-.752c-.337.18-.693.328-1.065.442l-.531 1.594C9.1 15.701 8.687 16 8.3 16h-.6c-.388 0-.8-.298-.922-.666l-.531-1.594a5.963 5.963 0 01-1.065-.442l-1.504.752c-.346.173-.85.093-1.122-.18l-.425-.426c-.274-.273-.354-.775-.181-1.122l.752-1.504a5.963 5.963 0 01-.442-1.065L.666 9.222C.299 9.1 0 8.687 0 8.3v-.6c0-.388.298-.8.666-.922l1.594-.531c.114-.372.262-.728.442-1.065L1.95 3.678c-.173-.346-.093-.85.18-1.122l.426-.425c.273-.274.775-.354 1.122-.181l1.504.752z" fill="#ffffff" fill-rule="evenodd">

                </path>  
              </svg>
            Users</a>
          </li>
       <?php endif; ?>   

          <!--module 3 projects-->
           <?php endif; ?>
          <?php if(  Auth::user()->getRoleModule(Auth::user()->role_id,3) == 3): ?>
          <li class="nav-item">
            <a class="nav-link" href="/projects">
              <svg width="14" height="16" viewBox="0 0 14 16">
                <path d="M8 11a3 3 0 100-6 3 3 0 000 6zM5.182 2.702c.337-.18.693-.328 1.065-.442L6.778.666C6.9.299 7.313 0 7.7 0h.6c.387 0 .8.298.922.666l.531 1.594c.372.114.728.262 1.065.442l1.504-.752c.346-.173.85-.093 1.122.18l.425.426c.274.273.354.775.181 1.122l-.752 1.504c.18.337.328.693.442 1.065l1.594.531c.367.122.666.535.666.922v.6c0 .387-.298.8-.666.922l-1.594.531a5.963 5.963 0 01-.442 1.065l.752 1.504c.173.346.093.85-.18 1.122l-.426.425c-.273.274-.775.354-1.122.181l-1.504-.752c-.337.18-.693.328-1.065.442l-.531 1.594C9.1 15.701 8.687 16 8.3 16h-.6c-.388 0-.8-.298-.922-.666l-.531-1.594a5.963 5.963 0 01-1.065-.442l-1.504.752c-.346.173-.85.093-1.122-.18l-.425-.426c-.274-.273-.354-.775-.181-1.122l.752-1.504a5.963 5.963 0 01-.442-1.065L.666 9.222C.299 9.1 0 8.687 0 8.3v-.6c0-.388.298-.8.666-.922l1.594-.531c.114-.372.262-.728.442-1.065L1.95 3.678c-.173-.346-.093-.85.18-1.122l.426-.425c.273-.274.775-.354 1.122-.181l1.504.752z" fill="#ffffff" fill-rule="evenodd">

                </path>  
              </svg>
            Projects</a>
          </li>
          <?php endif; ?>
            <?php if( Auth::user()->getRoleModule(Auth::user()->role_id,7) == 7): ?> <!-- module 7 = Role Module-->
          <li class="nav-item">
            <a class="nav-link" href="/role_modules">
              <svg width="14" height="16" viewBox="0 0 14 16">
                <path d="M8 11a3 3 0 100-6 3 3 0 000 6zM5.182 2.702c.337-.18.693-.328 1.065-.442L6.778.666C6.9.299 7.313 0 7.7 0h.6c.387 0 .8.298.922.666l.531 1.594c.372.114.728.262 1.065.442l1.504-.752c.346-.173.85-.093 1.122.18l.425.426c.274.273.354.775.181 1.122l-.752 1.504c.18.337.328.693.442 1.065l1.594.531c.367.122.666.535.666.922v.6c0 .387-.298.8-.666.922l-1.594.531a5.963 5.963 0 01-.442 1.065l.752 1.504c.173.346.093.85-.18 1.122l-.426.425c-.273.274-.775.354-1.122.181l-1.504-.752c-.337.18-.693.328-1.065.442l-.531 1.594C9.1 15.701 8.687 16 8.3 16h-.6c-.388 0-.8-.298-.922-.666l-.531-1.594a5.963 5.963 0 01-1.065-.442l-1.504.752c-.346.173-.85.093-1.122-.18l-.425-.426c-.274-.273-.354-.775-.181-1.122l.752-1.504a5.963 5.963 0 01-.442-1.065L.666 9.222C.299 9.1 0 8.687 0 8.3v-.6c0-.388.298-.8.666-.922l1.594-.531c.114-.372.262-.728.442-1.065L1.95 3.678c-.173-.346-.093-.85.18-1.122l.426-.425c.273-.274.775-.354 1.122-.181l1.504.752z" fill="#ffffff" fill-rule="evenodd">

                </path>  
              </svg>
            Role modules</a>
          </li>
          <?php endif; ?>
           <?php if(  Auth::user()->role_id == 2 || Auth::user()->role_id == 1): ?>
          <li class="nav-item">
            <a class="nav-link" href="/contests/comments">FB Contest</a>
          </li>

          <div class="dropdown">
            <button class="btn btn-default dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
              Tools
            </button>
            <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
              <a class="nav-link" href="/contests/comments">Facebook Contest</a>
              <a class="nav-link" href="/contests/instagram/comments">Instagram Contest</a>
              <a class="nav-link" href="/proximity/list">Users Aladino</a>      
              <a class="nav-link" href="/proximity/listMap">Map Users Aladino</a>
              <a class="nav-link" href="/reports/tasks_time">Tasks en el tiempo</a>
              <li class="nav-link"><a class="nav-link" href="/proximity">Proximiy</a></li>
                         
            </div>
          </div>

          <?php endif; ?>


          <?php if(  Auth::user()->role_id == 2 || Auth::user()->role_id == 1): ?>
      <!-- <?php if( Auth::user()->getRoleModule(Auth::user()->role_id,5) == 5): ?>-->
          <li class="nav-item">
            <a class="nav-link" href="/documents"><svg class="css-wlea3r" width="16" height="13" viewBox="0 0 16 13">
              <path fill="#ffffff" fill-rule="evenodd" d="M0 6h16v4.994A2.001 2.001 0 0114.006 13H1.994A1.993 1.993 0 010 10.994V6zm0-4a2 2 0 012.004-2h3.05c1.107 0 2.004.895 2.004 2h6.935C15.102 2 16 2.895 16 4H0V2z"></path></svg>
            Documents
            </a>
          </li>
         <!-- <?php endif; ?>-->
    
            <li class="dropdown">
                <a class="nav-link" href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">
                   <svg width="16" height="16" viewBox="0 0 16 16"><path fill="#ffffff" fill-rule="evenodd" d="M8 16A8 8 0 118 0a8 8 0 010 16zm0-3a1 1 0 100-2 1 1 0 000 2zm0-8c.683 0 1.068.57.955 1.1-.077.362-.2.53-.623.908l-.052.046c-.83.741-1.202 1.316-1.278 2.416a1 1 0 101.996.138c.032-.476.135-.634.613-1.061l.053-.048c.711-.634 1.062-1.113 1.247-1.982A2.914 2.914 0 008 3c-1.572 0-2.501.84-3.057 2.145a1 1 0 001.84.784C7.057 5.285 7.373 5 8 5z"></path></svg>My Account <span class="caret"></span>
                </a>
            </li>
            <li>
              <a class="nav-link" href="<?php echo e(route('logout')); ?>"
                  onclick="event.preventDefault();
                           document.getElementById('logout-form').submit();">
                           <svg width="16" height="16" viewBox="0 0 16 16"><path fill="#ffffff" fill-rule="evenodd" d="M8 16A8 8 0 118 0a8 8 0 010 16zm0-3a1 1 0 100-2 1 1 0 000 2zm0-8c.683 0 1.068.57.955 1.1-.077.362-.2.53-.623.908l-.052.046c-.83.741-1.202 1.316-1.278 2.416a1 1 0 101.996.138c.032-.476.135-.634.613-1.061l.053-.048c.711-.634 1.062-1.113 1.247-1.982A2.914 2.914 0 008 3c-1.572 0-2.501.84-3.057 2.145a1 1 0 001.84.784C7.057 5.285 7.373 5 8 5z"></path></svg>
                  Logout
              </a>

            <form id="logout-form" action="<?php echo e(route('logout')); ?>" method="POST" style="display: none;">
                  <?php echo e(csrf_field()); ?>

              </form>
          </li>
                       <!-- <?php endif; ?>-->

           <?php endif; ?>            
        </ul>
  
      </div>

</div>



<div class="modal fade" id="notificationModal" tabindex="-1" role="dialog" aria-labelledby="notificationModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="notificationModalLabel">Notifications</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
          <div class="row">
            <div class="col-md">
              <div id="table-notification-modal"></div>
            </div>
          </div>
      </div>
    </div>
  </div>
</div>



<script type="text/javascript">
  $(document).ready(function(){
   $.ajax({
      type: 'GET',
      url: "/get_notifications/<?php echo e(Auth::user()->id); ?>",
      dataType: 'json',
      success: function (data) {
        if(data!=0){
          $("#notification").append('<span class="badge" style="background: #007bff;" data-toggle="modal" data-target="#notificationModal" data-backdrop="false">'+data+'</span>');   
        }                
      },
      error: function(data) { 
           console.log(data);
      }
    });

   $.ajax({
      type: 'GET',
      url: "/get_all_notifications",
      dataType: 'json',
      success: function (data) {


        

        str = '<table class="table table-hover"><thead><tr><th>Message</th></tr></thead><tbody>';
        $.each(data, function(i, obj) {
          str += '<tr><td><a href="" onclick="viewTaskCommentary('+obj.id+')">'+obj.body+'</a></td></tr>';
        });
        str += '</tbody></table>';
        $("#table-notification-modal").html(str);





      },
      error: function(data) { 
           console.log(data);
      }
    });
 });

  function viewTaskCommentary(cid){
    console.log(cid);
    $.ajax({
      type: 'GET',
      url: "/set_notification_reviewed/"+cid,
      dataType: 'json',
      success: function (data) {
        window.location = "/tasks/"+data.task_id;                       
      },
      error: function(data) { 
           console.log(data);
      }
    });
  }
</script>