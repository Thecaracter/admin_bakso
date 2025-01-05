<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Print Riwayat Penjualan</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 30px;
            font-size: 14px;
            background: #fff;
            color: #374151;
        }

        .header {
            text-align: center;
            margin-bottom: 40px;
            padding-bottom: 20px;
            border-bottom: 2px solid #F3F4F6;
        }

        .logo-section {
            margin-bottom: 20px;
        }

        .logo-section .icon {
            width: 50px;
            height: 50px;
            background: #FDE68A;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 15px;
            color: #D97706;
        }

        .header h1 {
            margin: 0;
            font-size: 28px;
            margin-bottom: 10px;
            color: #1F2937;
        }

        .header p {
            margin: 0;
            color: #6B7280;
            font-size: 15px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
            background: #fff;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
            overflow: hidden;
        }

        th,
        td {
            padding: 12px 16px;
            text-align: left;
            border-bottom: 1px solid #F3F4F6;
        }

        th {
            background-color: #FEF3C7;
            font-weight: 600;
            color: #1F2937;
            font-size: 13px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        tr:hover {
            background-color: #FAFAFA;
        }

        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        .total-row {
            font-weight: 600;
            background-color: #FEF3C7;
            color: #1F2937;
        }

        .badge {
            padding: 6px 12px;
            border-radius: 9999px;
            font-size: 12px;
            font-weight: 600;
            display: inline-block;
            text-align: center;
        }

        .badge-pending {
            background-color: #FEF3C7;
            color: #92400E;
        }

        .badge-success {
            background-color: #D1FAE5;
            color: #065F46;
        }

        .badge-danger {
            background-color: #FEE2E2;
            color: #991B1B;
        }

        .print-button {
            padding: 10px 20px;
            background: #F59E0B;
            color: white;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 600;
            font-size: 14px;
            transition: background-color 0.2s;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        }

        .print-button:hover {
            background: #D97706;
        }

        @media print {
            @page {
                margin: 20mm;
                size: A4;
            }

            body {
                padding: 0;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }

            .no-print {
                display: none;
            }

            table {
                box-shadow: none;
            }

            th {
                background-color: #FEF3C7 !important;
            }

            .badge-pending {
                background-color: #FEF3C7 !important;
                color: #92400E !important;
            }

            .badge-success {
                background-color: #D1FAE5 !important;
                color: #065F46 !important;
            }

            .badge-danger {
                background-color: #FEE2E2 !important;
                color: #991B1B !important;
            }

            .total-row {
                background-color: #FEF3C7 !important;
            }
        }
    </style>
</head>

<body>
    <div class="header">
        <div class="logo-section">
            <div class="icon">
                <svg width="24" height="24" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                </svg>
            </div>
        </div>
        <h1>Riwayat Penjualan</h1>
        <p>Periode: {{ Carbon\Carbon::parse($startDate)->format('d/m/Y') }} -
            {{ Carbon\Carbon::parse($endDate)->format('d/m/Y') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 5%;">No</th>
                <th style="width: 15%;">No. Invoice</th>
                <th style="width: 20%;">Tanggal</th>
                <th style="width: 20%;">Kasir</th>
                <th style="width: 15%;">Metode Pembayaran</th>
                <th style="width: 10%;" class="text-center">Status</th>
                <th style="width: 15%;" class="text-right">Total</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($riwayat as $index => $item)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>{{ $item->nomor_invoice }}</td>
                    <td>{{ $item->created_at->format('d/m/Y H:i') }}</td>
                    <td>{{ $item->user->name }}</td>
                    <td>{{ $item->metode_pembayaran }}</td>
                    <td class="text-center">
                        @if ($item->status === 'pending')
                            <span class="badge badge-pending">Pending</span>
                        @elseif ($item->status === 'lunas')
                            <span class="badge badge-success">Lunas</span>
                        @else
                            <span class="badge badge-danger">Batal</span>
                        @endif
                    </td>
                    <td class="text-right">Rp {{ number_format($item->total_harga, 0, ',', '.') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="text-center" style="padding: 30px;">
                        <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="#9CA3AF"
                            stroke-width="2" class="mx-auto mb-3">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 10h16M4 14h16M4 18h16" />
                        </svg>
                        <p style="color: #4B5563; margin-bottom: 4px;">Belum ada data riwayat</p>
                        <p style="color: #6B7280; font-size: 12px;">Coba ubah filter tanggal untuk melihat data lainnya
                        </p>
                    </td>
                </tr>
            @endforelse
        </tbody>
        @if ($riwayat->isNotEmpty())
            <tfoot>
                <tr class="total-row">
                    <td colspan="6" class="text-right">Total Penjualan:</td>
                    <td class="text-right">Rp {{ number_format($riwayat->sum('total_harga'), 0, ',', '.') }}</td>
                </tr>
            </tfoot>
        @endif
    </table>

    <button class="no-print print-button" onclick="window.print()">
        Cetak Laporan
    </button>

    <script>
        window.onload = function() {
            window.print();
        }
    </script>
</body>

</html>
