<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Aplikasi Praktik Dokter') - {{ \App\Models\Setting::getAppName() }}</title>
    @php
        $appName = \App\Models\Setting::getAppName();
        $appLogoBase64 = \App\Models\Setting::getAppLogoBase64();
    @endphp
    @if($appLogoBase64)
    <link rel="icon" href="{{ $appLogoBase64 }}" type="image/png">
    @endif
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        /* Custom scrollbar */
        ::-webkit-scrollbar { width: 6px; }
        ::-webkit-scrollbar-track { background: #f1f5f9; }
        ::-webkit-scrollbar-thumb { background: #86efac; border-radius: 3px; }
        ::-webkit-scrollbar-thumb:hover { background: #22c55e; }

        /* Sidebar collapsible */
        .sidebar { transition: width 0.3s ease-in-out; }
        .sidebar.sidebar-collapsed { width: 4rem !important; }
        .sidebar .sidebar-label,
        .sidebar .sidebar-user-info,
        .sidebar .sidebar-logo-text,
        .sidebar .sidebar-subtitle {
            transition: opacity 0.2s ease-in-out, width 0.3s ease-in-out;
            white-space: nowrap; overflow: hidden;
        }
        .sidebar.sidebar-collapsed .sidebar-label,
        .sidebar.sidebar-collapsed .sidebar-user-info,
        .sidebar.sidebar-collapsed .sidebar-logo-text,
        .sidebar.sidebar-collapsed .sidebar-subtitle {
            opacity: 0; width: 0;
        }
        .sidebar-toggle-btn { transition: transform 0.3s ease-in-out; }
        .sidebar.sidebar-collapsed .sidebar-toggle-btn { transform: rotate(180deg); }
        .sidebar.sidebar-collapsed .nav-item { justify-content: center; padding-left: 0.5rem; padding-right: 0.5rem; }
        .sidebar.sidebar-collapsed .sidebar-header { justify-content: center; padding-left: 0.5rem; padding-right: 0.5rem; }
        .sidebar.sidebar-collapsed .sidebar-user-section { justify-content: center; padding-left: 0.5rem; padding-right: 0.5rem; }
        .sidebar.sidebar-collapsed .sidebar-logout-text { display: none; }
        .sidebar.sidebar-collapsed .sidebar-logout-btn { justify-content: center; padding-left: 0.5rem; padding-right: 0.5rem; }
        .sidebar.sidebar-collapsed .sidebar-nav { padding-left: 0.5rem; padding-right: 0.5rem; }
        .sidebar.sidebar-collapsed .sidebar-nav a { padding-left: 0.5rem; padding-right: 0.5rem; }
    </style>
    @stack('styles')
</head>
<body class="bg-gray-50 font-sans antialiased">
    @auth
        @php
            $role = Auth::user()->role;
            $menuItems = [
                'Dashboard' => ['route' => 'dashboard', 'icon' => 'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6'],
                'Pendaftaran' => ['route' => 'registration.index', 'icon' => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z'],
                'Antrean' => ['route' => 'queue.index', 'icon' => 'M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0z'],
                'Pemeriksaan' => ['route' => 'examination.index', 'icon' => 'M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z'],
                'Pembayaran' => ['route' => 'payment.index', 'icon' => 'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z'],
                'Apotek' => ['route' => 'pharmacy.index', 'icon' => 'M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z'],
                'Obat' => ['route' => 'medicine.index', 'icon' => 'M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10'],
                'Rekam Medis' => ['route' => 'medical-record.index', 'icon' => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z'],
                'Laporan' => ['route' => 'report.index', 'icon' => 'M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z'],
                'Jasa/Tindakan' => ['route' => 'service-action.index', 'icon' => 'M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z'],
                'Profil Dokter' => ['route' => 'doctor-profile.index', 'icon' => 'M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z'],
                'Pengguna' => ['route' => 'user.index', 'icon' => 'M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0z'],
                'Pengaturan Website' => ['route' => 'setting.index', 'icon' => 'M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.066 2.573c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.573 1.066c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.066-2.573c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z M15 12a3 3 0 11-6 0 3 3 0 016 0z'],
            ];
            $roleMenus = [
                'admin' => ['Dashboard', 'Pendaftaran', 'Antrean', 'Pemeriksaan', 'Pembayaran', 'Apotek', 'Obat', 'Rekam Medis', 'Laporan', 'Jasa/Tindakan', 'Profil Dokter', 'Pengguna', 'Pengaturan Website'],
                'pendaftaran' => ['Dashboard', 'Pendaftaran', 'Antrean'],
                'dokter' => ['Dashboard', 'Antrean', 'Pemeriksaan', 'Rekam Medis'],
                'kasir' => ['Dashboard', 'Pembayaran'],
                'apoteker' => ['Dashboard', 'Apotek', 'Obat'],
            ];
            $allowedMenus = $roleMenus[$role] ?? ['Dashboard'];
        @endphp
        <div class="flex h-screen overflow-hidden">
            <!-- Sidebar Desktop -->
            <aside id="desktopSidebar" class="sidebar w-64 bg-emerald-800 text-white flex-shrink-0 hidden md:flex md:flex-col">
                <!-- Header with toggle -->
                <div class="sidebar-header px-4 py-4 border-b border-emerald-700 flex items-center gap-3">
                    <div class="flex items-center gap-3 flex-1 min-w-0">
                        <div class="w-10 h-10 bg-emerald-500 rounded-lg flex items-center justify-center text-white font-bold text-lg flex-shrink-0 overflow-hidden">
                            @if($appLogoBase64)
                            <img src="{{ $appLogoBase64 }}" alt="Logo" class="w-full h-full object-contain p-0.5">
                            @else
                            <span>{{ substr($appName, 0, 1) }}</span>
                            @endif
                        </div>
                        <div class="min-w-0">
                            <h1 class="sidebar-logo-text text-lg font-bold text-white truncate">{{ $appName }}</h1>
                            <p class="sidebar-subtitle text-xs text-emerald-200 truncate">Praktik Dokter Umum</p>
                        </div>
                    </div>
                    <button id="sidebarCollapseBtn" class="sidebar-toggle-btn text-emerald-200 hover:text-white flex-shrink-0 p-1 rounded-lg hover:bg-emerald-700 transition-colors" title="Ciutkan sidebar">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 19l-7-7 7-7m8 14l-7-7 7-7"/>
                        </svg>
                    </button>
                </div>

                <!-- Navigation -->
                <nav class="sidebar-nav flex-1 px-3 py-4 space-y-1 overflow-y-auto">
                    @foreach($menuItems as $label => $item)
                        @if(in_array($label, $allowedMenus))
                        <a href="{{ route($item['route']) }}" class="nav-item flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-all duration-200 {{ request()->routeIs($item['route']) ? 'bg-emerald-600 text-white shadow-lg shadow-emerald-900/20' : 'text-emerald-100 hover:bg-emerald-700 hover:text-white' }}" title="{{ $label }}">
                            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $item['icon'] }}"/>
                            </svg>
                            <span class="sidebar-label">{{ $label }}</span>
                        </a>
                        @endif
                    @endforeach
                </nav>

                <!-- User & Logout -->
                <div class="sidebar-user-section px-3 py-4 border-t border-emerald-700">
                    <div class="flex items-center gap-3 px-3">
                        <div class="w-9 h-9 bg-emerald-600 rounded-full flex items-center justify-center text-white font-semibold text-sm uppercase flex-shrink-0">
                            {{ substr(Auth::user()->name, 0, 1) }}
                        </div>
                        <div class="sidebar-user-info min-w-0 flex-1">
                            <p class="text-sm font-medium text-white truncate">{{ Auth::user()->name }}</p>
                            <p class="text-xs text-emerald-200 capitalize truncate">{{ Auth::user()->role }}</p>
                        </div>
                    </div>
                    <form method="POST" action="{{ route('logout') }}" class="mt-2 px-0">
                        @csrf
                        <button type="submit" class="sidebar-logout-btn flex items-center gap-3 w-full px-3 py-2.5 rounded-lg text-sm font-medium text-emerald-100 hover:bg-emerald-700 hover:text-white transition-all duration-200" title="Logout">
                            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                            </svg>
                            <span class="sidebar-label sidebar-logout-text">Logout</span>
                        </button>
                    </form>
                </div>
            </aside>

            <!-- Main Content -->
            <div class="flex-1 flex flex-col overflow-hidden">
                <!-- Top Bar (Mobile) -->
                <header class="bg-white border-b border-gray-200 shadow-sm md:hidden">
                    <div class="flex items-center justify-between px-4 py-3">
                        <button id="sidebarToggle" class="text-gray-500 hover:text-gray-700 focus:outline-none">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                            </svg>
                        </button>
                        <h1 class="text-lg font-semibold text-gray-800">{{ $appName }}</h1>
                        <div class="w-6"></div>
                    </div>
                </header>

                <!-- Page Content -->
                <main class="flex-1 overflow-y-auto bg-gray-50">
                    <div class="px-4 sm:px-6 lg:px-8 py-6">
                        @yield('content')
                    </div>
                </main>
            </div>
        </div>

        <!-- Mobile Sidebar Overlay -->
        <div id="sidebarOverlay" class="fixed inset-0 bg-black/50 z-40 hidden md:hidden"></div>
        <aside id="mobileSidebar" class="fixed inset-y-0 left-0 w-64 bg-emerald-800 text-white z-50 transform -translate-x-full transition-transform duration-300 ease-in-out md:hidden">
            <div class="px-6 py-5 border-b border-emerald-700 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-emerald-500 rounded-lg flex items-center justify-center text-white font-bold text-lg flex-shrink-0 overflow-hidden">
                        @if($appLogoBase64)
                        <img src="{{ $appLogoBase64 }}" alt="" class="w-full h-full object-contain p-0.5">
                        @else
                        <span>{{ substr($appName, 0, 1) }}</span>
                        @endif
                    </div>
                    <div>
                        <h1 class="text-lg font-bold text-white">{{ $appName }}</h1>
                        <p class="text-xs text-emerald-200">Praktik Dokter Umum</p>
                    </div>
                </div>
                <button id="closeSidebar" class="text-emerald-200 hover:text-white">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
            <nav class="flex-1 px-3 py-4 space-y-1 overflow-y-auto">
                @foreach($menuItems as $label => $item)
                    @if(in_array($label, $allowedMenus))
                    <a href="{{ route($item['route']) }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-all duration-200 {{ request()->routeIs($item['route']) ? 'bg-emerald-600 text-white' : 'text-emerald-100 hover:bg-emerald-700 hover:text-white' }}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $item['icon'] }}"/>
                        </svg>
                        {{ $label }}
                    </a>
                    @endif
                @endforeach
            </nav>
            <div class="px-3 py-4 border-t border-emerald-700">
                <div class="flex items-center gap-3 px-3 py-2">
                    <div class="w-9 h-9 bg-emerald-600 rounded-full flex items-center justify-center text-white font-semibold text-sm uppercase">{{ substr(Auth::user()->name, 0, 1) }}</div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-white truncate">{{ Auth::user()->name }}</p>
                        <p class="text-xs text-emerald-200 capitalize">{{ Auth::user()->role }}</p>
                    </div>
                </div>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="flex items-center gap-3 w-full px-3 py-2.5 rounded-lg text-sm font-medium text-emerald-100 hover:bg-emerald-700">Logout</button>
                </form>
            </div>
        </aside>

        <script>
            // Mobile sidebar toggle
            document.getElementById('sidebarToggle')?.addEventListener('click', function() {
                document.getElementById('mobileSidebar').classList.remove('-translate-x-full');
                document.getElementById('sidebarOverlay').classList.remove('hidden');
            });
            document.getElementById('closeSidebar')?.addEventListener('click', function() {
                document.getElementById('mobileSidebar').classList.add('-translate-x-full');
                document.getElementById('sidebarOverlay').classList.add('hidden');
            });
            document.getElementById('sidebarOverlay')?.addEventListener('click', function() {
                document.getElementById('mobileSidebar').classList.add('-translate-x-full');
                document.getElementById('sidebarOverlay').classList.add('hidden');
            });

            // Desktop sidebar collapse/expand
            (function() {
                const sidebar = document.getElementById('desktopSidebar');
                const toggleBtn = document.getElementById('sidebarCollapseBtn');
                const sidebarState = localStorage.getItem('sidebar_collapsed');

                if (sidebarState === 'true' && sidebar) {
                    sidebar.classList.add('sidebar-collapsed');
                }

                toggleBtn?.addEventListener('click', function() {
                    if (sidebar) {
                        sidebar.classList.toggle('sidebar-collapsed');
                        localStorage.setItem('sidebar_collapsed', sidebar.classList.contains('sidebar-collapsed'));
                        const isCollapsed = sidebar.classList.contains('sidebar-collapsed');
                        toggleBtn.title = isCollapsed ? 'Perluas sidebar' : 'Ciutkan sidebar';
                    }
                });
            })();
        </script>
    @endauth

    @guest
        @yield('content')
    @endguest

    @stack('scripts')
</body>
</html>