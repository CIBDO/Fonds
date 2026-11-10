<?php

namespace App\Services;

use App\Models\Poste;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\StreamedResponse;

class PosteRpImportService
{
    private const ENTETES_NOM = ['NOM', 'NOM RP', 'RP', 'RECETTE', 'RECETTE PERCEPTION', 'LIBELLE'];

    private const ENTETES_PARENT = [
        'POSTE SUPERIEUR', 'POSTE', 'PARENT', 'TRESORERIE', 'TR', 'RATTACHE A', 'RATTACHEMENT',
    ];

    private const ENTETES_INTITULE = ['INTITULE', 'INTITULE OFFICIEL'];

    /**
     * @return array{creees:int,mises_a_jour:int,inchangees:int,erreurs:array<int,string>}
     */
    public static function importer(string $path, string $extension, ?Poste $parentDefaut = null): array
    {
        $lignes = self::lireLignes($path, $extension);
        $resultat = [
            'creees' => 0,
            'mises_a_jour' => 0,
            'inchangees' => 0,
            'erreurs' => [],
        ];

        if ($lignes === []) {
            $resultat['erreurs'][] = 'Le fichier ne contient aucune ligne.';

            return $resultat;
        }

        $premiere = array_shift($lignes);
        $numero = 1;
        $colonnes = [
            'nom' => 0,
            'parent' => 1,
            'intitule' => 2,
            'region' => 3,
        ];

        if (self::estEntete($premiere)) {
            $colonnes = self::colonnesDepuisEntete($premiere);
            $numero = 1;
            if ($colonnes['nom'] === null) {
                $resultat['erreurs'][] = 'Colonne « Nom RP » introuvable dans la première ligne.';

                return $resultat;
            }
        } else {
            array_unshift($lignes, $premiere);
            $numero = 0;
        }

        $superieurs = Poste::query()
            ->where('type', Poste::TYPE_SUPERIEUR)
            ->orderBy('nom')
            ->get();

        $parId = [];
        $indexParent = [];
        foreach ($superieurs as $poste) {
            $parId[$poste->id] = $poste;
            $cle = self::cleParent($poste->nom);
            if ($cle !== '') {
                $indexParent[$cle][$poste->id] = $poste;
            }
        }

        $indexRp = [];
        foreach (Poste::query()->where('type', Poste::TYPE_RP)->get() as $rp) {
            $cle = self::cleRp($rp->nom);
            if ($cle !== '') {
                $indexRp[$cle][] = $rp;
            }
        }

        DB::transaction(function () use (
            $lignes,
            &$numero,
            $colonnes,
            $parentDefaut,
            $parId,
            $indexParent,
            &$indexRp,
            &$resultat
        ) {
            foreach ($lignes as $ligne) {
                $numero++;
                if ($numero > 2000) {
                    $resultat['erreurs'][] = 'Import limité à 2 000 lignes. Le reste du fichier a été ignoré.';
                    break;
                }

                if (self::ligneVide($ligne)) {
                    continue;
                }

                $nom = self::cellule($ligne, $colonnes['nom']);
                $parentLibelle = self::cellule($ligne, $colonnes['parent']);
                $intitule = self::cellule($ligne, $colonnes['intitule']);
                $region = self::cellule($ligne, $colonnes['region']);

                if ($nom === '') {
                    $resultat['erreurs'][] = "Ligne {$numero} : nom de la RP manquant.";
                    continue;
                }

                if (self::cleRp($nom) === '') {
                    $resultat['erreurs'][] = "Ligne {$numero} : nom de RP invalide (« {$nom} »).";
                    continue;
                }

                $parent = $parentDefaut;
                if ($parentLibelle !== '') {
                    $resolu = self::resoudreParent($parentLibelle, $indexParent, $parId);
                    if (is_string($resolu)) {
                        $resultat['erreurs'][] = "Ligne {$numero} : {$resolu}";
                        continue;
                    }
                    $parent = $resolu;
                }

                if (!$parent || !$parent->isSuperieur()) {
                    $resultat['erreurs'][] = "Ligne {$numero} : poste supérieur manquant pour « {$nom} ».";
                    continue;
                }

                $cle = self::cleRp($nom);
                $existants = $indexRp[$cle] ?? [];
                $cible = null;

                if (count($existants) === 1) {
                    $cible = $existants[0];
                } elseif (count($existants) > 1) {
                    foreach ($existants as $existant) {
                        if ((int) $existant->parent_id === (int) $parent->id) {
                            $cible = $existant;
                            break;
                        }
                    }
                    if (!$cible) {
                        $resultat['erreurs'][] = "Ligne {$numero} : plusieurs RP portent déjà le nom « {$nom} ». Ligne ignorée.";
                        continue;
                    }
                }

                if ($cible) {
                    $modifie = false;
                    if ((int) $cible->parent_id !== (int) $parent->id) {
                        $cible->parent_id = $parent->id;
                        $modifie = true;
                    }
                    if ($intitule !== '' && $cible->intitule_officiel !== $intitule) {
                        $cible->intitule_officiel = $intitule;
                        $modifie = true;
                    }
                    if ($region !== '' && $cible->region !== $region) {
                        $cible->region = $region;
                        $modifie = true;
                    }
                    if ($modifie) {
                        $cible->save();
                        $resultat['mises_a_jour']++;
                    } else {
                        $resultat['inchangees']++;
                    }
                    continue;
                }

                $cree = Poste::create([
                    'nom' => $nom,
                    'type' => Poste::TYPE_RP,
                    'parent_id' => $parent->id,
                    'intitule_officiel' => $intitule !== '' ? $intitule : null,
                    'region' => $region !== '' ? $region : null,
                ]);
                $indexRp[$cle][] = $cree;
                $resultat['creees']++;
            }
        });

        if (
            $resultat['creees'] === 0
            && $resultat['mises_a_jour'] === 0
            && $resultat['inchangees'] === 0
            && $resultat['erreurs'] === []
        ) {
            $resultat['erreurs'][] = 'Aucune RP à importer.';
        }

        if (count($resultat['erreurs']) > 40) {
            $reste = count($resultat['erreurs']) - 40;
            $resultat['erreurs'] = array_slice($resultat['erreurs'], 0, 40);
            $resultat['erreurs'][] = "... et {$reste} autre(s) ligne(s).";
        }

        return $resultat;
    }

    public static function templateResponse(): StreamedResponse
    {
        $spreadsheet = new Spreadsheet();
        $feuille = $spreadsheet->getActiveSheet();
        $feuille->setTitle('RP');
        $feuille->fromArray([
            ['Nom RP', 'Poste supérieur', 'Intitulé officiel', 'Région'],
            ['RP Exemple', 'KAYES', 'Recette-Perception d\'exemple', 'Région de Kayes'],
        ], null, 'A1');
        $feuille->getStyle('A1:D1')->getFont()->setBold(true);
        foreach (['A' => 28, 'B' => 24, 'C' => 42, 'D' => 28] as $col => $largeur) {
            $feuille->getColumnDimension($col)->setWidth($largeur);
        }

        $reference = $spreadsheet->createSheet();
        $reference->setTitle('Postes superieurs');
        $reference->fromArray([['ID', 'Nom']], null, 'A1');
        $reference->getStyle('A1:B1')->getFont()->setBold(true);

        $ligne = 2;
        $superieurs = Poste::query()
            ->where('type', Poste::TYPE_SUPERIEUR)
            ->orderBy('nom')
            ->get(['id', 'nom']);
        foreach ($superieurs as $poste) {
            $reference->fromArray([[$poste->id, $poste->nom]], null, 'A'.$ligne);
            $ligne++;
        }
        $reference->getColumnDimension('A')->setWidth(10);
        $reference->getColumnDimension('B')->setWidth(36);

        $spreadsheet->setActiveSheetIndex(0);

        return response()->streamDownload(function () use ($spreadsheet) {
            $writer = new Xlsx($spreadsheet);
            $writer->save('php://output');
        }, 'template_import_rp.xlsx', [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }

    /**
     * @return array<int, array<int, mixed>>
     */
    private static function lireLignes(string $path, string $extension): array
    {
        $extension = strtolower($extension);
        if (in_array($extension, ['csv', 'txt'], true)) {
            $reader = IOFactory::createReader('Csv');
            $echantillon = (string) file_get_contents($path, false, null, 0, 8192);
            $delimiteur = substr_count($echantillon, ';') > substr_count($echantillon, ',') ? ';' : ',';
            $reader->setDelimiter($delimiteur);
            $reader->setInputEncoding('UTF-8');
            $classeur = $reader->load($path);
        } else {
            $classeur = IOFactory::load($path);
        }

        return $classeur->getActiveSheet()->toArray(null, true, true, false);
    }

    /**
     * @param  array<int, mixed>  $row
     */
    private static function estEntete(array $row): bool
    {
        foreach ($row as $cell) {
            $cle = self::normaliser((string) $cell);
            if (
                in_array($cle, self::ENTETES_NOM, true)
                || in_array($cle, self::ENTETES_PARENT, true)
                || in_array($cle, self::ENTETES_INTITULE, true)
                || $cle === 'REGION'
            ) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param  array<int, mixed>  $row
     * @return array{nom:?int,parent:?int,intitule:?int,region:?int}
     */
    private static function colonnesDepuisEntete(array $row): array
    {
        $colonnes = ['nom' => null, 'parent' => null, 'intitule' => null, 'region' => null];
        foreach ($row as $index => $cell) {
            $cle = self::normaliser((string) $cell);
            if ($colonnes['nom'] === null && in_array($cle, self::ENTETES_NOM, true)) {
                $colonnes['nom'] = $index;
            } elseif ($colonnes['parent'] === null && in_array($cle, self::ENTETES_PARENT, true)) {
                $colonnes['parent'] = $index;
            } elseif ($colonnes['intitule'] === null && in_array($cle, self::ENTETES_INTITULE, true)) {
                $colonnes['intitule'] = $index;
            } elseif ($colonnes['region'] === null && $cle === 'REGION') {
                $colonnes['region'] = $index;
            }
        }

        return $colonnes;
    }

    /**
     * @param  array<int, Poste>  $parId
     * @param  array<string, array<int, Poste>>  $indexParent
     */
    private static function resoudreParent(string $libelle, array $indexParent, array $parId): Poste|string
    {
        if (preg_match('/#\s*(\d+)\s*$/', $libelle, $correspondance) || preg_match('/^(?:id\s*:?\s*)?(\d+)$/i', $libelle, $correspondance)) {
            $id = (int) $correspondance[1];
            if (!isset($parId[$id])) {
                return "poste supérieur n° {$id} introuvable.";
            }
            if (!$parId[$id]->isSuperieur()) {
                return "le poste n° {$id} ({$parId[$id]->nom}) n'est pas un poste supérieur.";
            }

            return $parId[$id];
        }

        $cle = self::cleParent($libelle);
        if ($cle === '' || !isset($indexParent[$cle])) {
            return "poste supérieur « {$libelle} » introuvable. Utilisez le nom exact (feuille « Postes superieurs » du modèle) ou « Nom #ID ».";
        }

        $candidats = array_values($indexParent[$cle]);
        if (count($candidats) > 1) {
            $liste = implode(', ', array_map(
                fn (Poste $poste) => $poste->nom.' #'.$poste->id,
                $candidats
            ));

            return "plusieurs postes supérieurs correspondent à « {$libelle} » ({$liste}). Précisez l'identifiant, par exemple « {$candidats[0]->nom} #{$candidats[0]->id} ».";
        }

        return $candidats[0];
    }

    /**
     * @param  array<int, mixed>  $row
     */
    private static function cellule(array $row, ?int $index): string
    {
        if ($index === null || !array_key_exists($index, $row) || $row[$index] === null) {
            return '';
        }

        return trim((string) $row[$index]);
    }

    /**
     * @param  array<int, mixed>  $row
     */
    private static function ligneVide(array $row): bool
    {
        foreach ($row as $cell) {
            if (trim((string) $cell) !== '') {
                return false;
            }
        }

        return true;
    }

    private static function cleParent(string $valeur): string
    {
        $normalise = self::normaliser($valeur);
        $normalise = preg_replace(
            '/^(TRESORERIE REGIONALE DE|TRESORERIE REGIONALE|TRESORERIE DE|TRESORERIE|TR DE|TR|POSTE SUPERIEUR|POSTE)\s+/',
            '',
            $normalise
        ) ?? $normalise;

        return trim($normalise);
    }

    private static function cleRp(string $nom): string
    {
        $normalise = self::normaliser($nom);
        $normalise = preg_replace('/^(RECETTE PERCEPTION|RECETTE|RP)\s+/', '', $normalise) ?? $normalise;

        return trim($normalise);
    }

    private static function normaliser(string $valeur): string
    {
        $valeur = str_replace(["'", '’', '`'], '', trim($valeur));
        $valeur = mb_strtoupper($valeur, 'UTF-8');
        $valeur = strtr($valeur, [
            'À' => 'A', 'Á' => 'A', 'Â' => 'A', 'Ä' => 'A', 'Ã' => 'A', 'Å' => 'A',
            'È' => 'E', 'É' => 'E', 'Ê' => 'E', 'Ë' => 'E',
            'Ì' => 'I', 'Í' => 'I', 'Î' => 'I', 'Ï' => 'I',
            'Ò' => 'O', 'Ó' => 'O', 'Ô' => 'O', 'Ö' => 'O', 'Õ' => 'O',
            'Ù' => 'U', 'Ú' => 'U', 'Û' => 'U', 'Ü' => 'U',
            'Ç' => 'C', 'Ñ' => 'N', 'Œ' => 'OE', 'Æ' => 'AE',
        ]);
        $valeur = preg_replace('/[^A-Z0-9]+/', ' ', $valeur) ?? '';

        return trim(preg_replace('/\s+/', ' ', $valeur) ?? '');
    }
}
