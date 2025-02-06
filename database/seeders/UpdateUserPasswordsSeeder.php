<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UpdateUserPasswordsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        // Get all users
        $users = DB::table('users_new_1')->get();

        // Loop through each user and update the password
        foreach ($users as $user) {
            $password = $user->matricule . '-' . $user->nom;  // Create password in the format matricule-nom
            DB::table('users_new_1')
                ->where('id', $user->id)  // Update the user by their ID
                ->update([
                    'password' => Hash::make($password),  // Hash the password
                ]);
        }
    }
}
