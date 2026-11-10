<?php

namespace App\Services;

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ExcelVirementService
{
    public static function templateDetailResponse(): StreamedResponse
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Detail VIR');
        $sheet->fromArray([
            ['N° BE', 'N° Mdt', 'Ord.', 'Programme', 'Créancier', 'N° Compte', 'Montant'],
            ['767', '1356', '906', '2045', 'BNDA - BANQUE NATIONALE DE DEVELOPPEMENT AGRICOLE', 'NEANT', 1000000],
        ], null, 'A1');

        return self::download($spreadsheet, 'template_lignes_virements.xlsx');
    }

    /**
     * @return array<int, array{num_be:?string,num_mdt:?string,ord:?string,programme:?string,creancier:?string,num_compte:?string,montant:float}>
     */
    public static function parseDetailFile(string $path): array
    {
        $spreadsheet = IOFactory::load($path);
        $rows = $spreadsheet->getActiveSheet()->toArray(null, true, true, false);
        if ($rows === []) {
            return [];
        }

        // Skip header
        array_shift($rows);
        $lignes = [];
        foreach ($rows as $row) {
            $montant = self::parseMontant($row[6] ?? null);
            $creancier = trim((string) ($row[4] ?? ''));
            if ($montant <= 0 && $creancier === '') {
                continue;
            }
            $lignes[] = [
                'num_be' => self::str($row[0] ?? null),
                'num_mdt' => self::str($row[1] ?? null),
                'ord' => self::str($row[2] ?? null),
                'programme' => self::str($row[3] ?? null),
                'creancier' => $creancier !== '' ? $creancier : null,
                'num_compte' => self::str($row[5] ?? null) ?: 'NEANT',
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
