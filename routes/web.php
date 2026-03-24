<?php

use Illuminate\Support\Facades\Route;
// use App\Http\Controllers\RewardController;
use App\Http\Controllers\PanenPoinController;
use App\Http\Controllers\BackController;



Route::get('/', [PanenPoinController::class, 'getReportData'])->name('home');

Route::post('/login', [BackController::class, 'login'])->name('login');
Route::post('/logout', [BackController::class, 'logout'])->name('logout');

Route::post('/redeem', [PanenPoinController::class, 'redeemPrize'])->name('redeem');
Route::post('/contact-info', [PanenPoinController::class, 'storeContactInfo'])->name('contact-info.store');
Route::get('/admin/redeems', [PanenPoinController::class, 'adminRedeems'])->name('admin.redeems');
Route::get('/admin/redeems/export', [PanenPoinController::class, 'exportRedeemsExcel'])->name('admin.redeems.export');
Route::post('/admin/redeems/{id}/ship', [PanenPoinController::class, 'markRedeemShipped'])->name('admin.redeems.ship');
Route::post('/redeem/proof', [PanenPoinController::class, 'uploadRedeemProof'])->name('redeem.proof');
