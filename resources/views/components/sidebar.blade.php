<div class="main-sidebar sidebar-style-2">
    <aside id="sidebar-wrapper">
        <div class="sidebar-brand">
            <a href="index.html">Resto Asep</a>
        </div>
        <div class="sidebar-brand sidebar-brand-sm">
            <a href="index.html">St</a>
        </div>
        <ul class="sidebar-menu">
            <li class="menu-header">Menu</li>
            <li class="nav-item dropdown">
                <a href="{{ route('home') }}"
                    class="nav-link"><i class="fas fa-fire"></i><span>Dashboard</span></a>  
            </li>           
            <li class="nav-item">
                <a href="{{ route('users.index') }}"
                    class="nav-link"><i class="fas fa-users"></i><span>Users</span></a>
            </li>
            <li class="nav-item">
                <a href="{{ route('categories.index') }}"
                    class="nav-link"><i class="fas fa-folder"></i><span>Category</span></a>
            </li>
            <li class="nav-item">
                <a href="{{ route('products.index') }}"
                    class="nav-link"><i class="fas fa-folder"></i><span>Products</span></a>
            </li>
            
        </ul>
    </aside>
</div>
