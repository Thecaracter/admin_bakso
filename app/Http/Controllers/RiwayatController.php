<?php

namespace App\Http\Controllers;

use App\Models\Penjualan;
use Carbon\Carbon;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;

class RiwayatController extends Controller
{
    public function index(Request $request)
    {
        $startDate = $request->filled('start_date')
            ? Carbon::parse($request->start_date)->startOfDay()
            : Carbon::now()->startOfWeek()->startOfDay();

        $endDate = $request->filled('end_date')
            ? Carbon::parse($request->end_date)->endOfDay()
            : Carbon::now()->endOfWeek()->endOfDay();

        $riwayat = Penjualan::with(['user', 'detail.produk'])
            ->where('status', 'lunas')  // Tambahkan filter status lunas
            ->whereBetween('created_at', [$startDate, $endDate])
            ->latest()
            ->get();

        return view('pages.riwayat', compact('riwayat', 'startDate', 'endDate'));
    }

    public function print(Request $request)
    {
        $startDate = $request->filled('start_date')
            ? Carbon::parse($request->start_date)->startOfDay()
            : Carbon::now()->startOfWeek()->startOfDay();

        $endDate = $request->filled('end_date')
            ? Carbon::parse($request->end_date)->endOfDay()
            : Carbon::now()->endOfWeek()->endOfDay();

        $riwayat = Penjualan::with(['user', 'detail.produk'])
            ->where('status', 'lunas')  // Tambahkan filter status lunas
            ->whereBetween('created_at', [$startDate, $endDate])
            ->latest()
            ->get();

        return view('print.print-riwayat', compact('riwayat', 'startDate', 'endDate'));
    }

    public function exportExcel(Request $request)
    {
        try {
            $startDate = $request->filled('start_date')
                ? Carbon::parse($request->start_date)->startOfDay()
                : Carbon::now()->startOfWeek()->startOfDay();

            $endDate = $request->filled('end_date')
                ? Carbon::parse($request->end_date)->endOfDay()
                : Carbon::now()->endOfWeek()->endOfDay();

            $riwayat = Penjualan::with(['user', 'detail.produk'])
                ->where('status', 'lunas')  // Tambahkan filter status lunas
                ->whereBetween('created_at', [$startDate, $endDate])
                ->latest()
                ->get();

            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();

            // Set headers
            $sheet->setCellValue('A1', 'Riwayat Penjualan (Lunas)'); // Ubah judul
            $sheet->mergeCells('A1:G1');
            $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
            $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

            // Headers
            $headers = ['No', 'Tanggal', 'No. Invoice', 'Kasir', 'Metode Pembayaran', 'Status', 'Total'];
            foreach (range('A', 'G') as $index => $column) {
                $sheet->setCellValue($column . '4', $headers[$index]);
                $sheet->getColumnDimension($column)->setAutoSize(true);
            }

            // Style header row
            $headerStyle = [
                'font' => ['bold' => true],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                'borders' => [
                    'allBorders' => ['borderStyle' => Border::BORDER_THIN]
                ],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['rgb' => 'E2E8F0']
                ]
            ];
            $sheet->getStyle('A4:G4')->applyFromArray($headerStyle);

            // Populate data
            $row = 5;
            foreach ($riwayat as $index => $item) {
                $sheet->setCellValue('A' . $row, $index + 1);
                $sheet->setCellValue('B' . $row, $item->created_at->format('d/m/Y H:i'));
                $sheet->setCellValue('C' . $row, $item->nomor_invoice);
                $sheet->setCellValue('D' . $row, $item->user->name);
                $sheet->setCellValue('E' . $row, $item->metode_pembayaran);
                $sheet->setCellValue('F' . $row, 'Lunas');
                $sheet->setCellValue('G' . $row, number_format($item->total_harga, 0, ',', '.'));

                // Apply borders to cells
                $sheet->getStyle('A' . $row . ':G' . $row)->applyFromArray([
                    'borders' => [
                        'allBorders' => ['borderStyle' => Border::BORDER_THIN]
                    ]
                ]);

                $row++;
            }

            // Total row
            $sheet->setCellValue('A' . $row, 'Total Penjualan');
            $sheet->mergeCells('A' . $row . ':F' . $row);
            $sheet->setCellValue('G' . $row, number_format($riwayat->sum('total_harga'), 0, ',', '.'));
            $sheet->getStyle('A' . $row . ':G' . $row)->applyFromArray([
                'font' => ['bold' => true],
                'borders' => [
                    'allBorders' => ['borderStyle' => Border::BORDER_THIN]
                ],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['rgb' => 'E2E8F0']
                ]
            ]);

            // Create Excel file
            $writer = new Xlsx($spreadsheet);
            $filename = 'riwayat_penjualan_lunas_' . Carbon::now()->format('Y-m-d_His') . '.xlsx';

            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment;filename="' . $filename . '"');
            header('Cache-Control: max-age=0');

            $writer->save('php://output');
            exit;
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan saat mengexport data: ' . $e->getMessage());
        }
    }

    public function show(Penjualan $penjualan)
    {
        $penjualan->load(['user', 'detail.produk']);
        return response()->json($penjualan);
    }
}