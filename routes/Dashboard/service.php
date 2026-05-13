<?php

use App\Features\Dashboard\Service\Create\CreateServiceController;
use App\Features\Dashboard\Service\Delete\DeleteServiceController;
use App\Features\Dashboard\Service\Read\ReadServiceController;
use App\Features\Dashboard\Service\Update\UpdateServiceController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth')->group(function (): void {
    Route::get('/service', ReadServiceController::class)->name('service.read');
    Route::post('/service', CreateServiceController::class)->name('service.create');
    Route::put('/service/{serviceId}', UpdateServiceController::class)->whereUuid('serviceId')->name('service.update');
    Route::delete('/service/{serviceId}', DeleteServiceController::class)->whereUuid('serviceId')->name('service.delete');
});

