<?php

use App\Features\Dashboard\Service\Create\CreateServiceController;
use App\Features\Dashboard\Service\Delete\DeleteServiceController;
use App\Features\Dashboard\Service\Read\ReadServiceController;
use App\Features\Dashboard\Service\Share\Create\ShareServiceController;
use App\Features\Dashboard\Service\Share\LookupRecipient\LookupRecipientKeyController;
use App\Features\Dashboard\Service\Share\Read\ListIncomingSharesController;
use App\Features\Dashboard\Service\Share\Revoke\RevokeServiceShareController;
use App\Features\Dashboard\Service\Update\UpdateServiceController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth')->group(function (): void {
    Route::get('/service', ReadServiceController::class)->name('service.read');
    Route::get('/service/sharing/recipient-key', LookupRecipientKeyController::class)->name('service.share.recipient');
    Route::get('/service/share/incoming', ListIncomingSharesController::class)->name('service.share.incoming');
    Route::post('/service', CreateServiceController::class)->name('service.create');
    Route::post('/service/{serviceId}/share', ShareServiceController::class)->whereUuid('serviceId')->name('service.share.create');
    Route::put('/service/{serviceId}', UpdateServiceController::class)->whereUuid('serviceId')->name('service.update');
    Route::delete('/service/{serviceId}', DeleteServiceController::class)->whereUuid('serviceId')->name('service.delete');
    Route::delete('/service/{serviceId}/share/{shareId}', RevokeServiceShareController::class)
        ->whereUuid('serviceId')
        ->whereUuid('shareId')
        ->name('service.share.revoke');
});
