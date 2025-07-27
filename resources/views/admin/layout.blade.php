<!doctype html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'لوحة تحكم Cosmelea')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;500;700&display=swap" rel="stylesheet">

    <script src="https://cdn.jsdelivr.net/npm/@alpinejs/collapse@3.x.x/dist/cdn.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <style>
        :root {
            --sidebar-width: 280px;
            --topbar-height: 70px;
            --bg-light: #f9f5f1;
            --primary-dark: #be6661;
            --primary-medium: #cd8985;
            --primary-light: #dcaca9;
            --secondary-light: #eadbcd;
            --white: #ffffff;
            --text-dark: #333333;
            --text-light: #666666;
            --transition: all 0.25s ease;
            --border-radius: 8px;
            --icon-size: 1.2rem;
            --nav-link-padding: 0.75rem 1.25rem;
        }
        
        body {
            font-family: 'Tajawal', sans-serif;
            background-color: var(--bg-light);
            color: var(--text-dark);
            overflow-x: hidden;
            scroll-behavior: smooth;
        }
        
        .main-wrapper {
            display: flex;
            min-height: 100vh;
        }
        
        .sidebar {
            width: var(--sidebar-width);
            background-color: var(--white);
            position: sticky;
            top: 0;
            height: 100vh;
            z-index: 1000;
            border-left: 1px solid var(--secondary-light);
            display: flex;
            flex-direction: column;
            box-shadow: 2px 0 10px rgba(0, 0, 0, 0.05);
            overflow-y: auto;
        }
        
        .sidebar a {
            scroll-behavior: auto;
        }
        
        .sidebar-content {
            overflow-y: auto;
            flex: 1;
            padding-bottom: 1rem;
            scrollbar-width: thin;
            scrollbar-color: var(--primary-light) var(--secondary-light);
        }
        
        .sidebar-content::-webkit-scrollbar {
            width: 6px;
        }
        
        .sidebar-content::-webkit-scrollbar-thumb {
            background-color: var(--primary-light);
            border-radius: 3px;
        }
        
        .sidebar-content::-webkit-scrollbar-track {
            background-color: var(--secondary-light);
        }
        
        .sidebar-brand {
            padding: 1.5rem;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.4rem;
            font-weight: 700;
            color: var(--primary-dark);
            border-bottom: 1px solid var(--secondary-light);
            flex-shrink: 0;
        }
        
        .sidebar-brand i {
            font-size: 1.8rem;
            margin-left: 0.75rem;
            color: var(--primary-dark);
        }
        
        .nav-link {
            color: var(--text-dark);
            padding: var(--nav-link-padding);
            margin: 0.25rem 1rem;
            border-radius: var(--border-radius);
            display: flex;
            align-items: center;
            transition: var(--transition);
            font-weight: 500;
            font-size: 0.95rem;
            position: relative;
            text-decoration: none;
        }
        
        .nav-link:hover {
            background-color: var(--primary-light);
            color: var(--white);
            transform: translateX(-5px);
        }
        
        .nav-link.active {
            background-color: var(--primary-medium);
            color: var(--white);
            font-weight: 600;
            box-shadow: 0 4px 8px rgba(190, 102, 97, 0.2);
        }
        
        .nav-link.active::before {
            content: '';
            position: absolute;
            right: -1px;
            top: 0;
            height: 100%;
            width: 4px;
            background-color: var(--primary-dark);
            border-radius: 4px 0 0 4px;
        }
        
        .nav-link .bi {
            font-size: var(--icon-size);
            margin-left: 0.75rem;
            transition: var(--transition);
            width: 24px;
            text-align: center;
        }
        
        .nav-link:hover .bi,
        .nav-link.active .bi {
            color: var(--white);
            transform: scale(1.1);
        }
        
        .badge {
            font-size: 0.65rem;
            padding: 0.35em 0.5em;
            margin-right: auto;
        }
        
        .sidebar-footer {
            padding: 1rem;
            border-top: 1px solid var(--secondary-light);
            flex-shrink: 0;
            font-size: 0.8rem;
            color: var(--text-light);
            text-align: center;
        }
        
        .content-wrapper {
            flex: 1;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            width: calc(100% - var(--sidebar-width));
        }
        
        .main-content {
            flex: 1;
            padding: 1.75rem;
            overflow-y: auto;
        }
        
        .topbar {
            background: var(--white);
            padding: 0 1.75rem;
            height: var(--topbar-height);
            display: flex;
            align-items: center;
            border-bottom: 1px solid var(--secondary-light);
            flex-shrink: 0;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.03);
            position: sticky;
            top: 0;
            z-index: 999;
        }
        
        .sidebar-toggle-btn {
            transition: var(--transition);
            border: none;
            background: transparent;
            font-size: 1.5rem;
            color: var(--primary-dark);
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
        }
        
        .sidebar-toggle-btn:hover {
            color: var(--primary-medium);
            background-color: rgba(205, 137, 133, 0.1);
        }
        
        .user-dropdown .dropdown-toggle {
            display: flex;
            align-items: center;
            color: var(--text-dark);
            text-decoration: none;
            transition: var(--transition);
        }
        
        .user-dropdown .dropdown-toggle:hover {
            color: var(--primary-dark);
        }
        
        .user-dropdown img {
            width: 38px;
            height: 38px;
            border-radius: 50%;
            object-fit: cover;
            margin-left: 0.75rem;
            border: 2px solid var(--primary-light);
            transition: var(--transition);
        }
        
        .user-dropdown .dropdown-toggle:hover img {
            border-color: var(--primary-medium);
        }
        
        .dropdown-menu {
            border: none;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            border-radius: var(--border-radius);
            padding: 0.5rem;
            margin-top: 0.5rem !important;
        }
        
        .dropdown-item {
            border-radius: var(--border-radius);
            padding: 0.5rem 1rem;
            font-size: 0.9rem;
            transition: var(--transition);
        }
        
        .dropdown-item:hover {
            background-color: var(--primary-light);
            color: var(--white);
        }
        
        .card {
            border: none;
            border-radius: var(--border-radius);
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.03);
            background-color: var(--white);
            margin-bottom: 1.75rem;
            transition: var(--transition);
        }
        
        .card:hover {
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            transform: translateY(-2px);
        }
        
        .card-header {
            background-color: transparent;
            border-bottom: 1px solid var(--secondary-light);
            padding: 1.25rem 1.5rem;
            font-weight: 600;
            color: var(--primary-dark);
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        
        .card-header .bi {
            font-size: 1.2rem;
            color: var(--primary-medium);
        }
        
        .btn {
            border-radius: var(--border-radius);
            padding: 0.5rem 1.25rem;
            font-weight: 500;
            transition: var(--transition);
        }
        
        .btn-primary {
            background-color: var(--primary-dark);
            border-color: var(--primary-dark);
            color: var(--white);
        }
        
        .btn-primary:hover {
            background-color: var(--primary-medium);
            border-color: var(--primary-medium);
            box-shadow: 0 4px 12px rgba(205, 137, 133, 0.3);
        }
        
        .btn-sm {
            padding: 0.35rem 0.75rem;
            font-size: 0.85rem;
        }
        
        .alert {
            border-radius: var(--border-radius);
            border: none;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
        }

        .reports-submenu {
            margin-right: 1.5rem;
            border-right: 2px solid var(--secondary-light);
            padding-right: 1rem;
            margin-left: 1rem;
        }
        .reports-submenu .nav-link.sub-link {
            padding: 0.5rem 1rem;
            margin: 0.25rem 0;
            font-size: 0.85rem;
        }
        .reports-submenu .nav-link.sub-link:hover {
            background-color: var(--bg-light);
            color: var(--primary-dark);
            transform: none;
        }
        .reports-submenu .nav-link.sub-link .bi {
            font-size: 0.9rem;
            color: var(--primary-medium);
        }
        .reports-submenu .nav-link.sub-link:hover .bi {
            color: var(--primary-dark);
            transform: none;
        }
        .nav-link.reports-toggle.active,
        .nav-link.reports-toggle[aria-expanded="true"] {
             background-color: #a85a56;
             color: white;
        }
        
        @media (max-width: 992px) {
            .main-wrapper {
                flex-direction: column;
            }
            
            .sidebar {
                position: fixed;
                top: 0;
                right: -100%;
                width: var(--sidebar-width);
                height: 100vh;
                transition: var(--transition);
                z-index: 1050;
            }
            
            .sidebar.active {
                right: 0;
            }
            
            .content-wrapper {
                width: 100%;
                margin-right: 0;
            }
            
            .sidebar-overlay {
                position: fixed;
                top: 0;
                right: 0;
                width: 100%;
                height: 100%;
                background-color: rgba(0, 0, 0, 0.5);
                z-index: 1040;
                opacity: 0;
                visibility: hidden;
                transition: var(--transition);
            }
            
            .sidebar-overlay.active {
                opacity: 1;
                visibility: visible;
            }
        }
    </style>
    @stack('styles')
</head>
<body>
    
    <div class="main-wrapper">
        <div class="sidebar">
            <div class="sidebar-brand">
                <i class="bi bi-flower1"></i>
                <span>Cosmelea</span>
            </div>
            
            <div class="sidebar-content">
                <ul class="nav flex-column mt-2">
                    @can('view-admin-panel')
                    <li class="nav-item">
                        <a href="{{ route('admin.dashboard') }}" class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                            <i class="bi bi-speedometer2"></i>
                            <span>لوحة التحكم</span>
                        </a>
                    </li>
                    @endcan
                    
                    @can('view-orders')
                    <li class="nav-item">
                        <a href="{{ route('admin.orders.index') }}" class="nav-link {{ request()->routeIs('admin.orders.*') ? 'active' : '' }}">
                            <i class="bi bi-cart-check"></i>
                            <span>الطلبات</span>
                            @if (isset($pendingOrdersCount) && $pendingOrdersCount > 0)
                                <span class="badge bg-primary rounded-pill ms-auto">{{ $pendingOrdersCount }}</span>
                            @endif
                        </a>
                    </li>
                    @endcan
                    
                    @can('view-products')
                    <li class="nav-item">
                        <a href="{{ route('admin.products.index') }}" class="nav-link {{ request()->routeIs('admin.products.*') ? 'active' : '' }}">
                            <i class="bi bi-basket"></i>
                            <span>المنتجات</span>
                        </a>
                    </li>
                    @endcan
                    
                    @can('view-categories')
                    <li class="nav-item">
                        <a href="{{ route('admin.categories.index') }}" class="nav-link {{ request()->routeIs('admin.categories.*') ? 'active' : '' }}">
                            <i class="bi bi-tags"></i>
                            <span>الأقسام</span>
                        </a>
                    </li>
                    @endcan
                    
                    @can('view-customers')
                    <li class="nav-item">
                        <a href="{{ route('admin.customers.index') }}" class="nav-link {{ request()->routeIs('admin.customers.*') ? 'active' : '' }}">
                            <i class="bi bi-people"></i>
                            <span>العملاء</span>
                        </a>
                    </li>
                    @endcan
                    
                    @can('view-discount-codes')
                    <li class="nav-item">
                        <a href="{{ route('admin.discount-codes.index') }}" class="nav-link {{ request()->routeIs('admin.discount-codes.*') ? 'active' : '' }}">
                            <i class="bi bi-percent"></i>
                            <span>أكواد الخصم</span>
                        </a>
                    </li>
                    @endcan
                    
                    <hr class="mx-3 my-2 bg-secondary-light">
                    
                    @can('view-users')
                    <li class="nav-item">
                        <a href="{{ route('admin.users.index') }}" class="nav-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                            <i class="bi bi-person-gear"></i>
                            <span>المستخدمين</span>
                        </a>
                    </li>
                    @endcan
                    
                    @can('view-roles')
                    <li class="nav-item">
                        <a href="{{ route('admin.roles.index') }}" class="nav-link {{ request()->routeIs('admin.roles.*') ? 'active' : '' }}">
                            <i class="bi bi-shield-lock"></i>
                            <span>الصلاحيات</span>
                        </a>
                    </li>
                    @endcan
                    
                    <hr class="mx-3 my-2 bg-secondary-light">
                    @can('view-inventory')
                    <li class="nav-item">
                        <a href="{{ route('admin.inventory.index') }}" class="nav-link {{ request()->routeIs('admin.inventory.*') ? 'active' : '' }}">
                            <i class="bi bi-boxes"></i>
                            <span>المخزن</span>
                        </a>
                    </li>
                    @endcan
                    
                    @can('view-expenses')
                    <li class="nav-item">
                        <a href="{{ route('admin.expenses.index') }}" class="nav-link {{ request()->routeIs('admin.expenses.*') ? 'active' : '' }}">
                            <i class="bi bi-cash-stack"></i>
                            <span>المصاريف</span>
                        </a>
                    </li>
                    @endcan
                    
                    @can('view-suppliers')
                    <li class="nav-item">
                        <a href="{{ route('admin.suppliers.index') }}" class="nav-link {{ request()->routeIs('admin.suppliers.*') ? 'active' : '' }}">
                            <i class="bi bi-box-seam"></i>
                            <span>الموردين</span>
                        </a>
                    </li>
                    @endcan
                    
                    @can('view-purchases')
                    <li class="nav-item">
                        <a href="{{ route('admin.purchases.index') }}" class="nav-link {{ request()->routeIs('admin.purchases.*') ? 'active' : '' }}">
                            <i class="bi bi-bag-fill"></i>
                            <span>المشتريات</span>
                        </a>
                    </li>
                    @endcan
                    
                    @can('view-reports')
                    <li class="nav-item" x-data="{ open: {{ request()->routeIs('admin.reports.*') ? 'true' : 'false' }} }">
                        <a href="#" @click.prevent="open = !open" class="nav-link d-flex justify-content-between reports-toggle" 
                           :class="{ 'active': open }" 
                           aria-expanded="false" 
                           :aria-expanded="open.toString()">
                            <div>
                                <i class="bi bi-graph-up-arrow"></i>
                                <span>التقارير</span>
                            </div>
                            <i class="bi transition-transform" :class="open ? 'bi-chevron-down' : 'bi-chevron-right'"></i>
                        </a>
                        <div x-show="open" x-collapse class="reports-submenu">
                            <ul class="nav flex-column">
                                <li class="nav-item">
                                    <a href="{{ route('admin.reports.index') }}" class="nav-link sub-link">
                                        <i class="bi bi-bar-chart"></i>
                                        <span>التقارير الرئيسية</span>
                                    </a>
                                </li>
                                <li class="nav-item">
                                     <a href="#" class="nav-link sub-link" data-bs-toggle="modal" data-bs-target="#splitViewModal" data-url="{{ route('admin.reports.financial') }}" data-title="تقارير المبيعات">
                                        <i class="bi bi-cart"></i>
                                        <span>تقارير المبيعات</span>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="#" class="nav-link sub-link" data-bs-toggle="modal" data-bs-target="#splitViewModal" data-url="{{ route('admin.reports.inventory') }}" data-title="تقارير المخزون">
                                        <i class="bi bi-box"></i>
                                        <span>تقارير المخزون</span>
                                    </a>
                                </li>
                                 <li class="nav-item">
                                    <a href="#" class="nav-link sub-link" data-bs-toggle="modal" data-bs-target="#splitViewModal" data-url="{{ route('admin.reports.customers') }}" data-title="تقارير العملاء">
                                        <i class="bi bi-people"></i>
                                        <span>تقارير العملاء</span>
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </li>
                    @endcan
                    @can('edit-settings')
                    <hr class="mx-3 my-2 bg-secondary-light">
                    <li class="nav-item">
                        <a href="{{ route('admin.settings.index') }}" class="nav-link {{ request()->routeIs('admin.settings.*') ? 'active' : '' }}">
                            <i class="bi bi-gear-fill"></i>
                            <span>الإعدادات</span>
                        </a>
                    </li>
                    @endcan
                    
                    @can('view-activity-log')
                    <li class="nav-item">
                        <a href="{{ route('admin.activity-log.index') }}" class="nav-link {{ request()->routeIs('admin.activity-log.*') ? 'active' : '' }}">
                            <i class="bi bi-list-check"></i>
                            <span>سجل الأنشطة</span>
                        </a>
                    </li>
                    @endcan
                </ul>
            </div>
            
            <div class="sidebar-footer">
                الإصدار 1.0.0
            </div>
        </div>
        
        <div class="sidebar-overlay"></div>

        <div class="content-wrapper">
            <nav class="topbar">
                <button class="sidebar-toggle-btn" id="sidebarToggle">
                    <i class="bi bi-list"></i>
                </button>
                
                <div class="user-dropdown dropdown ms-auto">
                    <a class="dropdown-toggle d-flex align-items-center text-decoration-none" href="#" role="button" data-bs-toggle="dropdown">
                        <span class="me-2">{{ Auth::user()->name }}</span>
                        <img src="https://i.pravatar.cc/40?u={{ Auth::id() }}" alt="User">
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end shadow">
                        <li>
                            <a class="dropdown-item" href="{{ route('homepage') }}" target="_blank">
                                <i class="bi bi-box-arrow-up-right me-2"></i> عرض الموقع
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item text-danger" href="{{ route('logout') }}"
                               onclick="event.preventDefault(); document.getElementById('logout-form-top').submit();">
                                <i class="bi bi-box-arrow-left me-2"></i> تسجيل الخروج
                            </a>
                            <form id="logout-form-top" action="{{ route('logout') }}" method="POST" class="d-none">@csrf</form>
                        </li>
                    </ul>
                </div>
            </nav>
            
            <main class="main-content">
                <div class="container-fluid">
                    @if (session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="bi bi-check-circle-fill me-2"></i>
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                    @endif
                    
                    @if (session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="bi bi-exclamation-triangle-fill me-2"></i>
                        {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                    @endif
                    
                    @yield('content')
                </div>
            </main>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
document.addEventListener('DOMContentLoaded', function() {
    // عناصر DOM الأساسية
    const sidebar = document.querySelector('.sidebar');
    const overlay = document.querySelector('.sidebar-overlay');
    const toggleBtn = document.getElementById('sidebarToggle');
    
    // تبديل حالة القائمة الجانبية (للوضع الجوال)
    function toggleSidebar() {
        sidebar.classList.toggle('active');
        overlay.classList.toggle('active');
    }
    
    // إضافة event listeners للتبديل
    if (toggleBtn) toggleBtn.addEventListener('click', toggleSidebar);
    if (overlay) overlay.addEventListener('click', toggleSidebar);
    
    // حل المشكلة الرئيسية - منع التمرير التلقائي
    document.querySelectorAll('.sidebar a').forEach(link => {
        link.addEventListener('click', function(e) {
            // تجاهل الروابط التي تفتح modals أو dropdowns
            if (this.hasAttribute('data-bs-toggle') || 
                this.getAttribute('href') === '#' ||
                this.classList.contains('dropdown-toggle')) {
                return;
            }
            
            // منع السلوك الافتراضي تماماً
            e.preventDefault();
            
            // حفظ موضع التمرير الحالي
            const scrollPosition = window.pageYOffset || document.documentElement.scrollTop;
            
            // الانتقال إلى الصفحة الجديدة
            if (this.href && this.href !== '#') {
                // إضافة فصل زمني بسيط لضمان تنفيذ الأوامر بالترتيب الصحيح
                setTimeout(() => {
                    window.location.href = this.href;
                    
                    // استعادة موضع التمرير بعد تحميل الصفحة
                    window.addEventListener('load', () => {
                        window.scrollTo(0, scrollPosition);
                    }, { once: true });
                }, 100);
            }
            
            // إغلاق القائمة في وضع الجوال
            if (window.innerWidth <= 992) {
                toggleSidebar();
            }
        });
    });
});
    </script>
    
    @stack('scripts')
</body>
</html>