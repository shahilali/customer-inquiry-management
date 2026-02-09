<?php

use App\Http\Controllers\Api\InquiryController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/health', function () {
    return response()->json([
        'success' => true,
        'message' => 'API is running',
        'timestamp' => now()->toIso8601String(),
    ]);
});

Route::prefix('inquiries')->group(function () {
    Route::get('/', [InquiryController::class, 'index'])->name('inquiries.index');
    Route::post('/', [InquiryController::class, 'store'])->name('inquiries.store');
    Route::get('/statistics', [InquiryController::class, 'statistics'])->name('inquiries.statistics');
    Route::get('/{id}', [InquiryController::class, 'show'])->name('inquiries.show');
    Route::put('/{id}', [InquiryController::class, 'update'])->name('inquiries.update');
    Route::patch('/{id}', [InquiryController::class, 'update'])->name('inquiries.patch');
    Route::delete('/{id}', [InquiryController::class, 'destroy'])->name('inquiries.destroy');
});
