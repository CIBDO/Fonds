<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Poste;
use App\Models\BureauDouane;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class PcsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Créer les postes s'ils n'existent pas
        $this->createPostes();

        // 2. Créer les bureaux de douanes pour la RGD
        $this->createBureauxDouanes();

        // 3. Créer des utilisateurs de test
        $this->createUsersTest();
    }

    /**
     * Créer les postes
     */
    private function createPostes()
    {
        $postes = [
            'RGD',
            'ACCT',
            'KAYES',
            'KOULIKORO',
            'SIKASSO',
            'SEGOU',
            'MOPTI',
            'TOMBOUCTOU',
            'GAO',
            'KIDAL',
            'MENAKA',
            'BOUGOUNI',
            'NIORO',
            'KOUTIALA',
            'KITA',
            'SAN',
            'NARA',
            'BANDIAGARA',
        ];

        foreach ($postes as $nom) {
            Poste::firstOrCreate(
                ['nom' => $nom],
                ['created_at' => now(), 'updated_at' => now()]
            );
        }

        $this->command->info('✅ Postes créés ou mis à jour');
    }

    /**
     * Créer les bureaux de douanes
     */
    private function createBureauxDouanes()
    {
        $rgd = Poste::where('nom', 'RGD')->first();

        if (!$rgd) {
            $this->command->error('❌ Le poste RGD n\'existe pas');
            return;
        }

        $bureaux = [
            ['code' => '200', 'libelle' => 'BUREAU 200'],
            ['code' => '201', 'libelle' => 'BUREAU 201'],
            ['code' => '205', 'libelle' => 'BUREAU 205'],
            ['code' => '208', 'libelle' => 'BUREAU 208'],
            ['code' => '801', 'libelle' => 'BUREAU 801'],
            ['code' => '802', 'libelle' => 'BUREAU 802 & 806'],
            ['code' => '804', 'libelle' => 'BUREAU 804 & 805'],
            ['code' => '811', 'libelle' => 'BUREAU 811'],
            ['code' => '812', 'libelle' => 'BUREAU 812'],
            ['code' => '899', 'libelle' => 'BUREAU 899'],
            ['code' => 'PAQUETS_POSTAUX', 'libelle' => 'BUREAU PAQUETS POSTAUX'],
            ['code' => 'C_POSTAUX', 'libelle' => 'BUREAU C POSTAUX'],
            ['code' => 'CFM', 'libelle' => 'BUREAU CFM'],
            ['code' => 'SENOU', 'libelle' => 'BUREAU SENOU'],
        ];

        foreach ($bureaux as $bureau) {
            BureauDouane::firstOrCreate(
                ['code' => $bureau['code'], 'poste_rgd_id' => $rgd->id],
                [
                    'libelle' => $bureau['libelle'],
                    'actif' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }

        $this->command->info('✅ Bureaux de douanes créés ou mis à jour');
    }

    /**
     * Créer des utilisateurs de test
     */
    private function createUsersTest()
    {
        $rgd = Poste::where('nom', 'RGD')->first();
        $acct = Poste::where('nom', 'ACCT')->first();
        $kayes = Poste::where('nom', 'KAYES')->first();

        // Utilisateur RGD
        if ($rgd) {
            User::firstOrCreate(
                ['email' => 'rgd@pcs.ml'],
                [
                    'name' => 'Agent RGD',
                    'password' => Hash::make('password'),
                    'role' => 'tresorier',
                    'poste_id' => $rgd->id,
                    'peut_saisir_pcs' => true,
                    'peut_valider_pcs' => false,
                    'active' => true,
                ]
            );
            $this->command->info('✅ Utilisateur RGD créé (rgd@pcs.ml / password)');
        }

        // Utilisateur ACCT (Valideur)
        if ($acct) {
            User::firstOrCreate(
                ['email' => 'acct@pcs.ml'],
                [
                    'name' => 'Agent ACCT',
                    'password' => Hash::make('password'),
                    'role' => 'acct',
                    'poste_id' => $acct->id,
                    'peut_saisir_pcs' => false,
                    'peut_valider_pcs' => true,
                    'active' => true,
                ]
            );
            $this->command->info('✅ Utilisateur ACCT créé (acct@pcs.ml / password)');
        }

        // Utilisateur KAYES
        if ($kayes) {
            User::firstOrCreate(
                ['email' => 'kayes@pcs.ml'],
                [
                    'name' => 'Agent KAYES',
                    'password' => Hash::make('password'),
                    'role' => 'tresorier',
                    'poste_id' => $kayes->id,
                    'peut_saisir_pcs' => true,
                    'peut_valider_pcs' => false,
                    'active' => true,
                ]
            );
            $this->command->info('✅ Utilisateur KAYES créé (kayes@pcs.ml / password)');
        }

        // Admin avec droits PCS
        User::firstOrCreate(
            ['email' => 'admin@pcs.ml'],
            [
                'name' => 'Administrateur PCS',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'poste_id' => $acct ? $acct->id : null,
                'peut_saisir_pcs' => true,
                'peut_valider_pcs' => true,
                'active' => true,
            ]
        );
        $this->command->info('✅ Administrateur créé (admin@pcs.ml / password)');
    }
}


