            <!-- Start Navbar -->
            <!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-absolute fixed-top navbar-transparent">
    <div class="container-fluid">
        <div class="navbar-wrapper">
            <div class="navbar-minimize">
                <button id="minimizeSidebar" class="btn btn-icon btn-round">
                    <i class="nc-icon nc-minimal-right text-center visible-on-sidebar-mini"></i>
                    <i class="fa fa-chevron-right"></i>
                </button>
            </div>
            <div class="navbar-toggle">
                <button type="button" class="navbar-toggler">
                    <span class="navbar-toggler-bar bar1"></span>
                    <span class="navbar-toggler-bar bar2"></span>
                    <span class="navbar-toggler-bar bar3"></span>
                </button>
            </div>

            <a class="navbar-brand">
                @yield('title') 
            </a>
        </div>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navigation" aria-controls="navigation-index" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-bar navbar-kebab"></span>
            <span class="navbar-toggler-bar navbar-kebab"></span>
            <span class="navbar-toggler-bar navbar-kebab"></span>
        </button>
        <div class="collapse navbar-collapse justify-content-end" id="navigation">
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link btn-rotate" href="/profile">
                        <i class="fa fa-user"></i>
                        <p>
                            <span class="">{{ (auth()->check())  ? auth()->user()->name : 'Guest' }}</span>
                        </p>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link btn-magnify"  href="{{ route('logout') }}"
                             onclick="event.preventDefault();
                                           document.getElementById('logout-form2').submit();">
                               <i class="fa fa-sign-out"></i>
                        <p>
                            <span class="d-lg-none d-md-block">Logout</span>
                        </p>
                          </a>

                          <form id="logout-form2" action="{{ route('logout') }}" method="POST" class="d-none">
                              @csrf
                          </form>

                </li>
            </ul>
        </div>
    </div>
</nav>            <!-- End Navbar -->