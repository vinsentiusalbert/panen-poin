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

{{-- ================= LIGA ================= --}}
@if(isset($point) && is_object($point) && isset($point->poin))
<div class="section-card text-center mb-5">
    <h2 class="mb-4">Total Poin</h2>

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

            <div class="progress">
                <div 
                    class="progress-bar progress-animate"
                    data-percent="{{ $percent }}"
                    style="width: 0%">
                </div>
            </div>

            <small>Total Poin Anda: <b>{{ $point->poin }}</b></small>
        </div>

</div>

    @endif

{{-- ================= TABLE ================= --}}
<div class="section-card mb-5">
    <h4 class="mb-3">Reporting Champion</h4>

    <div class="">
        <div class="row">
            <div class="col-md-3 liga-card text-center animate-left scroll-animate my-2">
                <img src="{{ asset('img/champion.png') }}">
                <h5>Champion</h5>
                <span class="liga-range">201 – 300 Poin</span>
            </div>
            <div class="col-md-9">
                <div class="table-glass liga-champion animate-right scroll-animate">
                    <table class="table table-transparent align-middle mb-0">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama Pelanggan</th>
                            <th>Nama Akun</th>
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
                                <td>{{$row['nama_pelanggan']}}</td>
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


<div class="section-card mb-5">
    <h4 class="mb-3">Reporting Rising Star</h4>

    <div class="">
        <div class="row">
            <div class="col-md-3 liga-card text-center animate-left scroll-animate my-2">
                <img src="{{ asset('img/rising_star.png') }}">
                <h5>Rising Star</h5>
                <span class="liga-range">101 – 200 Poin</span>
            </div>
            <div class="col-md-9">
                <div class="table-glass liga-rising  animate-right scroll-animate">
                    <table class="table table-transparent align-middle mb-0">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama Pelanggan</th>
                            <th>Nama Akun</th>
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
                                <td>{{$row['nama_pelanggan']}}</td>
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

<div class="section-card mb-5">
    <h4 class="mb-3">Reporting Rookie</h4>

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
                    <table class="table table-transparent align-middle mb-0">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama Pelanggan</th>
                            <th>Nama Akun</th>
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
                                <td>{{$row['nama_pelanggan']}}</td>
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



{{-- ================= PRIZE ================= --}}
<div class="section-card" id="prizes">
    <h4 class="mb-4">Hadiah yang Bisa Diredeem</h4>

    <div class="row g-4 prize-wrapper">
        @foreach($prizes as $p)
            <div class="col-md-4 col-lg-3">
                <div class="prize-card p-4 text-center">
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
                    </div>

                    <button
                        class="btn btn-warning w-100 mt-3 fw-semibold"
                        {{ $p->stock <= 0 ? 'disabled' : '' }}
                    >
                        {{ $p->stock > 0 ? 'Redeem' : 'Habis' }}
                    </button>
                </div>
            </div>
        @endforeach
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


</script>
@endpush
