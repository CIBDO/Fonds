<?php

namespace App\Services;

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ExcelVirementService
{
    /**
     * Colonnes attendues du bordereau détaillé SI (libellés normalisés → clé métier).
     *
     * @var array<string, list<string>>
     */
    private const HEADER_ALIASES = [
        'num_be' => ['n be', 'n b e', 'num be', 'numero be', 'no be'],
        'num_mdt' => ['n mdt', 'num mdt', 'numero mdt', 'no mdt'],
        'ord' => ['ord', 'ord.'],
        'programme' => ['programme', 'prog'],
        'creancier' => ['noms creanciers', 'nom creanciers', 'creancier', 'creanciers', 'noms creancier'],
        'num_compte' => ['n compte', 'num compte', 'numero compte', 'no compte', 'compte'],
        'montant' => ['montant'],
    ];

    /**
     * Parse le bordereau détaillé Excel généré par le SI
     * (ou un fichier simple dont la 1ère ligne est déjà l'en-tête).
     *
     * @return array<int, array{num_be:?string,num_mdt:?string,ord:?string,programme:?string,creancier:?string,num_compte:?string,montant:float}>
     */
    public static function parseDetailFile(string $path): array
    {
        $spreadsheet = IOFactory::load($path);
        $rows = $spreadsheet->getActiveSheet()->toArray(null, true, true, false);
        if ($rows === []) {
            return [];
        }

        $headerIndex = self::findHeaderRowIndex($rows);
        if ($headerIndex === null) {
            return [];
        }

        $map = self::mapHeaderColumns($rows[$headerIndex]);
        if (! isset($map['montant']) && ! isset($map['creancier'])) {
            return [];
        }

        $lignes = [];
        for ($i = $headerIndex + 1; $i < count($rows); $i++) {
            $row = $rows[$i];

            if (self::isFooterOrEmptyRow($row)) {
                // Arrêt dès le pied de page SI, ou ligne totalement vide après des données
                if ($lignes !== [] || self::isFooterRow($row)) {
                    break;
                }
                continue;
            }

            $montant = self::parseMontant($row[$map['montant'] ?? -1] ?? null);
            $creancier = trim((string) ($row[$map['creancier'] ?? -1] ?? ''));
            if ($montant <= 0 && $creancier === '') {
                continue;
            }

            $lignes[] = [
                'num_be' => self::str($row[$map['num_be'] ?? -1] ?? null),
                'num_mdt' => self::str($row[$map['num_mdt'] ?? -1] ?? null),
                'ord' => self::str($row[$map['ord'] ?? -1] ?? null),
                'programme' => self::str($row[$map['programme'] ?? -1] ?? null),
                'creancier' => $creancier !== '' ? $creancier : null,
                'num_compte' => self::str($row[$map['num_compte'] ?? -1] ?? null) ?: 'NEANT',
                'montant' => $montant,
            ];
        }

        return $lignes;
    }

    /**
     * @param  iterable<int, object>  $virements
     */
    public static function exportVirements(iterable $virements, string $filename = 'virements.xlsx'): StreamedResponse
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Virements');
        $sheet->fromArray([
            ['N° VIR', 'Poste', 'Banque', 'Mois', 'Année', 'Montant', 'Statut', 'Date paiement'],
        ], null, 'A1');

        $r = 2;
        foreach ($virements as $v) {
            $sheet->fromArray([[
                $v->numero_vir,
                $v->poste->nom ?? '',
                $v->banque->nom ?? '',
                $v->mois,
                $v->annee,
                (float) $v->montant,
                $v->statut_paiement === 'paye' ? 'Payé' : 'Non payé',
                optional($v->date_paiement)->format('d/m/Y H:i'),
            ]], null, 'A'.$r);
            $r++;
        }

        return self::download($spreadsheet, $filename);
    }

    /**
     * @param  array<int, array{banque:string,total:float,paye:float,non_paye:float,nb:int}>  $rows
     */
    public static function exportConsolidation(array $rows, string $filename = 'consolidation_virements.xlsx'): StreamedResponse
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Consolidation');
        $sheet->fromArray([
            ['Banque', 'Nb VIR', 'Total', 'Payé', 'Non payé'],
        ], null, 'A1');

        $r = 2;
        foreach ($rows as $row) {
            $sheet->fromArray([[
                $row['banque'],
                $row['nb'],
                $row['total'],
                $row['paye'],
                $row['non_paye'],
            ]], null, 'A'.$r);
            $r++;
        }

        return self::download($spreadsheet, $filename);
    }

    /**
     * @param  array<int, string>  $headers
     * @param  array<int, array<int, mixed>>  $rows
     */
    public static function exportSimple(array $headers, array $rows, string $filename, string $sheetTitle = 'Export'): StreamedResponse
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle(mb_substr($sheetTitle, 0, 31));
        $sheet->fromArray([$headers], null, 'A1');
        if ($rows !== []) {
            $sheet->fromArray($rows, null, 'A2');
        }

        return self::download($spreadsheet, $filename);
    }

    /**
     * @param  array<int, array<int, mixed>>  $rows
     */
    private static function findHeaderRowIndex(array $rows): ?int
    {
        foreach ($rows as $index => $row) {
            $map = self::mapHeaderColumns($row);
            $matched = count(array_intersect_key($map, array_flip([
                'num_be', 'num_mdt', 'ord', 'programme', 'creancier', 'num_compte', 'montant',
            ])));
            // Au moins créancier + montant, ou ≥ 4 colonnes reconnues
            if ((isset($map['creancier'], $map['montant'])) || $matched >= 4) {
                return (int) $index;
            }
        }

        return null;
    }

    /**
     * @param  array<int, mixed>  $row
     * @return array<string, int>
     */
    private static function mapHeaderColumns(array $row): array
    {
        $map = [];
        foreach ($row as $colIndex => $cell) {
            $normalized = self::normalizeHeaderLabel($cell);
            if ($normalized === '') {
                continue;
            }
            foreach (self::HEADER_ALIASES as $key => $aliases) {
                if (isset($map[$key])) {
                    continue;
                }
                foreach ($aliases as $alias) {
                    if ($normalized === $alias || str_starts_with($normalized, $alias.' ')) {
                        $map[$key] = (int) $colIndex;
                        break 2;
                    }
                }
            }
        }

        return $map;
    }

    private static function normalizeHeaderLabel(mixed $value): string
    {
        $s = mb_strtolower(trim((string) $value));
        if ($s === '') {
            return '';
        }
        // Accents
        $s = strtr($s, [
            'à' => 'a', 'â' => 'a', 'ä' => 'a',
            'é' => 'e', 'è' => 'e', 'ê' => 'e', 'ë' => 'e',
            'î' => 'i', 'ï' => 'i',
            'ô' => 'o', 'ö' => 'o',
            'ù' => 'u', 'û' => 'u', 'ü' => 'u',
            'ç' => 'c',
        ]);
        // ° / º / ponctuation → espace, garder lettres/chiffres
        $s = str_replace(['°', 'º', 'n°', 'nº'], [' ', ' ', 'n ', 'n '], $s);
        $s = preg_replace('/[^a-z0-9]+/', ' ', $s) ?? '';
        $s = trim(preg_replace('/\s+/', ' ', $s) ?? '');

        return $s;
    }

    /**
     * @param  array<int, mixed>  $row
     */
    private static function isFooterOrEmptyRow(array $row): bool
    {
        if (self::isFooterRow($row)) {
            return true;
        }

        foreach ($row as $cell) {
            if (trim((string) ($cell ?? '')) !== '') {
                return false;
            }
        }

        return true;
    }

    /**
     * @param  array<int, mixed>  $row
     */
    private static function isFooterRow(array $row): bool
    {
        $joined = mb_strtolower(trim(implode(' ', array_map(
            static fn ($c) => trim((string) ($c ?? '')),
            $row
        ))));

        if ($joined === '') {
            return false;
        }

        $markers = [
            'nombre de beneficiaire',
            'nombre de bénéficiaire',
            'fait a ',
            'fait à ',
            'total general',
            'total général',
            'tresorier payeur',
            'trésorier payeur',
            'francs c.f.a',
            'francs cfa',
        ];

        foreach ($markers as $marker) {
            if (str_contains($joined, $marker)) {
                return true;
            }
        }

        // Ligne "N° xxx VIR du …" / référence seule en pied
        if (preg_match('/^n[°º]?\s*.+\s+du\s+\d/u', $joined)) {
            return true;
        }

        return false;
    }

    private static function download(Spreadsheet $spreadsheet, string $filename): StreamedResponse
    {
        return response()->streamDownload(function () use ($spreadsheet) {
            $writer = new Xlsx($spreadsheet);
            $writer->save('php://output');
        }, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }

    private static function str(mixed $value): ?string
    {
        if ($value === null) {
            return null;
        }
        $s = trim((string) $value);

        return $s === '' ? null : $s;
    }

    private static function parseMontant(mixed $value): float
    {
        if ($value === null || $value === '') {
            return 0.0;
        }
        if (is_numeric($value)) {
            return (float) $value;
        }
        $clean = str_replace([' ', "\u{00A0}", ','], ['', '', '.'], (string) $value);
        $clean = preg_replace('/[^0-9.\-]/', '', $clean) ?? '0';

        return (float) $clean;
    }
}
