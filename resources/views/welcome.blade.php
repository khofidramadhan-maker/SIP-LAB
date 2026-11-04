
@extends('layouts.app')

@section('title', 'SIP-LAB - Sistem Informasi Laboratorium')

@section('content')

<style>
    /* =========================================
       RESET & FULLSCREEN
    ========================================= */

    html,
    body {
        margin: 0 !important;
        padding: 0 !important;
        width: 100%;
        min-height: 100%;
    }

    body {
        overflow-x: hidden;
        background: #111c34;
    }

    .sip-landing,
    .sip-landing * {
        box-sizing: border-box;
    }

    .sip-landing {
        position: fixed;
        inset: 0;
        z-index: 100;
        width: 100%;
        height: 100vh;
        height: 100dvh;
        overflow-x: hidden;
        overflow-y: auto;
        background: #111c34;
        font-family: inherit;
    }

    /* =========================================
       HERO SECTION
    ========================================= */

    .sip-hero {
        position: relative;
        display: flex;
        align-items: center;
        justify-content: center;

        width: 100%;
        min-height: 100%;
        padding: clamp(28px, 6vw, 90px);

        overflow: hidden;

        background: linear-gradient(
            115deg,
            #111c34 0%,
            #1b3155 55%,
            #244b91 100%
        );
    }

    /* Background Decoration */

    .sip-hero::before {
        content: "";
        position: absolute;
        width: 420px;
        height: 420px;
        top: -260px;
        right: -80px;

        border: 1px solid rgba(255,255,255,.07);
        border-radius: 50%;

        box-shadow:
            0 0 0 40px rgba(255,255,255,.025),
            0 0 0 80px rgba(255,255,255,.02);

        pointer-events: none;
    }

    .sip-hero::after {
        content: "";
        position: absolute;
        width: 350px;
        height: 350px;
        bottom: -260px;
        left: 35%;

        border: 1px solid rgba(255,255,255,.04);
        border-radius: 50%;

        pointer-events: none;
    }

    .sip-content {
        position: relative;
        z-index: 2;

        width: 100%;
        max-width: 1500px;
        margin: 0 auto;
    }

    /* =========================================
       MAIN GRID
    ========================================= */

    .sip-grid {
        display: grid;

        grid-template-columns:
            minmax(0, 1.7fr)
            minmax(300px, .9fr);

        align-items: center;

        gap: clamp(35px, 7vw, 110px);
    }

    /* =========================================
       LEFT CONTENT
    ========================================= */

    .sip-main-content {
        min-width: 0;
    }

    .sip-eyebrow {
        display: inline-flex;
        align-items: center;
        gap: 9px;

        padding: 9px 14px;
        margin-bottom: 24px;

        border: 1px solid rgba(96,165,250,.28);
        border-radius: 50px;

        background: rgba(37,99,235,.16);

        color: #bfdbfe;
        font-size: 13px;
        font-weight: 600;
    }

    .sip-eyebrow i {
        font-size: 15px;
    }

    .sip-title {
        max-width: 850px;

        margin: 0 0 20px;

        color: #ffffff;

        font-size: clamp(34px, 4.2vw, 60px);
        font-weight: 750;

        letter-spacing: -2.5px;
        line-height: 1.12;

        overflow-wrap: break-word;
    }

    .sip-description {
        max-width: 680px;

        margin: 0 0 30px;

        color: #cbd5e1;

        font-size: clamp(14px, 1.2vw, 17px);
        line-height: 1.75;
    }

    /* =========================================
       BUTTONS
    ========================================= */

    .sip-actions {
        display: flex;
        flex-wrap: wrap;
        gap: 12px;
    }

    .sip-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 10px;

        min-height: 54px;
        padding: 0 25px;

        border: 1px solid transparent;
        border-radius: 10px;

        font-size: 15px;
        font-weight: 650;

        text-decoration: none;

        transition:
            background .2s ease,
            border-color .2s ease,
            transform .2s ease,
            box-shadow .2s ease;
    }

    .sip-btn-primary {
        background: #2563eb;
        color: #ffffff;
    }

    .sip-btn-primary:hover {
        background: #1d4ed8;
        color: #ffffff;

        transform: translateY(-2px);

        box-shadow: 0 8px 20px rgba(37,99,235,.25);
    }

    .sip-btn-outline {
        border-color: rgba(255,255,255,.45);

        background: transparent;
        color: #ffffff;
    }

    .sip-btn-outline:hover {
        border-color: #ffffff;

        background: rgba(255,255,255,.1);
        color: #ffffff;

        transform: translateY(-2px);
    }

    .sip-btn:focus-visible {
        outline: 3px solid #93c5fd;
        outline-offset: 3px;
    }

    /* =========================================
       RIGHT INFORMATION PANEL
    ========================================= */

    .sip-panel-wrapper {
        display: flex;
        justify-content: center;
        min-width: 0;
    }

    .sip-panel {
        width: 100%;
        max-width: 390px;

        padding: 25px;

        border: 1px solid rgba(255,255,255,.12);
        border-radius: 21px;

        background: rgba(255,255,255,.075);

        box-shadow: 0 20px 45px rgba(0,0,0,.15);

        backdrop-filter: blur(15px);
        -webkit-backdrop-filter: blur(15px);
    }

    .sip-panel-icon {
        display: flex;
        align-items: center;
        justify-content: center;

        width: 58px;
        height: 58px;

        margin: 0 auto 16px;

        border-radius: 17px;

        background: linear-gradient(
            135deg,
            #2563eb,
            #38bdf8
        );

        color: #ffffff;
        font-size: 28px;

        box-shadow: 0 8px 20px rgba(37,99,235,.25);
    }

    .sip-panel-title {
        margin: 0 0 5px;

        color: #ffffff;
        text-align: center;

        font-size: 21px;
        font-weight: 750;
    }

    .sip-panel-description {
        margin: 0 0 24px;

        color: #aebed6;
        text-align: center;

        font-size: 14px;
        line-height: 1.6;
    }

    /* =========================================
       INFORMATION ROWS
    ========================================= */

    .sip-info-list {
        display: flex;
        flex-direction: column;
        gap: 9px;
    }

    .sip-info-item {
        display: flex;
        align-items: center;
        justify-content: space-between;

        gap: 10px;
        min-height: 47px;

        padding: 10px 13px;

        border: 1px solid rgba(255,255,255,.08);
        border-radius: 10px;

        background: rgba(255,255,255,.055);
    }

    .sip-info-label {
        display: flex;
        align-items: center;
        gap: 10px;

        min-width: 0;

        color: #d5dfef;
        font-size: 13px;
        font-weight: 500;
    }

    .sip-info-label i {
        flex-shrink: 0;
        color: #bfdbfe;
        font-size: 15px;
    }

    .sip-info-value {
        flex-shrink: 0;

        padding: 5px 9px;

        border-radius: 20px;

        background: rgba(191,219,254,.17);

        color: #dbeafe;
        font-size: 11px;
        font-weight: 700;
    }

    /* =========================================
       LARGE DESKTOP
    ========================================= */

    @media (min-width: 1600px) {
        .sip-content {
            max-width: 1600px;
        }

        .sip-title {
            font-size: clamp(60px, 4vw, 72px);
        }
    }

    /* =========================================
       LAPTOP
    ========================================= */

    @media (max-width: 1200px) {
        .sip-hero {
            padding: 45px;
        }

        .sip-grid {
            grid-template-columns:
                minmax(0, 1.4fr)
                minmax(280px, .8fr);

            gap: 45px;
        }

        .sip-title {
            font-size: clamp(36px, 4.5vw, 52px);
        }
    }

    /* =========================================
       TABLET
    ========================================= */

    @media (max-width: 991px) {
        .sip-hero {
            padding: 55px 35px;
        }

        .sip-grid {
            grid-template-columns: 1fr;
            gap: 35px;
        }

        .sip-main-content {
            max-width: 750px;
        }

        .sip-title {
            max-width: 750px;
            font-size: clamp(38px, 6vw, 52px);
        }

        .sip-description {
            max-width: 650px;
        }

        .sip-panel-wrapper {
            justify-content: flex-start;
        }

        .sip-panel {
            max-width: 480px;
        }
    }

    /* =========================================
       MOBILE
    ========================================= */

    @media (max-width: 767px) {
        .sip-hero {
            min-height: 100%;
            padding: 40px 22px;
        }

        .sip-grid {
            grid-template-columns: 1fr;
            gap: 30px;
        }

        .sip-eyebrow {
            padding: 8px 12px;
            margin-bottom: 20px;
            font-size: 12px;
        }

        .sip-title {
            font-size: clamp(32px, 9vw, 43px);
            letter-spacing: -1.5px;
            line-height: 1.15;
        }

        .sip-description {
            margin-bottom: 24px;
            font-size: 14px;
            line-height: 1.7;
        }

        .sip-actions {
            flex-direction: column;
            gap: 10px;
        }

        .sip-btn {
            width: 100%;
            min-height: 50px;
            padding: 0 18px;
            font-size: 14px;
        }

        .sip-panel-wrapper {
            justify-content: flex-start;
        }

        .sip-panel {
            max-width: 100%;
            padding: 20px;
        }

        .sip-panel-icon {
            width: 52px;
            height: 52px;
            font-size: 24px;
        }

        .sip-panel-title {
            font-size: 19px;
        }

        .sip-panel-description {
            font-size: 13px;
        }
    }

    /* =========================================
       SMALL MOBILE
    ========================================= */

    @media (max-width: 380px) {
        .sip-hero {
            padding: 32px 17px;
        }

        .sip-title {
            font-size: 32px;
            letter-spacing: -1.2px;
        }

        .sip-info-item {
            padding: 9px 10px;
        }

        .sip-info-label {
            font-size: 12px;
            gap: 7px;
        }

        .sip-info-value {
            font-size: 10px;
        }
    }

    /* =========================================
       SHORT SCREEN
    ========================================= */

    @media (min-width: 768px) and (max-height: 650px) {
        .sip-hero {
            padding-top: 30px;
            padding-bottom: 30px;
        }

        .sip-eyebrow {
            margin-bottom: 15px;
        }

        .sip-title {
            font-size: clamp(32px, 4vw, 46px);
            margin-bottom: 12px;
        }

        .sip-description {
            margin-bottom: 20px;
        }

        .sip-panel {
            padding: 18px;
        }

        .sip-panel-icon {
            width: 48px;
            height: 48px;
            margin-bottom: 10px;
        }

        .sip-panel-description {
            margin-bottom: 15px;
        }

        .sip-info-item {
            min-height: 40px;
        }
    }

    /* Reduce Motion Accessibility */

    @media (prefers-reduced-motion: reduce) {
        .sip-btn {
            transition: none;
        }
    }
</style>

@php
    $roomCount = \App\Models\Ruangan::count();

    $assetCount = \App\Models\Alat::count();

    $completedLoans = \App\Models\Peminjaman::where(
        'status',
        'Selesai'
    )->count();
@endphp

<main class="sip-landing">

    <section class="sip-hero">

        <div class="sip-content">

            <div class="sip-grid">

                {{-- LEFT CONTENT --}}

                <div class="sip-main-content">

                    <div class="sip-eyebrow">
                        <i class="bi bi-shield-check"></i>

                        <span>
                            Sistem Informasi Laboratorium
                        </span>
                    </div>

                    <h1 class="sip-title">
                        Peminjaman Laboratorium & Inventaris Alat Riset
                    </h1>

                    <p class="sip-description">
                        Portal terpadu untuk mahasiswa dan pengelola
                        laboratorium dalam melihat informasi ruang,
                        mengajukan peminjaman alat, serta memantau
                        proses verifikasi dan persetujuan.
                    </p>

                    <div class="sip-actions">

                        @auth

                            @if(Auth::user()->role === 'mahasiswa')

                                <a href="{{ route('student.dashboard') }}"
                                   class="sip-btn sip-btn-primary">

                                    <i class="bi bi-box-arrow-in-right"></i>

                                    Buka Dashboard

                                </a>

                            @elseif(Auth::user()->role === 'staff')

                                <a href="{{ route('staff.dashboard') }}"
                                   class="sip-btn sip-btn-primary">

                                    <i class="bi bi-speedometer2"></i>

                                    Dashboard Staf

                                </a>

                            @else

                                <a href="/kepala-lab/dashboard"
                                   class="sip-btn sip-btn-primary">

                                    <i class="bi bi-clipboard-check"></i>

                                    Dashboard Kepala Lab

                                </a>

                            @endif

                        @else

                            <a href="{{ route('login') }}"
                               class="sip-btn sip-btn-primary">

                                <i class="bi bi-box-arrow-in-right"></i>

                                Masuk ke Akun

                            </a>

                            <a href="{{ route('register') }}"
                               class="sip-btn sip-btn-outline">

                                <i class="bi bi-person-plus"></i>

                                Registrasi Mahasiswa

                            </a>

                        @endauth

                    </div>

                </div>

                {{-- RIGHT INFORMATION PANEL --}}

                <div class="sip-panel-wrapper">

                    <div class="sip-panel">

                        <div class="sip-panel-icon">
                            <i class="bi bi-cpu"></i>
                        </div>

                        <h2 class="sip-panel-title">
                            SIP-LAB
                        </h2>

                        <p class="sip-panel-description">
                            Sistem pengelolaan laboratorium dan
                            inventaris riset.
                        </p>

                        <div class="sip-info-list">

                            {{-- Ruangan --}}

                            <div class="sip-info-item">

                                <div class="sip-info-label">

                                    <i class="bi bi-building"></i>

                                    <span>
                                        Data ruang laboratorium
                                    </span>

                                </div>

                                <span class="sip-info-value">
                                    {{ $roomCount }} ruang
                                </span>

                            </div>

                            {{-- Inventaris --}}

                            <div class="sip-info-item">

                                <div class="sip-info-label">

                                    <i class="bi bi-box-seam"></i>

                                    <span>
                                        Data alat dan inventaris
                                    </span>

                                </div>

                                <span class="sip-info-value">
                                    {{ $assetCount }} alat
                                </span>

                            </div>

                            {{-- Peminjaman --}}

                            <div class="sip-info-item">

                                <div class="sip-info-label">

                                    <i class="bi bi-clipboard-check"></i>

                                    <span>
                                        Peminjaman selesai
                                    </span>

                                </div>

                                <span class="sip-info-value">
                                    {{ $completedLoans }} data
                                </span>

                            </div>

                        </div>

                    </div>

                </div>

            </div>

        </div>

    </section>

</main>

@endsection
