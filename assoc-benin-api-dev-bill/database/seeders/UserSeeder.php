<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Créer le super admin
        User::create([
            'matricule' => 'SUPER001',
            'prenom' => 'Super',
            'nom' => 'Administrateur',
            'u_telephone' => '+22912345678',
            'u_email' => 'superadmin@assoc-benin.bj',
            'password' => Hash::make('password123'),
            'role' => 'super admin',
            'departement' => 'Administration',
            'statut' => 'actif',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Créer un admin normal (optionnel)
        User::create([
            'matricule' => 'ADMIN001',
            'prenom' => 'Admin',
            'nom' => 'Standard',
            'u_telephone' => '+22987654321',
            'u_email' => 'admin@assoc-benin.bj',
            'password' => Hash::make('password123'),
            'role' => 'admin',
            'departement' => '',
            'statut' => 'actif',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->command->info('Super admin et admin créés avec succès!');
        $this->command->info('Email super admin: superadmin@assoc-benin.bj');
        $this->command->info('Email admin: admin@assoc-benin.bj');
        $this->command->info('Mot de passe par défaut: password123');
    }
}
