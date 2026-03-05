<?php

use App\Livewire\BarangKeluarForm;
use App\Livewire\BarangMasukForm;
use App\Livewire\ImportProdukExcel;
use App\Livewire\LaporanIndex;
use App\Livewire\OpnameIndex;
use App\Livewire\PelangganForm;
use App\Livewire\PelangganIndex;
use App\Livewire\PemasokForm;
use App\Livewire\PemasokIndex;
use App\Livewire\ProdukForm;
use App\Livewire\ProdukIndex;
use App\Livewire\PengaturanPerusahaan;
use App\Livewire\ProfileForm;
use App\Livewire\ScanBarcode;
use App\Livewire\UserManagementIndex;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    if (Auth::check()) {
        return redirect()->route('dashboard');
    }
    return redirect()->route('login');
});

Route::get('/login', function () {
    return view('auth.login');
})->name('login')->middleware('guest');

Route::post('/login', function (\Illuminate\Http\Request $request) {
    $request->validate([
        'email' => 'required|email',
        'password' => 'required',
    ]);
    if (Auth::attempt($request->only('email', 'password'), (bool) $request->boolean('remember'))) {
        $request->session()->regenerate();
        return redirect()->intended(route('dashboard'));
    }
    return back()->withErrors(['email' => 'Email atau password salah.'])->onlyInput('email');
})->name('login.submit')->middleware('guest');

Route::post('/logout', function (\Illuminate\Http\Request $request) {
    Auth::logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();
    return redirect('/');
})->name('logout')->middleware('auth');

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', function () {
        $stats = [
            'total_inventory' => \App\Models\Product::sum('current_stock') ?? 0,
            'low_stock' => \App\Models\Product::whereColumn('current_stock', '<=', 'min_stock')->count(),
            'masuk_24h' => \App\Models\StockTransaction::where('type', 'IN')->where('created_at', '>=', now()->startOfDay())->count(),
            'keluar_24h' => \App\Models\StockTransaction::where('type', 'OUT')->where('created_at', '>=', now()->startOfDay())->count(),
        ];
        
        // Chart Data (Last 6 Months)
        $months = collect(range(5, 0))->map(fn($i) => now()->subMonths($i)->format('Y-m'));
        $masukData = \App\Models\StockTransaction::where('type', 'IN')
            ->where('created_at', '>=', now()->subMonths(5)->startOfMonth())
            ->selectRaw('DATE_FORMAT(created_at, "%Y-%m") as month, SUM(quantity) as total')
            ->groupBy('month')->pluck('total', 'month');
        $keluarData = \App\Models\StockTransaction::where('type', 'OUT')
            ->where('created_at', '>=', now()->subMonths(5)->startOfMonth())
            ->selectRaw('DATE_FORMAT(created_at, "%Y-%m") as month, SUM(quantity) as total')
            ->groupBy('month')->pluck('total', 'month');
            
        $chartData = [
            'labels' => $months->map(fn($m) => \Carbon\Carbon::createFromFormat('Y-m', $m)->translatedFormat('M Y'))->toArray(),
            'masuk' => $months->map(fn($m) => $masukData->get($m, 0))->toArray(),
            'keluar' => $months->map(fn($m) => $keluarData->get($m, 0))->toArray(),
        ];

        $stok_kritis = \App\Models\Product::whereColumn('current_stock', '<=', 'min_stock')->take(5)->get();
        $aktivitas = \App\Models\StockTransaction::with(['product', 'user'])->latest()->take(5)->get();
        return view('dashboard', compact('stats', 'chartData', 'stok_kritis', 'aktivitas'));
    })->name('dashboard');

    Route::get('/produk', ProdukIndex::class)->name('produk.index');
    Route::get('/produk/tambah', ProdukForm::class)->name('produk.tambah');
    Route::get('/produk/import', ImportProdukExcel::class)->name('produk.import');
    Route::get('/produk/template', [\App\Http\Controllers\TemplateProdukController::class, '__invoke'])->name('produk.template');
    Route::get('/produk/{id}/edit', ProdukForm::class)->name('produk.edit');

    Route::get('/pemasok', PemasokIndex::class)->name('pemasok.index');
    Route::get('/pemasok/tambah', PemasokForm::class)->name('pemasok.tambah');
    Route::get('/pemasok/{id}/edit', PemasokForm::class)->name('pemasok.edit');

    Route::get('/pelanggan', PelangganIndex::class)->name('pelanggan.index');
    Route::get('/pelanggan/tambah', PelangganForm::class)->name('pelanggan.tambah');
    Route::get('/pelanggan/{id}/edit', PelangganForm::class)->name('pelanggan.edit');

    Route::get('/barang-masuk', BarangMasukForm::class)->name('barang-masuk.index');
    Route::get('/barang-keluar', BarangKeluarForm::class)->name('barang-keluar.index');
    Route::get('/barang-keluar/pdf/{id}', [\App\Http\Controllers\SuratJalanPdfController::class, '__invoke'])->name('barang-keluar.pdf');

    Route::get('/scan', ScanBarcode::class)->name('scan.index');

    Route::get('/opname', OpnameIndex::class)->name('opname.index');
    Route::get('/opname/export/{id}', [\App\Http\Controllers\LaporanOpnameExportController::class, '__invoke'])->name('opname.export');

    Route::get('/laporan', LaporanIndex::class)->name('laporan.index');
    Route::get('/laporan/export-transaksi', [\App\Http\Controllers\LaporanExportController::class, 'transaksi'])->name('laporan.export-transaksi');
    Route::get('/laporan/export-harian', [\App\Http\Controllers\LaporanExportController::class, 'harian'])->name('laporan.export-harian');
    Route::get('/laporan/pdf', [\App\Http\Controllers\LaporanPdfController::class, 'transaksi'])->name('laporan.pdf');

    // QR Print
    Route::get('/qr-print', [App\Http\Controllers\QRController::class, 'index'])->name('qr.print');
    Route::get('/qr-print/{id}', [App\Http\Controllers\QRController::class, 'single'])->name('qr.print.single');

    Route::middleware('role.admin')->group(function() {
        Route::get('/pengaturan/perusahaan', PengaturanPerusahaan::class)->name('pengaturan.perusahaan');
        Route::get('/pengaturan/pengguna', UserManagementIndex::class)->name('pengaturan.pengguna');
    });
        
    Route::get('/profil', ProfileForm::class)->name('profil');
});
