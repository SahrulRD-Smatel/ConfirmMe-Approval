<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Register - {{ config('app.name', 'ConfirmMe') }}</title>

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
            background: linear-gradient(135deg, #82181a 0%, #5c1012 100%);
            min-height: 100vh;
            overflow-x: hidden;
        }

        .container {
            display: flex;
            min-height: 100vh;
        }

        /* Left Side - Login Form */
        .left-side {
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            padding: 40px;
            background: #ffffff;
            position: relative;
            overflow: hidden;
        }

        .left-side::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(130, 24, 26, 0.05) 0%, transparent 70%);
            animation: pulse 15s ease-in-out infinite;
        }

        @keyframes pulse {

            0%,
            100% {
                transform: scale(1) rotate(0deg);
            }

            50% {
                transform: scale(1.1) rotate(180deg);
            }
        }

        .login-content {
            width: 100%;
            max-width: 420px;
            position: relative;
            z-index: 1;
            animation: slideUp 0.8s ease-out;
        }

        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .logo-container {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 48px;
            animation: fadeIn 1s ease-out 0.2s both;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
            }

            to {
                opacity: 1;
            }
        }

        .logo-icon {
            width: 64px;
            height: 64px;
            background: #ffffff;
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 10px 30px rgba(130, 24, 26, 0.2);
            animation: float 3s ease-in-out infinite;
            overflow: hidden;
        }

        @keyframes float {

            0%,
            100% {
                transform: translateY(0);
            }

            50% {
                transform: translateY(-8px);
            }
        }

        .logo-icon svg {
            width: 32px;
            height: 32px;
            fill: white;
        }

        .logo-text {
            font-size: 28px;
            font-weight: 700;
            background: linear-gradient(135deg, #82181a 0%, #a52a2a 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .welcome-text {
            margin-bottom: 8px;
            animation: fadeIn 1s ease-out 0.3s both;
        }

        .welcome-text h1 {
            font-size: 32px;
            font-weight: 700;
            color: #1a1a2e;
            margin-bottom: 8px;
        }

        .welcome-text p {
            font-size: 15px;
            color: #6b7280;
        }

        .login-form {
            margin-top: 32px;
            animation: fadeIn 1s ease-out 0.4s both;
        }

        .form-group {
            margin-bottom: 24px;
            position: relative;
        }

        .form-group label {
            display: block;
            font-size: 14px;
            font-weight: 500;
            color: #374151;
            margin-bottom: 8px;
        }

        .form-group label span {
            color: #ef4444;
        }

        .input-wrapper {
            position: relative;
        }

        .input-wrapper input {
            width: 100%;
            padding: 14px 16px;
            padding-left: 48px;
            font-size: 15px;
            border: 2px solid #e5e7eb;
            border-radius: 12px;
            outline: none;
            transition: all 0.3s ease;
            background: #f9fafb;
        }

        .input-wrapper input:focus {
            border-color: #82181a;
            background: #ffffff;
            box-shadow: 0 0 0 4px rgba(130, 24, 26, 0.1);
        }

        .input-wrapper input::placeholder {
            color: #9ca3af;
        }

        .input-icon {
            position: absolute;
            left: 16px;
            top: 50%;
            transform: translateY(-50%);
            color: #9ca3af;
            transition: color 0.3s ease;
        }

        .input-wrapper input:focus+.input-icon,
        .input-wrapper input:focus~.input-icon {
            color: #82181a;
        }

        .password-toggle {
            position: absolute;
            right: 16px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            cursor: pointer;
            color: #9ca3af;
            padding: 4px;
            transition: color 0.3s ease;
        }

        .password-toggle:hover {
            color: #82181a;
        }

        .form-options {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 28px;
        }

        .remember-me {
            display: flex;
            align-items: center;
            gap: 10px;
            cursor: pointer;
        }

        .remember-me input[type="checkbox"] {
            width: 18px;
            height: 18px;
            accent-color: #82181a;
            cursor: pointer;
        }

        .remember-me span {
            font-size: 14px;
            color: #6b7280;
        }

        .forgot-password {
            font-size: 14px;
            color: #82181a;
            text-decoration: none;
            font-weight: 500;
            transition: color 0.3s ease;
        }

        .forgot-password:hover {
            color: #a52a2a;
        }

        .submit-btn {
            width: 100%;
            padding: 16px;
            font-size: 16px;
            font-weight: 600;
            color: #ffffff;
            background: linear-gradient(135deg, #82181a 0%, #a52a2a 100%);
            border: none;
            border-radius: 12px;
            cursor: pointer;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .submit-btn::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
            transition: left 0.5s ease;
        }

        .submit-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 30px rgba(130, 24, 26, 0.4);
        }

        .submit-btn:hover::before {
            left: 100%;
        }

        .submit-btn:active {
            transform: translateY(0);
        }

        .error-message {
            background: #fef2f2;
            border: 1px solid #fecaca;
            color: #dc2626;
            padding: 12px 16px;
            border-radius: 10px;
            font-size: 14px;
            margin-bottom: 20px;
            animation: shake 0.5s ease-in-out;
        }

        @keyframes shake {

            0%,
            100% {
                transform: translateX(0);
            }

            25% {
                transform: translateX(-5px);
            }

            75% {
                transform: translateX(5px);
            }
        }

        .success-message {
            background: #f0fdf4;
            border: 1px solid #bbf7d0;
            color: #16a34a;
            padding: 12px 16px;
            border-radius: 10px;
            font-size: 14px;
            margin-bottom: 20px;
        }

        /* Right Side - Hero */
        .right-side {
            flex: 1;
            background: linear-gradient(135deg, #2a0d0e 0%, #4a1618 50%, #82181a 100%);
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            padding: 60px;
            position: relative;
            overflow: hidden;
        }

        /* Floating shapes animation */
        .floating-shapes {
            position: absolute;
            width: 100%;
            height: 100%;
            top: 0;
            left: 0;
            overflow: hidden;
        }

        .shape {
            position: absolute;
            border-radius: 50%;
            opacity: 0.1;
        }

        .shape-1 {
            width: 300px;
            height: 300px;
            background: linear-gradient(135deg, #82181a, #a52a2a);
            top: 10%;
            right: -10%;
            animation: floatShape1 20s ease-in-out infinite;
        }

        .shape-2 {
            width: 200px;
            height: 200px;
            background: linear-gradient(135deg, #d4a574, #c9956c);
            bottom: 20%;
            left: -5%;
            animation: floatShape2 15s ease-in-out infinite;
        }

        .shape-3 {
            width: 150px;
            height: 150px;
            background: linear-gradient(135deg, #f5d0c5, #e8b4a8);
            top: 50%;
            right: 20%;
            animation: floatShape3 18s ease-in-out infinite;
        }

        @keyframes floatShape1 {

            0%,
            100% {
                transform: translate(0, 0) rotate(0deg);
            }

            25% {
                transform: translate(-30px, 30px) rotate(90deg);
            }

            50% {
                transform: translate(20px, -20px) rotate(180deg);
            }

            75% {
                transform: translate(-20px, -30px) rotate(270deg);
            }
        }

        @keyframes floatShape2 {

            0%,
            100% {
                transform: translate(0, 0) scale(1);
            }

            50% {
                transform: translate(40px, -40px) scale(1.1);
            }
        }

        @keyframes floatShape3 {

            0%,
            100% {
                transform: translate(0, 0);
            }

            33% {
                transform: translate(30px, 30px);
            }

            66% {
                transform: translate(-20px, 20px);
            }
        }

        .hero-content {
            position: relative;
            z-index: 1;
            text-align: center;
            animation: fadeInUp 1s ease-out 0.5s both;
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(40px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .hero-illustration {
            width: 100%;
            max-width: 400px;
            margin-bottom: 40px;
            animation: bounce 3s ease-in-out infinite;
        }

        @keyframes bounce {

            0%,
            100% {
                transform: translateY(0);
            }

            50% {
                transform: translateY(-15px);
            }
        }

        .hero-illustration svg {
            width: 100%;
            height: auto;
        }

        .hero-text h2 {
            font-size: 28px;
            font-weight: 700;
            color: #ffffff;
            margin-bottom: 16px;
            text-shadow: 0 2px 10px rgba(0, 0, 0, 0.3);
        }

        .hero-text p {
            font-size: 16px;
            color: rgba(255, 255, 255, 0.7);
            line-height: 1.6;
        }

        .hero-text .highlight {
            color: #f5d0c5;
            font-weight: 600;
        }

        /* Features badges */
        .features {
            display: flex;
            gap: 16px;
            margin-top: 32px;
            flex-wrap: wrap;
            justify-content: center;
        }

        .feature-badge {
            display: flex;
            align-items: center;
            gap: 8px;
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            padding: 10px 18px;
            border-radius: 30px;
            border: 1px solid rgba(255, 255, 255, 0.2);
            animation: fadeIn 1s ease-out 0.8s both;
        }

        .feature-badge svg {
            width: 18px;
            height: 18px;
            fill: #4ade80;
        }

        .feature-badge span {
            font-size: 13px;
            color: #ffffff;
            font-weight: 500;
        }

        /* Responsive */
        @media (max-width: 1024px) {
            .container {
                flex-direction: column;
            }

            .right-side {
                display: none;
            }

            .left-side {
                min-height: 100vh;
                background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
            }
        }

        @media (max-width: 480px) {
            .left-side {
                padding: 24px;
            }

            .login-content {
                max-width: 100%;
            }

            .welcome-text h1 {
                font-size: 26px;
            }
        }

        /* Loading state */
        .submit-btn.loading {
            pointer-events: none;
            opacity: 0.8;
        }

        .submit-btn.loading::after {
            content: '';
            width: 20px;
            height: 20px;
            border: 2px solid #ffffff;
            border-top-color: transparent;
            border-radius: 50%;
            display: inline-block;
            margin-left: 10px;
            animation: spin 0.8s linear infinite;
            vertical-align: middle;
        }

        @keyframes spin {
            to {
                transform: rotate(360deg);
            }
        }
    </style>
</head>

<body>
    <div class="container">
        <!-- Left Side - Login Form -->
        <div class="left-side">
            <div class="login-content">
                <!-- Logo -->
                <div class="logo-container">
                    <div class="logo-icon">
                        <img src="{{ asset('images/logo-infodata.jpg') }}" alt="Info Data Logo"
                            style="width: 100%; height: 100%; object-fit: contain; border-radius: 12px;">
                    </div>
                    <span class="logo-text">ConfirmMe</span>
                </div>

                <!-- Welcome Text -->
                <div class="welcome-text">
                    <h1>Buat Akun Baru</h1>
                    <p>Daftar untuk mulai mengelola persetujuan dokumen Anda</p>
                </div>

                <!-- Session Status -->
                @if (session('status'))
                    <div class="success-message">
                        {{ session('status') }}
                    </div>
                @endif

                <!-- Error Messages -->
                @if ($errors->any())
                    <div class="error-message">
                        @foreach ($errors->all() as $error)
                            {{ $error }}
                        @endforeach
                    </div>
                @endif

                <!-- Register Form -->
                <form class="login-form" method="POST" action="{{ route('register') }}" id="registerForm">
                    @csrf

                    <!-- Name -->
                    <div class="form-group">
                        <label for="name">Nama Lengkap <span>*</span></label>
                        <div class="input-wrapper">
                            <input type="text" id="name" name="name" placeholder="Masukkan nama lengkap"
                                value="{{ old('name') }}" required autofocus autocomplete="name">
                            <svg class="input-icon" width="20" height="20" viewBox="0 0 24 24" fill="none"
                                stroke="currentColor" stroke-width="2">
                                <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle>
                            </svg>
                        </div>
                    </div>

                    <!-- Email -->
                    <div class="form-group">
                        <label for="email">Email <span>*</span></label>
                        <div class="input-wrapper">
                            <input type="email" id="email" name="email" placeholder="nama@perusahaan.com"
                                value="{{ old('email') }}" required autofocus autocomplete="username">
                            <svg class="input-icon" width="20" height="20" viewBox="0 0 24 24" fill="none"
                                stroke="currentColor" stroke-width="2">
                                <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z" />
                                <polyline points="22,6 12,13 2,6" />
                            </svg>
                        </div>
                    </div>

                    <!-- Password -->
                    <div class="form-group">
                        <label for="password">Password <span>*</span></label>
                        <div class="input-wrapper">
                            <input type="password" id="password" name="password" placeholder="Masukkan password"
                                required autocomplete="current-password">
                            <svg class="input-icon" width="20" height="20" viewBox="0 0 24 24" fill="none"
                                stroke="currentColor" stroke-width="2">
                                <rect x="3" y="11" width="18" height="11" rx="2" ry="2" />
                                <path d="M7 11V7a5 5 0 0 1 10 0v4" />
                            </svg>
                            <button type="button" class="password-toggle" onclick="togglePassword()">
                                <svg id="eyeIcon" width="20" height="20" viewBox="0 0 24 24" fill="none"
                                    stroke="currentColor" stroke-width="2">
                                    <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z" />
                                    <circle cx="12" cy="12" r="3" />
                                </svg>
                            </button>
                        </div>
                    </div>

                    <!-- Confirm Password -->
                    <div class="form-group">
                        <label for="password_confirmation">Konfirmasi Password <span>*</span></label>
                        <div class="input-wrapper">
                            <input type="password" id="password_confirmation" name="password_confirmation" placeholder="Konfirmasi password"
                                required autocomplete="new-password">
                            <svg class="input-icon" width="20" height="20" viewBox="0 0 24 24" fill="none"
                                stroke="currentColor" stroke-width="2">
                                <rect x="3" y="11" width="18" height="11" rx="2" ry="2" />
                                <path d="M7 11V7a5 5 0 0 1 10 0v4" />
                            </svg>
                        </div>
                    </div>

                    <div style="margin-bottom: 24px; text-align: right;">
                        <a href="{{ route('login') }}" class="forgot-password" style="font-size: 14px;">Sudah punya akun? Masuk di sini</a>
                    </div>

                    <!-- Submit Button -->
                    <button type="submit" class="submit-btn" id="submitBtn">
                        Daftar
                    </button>
                </form>
            </div>
        </div>

        <!-- Right Side - Hero Section -->
        <div class="right-side">
            <!-- Floating Shapes -->
            <div class="floating-shapes">
                <div class="shape shape-1"></div>
                <div class="shape shape-2"></div>
                <div class="shape shape-3"></div>
            </div>

            <div class="hero-content">
                <!-- Illustration -->
                <div class="hero-illustration">
                    <svg viewBox="0 0 400 300" xmlns="http://www.w3.org/2000/svg">
                        <!-- Background elements -->
                        <defs>
                            <linearGradient id="grad1" x1="0%" y1="0%" x2="100%" y2="100%">
                                <stop offset="0%" style="stop-color:#82181a;stop-opacity:1" />
                                <stop offset="100%" style="stop-color:#a52a2a;stop-opacity:1" />
                            </linearGradient>
                            <linearGradient id="grad2" x1="0%" y1="0%" x2="100%" y2="0%">
                                <stop offset="0%" style="stop-color:#d4a574;stop-opacity:1" />
                                <stop offset="100%" style="stop-color:#f5d0c5;stop-opacity:1" />
                            </linearGradient>
                        </defs>

                        <!-- Document stack -->
                        <rect x="80" y="80" width="120" height="150" rx="8" fill="#ffffff" opacity="0.9">
                            <animate attributeName="y" values="80;75;80" dur="3s" repeatCount="indefinite" />
                        </rect>
                        <rect x="95" y="100" width="90" height="6" rx="3" fill="#e5e7eb" />
                        <rect x="95" y="115" width="70" height="6" rx="3" fill="#e5e7eb" />
                        <rect x="95" y="130" width="80" height="6" rx="3" fill="#e5e7eb" />
                        <rect x="95" y="160" width="60" height="6" rx="3" fill="url(#grad1)" />

                        <!-- Checkmark circle -->
                        <circle cx="170" cy="200" r="25" fill="url(#grad1)">
                            <animate attributeName="r" values="25;28;25" dur="2s" repeatCount="indefinite" />
                        </circle>
                        <path d="M158 200 L166 208 L182 192" stroke="#ffffff" stroke-width="4" fill="none"
                            stroke-linecap="round" stroke-linejoin="round" />

                        <!-- Flow arrows and secondary doc -->
                        <path d="M210 150 Q240 150 260 120" stroke="url(#grad2)" stroke-width="3" fill="none"
                            stroke-dasharray="8,4">
                            <animate attributeName="stroke-dashoffset" values="0;-24" dur="1s"
                                repeatCount="indefinite" />
                        </path>

                        <rect x="260" y="60" width="80" height="100" rx="6" fill="#ffffff" opacity="0.85">
                            <animate attributeName="y" values="60;55;60" dur="2.5s" repeatCount="indefinite" />
                        </rect>
                        <rect x="272" y="75" width="56" height="4" rx="2" fill="#d1d5db" />
                        <rect x="272" y="85" width="40" height="4" rx="2" fill="#d1d5db" />
                        <circle cx="295" cy="120" r="18" fill="url(#grad2)" opacity="0.8" />
                        <path d="M288 120 L293 125 L305 113" stroke="#ffffff" stroke-width="3" fill="none"
                            stroke-linecap="round" />

                        <!-- User icons -->
                        <circle cx="320" cy="200" r="20" fill="url(#grad1)" opacity="0.9">
                            <animate attributeName="opacity" values="0.9;1;0.9" dur="2s" repeatCount="indefinite" />
                        </circle>
                        <circle cx="320" cy="195" r="6" fill="#ffffff" />
                        <path d="M310 210 Q320 200 330 210" stroke="#ffffff" stroke-width="3" fill="none" />

                        <circle cx="60" cy="180" r="16" fill="url(#grad2)" opacity="0.8">
                            <animate attributeName="opacity" values="0.8;1;0.8" dur="1.8s" repeatCount="indefinite" />
                        </circle>
                        <circle cx="60" cy="176" r="5" fill="#ffffff" />
                        <path d="M52 186 Q60 178 68 186" stroke="#ffffff" stroke-width="2" fill="none" />

                        <!-- Connection lines -->
                        <line x1="76" y1="180" x2="80" y2="155" stroke="url(#grad2)" stroke-width="2" opacity="0.5"
                            stroke-dasharray="4,4">
                            <animate attributeName="stroke-dashoffset" values="0;-16" dur="1.5s"
                                repeatCount="indefinite" />
                        </line>
                        <line x1="300" y1="200" x2="260" y2="160" stroke="url(#grad1)" stroke-width="2" opacity="0.5"
                            stroke-dasharray="4,4">
                            <animate attributeName="stroke-dashoffset" values="0;-16" dur="1.5s"
                                repeatCount="indefinite" />
                        </line>
                    </svg>
                </div>

                <!-- Hero Text -->
                <div class="hero-text">
                    <h2>Document Approval System</h2>
                    <p>Kelola persetujuan dokumen dengan <span class="highlight">Mudah</span>, <span
                            class="highlight">Transparan</span>, dan <span class="highlight">Profesional</span></p>
                </div>

                <!-- Features -->
                <div class="features">
                    <div class="feature-badge">
                        <svg viewBox="0 0 24 24">
                            <path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41L9 16.17z" />
                        </svg>
                        <span>Multi-Level Approval</span>
                    </div>
                    <div class="feature-badge">
                        <svg viewBox="0 0 24 24">
                            <path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41L9 16.17z" />
                        </svg>
                        <span>Real-time Tracking</span>
                    </div>
                    <div class="feature-badge">
                        <svg viewBox="0 0 24 24">
                            <path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41L9 16.17z" />
                        </svg>
                        <span>Secure & Verified</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Toggle password visibility
        function togglePassword() {
            const passwordInput = document.getElementById('password');
            const eyeIcon = document.getElementById('eyeIcon');

            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                eyeIcon.innerHTML = `
                    <path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"/>
                    <line x1="1" y1="1" x2="23" y2="23"/>
                `;
            } else {
                passwordInput.type = 'password';
                eyeIcon.innerHTML = `
                    <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                    <circle cx="12" cy="12" r="3"/>
                `;
            }
        }

        // Form submit loading state
        document.getElementById('registerForm').addEventListener('submit', function () {
            const btn = document.getElementById('submitBtn');
            btn.classList.add('loading');
            btn.textContent = 'Memproses';
        });

        // Add focus animations
        document.querySelectorAll('.input-wrapper input').forEach(input => {
            input.addEventListener('focus', function () {
                this.parentElement.style.transform = 'scale(1.02)';
                this.parentElement.style.transition = 'transform 0.2s ease';
            });

            input.addEventListener('blur', function () {
                this.parentElement.style.transform = 'scale(1)';
            });
        });
    </script>
</body>

</html>