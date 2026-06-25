<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Dashboard') - Scholartzy</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap');
        body { font-family: 'Inter', sans-serif; }
        
        /* Custom Scrollbar */
        ::-webkit-scrollbar { width: 6px; }
        ::-webkit-scrollbar-track { background: #0f172a; }
        ::-webkit-scrollbar-thumb { background: #334155; border-radius: 3px; }
        ::-webkit-scrollbar-thumb:hover { background: #475569; }
        
        /* Sidebar Link Styles */
        .nav-item {
            @apply flex items-center gap-3 px-4 py-3 rounded-xl text-gray-400 transition-all duration-200 relative overflow-hidden;
        }
        .nav-item:hover {
            @apply text-white bg-white/5;
        }
        .nav-item.active {
            @apply text-white bg-blue-600/15;
        }
        .nav-item.active::before {
            content: '';
            @apply absolute left-0 top-1/2 -translate-y-1/2 w-1 h-8 bg-blue-500 rounded-r-full;
        }
        .nav-item.active i {
            @apply text-blue-400;
        }
        
        /* Glass Effect */
        .glass { background: rgba(15, 23, 42, 0.8); backdrop-filter: blur(12px); }
    </style>
    @stack('styles')
</head>
<body class="bg-slate-950 text-white" x-data="{ sidebarOpen: false }">

    <!-- Mobile Overlay -->
    <div x-show="sidebarOpen" 
         x-transition:enter="transition-opacity ease-linear duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition-opacity ease-linear duration-300"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         @click="sidebarOpen = false" 
         class="fixed inset-0 bg-black/60 backdrop-blur-sm z-40 lg:hidden"></div>

    <!-- Sidebar -->
    <aside class="fixed left-0 top-0 h-full w-72 bg-slate-900 border-r border-slate-800/50 z-50 transform transition-transform duration-300 ease-in-out lg:translate-x-0 flex flex-col"
           :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'">
        
        <!-- Logo -->
        <div class="h-20 flex items-center gap-3 px-6 border-b border-slate-800/50 flex-shrink-0">
            <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-purple-600 rounded-xl flex items-center justify-center shadow-lg shadow-blue-500/20">
                <i class="fas fa-graduation-cap text-lg text-white"></i>
            </div>
            <div>
                <span class="text-xl font-bold tracking-tight">Scholartzy</span>
                <p class="text-[10px] text-gray-500 -mt-1 uppercase tracking-wider">SPK Beasiswa</p>
            </div>
        </div>

        <!-- User Info -->
        <div class="px-5 py-4 border-b border-slate-800/50 flex-shrink-0">
            <div class="flex items-center gap-3 p-3 rounded-xl bg-slate-800/50 border border-slate-700/50">
                <div class="w-11 h-11 bg-gradient-to-br from-blue-500 to-purple-600 rounded-full flex items-center justify-center flex-shrink-0 shadow-lg">
                    <span class="text-sm font-bold text-white">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</span>
                </div>
                <div class="overflow-hidden min-w-0">
                    <p class="text-sm font-semibold text-white truncate">{{ auth()->user()->name }}</p>
                    <div class="flex items-center gap-1.5 mt-0.5">
                        <span class="w-1.5 h-1.5 rounded-full {{ auth()->user()->user_status === 'active' ? 'bg-green-400' : 'bg-gray-400' }}"></span>
                        <p class="text-xs text-gray-400 capitalize">{{ auth()->user()->user_role }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Navigation -->
        <nav class="flex-1 overflow-y-auto py-4 px-3 space-y-1">
            @php $role = auth()->user()->user_role; @endphp

            <p class="px-3 text-[10px] font-bold text-gray-600 uppercase tracking-widest mb-3">Menu</p>

            @if($role === 'student')
                <a href="{{ route('student.dashboard') }}" 
                   class="group flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-all duration-200
                   {{ request()->routeIs('student.dashboard') 
                      ? 'bg-blue-600/15 text-white' 
                      : 'text-gray-400 hover:text-white hover:bg-white/5' }}">
                    <span class="w-8 h-8 rounded-lg flex items-center justify-center flex-shrink-0 transition-colors
                        {{ request()->routeIs('student.dashboard') ? 'bg-blue-500/20 text-blue-400' : 'bg-slate-800 text-gray-500 group-hover:bg-slate-700' }}">
                        <i class="fas fa-home text-xs"></i>
                    </span>
                    <span class="truncate">Dashboard</span>
                    @if(request()->routeIs('student.dashboard'))
                        <span class="ml-auto w-1.5 h-1.5 rounded-full bg-blue-400"></span>
                    @endif
                </a>

                <a href="{{ route('student.profile') }}" 
                   class="group flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-all duration-200
                   {{ request()->routeIs('student.profile') 
                      ? 'bg-blue-600/15 text-white' 
                      : 'text-gray-400 hover:text-white hover:bg-white/5' }}">
                    <span class="w-8 h-8 rounded-lg flex items-center justify-center flex-shrink-0 transition-colors
                        {{ request()->routeIs('student.profile') ? 'bg-blue-500/20 text-blue-400' : 'bg-slate-800 text-gray-500 group-hover:bg-slate-700' }}">
                        <i class="fas fa-user text-xs"></i>
                    </span>
                    <span class="truncate">Profil Saya</span>
                    @if(request()->routeIs('student.profile'))
                        <span class="ml-auto w-1.5 h-1.5 rounded-full bg-blue-400"></span>
                    @endif
                </a>

                <a href="{{ route('student.apply') }}" 
                   class="group flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-all duration-200
                   {{ request()->routeIs('student.apply') 
                      ? 'bg-blue-600/15 text-white' 
                      : 'text-gray-400 hover:text-white hover:bg-white/5' }}">
                    <span class="w-8 h-8 rounded-lg flex items-center justify-center flex-shrink-0 transition-colors
                        {{ request()->routeIs('student.apply') ? 'bg-blue-500/20 text-blue-400' : 'bg-slate-800 text-gray-500 group-hover:bg-slate-700' }}">
                        <i class="fas fa-file-signature text-xs"></i>
                    </span>
                    <span class="truncate">Ajukan Beasiswa</span>
                    @if(request()->routeIs('student.apply'))
                        <span class="ml-auto w-1.5 h-1.5 rounded-full bg-blue-400"></span>
                    @endif
                </a>

                <a href="{{ route('student.status') }}" 
                   class="group flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-all duration-200
                   {{ request()->routeIs('student.status') 
                      ? 'bg-blue-600/15 text-white' 
                      : 'text-gray-400 hover:text-white hover:bg-white/5' }}">
                    <span class="w-8 h-8 rounded-lg flex items-center justify-center flex-shrink-0 transition-colors
                        {{ request()->routeIs('student.status') ? 'bg-blue-500/20 text-blue-400' : 'bg-slate-800 text-gray-500 group-hover:bg-slate-700' }}">
                        <i class="fas fa-clipboard-list text-xs"></i>
                    </span>
                    <span class="truncate">Status Pengajuan</span>
                    @if(request()->routeIs('student.status'))
                        <span class="ml-auto w-1.5 h-1.5 rounded-full bg-blue-400"></span>
                    @endif
                </a>

            @elseif($role === 'staff')
                <a href="{{ route('staff.dashboard') }}" 
                   class="group flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-all duration-200
                   {{ request()->routeIs('staff.dashboard') 
                      ? 'bg-yellow-600/15 text-white' 
                      : 'text-gray-400 hover:text-white hover:bg-white/5' }}">
                    <span class="w-8 h-8 rounded-lg flex items-center justify-center flex-shrink-0 transition-colors
                        {{ request()->routeIs('staff.dashboard') ? 'bg-yellow-500/20 text-yellow-400' : 'bg-slate-800 text-gray-500 group-hover:bg-slate-700' }}">
                        <i class="fas fa-home text-xs"></i>
                    </span>
                    <span class="truncate">Dashboard</span>
                    @if(request()->routeIs('staff.dashboard'))
                        <span class="ml-auto w-1.5 h-1.5 rounded-full bg-yellow-400"></span>
                    @endif
                </a>

                <a href="{{ route('staff.verification') }}" 
                   class="group flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-all duration-200
                   {{ request()->routeIs('staff.verification') 
                      ? 'bg-yellow-600/15 text-white' 
                      : 'text-gray-400 hover:text-white hover:bg-white/5' }}">
                    <span class="w-8 h-8 rounded-lg flex items-center justify-center flex-shrink-0 transition-colors
                        {{ request()->routeIs('staff.verification') ? 'bg-yellow-500/20 text-yellow-400' : 'bg-slate-800 text-gray-500 group-hover:bg-slate-700' }}">
                        <i class="fas fa-check-double text-xs"></i>
                    </span>
                    <span class="truncate">Verifikasi</span>
                    @if(request()->routeIs('staff.verification'))
                        <span class="ml-auto w-1.5 h-1.5 rounded-full bg-yellow-400"></span>
                    @endif
                </a>

                <a href="{{ route('staff.assessment') }}" 
                   class="group flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-all duration-200
                   {{ request()->routeIs('staff.assessment') 
                      ? 'bg-yellow-600/15 text-white' 
                      : 'text-gray-400 hover:text-white hover:bg-white/5' }}">
                    <span class="w-8 h-8 rounded-lg flex items-center justify-center flex-shrink-0 transition-colors
                        {{ request()->routeIs('staff.assessment') ? 'bg-yellow-500/20 text-yellow-400' : 'bg-slate-800 text-gray-500 group-hover:bg-slate-700' }}">
                        <i class="fas fa-calculator text-xs"></i>
                    </span>
                    <span class="truncate">Assessment</span>
                    @if(request()->routeIs('staff.assessment'))
                        <span class="ml-auto w-1.5 h-1.5 rounded-full bg-yellow-400"></span>
                    @endif
                </a>

                <a href="{{ route('staff.results') }}" 
                   class="group flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-all duration-200
                   {{ request()->routeIs('staff.results') 
                      ? 'bg-yellow-600/15 text-white' 
                      : 'text-gray-400 hover:text-white hover:bg-white/5' }}">
                    <span class="w-8 h-8 rounded-lg flex items-center justify-center flex-shrink-0 transition-colors
                        {{ request()->routeIs('staff.results') ? 'bg-yellow-500/20 text-yellow-400' : 'bg-slate-800 text-gray-500 group-hover:bg-slate-700' }}">
                        <i class="fas fa-chart-bar text-xs"></i>
                    </span>
                    <span class="truncate">Hasil Penilaian</span>
                    @if(request()->routeIs('staff.results'))
                        <span class="ml-auto w-1.5 h-1.5 rounded-full bg-yellow-400"></span>
                    @endif
                </a>

            @elseif($role === 'admin')
                <a href="{{ route('admin.dashboard') }}" 
                   class="group flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-all duration-200
                   {{ request()->routeIs('admin.dashboard') 
                      ? 'bg-red-600/15 text-white' 
                      : 'text-gray-400 hover:text-white hover:bg-white/5' }}">
                    <span class="w-8 h-8 rounded-lg flex items-center justify-center flex-shrink-0 transition-colors
                        {{ request()->routeIs('admin.dashboard') ? 'bg-red-500/20 text-red-400' : 'bg-slate-800 text-gray-500 group-hover:bg-slate-700' }}">
                        <i class="fas fa-home text-xs"></i>
                    </span>
                    <span class="truncate">Dashboard</span>
                    @if(request()->routeIs('admin.dashboard'))
                        <span class="ml-auto w-1.5 h-1.5 rounded-full bg-red-400"></span>
                    @endif
                </a>

                <p class="px-3 text-[10px] font-bold text-gray-600 uppercase tracking-widest mt-4 mb-2">Manajemen</p>

                <a href="{{ route('admin.users') }}" 
                   class="group flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-all duration-200
                   {{ request()->routeIs('admin.users') 
                      ? 'bg-red-600/15 text-white' 
                      : 'text-gray-400 hover:text-white hover:bg-white/5' }}">
                    <span class="w-8 h-8 rounded-lg flex items-center justify-center flex-shrink-0 transition-colors
                        {{ request()->routeIs('admin.users') ? 'bg-red-500/20 text-red-400' : 'bg-slate-800 text-gray-500 group-hover:bg-slate-700' }}">
                        <i class="fas fa-users-cog text-xs"></i>
                    </span>
                    <span class="truncate">Kelola Pengguna</span>
                    @if(request()->routeIs('admin.users'))
                        <span class="ml-auto w-1.5 h-1.5 rounded-full bg-red-400"></span>
                    @endif
                </a>

                <a href="{{ route('admin.students') }}" 
                   class="group flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-all duration-200
                   {{ request()->routeIs('admin.students') 
                      ? 'bg-red-600/15 text-white' 
                      : 'text-gray-400 hover:text-white hover:bg-white/5' }}">
                    <span class="w-8 h-8 rounded-lg flex items-center justify-center flex-shrink-0 transition-colors
                        {{ request()->routeIs('admin.students') ? 'bg-red-500/20 text-red-400' : 'bg-slate-800 text-gray-500 group-hover:bg-slate-700' }}">
                        <i class="fas fa-user-graduate text-xs"></i>
                    </span>
                    <span class="truncate">Data Mahasiswa</span>
                    @if(request()->routeIs('admin.students'))
                        <span class="ml-auto w-1.5 h-1.5 rounded-full bg-red-400"></span>
                    @endif
                </a>

                <a href="{{ route('admin.applications') }}" 
                   class="group flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-all duration-200
                   {{ request()->routeIs('admin.applications') 
                      ? 'bg-red-600/15 text-white' 
                      : 'text-gray-400 hover:text-white hover:bg-white/5' }}">
                    <span class="w-8 h-8 rounded-lg flex items-center justify-center flex-shrink-0 transition-colors
                        {{ request()->routeIs('admin.applications') ? 'bg-red-500/20 text-red-400' : 'bg-slate-800 text-gray-500 group-hover:bg-slate-700' }}">
                        <i class="fas fa-file-alt text-xs"></i>
                    </span>
                    <span class="truncate">Pengajuan Beasiswa</span>
                    @if(request()->routeIs('admin.applications'))
                        <span class="ml-auto w-1.5 h-1.5 rounded-full bg-red-400"></span>
                    @endif
                </a>

                <a href="{{ route('admin.assessment-results') }}" 
                   class="group flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-all duration-200
                   {{ request()->routeIs('admin.assessment-results') 
                      ? 'bg-red-600/15 text-white' 
                      : 'text-gray-400 hover:text-white hover:bg-white/5' }}">
                    <span class="w-8 h-8 rounded-lg flex items-center justify-center flex-shrink-0 transition-colors
                        {{ request()->routeIs('admin.assessment-results') ? 'bg-red-500/20 text-red-400' : 'bg-slate-800 text-gray-500 group-hover:bg-slate-700' }}">
                        <i class="fas fa-chart-pie text-xs"></i>
                    </span>
                    <span class="truncate">Hasil Assessment</span>
                    @if(request()->routeIs('admin.assessment-results'))
                        <span class="ml-auto w-1.5 h-1.5 rounded-full bg-red-400"></span>
                    @endif
                </a>
            @endif
        </nav>
        <!-- Logout -->
        <div class="p-4 border-t border-slate-800/50 flex-shrink-0">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="w-full flex items-center gap-3 px-4 py-3 rounded-xl text-red-400 hover:text-red-300 hover:bg-red-500/10 transition-all duration-200 group">
                    <div class="w-9 h-9 rounded-lg bg-red-500/10 flex items-center justify-center group-hover:bg-red-500/20 transition">
                        <i class="fas fa-sign-out-alt text-sm"></i>
                    </div>
                    <div class="text-left">
                        <span class="text-sm font-medium">Logout</span>
                        <p class="text-[10px] text-gray-500">Keluar dari akun</p>
                    </div>
                </button>
            </form>
        </div>
    </aside>
    
    <!-- Main Content -->
    <div class="lg:ml-72 min-h-screen flex flex-col">
        <!-- Top Navbar -->
        <header class="h-16 glass border-b border-slate-800/50 flex items-center justify-between px-4 lg:px-8 sticky top-0 z-30">
            <div class="flex items-center gap-3">
                <button @click="sidebarOpen = true" class="lg:hidden text-gray-400 hover:text-white p-2 -ml-2 rounded-lg hover:bg-white/5 transition">
                    <i class="fas fa-bars text-lg"></i>
                </button>
                <h2 class="text-base lg:text-lg font-semibold truncate max-w-[150px] sm:max-w-none">@yield('title')</h2>
            </div>
            
            <div class="flex items-center gap-2 lg:gap-4">
                <div class="hidden sm:flex items-center gap-2 px-3 py-1.5 bg-slate-800/50 rounded-lg border border-slate-700/50">
                    <i class="fas fa-calendar-alt text-gray-500 text-xs"></i>
                    <span class="text-xs text-gray-400">{{ now()->format('d M Y') }}</span>
                </div>
                <div class="flex items-center gap-2 lg:gap-3 pl-2 lg:pl-4 border-l border-slate-800">
                    <div class="text-right hidden md:block">
                        <p class="text-sm font-semibold leading-tight">{{ auth()->user()->name }}</p>
                        <p class="text-[10px] text-gray-500">{{ auth()->user()->email }}</p>
                    </div>
                    <div class="w-8 h-8 lg:w-9 lg:h-9 bg-gradient-to-br from-blue-500 to-purple-600 rounded-full flex items-center justify-center shadow-lg flex-shrink-0">
                        <span class="text-xs lg:text-sm font-bold">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</span>
                    </div>
                </div>
            </div>
        </header>

        <!-- Page Content -->
        <main class="flex-1 p-3 sm:p-4 lg:p-8">
            @if(session('success'))
                <div class="bg-green-500/10 border border-green-500/20 text-green-400 px-4 py-3 rounded-xl mb-4 lg:mb-6 flex items-center gap-3">
                    <i class="fas fa-check-circle flex-shrink-0"></i>
                    <span class="text-sm font-medium">{{ session('success') }}</span>
                </div>
            @endif

            @if(session('error'))
                <div class="bg-red-500/10 border border-red-500/20 text-red-400 px-4 py-3 rounded-xl mb-4 lg:mb-6 flex items-center gap-3">
                    <i class="fas fa-exclamation-circle flex-shrink-0"></i>
                    <span class="text-sm font-medium">{{ session('error') }}</span>
                </div>
            @endif

            @yield('content')
        </main>
    </div>

    @stack('scripts')
</body>
</html>