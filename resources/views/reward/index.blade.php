@extends('layouts.app')

@section('title', 'MyAds Reward League')

@push('styles')
<style>
/* ==== PASTE SELURUH CSS DARI HTML KAMU DI SINI ==== */
body {
    background: radial-gradient(circle at top, #0f4c5c, #062c36);
    color: #fff;
    font-family: 'Segoe UI', sans-serif;
}
/* ... lanjutkan sampai habis */
</style>
@endpush

@section('content')
<div class="container my-5">
@php
    $user = auth()->user();
    $latestShippingProofUrl = $latestRedeem && $latestRedeem->shipping_proof_path
        ? asset('storage/'.$latestRedeem->shipping_proof_path)
        : null;
    $latestShippingProofExtension = $latestRedeem && $latestRedeem->shipping_proof_path
        ? strtolower(pathinfo($latestRedeem->shipping_proof_path, PATHINFO_EXTENSION))
        : null;
    $latestShippingProofIsImage = in_array($latestShippingProofExtension, ['jpg', 'jpeg', 'png']);
@endphp

{{-- ================= LIGA ================= --}}
@if(isset($point) && is_object($point) && isset($point->poin))
<div class="section-card text-center mb-5">
    <h2 class="mb-4">Badges</h2>

    {{-- <div class="row justify-content-center g-4">
        <div class="col-md-4 liga-card">
            <img src="{{ asset('img/rookie.png') }}">
            <h5>Rookie</h5>
            <span class="liga-range">0 – 100 Poin</span>
        </div>
        <div class="col-md-4 liga-card">
            <img src="{{ asset('img/rising_star.png') }}">
            <h5>Rising Star</h5>
            <span class="liga-range">101 – 200 Poin</span>
        </div>
        <div class="col-md-4 liga-card">
            <img src="{{ asset('img/champion.png') }}">
            <h5>Champion</h5>
            <span class="liga-range">201 – 300 Poin</span>
        </div>
    </div> --}}
    
        <div class="mt-4">
            @php
                $percent = min(($point->poin / 300) * 100, 100);
            @endphp
            <div class="row justify-content-center g-4">
                @if($point->poin >= 0 && $point->poin <= 100)
                <div class="col-md-4 liga-card">
                    <img src="{{ asset('img/rookie.png') }}">
                    <h5>Rookie</h5>
                    
                </div>
                @elseif($point->poin >= 101 && $point->poin <= 200)
                <div class="col-md-4 liga-card">
                    <img src="{{ asset('img/rising_star.png') }}">
                    <h5>Rising Star</h5>
                    
                </div>
                @elseif($point->poin >= 201)
                <div class="col-md-4 liga-card">
                    <img src="{{ asset('img/champion.png') }}">
                    <h5>Champion</h5>
                    
                </div>
                @endif
            </div>
            <div class="progress">
                <div 
                    class="progress-bar progress-animate"
                    data-percent="{{ $percent }}"
                    style="width: 0%">
                </div>
            </div>

            <small>Total Poin Anda: <b>{{ $point->poin }}</b></small>

            @if($user)
                <div class="mt-3">
                    <button type="button" class="btn btn-contact fw-semibold" data-bs-toggle="modal" data-bs-target="#contactInfoModal">
                        Isi Alamat Pengiriman
                    </button>
                </div>
                @if($userContactInfos->isNotEmpty())
                    @php
                        $latestContact = $userContactInfos->first();
                    @endphp
                    <small class="contact-summary d-block mt-2">
                        Kontak tersimpan: <span>{{ $latestContact->phone }}</span> | <span>{{ $latestContact->address }}</span>
                    </small>
                    @if($latestRedeem && $latestRedeem->shipped_at)
                        <div class="shipping-status-card mt-3 text-start mx-auto">
                            <small class="contact-summary d-block">Hadiah sudah terkirim</small>
                            <div class="shipping-status-date">
                                Tanggal kirim:
                                <span>{{ \Carbon\Carbon::parse($latestRedeem->shipped_at)->format('d M Y') }}</span>
                            </div>
                            @if($latestShippingProofUrl)
                                @if($latestShippingProofIsImage)
                                    <a href="{{ $latestShippingProofUrl }}" target="_blank" class="shipping-proof-link">
                                        <img
                                            src="{{ $latestShippingProofUrl }}"
                                            alt="Bukti pengiriman hadiah"
                                            class="shipping-proof-image mt-3"
                                        >
                                    </a>
                                @else
                                    <a class="btn btn-outline-light btn-sm mt-3" href="{{ $latestShippingProofUrl }}" target="_blank">
                                        Lihat Bukti Pengiriman
                                    </a>
                                @endif
                            @endif
                        </div>
                    @endif
                    <div class="mt-3">
                        @if($latestRedeemProof && $latestRedeemProof->proof_path)
                            <small class="contact-summary d-block">
                                Bukti terima sudah diupload.
                            </small>
                            <a class="btn btn-outline-light btn-sm mt-2" href="{{ asset('storage/'.$latestRedeemProof->proof_path) }}" target="_blank">
                                Lihat Bukti
                            </a>
                            <form method="POST" action="{{ route('redeem.proof') }}" enctype="multipart/form-data" class="d-flex flex-column align-items-center gap-2 mt-2">
                                @csrf
                                <input type="file" name="proof" class="form-control contact-input" accept=".jpg,.jpeg,.png,.pdf" required>
                                <button type="submit" class="btn btn-contact">Ganti Bukti</button>
                            </form>
                        @else
                            <form method="POST" action="{{ route('redeem.proof') }}" enctype="multipart/form-data" class="d-flex flex-column align-items-center gap-2">
                                @csrf
                                <input type="file" name="proof" class="form-control contact-input" accept=".jpg,.jpeg,.png,.pdf" required>
                                <button type="submit" class="btn btn-contact">Upload Bukti Terima</button>
                            </form>
                        @endif
                    </div>
                @else
                    <small class="contact-summary d-block mt-2">Belum ada data kontak tersimpan.</small>
                @endif
            @else
                <small class="contact-summary d-block mt-3">Login untuk mengisi data kontak.</small>
            @endif
        </div>

</div>

    @endif

@if($user)
<div class="section-card mb-5">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-2 mb-4">
        <div>
            <h4 class="mb-1">Riwayat Redeem Saya</h4>
            <small class="contact-summary">Cek reward yang sudah pernah diredeem dan lihat bukti kirimnya di sini.</small>
        </div>
        <span class="history-counter">{{ $userRedeemHistory->count() }} Redeem</span>
    </div>

    @if($userRedeemHistory->isNotEmpty())
        <div class="row g-4">
            @foreach($userRedeemHistory as $redeem)
                @php
                    $historyShippingProofUrl = $redeem->shipping_proof_path
                        ? asset('storage/'.$redeem->shipping_proof_path)
                        : null;
                    $historyShippingProofExtension = $redeem->shipping_proof_path
                        ? strtolower(pathinfo($redeem->shipping_proof_path, PATHINFO_EXTENSION))
                        : null;
                    $historyShippingProofIsImage = in_array($historyShippingProofExtension, ['jpg', 'jpeg', 'png']);
                @endphp
                <div class="col-lg-6">
                    <div class="redeem-history-card h-100">
                        <div class="redeem-history-head">
                            <div class="redeem-history-prize">
                                <div class="redeem-history-thumb">
                                    <img src="{{ asset('img/'.$redeem->prize_image) }}" alt="{{ $redeem->prize_name }}">
                                </div>
                                <div>
                                    <h5 class="mb-1">{{ $redeem->prize_name }}</h5>
                                    <div class="redeem-history-meta">{{ $redeem->prize_point }} poin</div>
                                </div>
                            </div>
                            <span class="redeem-status-badge {{ $redeem->shipped_at ? 'is-shipped' : 'is-pending' }}">
                                {{ $redeem->shipped_at ? 'Terkirim' : 'Diproses' }}
                            </span>
                        </div>

                        <div class="redeem-history-info">
                            <div class="redeem-history-line">
                                <span>Redeem</span>
                                <strong>{{ \Carbon\Carbon::parse($redeem->created_at)->format('d M Y') }}</strong>
                            </div>
                            <div class="redeem-history-line">
                                <span>Tanggal kirim</span>
                                <strong>{{ $redeem->shipped_at ? \Carbon\Carbon::parse($redeem->shipped_at)->format('d M Y') : '-' }}</strong>
                            </div>
                        </div>

                        <div class="redeem-history-proof mt-3">
                            <div class="redeem-history-proof-title">Bukti kirim</div>
                            @if($historyShippingProofUrl)
                                @if($historyShippingProofIsImage)
                                    <a href="{{ $historyShippingProofUrl }}" target="_blank" class="shipping-proof-link">
                                        <img
                                            src="{{ $historyShippingProofUrl }}"
                                            alt="Bukti kirim {{ $redeem->prize_name }}"
                                            class="shipping-proof-image"
                                        >
                                    </a>
                                @else
                                    <a class="btn btn-outline-light btn-sm mt-2" href="{{ $historyShippingProofUrl }}" target="_blank">
                                        Lihat Bukti Kirim
                                    </a>
                                @endif
                            @else
                                <div class="redeem-history-empty">Bukti kirim belum tersedia.</div>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="redeem-history-empty">
            Belum ada reward yang diredeem.
        </div>
    @endif
</div>
@endif

{{-- ================= TABLE ================= --}}
<div class="section-card mb-5">
    <h4 class="mb-3">TOP 10 Champion</h4>

    <div class="">
        <div class="row">
            <div class="col-md-3 liga-card text-center animate-left scroll-animate my-2">
                <img src="{{ asset('img/champion.png') }}">
                <h5>Champion</h5>
                <span class="liga-range">201 – 300 Poin</span>
            </div>
            <div class="col-md-9">
                <div class="table-glass liga-champion animate-right scroll-animate">
                    
                    <div class="table-scroll-x">
                    <table class="table table-transparent align-middle mb-0">
                    <thead>
                        <tr>
                            <th>No</th>
                            {{-- <th>ID</th> --}}
                            <th>Nama Akun</th>
                            <th>Nama Pelanggan</th>
                            <th>Canvasser</th>
                            <th>Total Poin</th>
                            <th>Kategori Liga</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($data['poin_201_300'] as $index => $row)

                            @php
                                // Tentukan kategori liga
                                if ($row['poin'] <= 100) {
                                    $liga = 'Rookie';
                                } elseif ($row['poin'] <= 200) {
                                    $liga = 'Rising Star';
                                } else {
                                    $liga = 'Champion';
                                }
                            @endphp

                            <tr>
                                @php
                                    $email = $row['email_client'];
                                    [$name, $domain] = explode('@', $email);

                                    $maskedName = substr($name, 0, 2)
                                        . str_repeat('*', max(strlen($name) - 2, 0));

                                    $maskedDomain = substr($domain, 0, 2)
                                        . str_repeat('*', max(strlen($domain) - 2, 0));
                                @endphp
                                <td>{{ $index + 1 }}</td>
                                {{-- <td>{{ $uuid }}</td> --}}
                                <td>{{
                                    substr($row['nama_akun'], 0, 2) . str_repeat('*', strlen($row['nama_akun']) - 2)
                                }}</td>
                                {{-- <td>{{$row['nama_pelanggan']}}</td> --}}
                                <td>{{ $maskedName .'@'. $maskedDomain }}</td>
                                
                                <td>{{ $row['nama_canvasser'] }}</td>
                                <td>
                                    <span class="fw-bold text-warning">
                                        {{ $row['poin'] }}
                                    </span>
                                </td>
                                <td style="text-align: center">
                                    <span class="badge-liga 
                                        {{ $liga == 'Rookie' ? 'bg-secondary' : '' }}
                                        {{ $liga == 'Rising Star' ? 'bg-info' : '' }}
                                        {{ $liga == 'Champion' ? 'bg-success' : '' }}
                                    ">
                                        {{ $liga }}
                                    </span>
                                </td>
                            </tr>

                        @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted">
                                Data belum tersedia
                            </td>
                        </tr>
                    @endforelse
                    </tbody>

                </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


<div class="section-card mb-5">
    <h4 class="mb-3">TOP 10 Rising Star</h4>

    <div class="">
        <div class="row">
            <div class="col-md-3 liga-card text-center animate-left scroll-animate my-2">
                <img src="{{ asset('img/rising_star.png') }}">
                <h5>Rising Star</h5>
                <span class="liga-range">101 – 200 Poin</span>
            </div>
            <div class="col-md-9">
                <div class="table-glass liga-rising  animate-right scroll-animate">
                    <div class="table-scroll-x">
                    <table class="table table-transparent align-middle mb-0">
                    <thead>
                        <tr>
                            <th>No</th>
                            {{-- <th>ID</th> --}}
                            <th>Nama Akun</th>
                            <th>Nama Pelanggan</th>
                            <th>Canvasser</th>
                            <th>Total Poin</th>
                            <th>Kategori Liga</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($data['poin_101_200'] as $index => $row)

                            @php
                                // Tentukan kategori liga
                                if ($row['poin'] <= 100) {
                                    $liga = 'Rookie';
                                } elseif ($row['poin'] <= 200) {
                                    $liga = 'Rising Star';
                                } else {
                                    $liga = 'Champion';
                                }
                            @endphp

                            <tr>
                                @php
                                    $email = $row['email_client'];
                                    [$name, $domain] = explode('@', $email);

                                    $maskedName = substr($name, 0, 2)
                                        . str_repeat('*', max(strlen($name) - 2, 0));

                                    $maskedDomain = substr($domain, 0, 2)
                                        . str_repeat('*', max(strlen($domain) - 2, 0));
                                @endphp
                                <td>{{ $index + 1 }}</td>
                                {{-- <td>{{ $uuid }}</td> --}}
                                <td>{{
                                    substr($row['nama_akun'], 0, 2) . str_repeat('*', strlen($row['nama_akun']) - 2)
                                }}</td>
                                {{-- <td>{{$row['nama_pelanggan']}}</td> --}}
                                <td>{{ $maskedName .'@'. $maskedDomain }}</td>
                                
                                <td>{{ $row['nama_canvasser'] }}</td>
                                <td>
                                    <span class="fw-bold text-warning">
                                        {{ $row['poin'] }}
                                    </span>
                                </td>
                                <td style="text-align: center">
                                    <span class="badge-liga 
                                        {{ $liga == 'Rookie' ? 'bg-secondary' : '' }}
                                        {{ $liga == 'Rising Star' ? 'bg-info' : '' }}
                                        {{ $liga == 'Champion' ? 'bg-success' : '' }}
                                    ">
                                        {{ $liga }}
                                    </span>
                                </td>
                            </tr>

                        @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted">
                                Data belum tersedia
                            </td>
                        </tr>
                    @endforelse
                    </tbody>

                </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="section-card mb-5">
    <h4 class="mb-3">TOP 10 Rookie</h4>

    {{-- <div class="table-responsive table-scroll"> --}}
    <div>
        <div class="row">
            <div class="col-md-3 liga-card text-center animate-left scroll-animate my-2">
                <img src="{{ asset('img/rookie.png') }}">
                <h5>Rookie</h5>
                <span class="liga-range">0 – 100 Poin</span>
            </div>
            <div class="col-md-9">
                <div class="table-glass liga-rookie animate-right scroll-animate">
                    <div class="table-scroll-x">
                    <table class="table table-transparent align-middle mb-0">
                    <thead>
                        <tr>
                            <th>No</th>
                            {{-- <th>ID</th> --}}
                            <th>Nama Akun</th>
                            <th>Nama Pelanggan</th>
                            <th>Canvasser</th>
                            <th>Total Poin</th>
                            <th>Kategori Liga</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($data['poin_0_100'] as $index => $row)

                            @php
                                // Tentukan kategori liga
                                if ($row['poin'] <= 100) {
                                    $liga = 'Rookie';
                                } elseif ($row['poin'] <= 200) {
                                    $liga = 'Rising Star';
                                } else {
                                    $liga = 'Champion';
                                }
                            @endphp

                            <tr>
                                @php
                                    $email = $row['email_client'];
                                    [$name, $domain] = explode('@', $email);

                                    $maskedName = substr($name, 0, 2)
                                        . str_repeat('*', max(strlen($name) - 2, 0));

                                    $maskedDomain = substr($domain, 0, 2)
                                        . str_repeat('*', max(strlen($domain) - 2, 0));
                                @endphp
                                <td>{{ $index + 1 }}</td>
                                {{-- <td>{{ $row['uuid'] }}</td> --}}
                                <td>{{
                                    substr($row['nama_akun'], 0, 2) . str_repeat('*', strlen($row['nama_akun']) - 2)
                                }}</td>
                                <td>{{ $maskedName .'@'. $maskedDomain }}</td>
                                
                                <td>{{ $row['nama_canvasser'] }}</td>
                                <td>
                                    <span class="fw-bold text-warning">
                                        {{ $row['poin'] }}
                                    </span>
                                </td>
                                <td style="text-align: center">
                                    <span class="badge-liga 
                                        {{ $liga == 'Rookie' ? 'bg-secondary' : '' }}
                                        {{ $liga == 'Rising Star' ? 'bg-info' : '' }}
                                        {{ $liga == 'Champion' ? 'bg-success' : '' }}
                                    ">
                                        {{ $liga }}
                                    </span>
                                </td>
                            </tr>

                        @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted">
                                Data belum tersedia
                            </td>
                        </tr>
                    @endforelse
                    </tbody>

                </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>



{{-- ================= PRIZE ================= --}}
<div class="section-card" id="prizes">
    <h4 class="mb-4">Hadiah yang Bisa Diredeem</h4>

    <div class="row g-4 prize-wrapper">
        @foreach($prizes as $p)
    @php
        $user = auth()->user();
        $userEmail = $user ? strtolower($user->email ?? $user->email_client ?? '') : '';
        $notLogin = !auth()->check();
        $notEnoughPoint = !$user || !$point || $point->poin < $p->point;
        $outOfStock = $p->stock <= 0;
        $redeemCount = $redeemCounts[$p->id] ?? 0;
        $monthlyRedeemLimitReached = ($totalRedeemThisMonth ?? 0) >= ($redeemMonthlyLimit ?? 2);
        
       
        $disableForAllExceptSpecificUser = ($userEmail !== 'a@gmail.com');

        $disabled = !$isRedeemPeriod
                || $disableForAllExceptSpecificUser || $notLogin || $outOfStock || $notEnoughPoint
                || $monthlyRedeemLimitReached
                || $redeemCount > 0; // disable jika sudah redeem hadiah ini di bulan berjalan
        // center jika ganjil
        $centerClass = ($loop->last && $loop->count % 2 == 1) ? 'mx-auto' : '';
    @endphp

    <div class="col-md-4 col-lg-3 {{ $centerClass }}">
            <div class="prize-card p-4 text-center
                {{ $redeemCount > 0 ? 'border border-success opacity-75' : '' }}
            ">
            <div>
                <div class="prize-image">
                    <img src="{{ asset('img/'.$p->img) }}" alt="{{ $p->name }}">
                </div>

                <div class="prize-title my-1">
                    {{ $p->name }}
                </div>

                <span class="point-badge">
                    {{ $p->point }} Poin
                </span>

                <div class="prize-meta mt-2">
                    Stok: {{ $p->stock }} Unit
                </div>
                
                {{-- <div class="prize-meta mt-1">
                    Redeemed: {{ $redeemCount }}x
                </div> --}}
            </div>

            <button
                type="button"
                class="btn {{ $redeemCount > 0 ? 'btn-success' : 'btn-warning' }} w-100 mt-3 fw-semibold btn-redeem"
                data-prize-id="{{ $p->id }}"
                {{ $disabled ? 'disabled' : '' }}
            >
            
                @if ($redeemCount > 0)
                    Sudah Diredeem
                @elseif ($isRedeemEnded)
                    Berakhir 31 Maret 2026
                @elseif (!$isRedeemPeriod)
                    Mulai 1 Maret 2026
                @elseif ($outOfStock)
                    Habis
                @elseif ($monthlyRedeemLimitReached)
                    Limit
                @elseif ($notLogin)
                    Redeem
                @elseif ($notEnoughPoint)
                    Poin Tidak Cukup
                @else
                    Redeem
                @endif
            </button>
        </div>
    </div>
@endforeach



    </div>

</div>

</div>

<!-- Modal Contact Info -->
<div class="modal fade contact-modal" id="contactInfoModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content contact-modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Isi Nomor Telp & Alamat</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="{{ route('contact-info.store') }}">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Nomor Telp</label>
                        <input type="text"
                               name="phone"
                               class="form-control contact-input"
                               placeholder="62xxxxxxxxx"
                               inputmode="numeric"
                               minlength="10"
                               maxlength="14"
                               pattern="^62[0-9]{8,12}$"
                               required>
                        <small>*) Nomor untuk OVO / Gopay / Link Aja</small>
                        {{-- <small class="text-muted">Format: 62 + 8-12 digit (total 10-14 digit)</small> --}}
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Alamat</label>
                        <textarea name="address" class="form-control contact-input" rows="3" required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-light" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-contact">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener("DOMContentLoaded", function () {
    const cards = document.querySelectorAll(".prize-card");

    const observer = new IntersectionObserver(entries => {
        entries.forEach((entry, index) => {
            if (entry.isIntersecting) {
                entry.target.style.animationDelay = `${index * 0.15}s`;
                entry.target.classList.add("animate");
                observer.unobserve(entry.target);
            }
        });
    }, { threshold: 0.2 });

    cards.forEach(card => observer.observe(card));
    
});
document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.progress-animate').forEach(bar => {
        const percent = bar.dataset.percent;
        setTimeout(() => {
            bar.style.width = percent + '%';
        }, 200); // delay dikit biar kelihatan animasinya
    });
});

document.addEventListener("DOMContentLoaded", function () {
    const observer = new IntersectionObserver(
        (entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add("show");
                    observer.unobserve(entry.target); // animate once
                }
            });
        },
        {
            threshold: 0.2
        }
    );

    document.querySelectorAll(".scroll-animate").forEach(el => {
        observer.observe(el);
    });
});
document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.btn-redeem').forEach(button => {
        button.addEventListener('click', function () {
            const prizeId = this.dataset.prizeId;

            Swal.fire({
                title: 'Yakin redeem hadiah ini?',
                text: 'Poin akan langsung dipotong',
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Ya, Redeem',
                cancelButtonText: 'Batal',
                confirmButtonColor: '#f59e0b',
            }).then((result) => {
                if (result.isConfirmed) {
                    redeemPrize(prizeId);
                }
            });
        });
    });

    function redeemPrize(prizeId) {
        fetch("{{ route('redeem') }}", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({
                prize_id: prizeId
            })
        })
        .then(res => res.json())
        .then(data => {
            if (data.status) {
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: data.message,
                }).then(() => {
                    location.reload();
                });
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal',
                    text: data.message,
                });
            }
        })
        .catch(() => {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Terjadi kesalahan sistem',
            });
        });
    }
});


</script>
@endpush
