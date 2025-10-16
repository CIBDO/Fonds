<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;

class ListeUtilisateursPcs extends Command
{
    protected $signature = 'pcs:liste-utilisateurs';
    protected $description = 'Liste tous les utilisateurs avec leurs droits PCS';

    public function handle()
    {
        $users = User::with('poste')->get();

        $this->info("=== LISTE DES UTILISATEURS ===");
        $this->line("");

        $headers = ['ID', 'Nom', 'Email', 'Rôle', 'Poste', 'Peut Saisir PCS', 'Peut Valider PCS'];
        $rows = [];

        foreach ($users as $user) {
            $rows[] = [
                $user->id,
                $user->name,
                $user->email,
                $user->role ?? 'N/A',
                $user->poste->nom ?? 'N/A',
                $user->peut_saisir_pcs ? 'OUI' : 'NON',
                $user->peut_valider_pcs ? 'OUI' : 'NON'
            ];
        }

        $this->table($headers, $rows);

        $this->line("");
        $this->info("Pour activer les droits PCS pour un utilisateur :");
        $this->line("php artisan pcs:activer-droits email@example.com");
    }
}
