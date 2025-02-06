<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;

class ShiftsExport implements FromArray, WithHeadings, WithStyles
{
   protected $data;
   protected $date;

   public function __construct($data, $date)
   {
      $this->data = $data;
      $this->date = $date;
   }

   public function array(): array
   {
      // Add the date as the first row
      return array_merge([
         ['Date:', $this->date, '', '', ''] // First row with the date
      ], $this->data);
   }

   public function headings(): array
   {
      return [
         'Matricule',
         'Nom & Prénom',
         'Service',
         'Présence',
         'Shift',
      ];
   }

   public function styles(Worksheet $sheet)
   {
      // Style for the date row
      $sheet->getStyle('A1:E1')->applyFromArray([
         'font' => [
            'bold' => true,
            'color' => ['rgb' => '000000'],
            'size' => 12,
         ],
         'fill' => [
            'fillType' => Fill::FILL_SOLID,
            'startColor' => ['rgb' => 'FFFF00'], // Yellow background for headers
         ],
         'alignment' => [
            'horizontal' => Alignment::HORIZONTAL_LEFT,
            'vertical' => Alignment::VERTICAL_CENTER,
         ],
         'borders' => [
            'allBorders' => [
               'borderStyle' => Border::BORDER_THIN,
               'color' => ['rgb' => '000000'],
            ],
         ],
      ]);

      // Adjust the style for the header row
      $sheet->getStyle('A2:E2')->applyFromArray([
         'font' => [
            'bold' => true,
            'color' => ['rgb' => '000000'],
            'size' => 12,
         ],
         'fill' => [
            'fillType' => Fill::FILL_SOLID,
            'startColor' => ['rgb' => 'FFFF00'], // Yellow background for headers
         ],
         'alignment' => [
            'horizontal' => Alignment::HORIZONTAL_CENTER,
            'vertical' => Alignment::VERTICAL_CENTER,
         ],
         'borders' => [
            'allBorders' => [
               'borderStyle' => Border::BORDER_THIN,
               'color' => ['rgb' => '000000'],
            ],
         ],
      ]);

      $sheet->getStyle('A3:E' . ($sheet->getHighestRow()))->applyFromArray([
         'alignment' => [
            'horizontal' => Alignment::HORIZONTAL_CENTER,
            'vertical' => Alignment::VERTICAL_CENTER,
         ],
      ]);

      $sheet->getColumnDimension('A')->setWidth(15);
      $sheet->getColumnDimension('B')->setWidth(30);
      $sheet->getColumnDimension('C')->setWidth(25);
      $sheet->getColumnDimension('D')->setWidth(15);
      $sheet->getColumnDimension('E')->setWidth(15);

      $sheet->getRowDimension(1)->setRowHeight(25); // Date row height
      $sheet->getRowDimension(2)->setRowHeight(25); // Header row height
      
      // Apply styles based on status
      $rowIndex = 3; // Start from row 3 because row 1 is date and row 2 is the header

      foreach ($this->data as $row) {
         $statusCell = 'D' . $rowIndex;

         if ($row[3] == 'Présent') {
            $sheet->getStyle($statusCell)->applyFromArray([
               'fill' => [
                  'fillType' => Fill::FILL_SOLID,
                  'startColor' => ['rgb' => '28A745'], // Green color
               ],
               'font' => ['color' => ['rgb' => 'FFFFFF']], // White text
            ]);
         } elseif ($row[3] == 'N/A') {
            $sheet->getStyle($statusCell)->applyFromArray([
               'fill' => [
                  'fillType' => Fill::FILL_SOLID,
                  'startColor' => ['rgb' => 'FFC107'], // Warning color (yellow)
               ],
               'font' => ['color' => ['rgb' => '000000']], // Black text
            ]);
         } else {
            $sheet->getStyle($statusCell)->applyFromArray([
               'fill' => [
                  'fillType' => Fill::FILL_SOLID,
                  'startColor' => ['rgb' => 'DC3545'], // Red color
               ],
               'font' => ['color' => ['rgb' => 'FFFFFF']], // White text
            ]);
         }

         $rowIndex++;
      }
      $highestRow = $sheet->getHighestRow();
      for ($row = 3; $row <= $highestRow; $row++) {
         $sheet->getRowDimension($row)->setRowHeight(25); // Data row height
      }
   }
}
