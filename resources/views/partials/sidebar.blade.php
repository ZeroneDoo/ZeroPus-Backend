<ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">

    <!-- Sidebar - Brand -->
    <a class="sidebar-brand d-flex align-items-center justify-content-center" href="index.html">
        <div class="sidebar-brand-icon rotate-n-15">
            <i class="fas fa-laugh-wink"></i>
        </div>
        <div class="sidebar-brand-text mx-3">ZeroPus</div>
    </a>

    <!-- Divider -->
    <hr class="sidebar-divider my-0">

    <!-- Nav Item - Dashboard -->
    <li class="nav-item {{ Route::is('dashboard*') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('dashboard') }}">
            <i class="fas fa-fw fa-tachometer-alt"></i>
            <span>Dashboard</span></a>
    </li>

    <!-- Divider -->
    <hr class="sidebar-divider">

    <!-- Heading -->
    <div class="sidebar-heading">
        Master
    </div>

    <!-- Nav Item - Pages Collapse Menu -->
    <li class="nav-item {{ Route::is('credit*') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('credit.index') }}">
            <i class="fas fa-fw fa-folder"></i>
            <span>Credit</span>
        </a>
    </li>
    <li class="nav-item {{ Route::is('category*') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('category.index') }}">
            <i class="fas fa-fw fa-folder"></i>
            <span>Category</span>
        </a>
    </li>
    <li class="nav-item {{ Route::is('book*') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('book.index') }}">
            <i class="fas fa-fw fa-folder"></i>
            <span>Book</span>
        </a>
    </li>

    <!-- Nav Item - Utilities Collapse Menu -->
    <li class="nav-item {{ Route::is('user*') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('user.index') }}">
            <i class="fas fa-solid fa-user"></i>
            <span>User</span>
        </a>
    </li>

    <!-- Divider -->
    <hr class="sidebar-divider">

    <!-- Heading -->
    <div class="sidebar-heading">
        Sheild
    </div>
    
    <!-- Nav Item - Utilities Collapse Menu -->
    <li class="nav-item {{ Route::is('role*') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('role.index') }}">
            <i class="fas fa-shield-alt"></i>
            <span>Role</span>
        </a>
    </li>

    <!-- Divider -->
    <hr class="sidebar-divider">

    <!-- Heading -->
    <div class="sidebar-heading">
        Transaction
    </div>

    <!-- Nav Item - Pages Collapse Menu -->
    <li class="nav-item">
        <a class="nav-link" href="#">
            <i class="fas fa-fw fa-folder"></i>
            <span>Rent</span>
        </a>
    </li>

    <!-- Divider -->
    <hr class="sidebar-divider d-none d-md-block">

    <!-- Sidebar Toggler (Sidebar) -->
    <div class="text-center d-none d-md-inline">
        <button class="rounded-circle border-0" id="sidebarToggle"></button>
    </div>

</ul>