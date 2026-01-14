<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class RewardController extends Controller
{
    public function index()
    {
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
                )->where('email_client', '=', Auth::user()->email)->first();
                dd($point);
        return view('reward.index', ['point' => $point]);
    }
}
