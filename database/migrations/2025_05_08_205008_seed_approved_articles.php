<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        // Insérer les articles approuvés
        DB::table('articles')->insert([
            [
                'reference' => 'MAT-IT-001',
                'designation' => 'Ordinateur Portable HP EliteBook',
                'description' => 'PC Portable HP EliteBook 840 G6, Core i7, 16GB RAM, 512GB SSD',
                'sn' => 'SN-HP-ELB-001',
                'status' => 'approved',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'reference' => 'MAT-IT-002',
                'designation' => 'Écran Dell 24"',
                'description' => 'Écran Dell UltraSharp 24" U2419H, Full HD, HDMI/DP',
                'sn' => 'SN-DELL-U2419',
                'status' => 'approved',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'reference' => 'MAT-IT-003',
                'designation' => 'Clavier Logitech MX Keys',
                'description' => 'Clavier sans fil Logitech MX Keys pour Mac/Windows',
                'sn' => 'SN-LOG-MXK01',
                'status' => 'approved',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'reference' => 'MAT-IT-004',
                'designation' => 'Souris Microsoft Sculpt',
                'description' => 'Souris ergonomique sans fil Microsoft Sculpt',
                'sn' => 'SN-MS-SCULPT',
                'status' => 'approved',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'reference' => 'MAT-IT-005',
                'designation' => 'Dock Station USB-C',
                'description' => 'Station d\'accueil USB-C 7-en-1 avec HDMI, USB 3.0, Ethernet',
                'sn' => 'SN-DOCK-UC01',
                'status' => 'approved',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'reference' => 'MAT-OFF-001',
                'designation' => 'Chaise de bureau ergonomique',
                'description' => 'Chaise de bureau haut de gamme avec support lombaire',
                'sn' => 'SN-CHAIR-ERG',
                'status' => 'approved',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'reference' => 'MAT-OFF-002',
                'designation' => 'Bureau réglable en hauteur',
                'description' => 'Bureau électrique réglable en hauteur 160x80cm',
                'sn' => 'SN-DESK-ADJ',
                'status' => 'approved',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'reference' => 'MAT-NET-001',
                'designation' => 'Switch réseau 24 ports',
                'description' => 'Switch Gigabit 24 ports avec gestion SNMP',
                'sn' => 'SN-SW-GB24',
                'status' => 'approved',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'reference' => 'MAT-PHONE-001',
                'designation' => 'Téléphone IP Yealink',
                'description' => 'Téléphone IP Yealink T54S avec écran couleur',
                'sn' => 'SN-YL-T54S01',
                'status' => 'approved',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'reference' => 'MAT-IT-006',
                'designation' => 'Disque Dur Externe 1To',
                'description' => 'Disque dur externe SSD 1To USB 3.2',
                'sn' => 'SN-SSD-EXT1T',
                'status' => 'approved',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'reference' => 'MAT-CONF-001',
                'designation' => 'Vidéoprojecteur Epson',
                'description' => 'Vidéoprojecteur Epson EB-U05 3LCD 3800 lumens',
                'sn' => 'SN-EPS-EPU05',
                'status' => 'approved',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'reference' => 'MAT-CONF-002',
                'designation' => 'Ecran interactif 65"',
                'description' => 'Ecran tactile interactif 65" 4K avec Android intégré',
                'sn' => 'SN-PNL-TCH65',
                'status' => 'approved',
                'created_at' => now(),
                'updated_at' => now()
            ]
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        // Supprimer les articles insérés (optionnel)
        DB::table('articles')
            ->whereIn('reference', [
                'MAT-IT-001', 'MAT-IT-002', 'MAT-IT-003', 
                'MAT-IT-004', 'MAT-IT-005', 'MAT-OFF-001',
                'MAT-OFF-002', 'MAT-NET-001', 'MAT-PHONE-001',
                'MAT-IT-006', 'MAT-CONF-001', 'MAT-CONF-002'
            ])
            ->delete();
    }
};