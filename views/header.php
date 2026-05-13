<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Anantapur Book Centre | ABC ERP</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Segoe+UI:wght@400;600;700&display=swap');

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f0f4f8;
            color: #333;
        }

        .sidebar {
            width: 260px;
            background: #fff;
            height: 100vh;
            position: fixed;
            left: 0;
            top: 0;
            border-right: 1px solid #d1d9e6;
            z-index: 1000;
            overflow-y: auto;
            transition: transform 0.3s ease;
        }

        .header {
            height: 60px;
            background: #fff;
            border-bottom: 1px solid #d1d9e6;
            position: fixed;
            top: 0;
            left: 260px;
            right: 0;
            display: flex;
            align-items: center;
            padding: 0 20px;
            z-index: 900;
        }

        .main-content {
            margin-left: 260px;
            padding-top: 80px;
            padding-left: 40px;
            padding-right: 40px;
        }

        /* Mobile Adjustments */
        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
                width: 280px;
            }
            .sidebar.show {
                transform: translateX(0);
            }
            .header {
                left: 0;
                padding: 0 15px;
            }
            .main-content {
                margin-left: 0;
                padding-left: 15px;
                padding-right: 15px;
                padding-top: 70px;
            }
            .mobile-overlay {
                display: none;
                position: fixed;
                inset: 0;
                background: rgba(0,0,0,0.5);
                z-index: 950;
            }
            .mobile-overlay.show {
                display: block;
            }
        }

        .nav-item {
            display: flex;
            align-items: center;
            padding: 14px 20px;
            color: #1a202c;
            font-size: 14px;
            font-weight: 500;
            border-bottom: 1px solid #f0f0f0;
            transition: all 0.2s;
        }

        .nav-item:hover {
            background: #eef2f7;
            color: #0056b3;
        }

        .nav-item.active {
            background: #f0f4f8;
            color: #0056b3;
            font-weight: 600;
        }

        .nav-item i {
            width: 25px;
            color: #0056b3;
            margin-right: 12px;
            font-size: 16px;
        }

        .nav-item .chevron {
            margin-left: auto;
            font-size: 10px;
            color: #0056b3;
            transition: transform 0.2s;
        }

        .nav-item .chevron.rotated {
            transform: rotate(90deg);
        }

        .sub-menu {
            display: none;
            background-color: #f4f8fc;
            border-bottom: 1px solid #eef2f7;
        }

        .sub-menu.open {
            display: block;
        }

        .sub-nav-item {
            display: block;
            padding: 10px 20px 10px 52px;
            color: #2d3748; /* Darker sub-items */
            font-size: 13px;
            font-weight: 500;
            text-decoration: none;
            border-bottom: 1px solid #eef2f7;
            transition: background 0.2s;
        }

        .sub-nav-item:last-child {
            border-bottom: none;
        }

        .sub-nav-item:hover {
            color: #0056b3;
            background-color: #ebf3fa;
        }

        .search-bar {
            background: #fff;
            border: 1px solid #0056b3;
            border-radius: 50px;
            width: 500px;
            height: 35px;
            display: flex;
            align-items: center;
            padding: 0 15px;
            margin: 0 auto;
        }

        .search-bar input {
            border: none;
            outline: none;
            width: 100%;
            font-size: 13px;
            margin-left: 10px;
        }

        .logo-area {
            padding: 15px 20px;
            border-bottom: 1px solid #d1d9e6;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .logo-text {
            color: #800000;
            font-weight: 800;
            font-style: italic;
            font-size: 22px;
            letter-spacing: -1px;
        }

        .klu-card {
            background: #fff;
            border-radius: 8px;
            padding: 30px;
            display: flex;
            flex-direction: column;
            align-items: center;
            position: relative;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            transition: transform 0.2s;
            cursor: pointer;
        }

        .klu-card:hover {
            transform: translateY(-2px);
        }

        .card-icon {
            width: 70px;
            height: 70px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 30px;
            color: #fff;
            margin-bottom: 20px;
        }

        .card-badge {
            position: absolute;
            right: 20px;
            top: 50%;
            transform: translateY(-50%);
            background: #ffc107;
            color: #000;
            font-weight: bold;
            padding: 5px 12px;
            border-radius: 4px;
            font-size: 14px;
        }
    </style>
</head>

<body>
    <!-- Mobile Overlay -->
    <div id="mobile-overlay" class="mobile-overlay" onclick="toggleSidebar()"></div>

    <!-- Sidebar -->
    <aside id="sidebar" class="sidebar">
        <div class="logo-area flex items-center justify-between">
            <div class="flex items-center gap-2">
                <div class="w-10 h-10 bg-[#800000] rounded-xl flex items-center justify-center text-white text-xl shadow-lg shadow-red-900/20">
                    <i class="fa-solid fa-book-open-reader"></i>
                </div>
                <span class="logo-text">ABC ERP</span>
            </div>
            <button onclick="toggleSidebar()" class="md:hidden text-gray-500 hover:text-[#800000]">
                <i class="fa-solid fa-xmark text-2xl"></i>
            </button>
        </div>

        <nav class="mt-2">
            <a href="index.php?page=dashboard" class="nav-item <?= ($page === 'dashboard' ? 'active' : '') ?>">
                <i class="fa-solid fa-house"></i> Home
            </a>
            
            <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
            <a href="index.php?page=inventory" class="nav-item <?= ($page === 'inventory' ? 'active' : '') ?>">
                <i class="fa-solid fa-boxes-stacked"></i> Inventory Master
            </a>
            <a href="index.php?page=products" class="nav-item <?= ($page === 'products' ? 'active' : '') ?>">
                <i class="fa-solid fa-book"></i> Product Master
            </a>
            <a href="index.php?page=pos" class="nav-item <?= ($page === 'pos' ? 'active' : '') ?>">
                <i class="fa-solid fa-receipt text-emerald-600"></i> Billing / POS
            </a>
            <a href="index.php?page=customers" class="nav-item <?= ($page === 'customers' ? 'active' : '') ?>">
                <i class="fa-solid fa-user-group text-blue-500"></i> Customer Registry
            </a>
            <a href="index.php?page=suppliers" class="nav-item <?= ($page === 'suppliers' ? 'active' : '') ?>">
                <i class="fa-solid fa-truck-field text-orange-600"></i> Supplier Master
            </a>
            <a href="index.php?page=purchases" class="nav-item <?= ($page === 'purchases' ? 'active' : '') ?>">
                <i class="fa-solid fa-cart-plus text-purple-600"></i> Stock Purchase
            </a>
            <?php endif; ?>

            <div>
                <a href="#" onclick="toggleSubmenu('staff-submenu', this); return false;" class="nav-item <?= (strpos($page, 'staff') !== false ? 'active' : '') ?>">
                    <i class="fa-solid fa-users text-orange-500"></i> <?= isset($_SESSION['role']) && $_SESSION['role'] === 'admin' ? 'Staff Registry' : 'My Portal' ?> <i class="fa-solid fa-chevron-right chevron <?= (strpos($page, 'staff') !== false ? 'rotated' : '') ?>"></i>
                </a>
                <div id="staff-submenu" class="sub-menu <?= (strpos($page, 'staff') !== false ? 'open' : '') ?>">
                    <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
                    <a href="index.php?page=staff" class="sub-nav-item <?= ($page === 'staff' ? 'font-bold text-[#0056b3]' : '') ?>">» Personnel List</a>
                    <?php endif; ?>
                    <a href="index.php?page=staff_attendance" class="sub-nav-item <?= ($page === 'staff_attendance' ? 'font-bold text-[#0056b3]' : '') ?>">» <?= isset($_SESSION['role']) && $_SESSION['role'] === 'admin' ? 'Staff Attendance' : 'My Attendance' ?></a>
                    <a href="index.php?page=staff_task" class="sub-nav-item <?= ($page === 'staff_task' ? 'font-bold text-[#0056b3]' : '') ?>">» <?= isset($_SESSION['role']) && $_SESSION['role'] === 'admin' ? 'Staff Task' : 'My Tasks' ?></a>
                    <a href="index.php?page=salaries" class="sub-nav-item <?= ($page === 'salaries' ? 'font-bold text-[#0056b3]' : '') ?>">» <?= isset($_SESSION['role']) && $_SESSION['role'] === 'admin' ? 'Payroll / Salaries' : 'My Salary' ?></a>
                    <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
                    <a href="index.php?page=staff_new" class="sub-nav-item <?= ($page === 'staff_new' ? 'font-bold text-[#0056b3]' : '') ?>">» New Staff</a>
                    <a href="index.php?page=attendance_settings" class="sub-nav-item <?= ($page === 'attendance_settings' ? 'font-bold text-[#0056b3]' : '') ?>">» Attendance Timings</a>
                    <?php endif; ?>
                </div>
            </div>

            <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
            <a href="index.php?page=expenses" class="nav-item <?= ($page === 'expenses' ? 'active' : '') ?>">
                <i class="fa-solid fa-credit-card"></i> Expenses Ledger
            </a>
            <a href="index.php?page=analytics" class="nav-item <?= ($page === 'analytics' ? 'active' : '') ?>">
                <i class="fa-solid fa-chart-pie"></i> Analytics
            </a>
            <a href="index.php?page=gst_reports" class="nav-item <?= ($page === 'gst_reports' ? 'active' : '') ?>">
                <i class="fa-solid fa-file-invoice-dollar text-rose-600"></i> GST Reports
            </a>
            <a href="index.php?page=tools" class="nav-item <?= ($page === 'tools' ? 'active' : '') ?>">
                <i class="fa-solid fa-screwdriver-wrench"></i> Tools
            </a>
            <a href="index.php?page=upload" class="nav-item <?= ($page === 'upload' ? 'active' : '') ?>">
                <i class="fa-solid fa-cloud-arrow-up"></i> Data Sync <i class="fa-solid fa-chevron-right chevron"></i>
            </a>
            <?php endif; ?>
        </nav>

        <div class="absolute bottom-0 w-full p-4 border-t border-gray-100 bg-gray-50">
            <p class="text-[9px] font-bold text-gray-400 uppercase tracking-widest text-center">Anantapur Book Centre ©
                2026</p>
        </div>
    </aside>

    <script>
        function toggleSidebar() {
            document.getElementById('sidebar').classList.toggle('show');
            document.getElementById('mobile-overlay').classList.toggle('show');
        }

        function toggleSubmenu(id, element) {
            const submenu = document.getElementById(id);
            const chevron = element.querySelector('.chevron');
            
            if (submenu.classList.contains('open')) {
                submenu.classList.remove('open');
                if(chevron) chevron.classList.remove('rotated');
            } else {
                submenu.classList.add('open');
                if(chevron) chevron.classList.add('rotated');
            }
        }
    </script>

    <!-- Header -->
    <header class="header">
        <button onclick="toggleSidebar()" class="md:hidden mr-4 text-[#800000]">
            <i class="fa-solid fa-bars text-2xl"></i>
        </button>
        <div class="search-bar hidden lg:flex">
            <i class="fa-solid fa-magnifying-glass text-[#0056b3]"></i>
            <input type="text" placeholder="Search across modules...">
        </div>

        <div class="flex items-center gap-6 ml-auto">
            <i class="fa-solid fa-gear text-gray-500 cursor-pointer"></i>
            <div class="flex items-center gap-2">
                <div class="w-8 h-8 rounded-full bg-[#800000] text-white flex items-center justify-center font-bold text-xs">
                    <?= strtoupper(substr($_SESSION['username'] ?? 'U', 0, 2)) ?>
                </div>
                <span class="text-xs font-bold text-gray-600"><?= htmlspecialchars($_SESSION['username'] ?? 'User') ?></span>
                <a href="index.php?page=logout" class="ml-3 text-gray-400 hover:text-red-600 transition-colors" title="Logout">
                    <i class="fa-solid fa-right-from-bracket"></i>
                </a>
            </div>
        </div>
    </header>

    <main class="main-content">