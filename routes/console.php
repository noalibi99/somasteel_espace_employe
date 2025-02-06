<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use App\Console\Commands\IncrementSoldConge; // Ensure this matches the namespace of your command class

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote')->hourly();

// Register a custom Artisan command directly
Artisan::command('app:increment-sold-conge', function () {
    $this->call(IncrementSoldConge::class);
})->monthly(); // Or any other frequency you need
