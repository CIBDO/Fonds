<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;

class ActiverDroitsPcs extends Command
{
    protected $signature = 'pcs:activer-droits {email}';
    protected $description = 'Active les droits PCS pour un utilisateur poste';

    public function handle()
    {
        $email = $this->argument('email');

        $user = User::where('email', $email)->first();

        if (!$user) {
            $this->error("Utilisateur avec l'email {$email} non trouvé.");
            return;
        }

        $user->update([
            'peut_saisir_pcs' => true,
            'peut_valider_pcs' => false
        ]);

        $this->info("Droits PCS activés pour {$user->name} ({$user->email})");
        $this->info("- peut_saisir_pcs: true");
        $this->info("- peut_valider_pcs: false");
        $this->info("- poste_id: {$user->poste_id}");
    }
}
