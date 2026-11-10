<?php

namespace Database\Seeders;

use App\Models\Banque;
use Illuminate\Database\Seeder;

class BanqueSeeder extends Seeder
{
    public function run(): void
    {
        $banques = [
            ['code' => 'BNDA', 'nom' => 'BNDA - BANQUE NATIONALE DE DEVELOPPEMENT AGRICOLE'],
            ['code' => 'BDM', 'nom' => 'BDM - BANQUE DE DEVELOPPEMENT DU MALI'],
            ['code' => 'BIM', 'nom' => 'BIM - BANQUE INTERNATIONALE POUR LE MALI'],
            ['code' => 'BOA', 'nom' => 'BOA - BANK OF AFRICA MALI'],
            ['code' => 'Ecobank', 'nom' => 'ECOBANK MALI'],
            ['code' => 'BCS', 'nom' => 'BCS - BANQUE COMMERCIALE DU SAHEL'],
            ['code' => 'Orabank', 'nom' => 'ORABANK MALI'],
            ['code' => 'Coris', 'nom' => 'CORIS BANK MALI'],
            ['code' => 'UBA', 'nom' => 'UBA MALI'],
            ['code' => 'BCI', 'nom' => 'BCI - BANQUE COMMERCIALE INTERNATIONALE'],
        ];

        foreach ($banques as $banque) {
            Banque::updateOrCreate(
                ['code' => $banque['code']],
                ['nom' => $banque['nom'], 'actif' => true]
            );
        }
    }
}
