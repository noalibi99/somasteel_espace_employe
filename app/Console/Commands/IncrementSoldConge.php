<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use App\Models\User;
use Carbon\Carbon;

class IncrementSoldConge extends Command
{
    protected $signature = 'app:increment-sold-conge';
    protected $description = 'Increment vacation balance monthly';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $users = User::all();
        foreach ($users as $user) {
            $yearsOfService = Carbon::parse($user->date_entre)->diffInYears(Carbon::now());

            // Base increment of 1.5 per month
            $monthlyIncrement = 1.5;

            // Extra yearly vacation increments based on years of service
            $yearlyIncrement = 0;
            if ($yearsOfService >= 5) {
                // Calculate additional increment as 1.5 for every 5 years
                $yearlyIncrement = floor($yearsOfService / 5) * 1.5;
            }

            // Increment monthly
            $user->solde_conge += $monthlyIncrement;

            // Add the yearly bonus at the start of the year
            if (Carbon::now()->month == 1) {
                $user->solde_conge += $yearlyIncrement;
            }

            // Save the updated balance
            $user->save();
        }

        $this->info('Vacation balances have been incremented successfully.');
        Log::info('Vacation balances have been incremented successfully on ' . now());
    }
}
