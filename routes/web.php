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
use App\Livewire\AuditLogIndex;
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
    Route::get('/dashboard', \App\Livewire\Dashboard::class)->name('dashboard');

    Route::get('/produk', ProdukIndex::class)->name('produk.index');
    Route::get('/produk/tambah', ProdukForm::class)->name('produk.tambah');
    Route::get('/produk/import', ImportProdukExcel::class)->name('produk.import');
    Route::get('/produk/template', [\App\Http\Controllers\TemplateProdukController::class, '__invoke'])->name('produk.template');
    Route::get('/produk/{id}/edit', ProdukForm::class)->name('produk.edit');
    Route::get('/produk/{id}/bin-card', \App\Livewire\BinCardDetail::class)->name('produk.bin-card');

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
    Route::get('/laporan/export-stok-barang', [\App\Http\Controllers\LaporanExportController::class, 'stokBarang'])->name('laporan.export-stok-barang');
    Route::get('/laporan/pdf', [\App\Http\Controllers\LaporanPdfController::class, 'transaksi'])->name('laporan.pdf');
    Route::get('/laporan/pdf-harian', [\App\Http\Controllers\LaporanPdfController::class, 'harian'])->name('laporan.pdf-harian');

    // QR Print
    Route::get('/qr-print', [App\Http\Controllers\QRController::class, 'index'])->name('qr.print');
    Route::get('/qr-print-all', [App\Http\Controllers\QRController::class, 'printAll'])->name('qr.print.all');
    Route::get('/qr-print/{id}', [App\Http\Controllers\QRController::class, 'single'])->name('qr.print.single');

    Route::middleware('role.admin')->group(function() {
        Route::get('/pengaturan/perusahaan', PengaturanPerusahaan::class)->name('pengaturan.perusahaan');
        Route::get('/pengaturan/pengguna', UserManagementIndex::class)->name('pengaturan.pengguna');
        Route::get('/pengaturan/audit-log', AuditLogIndex::class)->name('pengaturan.audit-log');
    });
        
    Route::get('/profil', ProfileForm::class)->name('profil');
    Route::get('/panduan', App\Livewire\PanduanSistem::class)->name('panduan');
    Route::get('/ping', function () { return response()->json(['status' => 'ok']); })->name('ping');
});
