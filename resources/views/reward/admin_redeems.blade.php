@extends('layouts.app')

@section('title', 'Laporan Redeem')

@section('content')
<div class="container my-5">
    <div class="section-card mb-4">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center">
            <div>
                <h3 class="mb-1">Laporan Redeem Hadiah</h3>
                <small class="text-muted">Data akun, alamat, nomor telp, dan hadiah yang diredeem</small>
            </div>
            <a href="{{ route('admin.redeems.export') }}" class="btn btn-contact mt-3 mt-md-0">
                Download Excel
            </a>
        </div>
    </div>

    <div class="section-card">
        <div class="table-responsive">
            <table class="table table-transparent align-middle mb-0">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama Akun</th>
                        <th>Email</th>
                        <th>Nomor Telp</th>
                        <th>Alamat</th>
                        <th>Hadiah</th>
                        <th>Tanggal Redeem</th>
                        <th>Status</th>
                        <th>Bukti Terima</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($redeems as $index => $row)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $row->nama_akun }}</td>
                            <td>{{ $row->email_client }}</td>
                            <td>{{ $row->phone }}</td>
                            <td>{{ $row->address }}</td>
                            <td>{{ $row->prize_name }}</td>
                            <td>{{ \Carbon\Carbon::parse($row->created_at)->format('d M Y') }}</td>
                            <td>
                                @if($row->shipped_at)
                                    <span class="badge bg-success">Terkirim</span>
                                @else
                                    <span class="badge bg-warning text-dark">Belum Dikirim</span>
                                @endif
                            </td>
                            <td>
                                @if($row->proof_path)
                                    <a class="btn btn-outline-light btn-sm" href="{{ \Illuminate\Support\Facades\Storage::url($row->proof_path) }}" target="_blank">
                                        Lihat
                                    </a>
                                @else
                                    <span class="text-muted">Belum ada</span>
                                @endif
                            </td>
                            <td>
                                @if(!$row->shipped_at)
                                    <form method="POST" action="{{ route('admin.redeems.ship', $row->id) }}">
                                        @csrf
                                        <button type="submit" class="btn btn-contact btn-sm">Sudah Dikirim</button>
                                    </form>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="text-center text-muted">Belum ada data redeem</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
