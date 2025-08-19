<div class="navbar-bg"></div>
{{-- Navbar Start --}}
@include('admin.layouts.navbar')
{{-- Nvabar End --}}
<div class="main-sidebar sidebar-style-2">
    <aside id="sidebar-wrapper">
        <div class="sidebar-brand">
            <a href="{{ route('admin.dashboard') }}">Shop</a>
        </div>
        <div class="sidebar-brand sidebar-brand-sm">
            <a href="{{ route('admin.dashboard') }}">Sh</a>
        </div>
        <ul class="sidebar-menu">
            <li class="menu-header">Dashboard</li>
            <li class=" active">
                <a href="{{ route('admin.dashboard') }}" class="nav-link"><i class="fas fa-fire"></i><span>Dashboard</span></a>
            </li>
            <li class="menu-header">Starter</li>

            <li><a class="nav-link" href="{{ route('admin.categories.index') }}"><i class="far fa-square"></i>
                    <span>Category</span></a>
            </li>
            <li><a class="nav-link" href="{{ route('admin.brands.index') }}"><i class="far fa-square"></i>
                    <span>Brand</span></a>
            </li>
            <li><a class="nav-link" href="{{ route('admin.products.index') }}"><i class="far fa-square"></i>
                    <span>Products</span></a>
            </li>

        </ul>

    </aside>
</div>
