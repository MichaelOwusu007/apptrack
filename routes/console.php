<?php

use Illuminate\Support\Facades\Artisan;

Artisan::command('app:about', function () {
    $this->info('AppTrack Pro command routes are loaded.');
})->purpose('Confirm that the application console routes are registered');
