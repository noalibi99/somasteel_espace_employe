<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UpdateUserResponsableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        // Get all distinct nomService from the services table
        $services = DB::table('services')->pluck('nomService');

        // Iterate through each service value
        foreach ($services as $service_value) {
            // Update the users in the same service with responsable_hiarchique
            DB::table('users as u1')
                ->join('users as u2', function ($join) use ($service_value) {
                    $join->on('u1.projet', '=', 'u2.projet')
                         ->whereRaw('u1.service LIKE ?', [$service_value])
                         ->where(function ($query) {
                             $query->where('u2.type', 'responsable')
                                   ->orWhere('u2.fonction', 'LIKE', '%ponsable%')
                                   ->orWhere('u2.fonction', 'LIKE', '%resp%')
                                   ->orWhere('u2.fonction', 'LIKE', 'resp%');
                         });
                })
                ->whereRaw('u1.service LIKE ?', [$service_value])
                ->update(['u1.responsable_hiarchique' => DB::raw('u2.matricule')]);
        }
    }
}
