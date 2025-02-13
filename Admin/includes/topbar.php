  <nav class="navbar default-layout-navbar col-lg-12 col-12 p-0 fixed-top d-flex flex-row">
    <div class="text-center navbar-brand-wrapper d-flex align-items-center justify-content-center">
        <a class="navbar-brand brand-logo" href="dashboard.php"><img src="assets/images/logo.svg" alt="logo"></a>
        <a class="navbar-brand brand-logo-mini" href="dashboard.php"><img src="assets/images/logo-mini.svg" alt="logo"></a>
    </div>
    <div class="navbar-menu-wrapper d-flex align-items-stretch">
        <button class="navbar-toggler navbar-toggler align-self-center" type="button" data-toggle="minimize">
            <span class="mdi mdi-menu"></span>
        </button>
        <ul class="navbar-nav navbar-nav-right">
            <li class="nav-item nav-profile dropdown">
                <a class="nav-link dropdown-toggle" id="profileDropdown" href="#" data-toggle="dropdown" aria-expanded="false">
                    <div class="nav-profile-img">
                        <img src="https://bootdey.com/img/Content/avatar/avatar7.png" alt="image">
                        <span class="availability-status online"></span>
                    </div>
                    <div class="nav-profile-text">
                        <p class="mb-1 text-black">Admin</p>
                    </div>
                </a>
                <div class="dropdown-menu navbar-dropdown" aria-labelledby="profileDropdown">
                    <a class="dropdown-item" href="change-password.php">
                        <i class="mdi mdi-account-settings text-primary"></i> Settings
                    </a>
                    <a class="dropdown-item" href="logout.php">
                        <i class="mdi mdi-logout text-primary"></i> Logout
                    </a>
                </div>
            </li>
        </ul>
        <button class="navbar-toggler navbar-toggler-right d-lg-none align-self-center" type="button" data-toggle="offcanvas">
            <span class="mdi mdi-menu"></span>
        </button>
    </div>
</nav>

