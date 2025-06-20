<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User; // Assurez-vous que le chemin vers votre modèle User est correct

class TestUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Mot de passe commun pour tous les utilisateurs de test (plus facile à retenir)
        // En PRODUCTION, utilisez des mots de passe uniques et forts.
        $commonPassword = Hash::make('password'); // Changez 'password' par ce que vous voulez

        // Administrateur
        User::create([
            'nom' => 'Admin',
            'prénom' => 'Super',
            'matricule' => 'ADM001',
            'fonction' => 'Administrateur Système',
            'service' => 'IT',
            'type' => 'administrateur',
            'solde_conge' => 25, // Exemple
            'responsable_hiarchique' => null, // L'admin n'a peut-être pas de responsable direct
            'directeur' => null,
            'email' => 'admin@somasteel.com',
            'password' => $commonPassword,
            // 'equipe_id' => null, // Si applicable
            // 'shift_id' => null,  // Si applicable
        ]);

        // RH
        User::create([
            'nom' => 'Human',
            'prénom' => 'Resource',
            'matricule' => 'RH001',
            'fonction' => 'Chargé RH',
            'service' => 'Ressources Humaines',
            'type' => 'rh',
            'solde_conge' => 22,
            'email' => 'rh@somasteel.com',
            'password' => $commonPassword,
        ]);

        // Directeur
        User::create([
            'nom' => 'Director',
            'prénom' => 'General',
            'matricule' => 'DIR001',
            'fonction' => 'Directeur Général',
            'service' => 'Direction',
            'type' => 'directeur',
            'solde_conge' => 30,
            'email' => 'directeur@somasteel.com',
            'password' => $commonPassword,
        ]);

        // Service Achat (Purchase)
        User::create([
            'nom' => 'Buyer',
            'prénom' => 'Efficient',
            'matricule' => 'PUR001',
            'fonction' => 'Responsable Achats',
            'service' => 'Achats',
            'type' => 'purchase',
            'solde_conge' => 20,
            'email' => 'purchase@somasteel.com',
            'password' => $commonPassword,
        ]);

        // Magasinier
        User::create([
            'nom' => 'Stocker',
            'prénom' => 'Warehouse',
            'matricule' => 'MAG001',
            'fonction' => 'Magasinier Principal',
            'service' => 'Logistique',
            'type' => 'magasinier',
            'solde_conge' => 18,
            'email' => 'magasinier@somasteel.com',
            'password' => $commonPassword,
        ]);

        // Comptable
        User::create([
            'nom' => 'Accountant',
            'prénom' => 'Finance',
            'matricule' => 'CPT001',
            'fonction' => 'Comptable',
            'service' => 'Finance et Comptabilité',
            'type' => 'comptable',
            'solde_conge' => 20,
            'email' => 'comptable@somasteel.com',
            'password' => $commonPassword,
        ]);

        // Responsable (exemple : Responsable de Production)
        User::create([
            'nom' => 'Manager',
            'prénom' => 'Production',
            'matricule' => 'RESP001',
            'fonction' => 'Responsable Production',
            'service' => 'Production',
            'type' => 'responsable',
            'solde_conge' => 24,
            'email' => 'responsable.prod@somasteel.com',
            'password' => $commonPassword,
            // 'directeur_id' => ID_DU_DIRECTEUR, // Si vous voulez lier à un directeur spécifique
        ]);

        // Ouvrier (exemple)
        User::create([
            'nom' => 'Worker',
            'prénom' => 'John',
            'matricule' => 'OUV001',
            'fonction' => 'Opérateur Machine',
            'service' => 'Production',
            'type' => 'ouvrier',
            'solde_conge' => 15,
            'email' => 'ouvrier.john@somasteel.com',
            'password' => $commonPassword,
            // 'responsable_hiarchique_id' => ID_DU_RESPONSABLE, // Lier à un responsable
            // 'equipe_id' => ID_EQUIPE,
            // 'shift_id' => ID_SHIFT,
        ]);

        // Un autre ouvrier pour avoir de la variété
        User::create([
            'nom' => 'Smith',
            'prénom' => 'Jane',
            'matricule' => 'OUV002',
            'fonction' => 'Assembleuse',
            'service' => 'Atelier',
            'type' => 'ouvrier',
            'solde_conge' => 16,
            'email' => 'ouvrier.jane@somasteel.com',
            'password' => $commonPassword,
        ]);

        // Vous pouvez ajouter d'autres exemples si nécessaire, par exemple un autre directeur de département.
        User::create([
            'nom' => 'Director',
            'prénom' => 'Marketing',
            'matricule' => 'DIR002',
            'fonction' => 'Directeur Marketing',
            'service' => 'Marketing',
            'type' => 'directeur',
            'solde_conge' => 28,
            'email' => 'directeur.mkt@somasteel.com',
            'password' => $commonPassword,
        ]);

        // Vous pouvez ajouter un utilisateur simple qui sera le demandeur dans le processus d'achat
        User::create([
            'nom' => 'Demandeur',
            'prénom' => 'Test',
            'matricule' => 'DEM001',
            'fonction' => 'Employé Standard',
            'service' => 'Administration',
            'type' => 'ouvrier', // Ou un autre type de base qui n'a pas de rôle spécial achat
            'solde_conge' => 18,
            'email' => 'demandeur@somasteel.com',
            'password' => $commonPassword,
        ]);


        // Afficher un message dans la console
        $this->command->info('Utilisateurs de test créés avec succès !');
        $this->command->info('Email: [role]@somasteel.com (ex: admin@somasteel.com)');
        $this->command->info('Mot de passe commun pour tous: password (ou ce que vous avez défini)');

    }
}
