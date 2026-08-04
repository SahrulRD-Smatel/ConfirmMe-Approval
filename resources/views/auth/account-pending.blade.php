<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Akun Menunggu Setup - {{ config('app.name', 'ConfirmMe') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1rem;
        }

        .card-container {
            max-width: 480px;
            width: 100%;
        }

        .card {
            background: #ffffff;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.08), 0 4px 16px rgba(0, 0, 0, 0.04);
            overflow: hidden;
        }

        .card-header-bar {
            height: 6px;
            background: linear-gradient(90deg, #f59e0b, #ef4444, #82181a);
        }

        .card-body {
            padding: 2.5rem;
            text-align: center;
        }

        .icon-circle {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            background: linear-gradient(135deg, #fef3c7, #fde68a);
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1.5rem;
        }

        .icon-circle svg {
            width: 40px;
            height: 40px;
            color: #d97706;
        }

        h1 {
            font-size: 1.5rem;
            font-weight: 700;
            color: #111827;
            margin-bottom: 0.75rem;
        }

        .description {
            color: #6b7280;
            line-height: 1.7;
            margin-bottom: 1.5rem;
            font-size: 0.95rem;
        }

        .info-box {
            background: #fffbeb;
            border: 1px solid #fde68a;
            border-radius: 12px;
            padding: 1rem 1.25rem;
            margin-bottom: 1.5rem;
            text-align: left;
            display: flex;
            gap: 0.75rem;
            align-items: flex-start;
        }

        .info-box svg {
            width: 20px;
            height: 20px;
            color: #d97706;
            flex-shrink: 0;
            margin-top: 2px;
        }

        .info-box .info-title {
            font-weight: 600;
            font-size: 0.875rem;
            color: #92400e;
            margin-bottom: 0.25rem;
        }

        .info-box .info-text {
            font-size: 0.85rem;
            color: #a16207;
            line-height: 1.5;
        }

        .user-card {
            background: #f9fafb;
            border-radius: 12px;
            padding: 1rem;
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.75rem;
        }

        .user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: linear-gradient(135deg, #82181a, #a31d1f);
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            font-weight: 700;
            font-size: 0.875rem;
        }

        .user-info {
            text-align: left;
        }

        .user-name {
            font-weight: 600;
            font-size: 0.9rem;
            color: #111827;
        }

        .user-email {
            font-size: 0.8rem;
            color: #9ca3af;
        }

        .user-badge {
            display: inline-block;
            background: #fef3c7;
            color: #92400e;
            font-size: 0.7rem;
            font-weight: 600;
            padding: 0.15rem 0.5rem;
            border-radius: 999px;
            margin-top: 0.25rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .actions {
            display: flex;
            flex-direction: column;
            gap: 0.75rem;
        }

        @media (min-width: 480px) {
            .actions {
                flex-direction: row;
                justify-content: center;
            }
        }

        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            padding: 0.7rem 1.5rem;
            font-size: 0.9rem;
            font-weight: 600;
            border-radius: 12px;
            border: none;
            cursor: pointer;
            transition: all 0.2s ease;
            text-decoration: none;
            font-family: inherit;
        }

        .btn svg {
            width: 18px;
            height: 18px;
        }

        .btn-primary {
            background: linear-gradient(135deg, #82181a, #a31d1f);
            color: #ffffff;
        }

        .btn-primary:hover {
            background: linear-gradient(135deg, #6b1315, #82181a);
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(130, 24, 26, 0.3);
        }

        .btn-secondary {
            background: #f3f4f6;
            color: #4b5563;
        }

        .btn-secondary:hover {
            background: #e5e7eb;
            transform: translateY(-1px);
        }

        .footer-text {
            text-align: center;
            font-size: 0.75rem;
            color: #9ca3af;
            margin-top: 1.5rem;
        }
    </style>
</head>

<body>
    <div class="card-container">
        <div class="card">
            <div class="card-header-bar"></div>

            <div class="card-body">
                {{-- Icon --}}
                <div class="icon-circle">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                            d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z" />
                    </svg>
                </div>

                {{-- Title --}}
                <h1>Akun Belum Disetup</h1>

                {{-- Description --}}
                <p class="description">
                    Akun Anda sudah terdaftar, namun belum ditugaskan role dan departemen oleh administrator.
                    Anda belum dapat mengakses fitur sistem sampai admin menyelesaikan setup akun Anda.
                </p>

                {{-- Info Box --}}
                <div class="info-box">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <div>
                        <p class="info-title">Apa yang perlu dilakukan?</p>
                        <p class="info-text">Hubungi administrator sistem untuk meminta aktivasi dan penugasan role pada akun Anda.</p>
                    </div>
                </div>

                {{-- User Info --}}
                <div class="user-card">
                    <div class="user-avatar">
                        {{ auth()->user()->initials }}
                    </div>
                    <div class="user-info">
                        <div class="user-name">{{ auth()->user()->name }}</div>
                        <div class="user-email">{{ auth()->user()->email }}</div>
                        <span class="user-badge">Menunggu Setup</span>
                    </div>
                </div>

                {{-- Actions --}}
                <div class="actions">
                    <a href="{{ route('profile.edit') }}" class="btn btn-primary">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                        Lihat Profil
                    </a>

                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="btn btn-secondary">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                            </svg>
                            Keluar
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <p class="footer-text">
            &copy; {{ date('Y') }} PT. Info Solusindo Data Utama. ConfirmMe v1.0
        </p>
    </div>
</body>

</html>
