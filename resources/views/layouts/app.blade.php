<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" x-data="{ theme: localStorage.getItem('theme') || 'dark' }" :class="{ 'dark': theme === 'dark' }">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', config('app.name', 'BINGO'))</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700,900" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest"></script>
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        .no-scrollbar::-webkit-scrollbar {
            display: none;
        }
        .no-scrollbar {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }
    </style>
</head>
<body class="bg-slate-50 dark:bg-[#13151A] text-slate-600 dark:text-slate-300 antialiased font-sans selection:bg-blue-500/30 selection:text-blue-200 transition-colors duration-300" x-data="{ sidebarOpen: window.innerWidth >= 1024 }">
    
    <!-- Mobile sidebar backdrop -->
    <div x-show="sidebarOpen" x-transition.opacity class="fixed inset-0 z-40 bg-slate-900/50 dark:bg-[#0A0D14]/80 backdrop-blur-sm lg:hidden transition-colors duration-300 ease-in-out" @click="sidebarOpen = false"></div>

    <div class="flex min-h-screen">
        <!-- Sidebar - Dark Enterprise Style -->
        <aside :class="sidebarOpen ? 'w-64 translate-x-0' : 'w-20 -translate-x-full lg:translate-x-0'" class="bg-white dark:bg-slate-900 transition-colors duration-300 transition-all duration-500 fixed h-full z-50 flex flex-col border-r border-slate-200 dark:border-[#21262D] shadow-xl">
            <div class="p-8 flex items-center space-x-3 h-24 border-b border-slate-200 dark:border-[#21262D] transition-colors duration-300">
                <div class="w-10 h-10 bg-emerald-600 dark:bg-emerald-500 rounded-xl flex items-center justify-center text-white shadow-lg shadow-emerald-600/20 dark:shadow-emerald-500/20 flex-shrink-0 transition-colors duration-300 ease-in-out">
                    <i data-lucide="scan" stroke-width="2.5" class="w-5 h-5"></i>
                </div>
                <span x-show="sidebarOpen" class="text-xl font-bold text-slate-800 dark:text-white tracking-tight whitespace-nowrap flex-1 transition-colors duration-300 ease-in-out">BINGO</span>
                <button x-show="sidebarOpen" @click="sidebarOpen = false" class="lg:hidden p-1.5 text-slate-500 hover:text-slate-900 dark:text-slate-400 dark:hover:text-white transition-colors bg-slate-50 dark:bg-slate-50 dark:bg-slate-950 rounded-lg border border-slate-200 dark:border-slate-200 dark:border-slate-800">
                    <i data-lucide="x" class="w-4 h-4"></i>
                </button>
            </div>

            <nav class="flex-1 px-4 space-y-8 overflow-y-auto no-scrollbar py-6">
                
                @auth
                <!-- MAIN -->
                <div>
                    <ul class="space-y-1">
                        <li>
                            <a href="{{ route('dashboard') }}" class="w-full flex items-center px-4 py-3 rounded-xl transition-all duration-300 {{ request()->routeIs('dashboard') ? 'bg-blue-500/10 text-blue-500 border-l-4 border-blue-500 dark:border-blue-400 font-medium' : 'text-slate-500 dark:text-slate-400 hover:text-slate-900 dark:hover:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-800 border-l-4 border-transparent' }}">
                                <span :class="sidebarOpen ? 'mr-4' : 'mx-auto'"><i data-lucide="layout-dashboard" class="w-5 h-5"></i></span>
                                <span x-show="sidebarOpen" class="text-sm transition-colors duration-300 ease-in-out">Dashboard</span>
                            </a>
                        </li>
                    </ul>
                </div>

                <!-- MASTER DATA -->
                <div>
                    <ul class="space-y-1">
                        <li>
                            <a href="{{ route('produk.index') }}" class="w-full flex items-center px-4 py-3 rounded-xl transition-all duration-300 {{ request()->routeIs('produk.*') ? 'bg-blue-500/10 text-blue-500 border-l-4 border-blue-500 dark:border-blue-400 font-medium' : 'text-slate-500 dark:text-slate-400 hover:text-slate-900 dark:hover:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-800 border-l-4 border-transparent' }}">
                                <span :class="sidebarOpen ? 'mr-4' : 'mx-auto'"><i data-lucide="package" class="w-5 h-5"></i></span>
                                <span x-show="sidebarOpen" class="text-sm transition-colors duration-300 ease-in-out">Data Produk</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('pemasok.index') }}" class="w-full flex items-center px-4 py-3 rounded-xl transition-all duration-300 {{ request()->routeIs('pemasok.*') ? 'bg-blue-500/10 text-blue-500 border-l-4 border-blue-500 dark:border-blue-400 font-medium' : 'text-slate-500 dark:text-slate-400 hover:text-slate-900 dark:hover:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-800 border-l-4 border-transparent' }}">
                                <span :class="sidebarOpen ? 'mr-4' : 'mx-auto'"><i data-lucide="truck" class="w-5 h-5"></i></span>
                                <span x-show="sidebarOpen" class="text-sm transition-colors duration-300 ease-in-out">Supplier</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('pelanggan.index') }}" class="w-full flex items-center px-4 py-3 rounded-xl transition-all duration-300 {{ request()->routeIs('pelanggan.*') ? 'bg-blue-500/10 text-blue-500 border-l-4 border-blue-500 dark:border-blue-400 font-medium' : 'text-slate-500 dark:text-slate-400 hover:text-slate-900 dark:hover:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-800 border-l-4 border-transparent' }}">
                                <span :class="sidebarOpen ? 'mr-4' : 'mx-auto'"><i data-lucide="users" class="w-5 h-5"></i></span>
                                <span x-show="sidebarOpen" class="text-sm transition-colors duration-300 ease-in-out">Customer</span>
                            </a>
                        </li>
                    </ul>
                </div>

                <!-- OPERATIONS -->
                <div>
                    <ul class="space-y-1">
                        <li>
                            <a href="{{ route('barang-masuk.index') }}" class="w-full flex items-center px-4 py-3 rounded-xl transition-all duration-300 {{ request()->routeIs('barang-masuk.*') ? 'bg-blue-500/10 text-blue-500 border-l-4 border-blue-500 dark:border-blue-400 font-medium' : 'text-slate-500 dark:text-slate-400 hover:text-slate-900 dark:hover:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-800 border-l-4 border-transparent' }}">
                                <span :class="sidebarOpen ? 'mr-4' : 'mx-auto'"><i data-lucide="arrow-down-left" class="w-5 h-5"></i></span>
                                <span x-show="sidebarOpen" class="text-sm whitespace-nowrap transition-colors duration-300 ease-in-out">Barang Masuk</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('barang-keluar.index') }}" class="w-full flex items-center px-4 py-3 rounded-xl transition-all duration-300 {{ request()->routeIs('barang-keluar.*') ? 'bg-blue-500/10 text-blue-500 border-l-4 border-blue-500 dark:border-blue-400 font-medium' : 'text-slate-500 dark:text-slate-400 hover:text-slate-900 dark:hover:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-800 border-l-4 border-transparent' }}">
                                <span :class="sidebarOpen ? 'mr-4' : 'mx-auto'"><i data-lucide="arrow-up-right" class="w-5 h-5"></i></span>
                                <span x-show="sidebarOpen" class="text-sm whitespace-nowrap transition-colors duration-300 ease-in-out">Barang Keluar</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('scan.index') }}" class="w-full flex items-center px-4 py-3 rounded-xl transition-all duration-300 {{ request()->routeIs('scan.*') ? 'bg-blue-500/10 text-blue-500 border-l-4 border-blue-500 dark:border-blue-400 font-medium' : 'text-slate-500 dark:text-slate-400 hover:text-slate-900 dark:hover:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-800 border-l-4 border-transparent' }}">
                                <span :class="sidebarOpen ? 'mr-4' : 'mx-auto'"><i data-lucide="scan-line" class="w-5 h-5"></i></span>
                                <span x-show="sidebarOpen" class="text-sm whitespace-nowrap transition-colors duration-300 ease-in-out">Scan Barcode</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('opname.index') }}" class="w-full flex items-center px-4 py-3 rounded-xl transition-all duration-300 {{ request()->routeIs('opname.*') ? 'bg-blue-500/10 text-blue-500 border-l-4 border-blue-500 dark:border-blue-400 font-medium' : 'text-slate-500 dark:text-slate-400 hover:text-slate-900 dark:hover:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-800 border-l-4 border-transparent' }}">
                                <span :class="sidebarOpen ? 'mr-4' : 'mx-auto'"><i data-lucide="check-square" class="w-5 h-5"></i></span>
                                <span x-show="sidebarOpen" class="text-sm whitespace-nowrap transition-colors duration-300 ease-in-out">Stok Opname</span>
                            </a>
                        </li>
                    </ul>
                </div>

                <!-- ANALYTICS & SETTINGS -->
                <div>
                    <ul class="space-y-1">
                        <li>
                            <a href="{{ route('laporan.index') }}" class="w-full flex items-center px-4 py-3 rounded-xl transition-all duration-300 {{ request()->routeIs('laporan.*') ? 'bg-blue-500/10 text-blue-500 border-l-4 border-blue-500 dark:border-blue-400 font-medium' : 'text-slate-500 dark:text-slate-400 hover:text-slate-900 dark:hover:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-800 border-l-4 border-transparent' }}">
                                <span :class="sidebarOpen ? 'mr-4' : 'mx-auto'"><i data-lucide="bar-chart-2" class="w-5 h-5"></i></span>
                                <span x-show="sidebarOpen" class="text-sm transition-colors duration-300 ease-in-out">Laporan Opname</span>
                            </a>
                        </li>
                        <!-- Tambahan Menu Cetak QR -->
                        <li>
                            <a href="/qr-print" class="w-full flex items-center px-4 py-3 rounded-xl transition-all duration-300 {{ request()->is('qr-print*') ? 'bg-blue-500/10 text-blue-500 border-l-4 border-blue-500 dark:border-blue-400 font-medium' : 'text-slate-500 dark:text-slate-400 hover:text-slate-900 dark:hover:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-800 border-l-4 border-transparent' }}">
                                <span :class="sidebarOpen ? 'mr-4' : 'mx-auto'"><i data-lucide="qr-code" class="w-5 h-5"></i></span>
                                <span x-show="sidebarOpen" class="text-sm transition-colors duration-300 ease-in-out">Cetak QR</span>
                            </a>
                        </li>
                        @if(auth()->user()?->hasRole('Admin'))
                        <li>
                            <a href="{{ route('pengaturan.perusahaan') }}" class="w-full flex items-center px-4 py-3 rounded-xl transition-all duration-300 {{ request()->routeIs('pengaturan.perusahaan') ? 'bg-blue-500/10 text-blue-500 border-l-4 border-blue-500 dark:border-blue-400 font-medium' : 'text-slate-500 dark:text-slate-400 hover:text-slate-900 dark:hover:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-800 border-l-4 border-transparent' }}">
                                <span :class="sidebarOpen ? 'mr-4' : 'mx-auto'"><i data-lucide="building-2" class="w-5 h-5"></i></span>
                                <span x-show="sidebarOpen" class="text-sm transition-colors duration-300 ease-in-out">Info Perusahaan</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('pengaturan.pengguna') }}" class="w-full flex items-center px-4 py-3 rounded-xl transition-all duration-300 {{ request()->routeIs('pengaturan.pengguna') ? 'bg-blue-500/10 text-blue-500 border-l-4 border-blue-500 dark:border-blue-400 font-medium' : 'text-slate-500 dark:text-slate-400 hover:text-slate-900 dark:hover:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-800 border-l-4 border-transparent' }}">
                                <span :class="sidebarOpen ? 'mr-4' : 'mx-auto'"><i data-lucide="users-round" class="w-5 h-5"></i></span>
                                <span x-show="sidebarOpen" class="text-sm transition-colors duration-300 ease-in-out">Manajemen Akun</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('pengaturan.audit-log') }}" class="w-full flex items-center px-4 py-3 rounded-xl transition-all duration-300 {{ request()->routeIs('pengaturan.audit-log') ? 'bg-blue-500/10 text-blue-500 border-l-4 border-blue-500 dark:border-blue-400 font-medium' : 'text-slate-500 dark:text-slate-400 hover:text-slate-900 dark:hover:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-800 border-l-4 border-transparent' }}">
                                <span :class="sidebarOpen ? 'mr-4' : 'mx-auto'"><i data-lucide="history" class="w-5 h-5"></i></span>
                                <span x-show="sidebarOpen" class="text-sm transition-colors duration-300 ease-in-out">Log Sistem</span>
                            </a>
                        </li>
                        @endif
                    </ul>
                </div>
                @endauth
            </nav>

            <!-- User Profile (Hidden in smaller widths or when collapsed, but hoverable) -->
            @auth
            <div class="px-6 py-4 border-t border-slate-200 dark:border-[#21262D] relative group transition-colors duration-300">
                <div class="flex items-center p-2 rounded-xl bg-slate-50 dark:bg-slate-50 dark:bg-slate-950 hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors cursor-pointer border border-slate-200 dark:border-slate-200 dark:border-slate-800">
                    <div class="w-9 h-9 flex-shrink-0 rounded-xl bg-white border border-slate-200 dark:border-transparent dark:bg-slate-100 dark:bg-slate-800 flex items-center justify-center text-slate-800 dark:text-slate-300 font-bold text-xs uppercase transition-colors duration-300 ease-in-out">
                        {{ substr(auth()->user()->name, 0, 2) }}
                    </div>
                    <div x-show="sidebarOpen" class="ml-3 overflow-hidden text-left flex-1 transition-colors duration-300 ease-in-out" style="display: none;" x-transition>
                        <p class="text-xs font-bold text-slate-800 dark:text-slate-200 truncate transition-colors duration-300 ease-in-out">{{ auth()->user()->name }}</p>
                        <p class="text-[10px] text-slate-500 truncate tracking-tight transition-colors duration-300 ease-in-out">Online</p>
                    </div>
                </div>
                <!-- Dropdown -->
                <div class="absolute bottom-full left-0 w-full p-4 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all -translate-y-2 group-hover:translate-y-0 z-[60]">
                    <div class="bg-white dark:bg-slate-900 rounded-2xl shadow-xl dark:shadow-2xl border border-slate-200 dark:border-slate-200 dark:border-slate-800 p-2 ring-1 ring-slate-900/5 dark:ring-white/10 flex flex-col gap-1 transition-colors duration-300 ease-in-out">
                        <a href="{{ route('profil') }}" class="w-full px-4 py-2 text-xs font-bold text-slate-700 hover:text-slate-900 dark:text-slate-300 dark:hover:text-white hover:bg-slate-50 dark:hover:bg-slate-800 rounded-xl flex items-center justify-center gap-2 transition-colors">
                            <i data-lucide="user-cog" class="w-4 h-4"></i> Edit Profil
                        </a>
                        <form method="POST" action="{{ route('logout') }}" class="w-full">
                            @csrf
                            <button type="submit" class="w-full px-4 py-2 text-xs font-bold text-rose-600 dark:text-rose-500 hover:bg-rose-50 dark:hover:bg-rose-500/10 rounded-xl flex items-center justify-center gap-2 transition-colors">
                                <i data-lucide="log-out" class="w-4 h-4"></i> Logout
                            </button>
                        </form>
                    </div>
                </div>
            </div>
            @endauth
        </aside>

        <!-- Main Content Area -->
        <main :class="sidebarOpen ? 'lg:ml-64' : 'lg:ml-20'" class="flex-1 transition-all duration-500 w-full min-h-screen flex flex-col pt-0 bg-slate-100 dark:bg-slate-50 dark:bg-slate-950">
            
            <div class="flex-1 p-6 md:p-8 lg:p-10 h-screen overflow-y-auto">
                <!-- Top Header -->
                <header class="flex flex-wrap items-center justify-between mb-10 gap-6 mt-4 lg:mt-0">
                    <div class="flex items-center gap-4 flex-1">
                        <button @click="sidebarOpen = !sidebarOpen" class="p-2 text-slate-500 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-200 dark:border-slate-800 rounded-xl shadow-sm lg:hidden transition-colors">
                            <i data-lucide="menu" class="w-6 h-6"></i>
                        </button>
                        <button @click="sidebarOpen = !sidebarOpen" class="hidden lg:block p-2 text-slate-500 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-200 dark:border-slate-800 rounded-xl shadow-sm">
                            <i data-lucide="menu" class="w-5 h-5"></i>
                        </button>
                        
                        @isset($header)
                            <div class="hidden sm:block ml-2 border-l border-slate-200 dark:border-slate-700 pl-4 transition-colors duration-300 ease-in-out">
                                {{ $header }}
                            </div>
                        @endisset
                    </div>
                
                    <div class="flex items-center space-x-4">
                        <button @click="theme = theme === 'dark' ? 'light' : 'dark'; localStorage.setItem('theme', theme)" class="p-2 text-slate-500 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white hover:bg-slate-200 dark:hover:bg-slate-800 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-200 dark:border-slate-800 rounded-xl transition-colors shadow-sm" aria-label="Toggle Theme">
                            <i data-lucide="sun" x-show="theme === 'dark'" class="w-5 h-5" style="display: none;"></i>
                            <i data-lucide="moon" x-show="theme === 'light'" class="w-5 h-5"></i>
                        </button>
                        <div class="flex flex-col items-end mr-2 text-right transition-colors duration-300 ease-in-out">
                            <span class="text-xs text-slate-600 dark:text-slate-200 transition-colors duration-300 ease-in-out font-medium tracking-wide">Hi, <span class="font-bold border-b border-blue-500/30 text-slate-800 dark:text-white">{{ auth()->user()->name ?? 'Admin' }}</span>!</span>
                        </div>
                    </div>
                </header>

                <!-- Notifications -->
                @if (session('sukses'))
                    <div class="mb-8 p-4 bg-emerald-50 text-emerald-700 rounded-2xl border border-emerald-100 flex items-center gap-3 shadow-sm transition-colors duration-300 ease-in-out">
                        <div class="w-8 h-8 rounded-full bg-emerald-100 flex items-center justify-center shrink-0 transition-colors duration-300 ease-in-out">
                            <i data-lucide="check" class="w-4 h-4 text-emerald-600 transition-colors duration-300 ease-in-out"></i>
                        </div>
                        <p class="font-bold text-sm transition-colors duration-300 ease-in-out">{{ session('sukses') }}</p>
                    </div>
                @endif
                @if (session('error'))
                    <div class="mb-8 p-4 bg-rose-50 text-rose-700 rounded-2xl border border-rose-100 flex items-center gap-3 shadow-sm transition-colors duration-300 ease-in-out">
                        <div class="w-8 h-8 rounded-full bg-rose-100 flex items-center justify-center shrink-0 transition-colors duration-300 ease-in-out">
                            <i data-lucide="x" class="w-4 h-4 text-rose-600 transition-colors duration-300 ease-in-out"></i>
                        </div>
                        <p class="font-bold text-sm transition-colors duration-300 ease-in-out">{{ session('error') }}</p>
                    </div>
                @endif

                <!-- Page Content -->
                @isset($slot)
                    {{ $slot }}
                @else
                    @yield('content')
                @endisset
            </div>
            
        </main>
    </div>

    @livewireScripts
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            lucide.createIcons();
        });
        document.addEventListener('livewire:navigated', function() {
            lucide.createIcons();
            // Solusi Bug: Wire:navigate mencuci <html class="dark"> dari server.
            // Kita terapkan ulang dark class jika user memang di dark mode.
            if (localStorage.getItem('theme') === 'dark') {
                document.documentElement.classList.add('dark');
            } else {
                document.documentElement.classList.remove('dark');
            }
        });
    </script>
    @stack('scripts')
    <script>
        document.addEventListener('livewire:initialized', () => {
            Livewire.hook('morph.updated', ({ el, component }) => {
                lucide.createIcons();
            });

            window.addEventListener('transaksi-sukses', (event) => {
                // Livewire v3 wraps dispatches, but if sent as object parameter it's often directly accessible
                // or under detail[0]. Let's safely extract it.
                let detail = event.detail[0] || event.detail;
                let msg = detail.message || 'Transaksi Berhasil';
                let sjId = detail.sjId ?? null;
                
                let isDark = document.documentElement.classList.contains('dark');
                Swal.fire({
                    title: 'Berhasil!',
                    text: msg,
                    icon: 'success',
                    showCancelButton: sjId ? true : false,
                    confirmButtonText: sjId ? 'Download Surat Jalan' : 'OK',
                    cancelButtonText: 'Tutup',
                    background: isDark ? '#161B22' : '#ffffff',
                    color: isDark ? '#c9d1d9' : '#1e293b',
                    confirmButtonColor: '#1F6FEB',
                    cancelButtonColor: isDark ? '#30363D' : '#e2e8f0'
                }).then((result) => {
                    if (result.isConfirmed && sjId) {
                        window.open('/barang-keluar/pdf/' + sjId, '_blank');
                    }
                });
            });

            window.addEventListener('transaksi-gagal', (event) => {
                let detail = event.detail[0] || event.detail;
                let msg = detail.message || 'Terjadi kesalahan sistem.';
                let isDark = document.documentElement.classList.contains('dark');

                Swal.fire({
                    title: 'Gagal!',
                    text: msg,
                    icon: 'error',
                    background: isDark ? '#161B22' : '#ffffff',
                    color: isDark ? '#c9d1d9' : '#1e293b',
                    confirmButtonColor: '#1F6FEB'
                });
            });
        });
    </script>
</body>
</html>
