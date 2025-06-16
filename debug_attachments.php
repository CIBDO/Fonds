<?php

require_once 'vendor/autoload.php';

use Illuminate\Support\Facades\DB;

// Initialiser Laravel sans interface web
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

// Récupérer tous les attachments
$attachments = DB::table('attachments')->get();

echo "=== DIAGNOSTIC DES ATTACHMENTS ===\n\n";
echo "Nombre total d'attachments: " . $attachments->count() . "\n\n";

foreach ($attachments as $attachment) {
    echo "ID: {$attachment->id}\n";
    echo "Filename: {$attachment->filename}\n";
    echo "Filepath: {$attachment->filepath}\n";

    // Vérifier l'existence du fichier
    $fullPath = storage_path('app/public/' . $attachment->filepath);
    $exists = file_exists($fullPath) ? 'OUI' : 'NON';
    echo "Fichier existe: {$exists}\n";
    echo "Chemin complet: {$fullPath}\n";

    // Vérifier l'URL public
    $publicUrl = url('storage/' . $attachment->filepath);
    echo "URL publique: {$publicUrl}\n";
    echo "-------------------\n";
}
