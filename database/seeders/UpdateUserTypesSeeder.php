<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UpdateUserTypesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        DB::table('users')
            ->where(function ($query) {
                $query->where('fonction', 'LIKE', '%ponsable%')
                      ->orWhere('fonction', 'LIKE', '%resp%')
                      ->orWhere('fonction', 'LIKE', 'resp%');
            })
            ->update(['type' => 'responsable']);
    }
}
