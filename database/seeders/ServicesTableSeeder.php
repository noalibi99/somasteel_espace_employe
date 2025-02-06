<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB; // Add this line to import the DB facade

class ServicesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Define the services to seed
        $services = [
            ['nomService' => 'INFORMATIQUE', 'created_at' => now()],
            ['nomService' => 'PRODUCTION', 'created_at' => now()],
            ['nomService' => 'DIRECTION', 'created_at' => now()],
            ['nomService' => 'UTILITES', 'created_at' => now()],
            ['nomService' => 'VENTES', 'created_at' => now()],
            ['nomService' => 'LOGISTIQUE', 'created_at' => now()],
            ['nomService' => 'USINAGE', 'created_at' => now()],
            ['nomService' => 'RH', 'created_at' => now()],
        ];

        // Insert the services into the database
        DB::table('services')->insert($services);

        $this->command->info('Services seeded successfully!');
    }
}
