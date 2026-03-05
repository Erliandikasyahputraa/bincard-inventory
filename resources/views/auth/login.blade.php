<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login - Bincard & Inventory</title>
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700,900" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-slate-50 dark:bg-slate-950 text-slate-600 dark:text-slate-300 antialiased font-sans min-h-screen flex items-center justify-center p-4 selection:bg-blue-500/30 selection:text-blue-200 transition-colors duration-300 ease-in-out">
    <div class="w-full max-w-md bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl shadow-2xl p-8 sm:p-10 relative overflow-hidden transition-colors duration-300 ease-in-out">
        <!-- Decoration -->
        <div class="absolute top-0 left-0 w-full h-1 bg-gradient-to-r from-[#238636] to-[#58A6FF] transition-colors duration-300 ease-in-out"></div>
        
        <div class="text-center mb-8 transition-colors duration-300 ease-in-out">
            <div class="w-16 h-16 bg-emerald-600 dark:bg-emerald-500 rounded-2xl mx-auto flex items-center justify-center text-white shadow-lg shadow-emerald-600/20 dark:shadow-emerald-500/20 mb-5 relative top-0 hover:-top-1 transition-all">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"></path></svg>
            </div>
            <h1 class="text-2xl font-bold text-slate-900 dark:text-white tracking-tight transition-colors duration-300 ease-in-out">BINCARD PRO</h1>
            <p class="text-slate-500 dark:text-slate-400 text-xs sm:text-sm mt-1 transition-colors duration-300 ease-in-out">Sistem Manajemen Inventaris Digital</p>
        </div>
        
        @if ($errors->any())
            <div class="mb-6 p-4 bg-rose-500/10 border border-rose-500/20 text-rose-500 rounded-xl text-sm font-medium text-center shadow-inner transition-colors duration-300 ease-in-out">
                {{ $errors->first() }}
            </div>
        @endif
        
        <form method="POST" action="{{ route('login.submit') }}" class="space-y-6">
            @csrf
            <div>
                <label for="email" class="block text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2 transition-colors duration-300 ease-in-out">Alamat Email</label>
                <div class="relative">
                    <input type="email" name="email" id="email" value="{{ old('email') }}" required autofocus
                        class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 hover:border-slate-300 dark:hover:border-slate-600 rounded-xl text-slate-800 dark:text-slate-200 placeholder-slate-600 focus:bg-white dark:bg-slate-900 focus:border-blue-500 dark:border-blue-400 focus:ring-1 focus:ring-blue-500 outline-none transition-all pl-11 pr-4 py-3" placeholder="admin@example.com">
                    <svg class="w-5 h-5 absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-500 transition-colors duration-300 ease-in-out" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207"></path></svg>
                </div>
            </div>
            <div>
                <label for="password" class="block text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2 transition-colors duration-300 ease-in-out">Password</label>
                <div class="relative">
                    <input type="password" name="password" id="password" required
                        class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 hover:border-slate-300 dark:hover:border-slate-600 rounded-xl text-slate-800 dark:text-slate-200 placeholder-slate-600 focus:bg-white dark:bg-slate-900 focus:border-blue-500 dark:border-blue-400 focus:ring-1 focus:ring-blue-500 outline-none transition-all pl-11 pr-4 py-3" placeholder="••••••••">
                    <svg class="w-5 h-5 absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-500 transition-colors duration-300 ease-in-out" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                </div>
            </div>
            <div class="flex items-center justify-between pt-1">
                <label class="flex items-center cursor-pointer group">
                    <input type="checkbox" name="remember" class="w-4 h-4 rounded bg-slate-50 dark:bg-slate-950 border-slate-200 dark:border-slate-800 text-blue-500 focus:ring-blue-500 focus:ring-offset-[#161B22] transition-colors duration-300 ease-in-out">
                    <span class="ml-2 text-sm text-slate-500 dark:text-slate-400 group-hover:text-slate-800 dark:text-slate-200 transition-colors">Ingat sesi saya</span>
                </label>
            </div>
            <button type="submit" class="w-full bg-emerald-600 dark:bg-emerald-500 hover:bg-emerald-700 dark:hover:bg-emerald-600 text-white font-bold py-3 px-4 rounded-xl transition-colors shadow-lg shadow-emerald-600/20 dark:shadow-emerald-500/20 flex justify-center items-center mt-2 group">
                Masuk ke Sistem 
                <svg class="w-4 h-4 ml-2 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
            </button>
        </form>
    </div>
</body>
</html>
