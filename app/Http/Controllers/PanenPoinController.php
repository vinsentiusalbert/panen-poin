<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\UserPanenPoin;
use App\Models\User;
use App\Models\Prize;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class PanenPoinController extends Controller
{
    // Tampilkan halaman input data
    public function index()
    {
        // logUserLogin();
        return view('panenpoin.inputdatapoin');
    }
    
    // Simpan data panen poin
    public function store(Request $request)
    {
        $request->validate([
            'nama_pelanggan' => 'required|string|max:255',
            'akun_myads_pelanggan' => 'required|max:255',
            'nomor_hp_pelanggan' => 'required|string|max:20',
        ]);
        
        try {
            UserPanenPoin::create([
                'user_id' => Auth::id(),
                'nama_pelanggan' => $request->nama_pelanggan,
                'akun_myads_pelanggan' => strtolower($request->akun_myads_pelanggan),
                'nomor_hp_pelanggan' => $request->nomor_hp_pelanggan,
            ]);
            
            return redirect()->route('panenpoin.index')
                ->with('success', 'Data pelanggan berhasil disimpan!');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Gagal menyimpan data: ' . $e->getMessage())
                ->withInput();
        }
    }
    
    // Tampilkan halaman report
    public function report()
    {
        logUserLogin();
        $months = [];

        $currentYear = Carbon::now()->year;
        $currentMonth = Carbon::now()->format('Y-m-01'); // bulan sekarang, tanggal 01

        for ($i = 1; $i <= 12; ++$i) {
            $date = Carbon::create($currentYear, $i, 1);
            $months[] = [
                'value' => $date->format('Y-m-d'), // e.g., 2025-05-01
                'label' => $date->translatedFormat('F Y'), // e.g., Mei 2025
                'selected' => $date->format('Y-m-d') === $currentMonth,
            ];
        }
        return view('panenpoin.reportpoin', compact('months'));
    }
    
    // Get data untuk DataTable
    public function getReportData(Request $request)
    {  
        \Log::info('=== GET REPORT DATA CALLED ===');
        // \Log::info('User: ' . Auth::user()->name);
        \Log::info('Request URI: ' . $request->getRequestUri());
        \Log::info('Filter Tanggal: ' . $request->tanggal);
        
        try {
            $user = auth()->user();
            \Log::info('Starting calculatePanenPoinData...');
            $data = $this->calculatePanenPoinData($request->tanggal);
            $prizes = Prize::orderBy('point', 'desc')->get();
            if ($user) {
            $point = DB::table('summary_panen_poin')
                ->select(
                    'nama_canvasser',
                    'email_client',
                    'nomor_hp_client',
                    DB::raw('CAST(total_settlement AS DECIMAL(15,2)) as total_settlement_raw'),
                    DB::raw('FORMAT(total_settlement, 0, "id_ID") as total_settlement'),
                    'poin_bulan_ini',
                    'poin_akumulasi',
                    'poin',
                    'bulan'
                )->where('email_client', '=', Auth::user()->email_client)->first();
            } else {
                $point = 0;
            }
            $redeem = DB::table('prize_redeems')->where('user_id', auth()->id())->first();

            $redeemedPrizeId = $redeem?->prize_id; // null kalau belum redeem
            $hasRedeemed = (bool) $redeem;
            return view('reward.index', compact('data', 'point','prizes', 'hasRedeemed', 'redeemedPrizeId'));
                
        } catch (\Exception $e) {
            \Log::error("Error in getReportData: " . $e->getMessage());
            \Log::error($e->getTraceAsString());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
    
    // Hitung data panen poin (ambil dari summary table)
    private function calculatePanenPoinData($tanggal = null)
    {
        try {
            \Log::info("=== READING FROM SUMMARY TABLE ===");

            $baseQuery = DB::table('summary_panen_poin as s')
                ->join('akun_panen_poin as u', 'u.email_client', '=', 's.email_client')
                ->select(
                    's.nama_canvasser',
                    's.email_client',
                    's.nomor_hp_client',
                    DB::raw('CAST(s.total_settlement AS DECIMAL(15,2)) as total_settlement_raw'),
                    's.poin_bulan_ini',
                    's.poin_akumulasi',
                    's.poin',
                    's.bulan',
                    'u.uuid',
                    'u.nama_akun',
                    // 'u.akun_myads_pelanggan',
                    // 'u.nomor_hp_pelanggan',
                    // 'u.nama_pelanggan'
                );

            // Filter bulan
            if ($tanggal) {
                $date = Carbon::parse($tanggal);
                $baseQuery->whereMonth('s.created_at', $date->month)
                        ->whereYear('s.created_at', $date->year);
            }

            // Helper mapper
            $mapResult = function ($query) {
                return $query->orderBy('s.poin', 'desc')
                    ->limit(10)
                    ->get()
                    ->map(function ($item) {
                        return [
                            'nama_canvasser' => $item->nama_canvasser,
                            'email_client' => $item->email_client,
                            'nomor_hp_client' => $item->nomor_hp_client,
                            'total_settlement' => number_format($item->total_settlement_raw, 0, ',', '.'),
                            'total_settlement_raw' => $item->total_settlement_raw,
                            'poin_bulan_ini' => $item->poin_bulan_ini,
                            'poin_akumulasi' => $item->poin_akumulasi,
                            'poin' => $item->poin,
                            'bulan' => $item->bulan,
                            'uuid' => $item->uuid,
                            'nama_akun' => $item->nama_akun,
                            // 'akun_myads_pelanggan' => $item->akun_myads_pelanggan,
                            // 'nomor_hp_pelanggan' => $item->nomor_hp_pelanggan,
                            // 'nama_pelanggan' => $item->nama_pelanggan,
                        ];
                    })
                    ->toArray();
            };

            $result = [
                'poin_0_100' => $mapResult(
                    (clone $baseQuery)->whereBetween('s.poin', [0, 100])
                ),
                'poin_101_200' => $mapResult(
                    (clone $baseQuery)->whereBetween('s.poin', [101, 200])
                ),
                'poin_201_300' => $mapResult(
                    (clone $baseQuery)->whereBetween('s.poin', [201, 300])
                ),
            ];

            \Log::info("Top 10 results generated per poin range");

            return $result;

        } catch (\Exception $e) {
            \Log::error("Error in calculatePanenPoinData: " . $e->getMessage());
            \Log::error($e->getTraceAsString());
            return [];
        }
    }

    
    // Export ke Excel
    public function export(Request $request)
    {
        try {
            $data = $this->calculatePanenPoinData($request->tanggal);
            
            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();
            
            // Header
            $monthYear = Carbon::now()->locale('id')->translatedFormat('F Y');
            $sheet->setCellValue('A1', 'LAPORAN PANEN POIN - ' . strtoupper($monthYear));
            $sheet->mergeCells('A1:F1');
            $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
            $sheet->getStyle('A1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
            
            // Column headers
            $sheet->setCellValue('A3', 'No');
            $sheet->setCellValue('B3', 'Nama Canvasser');
            $sheet->setCellValue('C3', 'Email Client');
            $sheet->setCellValue('D3', 'Nomor HP Client');
            $sheet->setCellValue('E3', 'Total Settlement');
            $sheet->setCellValue('F3', 'Poin');
            
            $sheet->getStyle('A3:F3')->getFont()->setBold(true);
            $sheet->getStyle('A3:F3')->getFill()
                ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                ->getStartColor()->setARGB('FFD9D9D9');
            
            // Data
            $row = 4;
            $no = 1;
            foreach ($data as $item) {
                $sheet->setCellValue('A' . $row, $no++);
                $sheet->setCellValue('B' . $row, $item['nama_canvasser']);
                $sheet->setCellValue('C' . $row, $item['email_client']);
                $sheet->setCellValue('D' . $row, $item['nomor_hp_client']);
                $sheet->setCellValue('E' . $row, $item['total_settlement']);
                $sheet->setCellValue('F' . $row, $item['poin']);
                $row++;
            }
            
            // Auto width
            foreach (range('A', 'F') as $col) {
                $sheet->getColumnDimension($col)->setAutoSize(true);
            }
            
            // Download
            $fileName = 'Laporan_Panen_Poin_' . $monthYear . '.xlsx';
            $writer = new Xlsx($spreadsheet);
            
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment;filename="' . $fileName . '"');
            header('Cache-Control: max-age=0');
            
            $writer->save('php://output');
            exit;
            
        } catch (\Exception $e) {
            \Log::error("Error in export: " . $e->getMessage());
            return redirect()->back()->with('error', 'Gagal export data: ' . $e->getMessage());
        }
    }
    
    // Refresh Summary Panen Poin (untuk di-schedule)
    public function refreshSummaryPanenPoin()
    {
        try {
            \Log::info('=== REFRESH SUMMARY PANEN POIN STARTED ===');
            
            // Tentukan range tanggal bulan berjalan
            $startDate = Carbon::now()->startOfMonth()->format('Y-m-d');
            $endDate = Carbon::now()->endOfMonth()->format('Y-m-d');
            
            // Ambil semua canvasser
            $canvassers = User::where('role', 'cvsr')->get();
            
            $totalProcessed = 0;
            
            // Hapus data summary bulan ini dulu
            DB::table('summary_panen_poin')->truncate();
            
            foreach ($canvassers as $canvasser) {
                // Ambil email dari user_panen_poin yang diinput oleh canvasser ini
                $panenPoinData = UserPanenPoin::where('user_id', $canvasser->id)
                    ->select('akun_myads_pelanggan', 'nomor_hp_pelanggan')
                    ->get();
                
                $clientEmails = [];
                
                if ($panenPoinData->isNotEmpty()) {
                    foreach ($panenPoinData as $data) {
                        $clientEmails[] = [
                            'email' => strtolower(trim($data->akun_myads_pelanggan)),
                            'nomor_hp' => $data->nomor_hp_pelanggan
                        ];
                    }
                } else {
                    $leadsData = DB::table('leads_master')
                        ->where('user_id', $canvasser->id)
                        ->select('email', 'mobile_phone')
                        ->get();
                    
                    foreach ($leadsData as $lead) {
                        $clientEmails[] = [
                            'email' => strtolower(trim($lead->email)),
                            'nomor_hp' => $lead->mobile_phone ?? '-'
                        ];
                    }
                }
                
                if (empty($clientEmails)) {
                    continue;
                }
                
                $emails = array_column($clientEmails, 'email');
                
                // Query settlement bulan ini
                $settlementsThisMonth = DB::table('report_balance_top_up')
                    ->select(DB::raw('LOWER(TRIM(email_client)) as email'), DB::raw('SUM(CAST(total_settlement_klien AS DECIMAL(15,2))) as total'))
                    ->whereBetween('tgl_transaksi', [$startDate, $endDate])
                    ->whereNotNull('total_settlement_klien')
                    ->whereIn(DB::raw('LOWER(TRIM(email_client))'), $emails)
                    ->groupBy(DB::raw('LOWER(TRIM(email_client))'))
                    ->pluck('total', 'email')
                    ->toArray();
                
                // Query settlement akumulasi
                $settlementsAccumulated = [];
                $currentMonth = Carbon::now()->month;
                if ($currentMonth > 1) {
                    $startYearDate = Carbon::now()->startOfYear()->format('Y-m-d');
                    $endPreviousMonth = Carbon::now()->subMonth()->endOfMonth()->format('Y-m-d');
                    
                    $settlementsAccumulated = DB::table('report_balance_top_up')
                        ->select(DB::raw('LOWER(TRIM(email_client)) as email'), DB::raw('SUM(CAST(total_settlement_klien AS DECIMAL(15,2))) as total'))
                        ->whereBetween('tgl_transaksi', [$startYearDate, $endPreviousMonth])
                        ->whereNotNull('total_settlement_klien')
                        ->whereIn(DB::raw('LOWER(TRIM(email_client))'), $emails)
                        ->groupBy(DB::raw('LOWER(TRIM(email_client))'))
                        ->pluck('total', 'email')
                        ->toArray();
                }
                
                // Insert ke summary table
                foreach ($clientEmails as $client) {
                    $email = $client['email'];
                    $totalSettlement = $settlementsThisMonth[$email] ?? 0;
                    $settlementPrevious = $settlementsAccumulated[$email] ?? 0;
                    
                    if ($totalSettlement == 0 && $settlementPrevious == 0) {
                        continue;
                    }
                    
                    $poinBulanIni = floor($totalSettlement / 250000);
                    $poinAkumulasi = floor($settlementPrevious / 250000);
                    $totalPoin = $poinBulanIni + $poinAkumulasi;
                    
                    DB::table('summary_panen_poin')->insert([
                        'user_id' => $canvasser->id,
                        'nama_canvasser' => $canvasser->name,
                        'email_client' => $email,
                        'nomor_hp_client' => $client['nomor_hp'],
                        'total_settlement' => $totalSettlement,
                        'poin_bulan_ini' => $poinBulanIni,
                        'poin_akumulasi' => $poinAkumulasi,
                        'poin' => $totalPoin,
                        'bulan' => Carbon::now()->locale('id')->translatedFormat('F Y'),
                        'created_at' => now(),
                        'updated_at' => now()
                    ]);
                    
                    $totalProcessed++;
                }
            }
            
            \Log::info("Summary Panen Poin refreshed. Total records: {$totalProcessed}");
            
            return response()->json([
                'status' => 'success',
                'message' => "Summary Panen Poin updated. Total records: {$totalProcessed}"
            ]);
            
        } catch (\Exception $e) {
            \Log::error("Error in refreshSummaryPanenPoin: " . $e->getMessage());
            \Log::error($e->getTraceAsString());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function redeemPrize(Request $request)
    {
        $request->validate([
            'prize_id' => 'required|integer|exists:prizes,id',
        ]);

        $user = auth()->user();

        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'Silakan login terlebih dahulu'
            ], 401);
        }
        // dd($user->email_client);
        try {
            return DB::transaction(function () use ($request, $user) {

                // Cek sudah pernah redeem
                $alreadyRedeem = DB::table('prize_redeems')
                    ->where('user_id', $user->id)
                    ->exists();

                if ($alreadyRedeem) {
                    return response()->json([
                        'status' => false,
                        'message' => 'Anda sudah pernah redeem hadiah'
                    ], 403);
                }

                $prize = Prize::lockForUpdate()->findOrFail($request->prize_id);

                // Ambil poin user
                $userPointRecord = DB::table('summary_panen_poin')
                    ->where('email_client', $user->email_client)
                    ->first();

                if (!$userPointRecord || $userPointRecord->poin < $prize->point) {
                    return response()->json([
                        'status' => false,
                        'message' => 'Poin tidak cukup untuk menukar hadiah ini'
                    ], 400);
                }

                if ($prize->stock <= 0) {
                    return response()->json([
                        'status' => false,
                        'message' => 'Stok hadiah habis'
                    ], 400);
                }

                // Kurangi stok
                $prize->decrement('stock');

                // Simpan log redeem
                DB::table('prize_redeems')->insert([
                    'user_id' => $user->id,
                    'prize_id' => $prize->id,
                    'point_used' => $prize->point,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
                $this->updateSummaryAfterRedeem($user->id);
                return response()->json([
                    'status' => true,
                    'message' => 'Hadiah berhasil ditukar'
                ]);
            });

        } catch (\Exception $e) {
            \Log::error('Redeem Error: ' . $e->getMessage());
            dd($e->getMessage());
            return response()->json([
                'status' => false,
                'message' => 'Terjadi kesalahan sistem'
            ], 500);
        }
    }


    // Update summary setelah redeem (dipanggil dari RedeemController)
    public function updateSummaryAfterRedeem($userId)
    {
        try {
            \Log::info("=== UPDATE SUMMARY AFTER REDEEM FOR USER: {$userId} ===");
            
            $currentMonth = Carbon::now()->month;
            $currentYear = Carbon::now()->year;
            
            // Hitung total poin yang sudah di-redeem user ini bulan ini
            $totalPoinRedeem = DB::table('prize_redeems')
                ->where('user_id', $userId)
                ->whereMonth('created_at', $currentMonth)
                ->whereYear('created_at', $currentYear)
                ->sum('point_used') ?? 0;
            
            \Log::info("Total poin redeem for user {$userId}: {$totalPoinRedeem}");
            
            // Update semua record summary user ini di bulan ini
            $summaries = DB::table('summary_panen_poin')
                ->where('user_id', $userId)
                ->whereMonth('created_at', $currentMonth)
                ->whereYear('created_at', $currentYear)
                ->get();
            
            $updatedCount = 0;
            foreach ($summaries as $summary) {
                $poinSisa = $summary->poin - $totalPoinRedeem;
                $remark = $this->calculateRemark($poinSisa);
                
                DB::table('summary_panen_poin')
                    ->where('id', $summary->id)
                    ->update([
                        'poin_redeem' => $totalPoinRedeem,
                        'remark' => $remark,
                        'updated_at' => now()
                    ]);
                
                $updatedCount++;
            }
            
            \Log::info("Updated {$updatedCount} summary records after redeem");
            
            return [
                'success' => true,
                'updated' => $updatedCount,
                'total_redeem' => $totalPoinRedeem
            ];
            
        } catch (\Exception $e) {
            \Log::error("Error in updateSummaryAfterRedeem: " . $e->getMessage());
            \Log::error($e->getTraceAsString());
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }
    // Hitung remark berdasarkan poin sisa
    private function calculateRemark($poinSisa)
    {
        if ($poinSisa >= 0 && $poinSisa <= 100) {
            return 'Rookie';
        } elseif ($poinSisa >= 101 && $poinSisa <= 200) {
            return 'Rising Star';
        } elseif ($poinSisa >= 201) {
            return 'Champion';
        }
        return 'Rookie'; // default
    }
}
