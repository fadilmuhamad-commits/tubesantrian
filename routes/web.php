<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\C_Admin;
use App\Http\Controllers\C_Ajax;
use App\Http\Controllers\C_Auth;
use App\Http\Controllers\C_Category;
use App\Http\Controllers\C_Client;
use App\Http\Controllers\C_Group;
use App\Http\Controllers\C_Loket;
use App\Http\Controllers\C_Panggil;
use App\Http\Controllers\C_Pengunjung;
use App\Http\Controllers\C_User;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// CLIENT PAGE
Route::redirect('/', '/booking');
Route::prefix('booking')->group(function () {
  Route::get('/', [C_Client::class, 'booking'])->name('booking');
  Route::get('/nomor-induk', [C_Client::class, 'tanyaInduk'])->name('nomor-induk');
  Route::post('/induk', [C_Client::class, 'ambilBookingByRegis'])->name('booking.induk');
  Route::post('/anggota', [C_Client::class, 'ambilBookingByAnggota'])->name('booking.anggota');
  Route::post('/store', [C_Client::class, 'submitBooking'])->name('booking.store');
  Route::get('/booking-success', [C_Client::class, 'bookingSuccess'])->name('booking.success');
});

// AJAX
Route::get('/show/ajax/{group}', [C_Ajax::class, 'show'])->name('ajax.show');
Route::get('/cetak/ajax', [C_Ajax::class, 'cetak'])->name('ajax.cetak');
Route::get('/group/ajax', [C_Ajax::class, 'group'])->name('ajax.group');
Route::get('/queue/ajax', [C_Ajax::class, 'queue'])->name('ajax.queue');
// Route::get('/antrian/ajax/{group}', [C_Ajax::class, 'antrian'])->name('ajax.antrian');
Route::delete('/antrian/ajax/{queue}/destroy', [C_Ajax::class, 'deleteAntrian'])->name('ajax.antrian.delete');

// ACCOUNT
Route::prefix('/admin')->group(function () {
  Route::get('/login', [C_Auth::class, 'index'])->name('login');
  Route::post('/login/validate', [C_Auth::class, 'login'])->name('login.validate');
});


// AUTH ROUTE
Route::middleware('auth')->group(function () {
  // CLIENT
  Route::prefix('/client')->group(function () {
    Route::get('/pdf', [C_Client::class, 'generatePdf'])->name('pdf');
    Route::get('/show/{group}', [C_Client::class, 'show'])->name('show');
    Route::get('/cetak', [C_Client::class, 'cetak'])->name('cetak');
    Route::post('/cetak/{counter_category}', [C_Client::class, 'tiket'])->name('cetak.store');
    Route::post('/cetak-register/{counter_category}', [C_Client::class, 'tiketForm'])->name('cetak.register');
    Route::post('/cetak-nomor-booking', [C_Client::class, 'cetakByNomorBooking'])->name('cetak.nomor-booking');
    Route::post('/cetak-nomor-regis/{counter_category}', [C_Client::class, 'cetakWithNomorRegis'])->name('cetak.nomor-regis');
    Route::post('/cetak-nomor-anggota/{counter_category}', [C_Client::class, 'cetakWithNomorAnggota'])->name('cetak.nomor-anggota');
    Route::view('/pilih-booking', 'pages.cetak-antrian.pilih-booking')->name('pilih-booking');
    Route::post('/nomor-booking/{counter_category}', [C_Client::class, 'tanyaOpsi'])->name('tanya-opsi');
    Route::any('/submit-booking/{counter_category}', [C_Client::class, 'tanyaBooking'])->name('tanya-booking');
    Route::get('/wait', [C_Client::class, 'wait'])->name('wait');
    Route::get('/success', [C_Client::class, 'success'])->name('success');
    Route::get('/check-member', [C_Client::class, 'checkMember'])->name('check-member');
  });

  // REDIRECT
  Route::redirect('/admin', '/admin/dashboard');
  Route::redirect('/client', '/client/cetak');

  // LOGOUT
  Route::get('/logout', [C_Auth::class, 'logout'])->name('logout');

  Route::prefix('/admin')->group(function () {
    // DASHBOARD
    Route::get('/dashboard', [C_Admin::class, 'dashboard'])->name('dashboard');

    // EDIT ACCOUNT
    Route::get('/account', [C_User::class, 'editAccount'])->name('account.edit');
    Route::put('/account/{user}/update', [C_User::class, 'updateAccount'])->name('account.update');

    // GET TICKET (DASHBOARD)
    Route::middleware('permission:get_ticket')->group(function () {
      Route::get('/ambil-antrian', [C_Admin::class, 'ambilAntrian'])->name('ambil-antrian');
      Route::post('/ambil-antrian/{counter}/store', [C_Admin::class, 'store'])->name('ambil-antrian.store');
      Route::get('/ambil-antrian/pdf', [C_Client::class, 'generatePdf'])->name('ambil-antrian.pdf');
    });

    // ANTRIAN
    Route::get('/antrian', [C_Pengunjung::class, 'dataAntrian'])->name('antrian')->middleware('permission:view_queue');
    Route::middleware('permission:manage_queue')->group(function () {
      Route::delete('/antrian/{ticket}/destroy', [C_Pengunjung::class, 'destroy'])->name('tiket.destroy');
      Route::any('/antrian/selected', [C_Pengunjung::class, 'destroySelected'])->name('antrian.destroy.selected');
      Route::get('/antrian/{ticket}', [C_Pengunjung::class, 'dataAntrianEdit'])->name('antrian-edit');
      Route::put('/antrian/{ticket}/update', [C_Pengunjung::class, 'edit'])->name('antrian.update');
      Route::put('/antrian/{ticket}/note', [C_Pengunjung::class, 'noteStore'])->name('antrian.note.store');
    });

    // BOOKING
    Route::get('/data-booking', [C_Pengunjung::class, 'dataBooking'])->name('data-booking')->middleware('permission:view_booking');
    Route::middleware('permission:manage_booking')->group(function () {
      Route::delete('/data-booking/{ticket}/destroy', [C_Pengunjung::class, 'destroyBooking'])->name('data-booking.destroy');
      Route::any('/data-booking/selected', [C_Pengunjung::class, 'destroyBookingSelected'])->name('data-booking.destroy.selected');
    });

    // PENGUNJUNG
    Route::middleware('permission:view_history')->group(function () {
      Route::get('/riwayat-kunjungan', [C_Pengunjung::class, 'riwayatKunjungan'])->name('riwayat-kunjungan');
      Route::get('/pengunjung', [C_Pengunjung::class, 'dataPengunjung'])->name('pengunjung');
    });

    // CS ONLY
    Route::middleware('permission:call_queue')->group(function () {
      Route::get('/panggil-antrian', [C_Admin::class, 'panggilAntrian'])->name('panggil-antrian');
      Route::post('/panggil-antrian/{ticket}/identitas', [C_Admin::class, 'identitasAntrian'])->name('identitas-antrian');
      Route::post('/panggil-antrian/update-queue', [C_Admin::class, 'updateQueue'])->name('update-queue');
      Route::put('/panggil-antrian/{ticket}/submit', [C_Admin::class, 'submitAntrian'])->name('submit-antrian');
      Route::put('/panggil-antrian/{ticket}/note', [C_Admin::class, 'noteAntrian'])->name('note-antrian');
      Route::put('/panggil-antrian/{ticket}/next', [C_Admin::class, 'nextAntrian'])->name('next-antrian');
      Route::put('/panggil-antrian/{ticket}/call', [C_Admin::class, 'callAntrian'])->name('call-antrian');
      Route::post('/panggil-antrian/{ticket}/recall', [C_Admin::class, 'recallAntrian'])->name('recall-antrian');
      Route::put('/panggil-antrian/{counter}/status', [C_Admin::class, 'updateStatus'])->name('switch-antrian');
    });

    // USERS
    Route::get('/users', [C_User::class, 'users'])->name('users')->middleware('permission:view_user');
    Route::middleware('permission:manage_user')->group(function () {
      Route::get('/tambah-user', [C_User::class, 'tambahUser'])->name('tambah-user');
      Route::get('/users/{user}', [C_User::class, 'editUser'])->name('user.edit');
      Route::delete('/users/{user}/destroy', [C_User::class, 'destroy'])->name('user.destroy');
      Route::any('/users/selected', [C_User::class, 'destroySelected'])->name('users.destroy.selected');
      Route::post('/users/store', [C_User::class, 'store'])->name('users.store');
      Route::put('/users/{user}/update', [C_User::class, 'update'])->name('user.update');
    });

    // ROLES
    Route::get('/roles', [C_Admin::class, 'roles'])->name('roles')->middleware('permission:view_role');
    Route::middleware('permission:manage_role')->group(function () {
      Route::get('/roles/add', [C_Admin::class, 'rolesAdd'])->name('roles.add');
      Route::get('/roles/{role}', [C_Admin::class, 'rolesEdit'])->name('roles.edit');
      Route::post('/roles/store', [C_Admin::class, 'rolesStore'])->name('roles.store');
      Route::put('/roles/{role}/update', [C_Admin::class, 'rolesUpdate'])->name('roles.update');
      Route::delete('/roles/{role}/destroy', [C_Admin::class, 'rolesDestroy'])->name('roles.destroy');
      Route::any('/roles/selected', [C_Admin::class, 'rolesDestroySelected'])->name('roles.destroy.selected');
    });

    // LOKET
    Route::get('/loket', [C_Loket::class, 'loket'])->name('loket')->middleware('permission:view_counter');
    Route::middleware('permission:manage_counter')->group(function () {
      Route::get('/loket-tambah', [C_Loket::class, 'loketTambah'])->name('loket-tambah');
      Route::get('/loket/{counter}', [C_Loket::class, 'loketEdit'])->name('loket-edit');
      Route::delete('/loket/{counter}/destroy', [C_Loket::class, 'destroy'])->name('loket.destroy');
      Route::any('/loket/selected', [C_Loket::class, 'destroySelected'])->name('loket.destroy.selected');
      Route::post('/loket/store', [C_Loket::class, 'store'])->name('loket.store');
      Route::put('/loket/{counter}/update', [C_Loket::class, 'update'])->name('loket.update');
      Route::put('/loket/{counter}/status', [C_Loket::class, 'updateStatus'])->name('loket.switch');
    });

    // GROUP
    Route::get('/group', [C_Group::class, 'index'])->name('group')->middleware('permission:view_group');
    Route::middleware('permission:manage_group')->group(function () {
      Route::get('/group/add', [C_Group::class, 'add'])->name('group.add');
      Route::post('/group/store', [C_Group::class, 'store'])->name('group.store');
      Route::put('/group/{group}/update', [C_Group::class, 'update'])->name('group.update');
      Route::delete('/group/{group}/destroy', [C_Group::class, 'destroy'])->name('group.destroy');
      Route::any('/group/selected', [C_Group::class, 'destroySelected'])->name('group.destroy.selected');
    });

    // CATEGORY
    Route::get('/category', [C_Category::class, 'index'])->name('category')->middleware('permission:view_category');
    Route::middleware('permission:manage_category')->group(function () {
      Route::get('/category-tambah', [C_Category::class, 'add'])->name('category-tambah');
      Route::get('/category/{counter_category}', [C_Category::class, 'edit'])->name('category-edit');
      Route::any('/category/selected', [C_Category::class, 'categoryDestroySelected'])->name('category.destroy.selected');

      // CATEGORY-LOKET
      Route::post('/categoryL/store', [C_Category::class, 'store'])->name('categoryL.store');
      Route::put('/categoryL/{counter_category}/update', [C_Category::class, 'update'])->name('categoryL.update');
      Route::delete('/categoryL/{counter_category}/destroy', [C_Category::class, 'destroy'])->name('categoryL.destroy');

      // CATEGORY-TIKET
      Route::post('/categoryT/store', [C_Category::class, 'storeT'])->name('categoryT.store');
      Route::put('/categoryT/{counter_category}/update', [C_Category::class, 'updateT'])->name('categoryT.update');
      Route::delete('/categoryT/{counter_category}/destroy', [C_Category::class, 'destroyT'])->name('categoryT.destroy');
    });

    // CONFIGURATION
    Route::middleware('permission:manage_config')->group(function () {
      Route::get('/config', [C_Admin::class, 'config'])->name('config');
      Route::put('/config/store', [C_Admin::class, 'configStore'])->name('config.store');
      Route::put('/config/status', [C_Admin::class, 'configUpdateStatus'])->name('config.update.status');
      Route::put('/config/partner/status', [C_Admin::class, 'partnerUpdateStatus'])->name('config.update.partner.status');
      Route::delete('/config/image', [C_Admin::class, 'configDeleteImage'])->name('config.delete.image');
    });
  });
});

// Documentation: route file maintained