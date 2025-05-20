<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Tirta Pakuan</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="icon" type="image/png" href="{{ asset('images/login.png') }}">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }

        body {
            display: flex;
            min-height: 100vh;
            overflow-x: hidden;
        }

        .left-section {
            flex: 1;
            background: #0061f2;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            color: white;
            padding: clamp(1rem, 5vw, 2rem);
            position: relative;
            overflow: hidden;
        }

        .logo-container {
            text-align: center;
            margin-bottom: clamp(1rem, 4vw, 2rem);
            z-index: 2;
        }

        .logo {
            width: clamp(100px, 30vw, 180px);
            height: clamp(100px, 30vw, 180px);
            margin-bottom: clamp(0.5rem, 2vw, 1rem);
            animation: float 6s ease-in-out infinite;
        }

        .welcome-text {
            font-size: clamp(1.2rem, 4vw, 2rem);
            font-weight: 700;
            margin-bottom: 0.5rem;
            text-align: center;
        }

        .company-name {
            font-size: clamp(1rem, 3.5vw, 1.8rem);
            font-weight: 600;
            text-align: center;
        }

        /* Animated waves */
        .waves {
            position: absolute;
            bottom: 0;
            left: 0;
            width: 100%;
            height: 100px;
        }

        .parallax > use {
            animation: move-forever 25s cubic-bezier(.55,.5,.45,.5) infinite;
        }
        .parallax > use:nth-child(1) {
            animation-delay: -2s;
            animation-duration: 7s;
        }
        .parallax > use:nth-child(2) {
            animation-delay: -3s;
            animation-duration: 10s;
        }
        .parallax > use:nth-child(3) {
            animation-delay: -4s;
            animation-duration: 13s;
        }
        .parallax > use:nth-child(4) {
            animation-delay: -5s;
            animation-duration: 20s;
        }

        @keyframes move-forever {
            0% { transform: translate3d(-90px,0,0); }
            100% { transform: translate3d(85px,0,0); }
        }

        @keyframes float {
            0% { transform: translateY(0px); }
            50% { transform: translateY(-20px); }
            100% { transform: translateY(0px); }
        }

        .right-section {
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            padding: clamp(1rem, 5vw, 2rem);
            background: white;
        }

        .login-container {
            width: 100%;
            max-width: 400px;
            padding: clamp(1rem, 4vw, 2rem);
            box-shadow: 0 4px 6px rgba(0,0,0,0.05);
            border-radius: 12px;
        }

        .login-header {
            text-align: center;
            margin-bottom: clamp(1rem, 4vw, 2rem);
            display: flex;
            align-items: center;
            justify-content: center;
            gap: clamp(0.5rem, 2vw, 1rem);
        }

        .login-title {
            font-size: clamp(1.2rem, 3vw, 1.5rem);
            color: #333;
            margin-bottom: 0;
        }

        .login-icon {
            width: clamp(24px, 6vw, 32px);
            height: clamp(24px, 6vw, 32px);
        }

        .form-group {
            margin-bottom: clamp(1rem, 3vw, 1.5rem);
        }

        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            color: #666;
            font-size: clamp(0.8rem, 2vw, 0.9rem);
        }

        .form-control {
            width: 100%;
            padding: clamp(0.6rem, 2vw, 0.75rem) clamp(0.8rem, 2vw, 1rem);
            border: 2px solid #e2e8f0;
            border-radius: 8px;
            font-size: clamp(0.9rem, 2vw, 1rem);
            transition: all 0.3s;
        }

        .form-control:focus {
            border-color: #0061f2;
            outline: none;
            box-shadow: 0 0 0 3px rgba(0, 97, 242, 0.1);
        }

        .form-check {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            margin-bottom: clamp(1rem, 3vw, 1.5rem);
        }

        .forgot-password {
            color: #0061f2;
            text-decoration: none;
            font-size: clamp(0.8rem, 2vw, 0.9rem);
            float: right;
        }

        .btn-login {
            width: 100%;
            padding: clamp(0.6rem, 2vw, 0.75rem);
            background: #0061f2;
            color: white;
            border: none;
            border-radius: 8px;
            font-size: clamp(0.9rem, 2vw, 1rem);
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
        }

        .btn-login:hover {
            background: #0056d6;
            transform: translateY(-1px);
        }

        .alert {
            padding: clamp(0.8rem, 2vw, 1rem);
            border-radius: 8px;
            margin-bottom: clamp(1rem, 3vw, 1.5rem);
            text-align: center;
            font-size: clamp(0.8rem, 2vw, 0.9rem);
        }

        .alert-danger {
            background-color: #fff5f5;
            color: #c53030;
            border: 1px solid #feb2b2;
        }

        .alert-success {
            background-color: #f0fff4;
            color: #2f855a;
            border: 1px solid #9ae6b4;
        }

        /* Responsive breakpoints */
        @media (max-width: 1024px) {
            .left-section, .right-section {
                padding: 1.5rem;
            }
            
            .login-container {
                padding: 1.5rem;
            }
        }

        @media (max-width: 768px) {
            body {
                flex-direction: column;
            }

            .left-section {
                min-height: 40vh;
                padding: 1.5rem 1rem;
            }

            .right-section {
                min-height: 60vh;
                padding: 1.5rem 1rem;
            }

            .waves {
                height: 40px;
            }
            
            .login-container {
                box-shadow: none;
            }
        }
        
        @media (max-width: 480px) {
            .left-section {
                min-height: 35vh;
                padding: 1rem 0.75rem;
            }
            
            .right-section {
                min-height: 65vh;
                padding: 1rem 0.75rem;
            }
            
            .waves {
                height: 30px;
            }
            
            .login-container {
                padding: 1rem;
            }
            
            .form-group {
                margin-bottom: 0.75rem;
            }
        }
        
        @media (max-height: 600px) and (orientation: landscape) {
            body {
                flex-direction: row;
            }
            
            .left-section {
                padding: 0.75rem;
            }
            
            .logo {
                width: 80px;
                height: 80px;
                margin-bottom: 0.5rem;
            }
            
            .welcome-text {
                font-size: 1.2rem;
            }
            
            .company-name {
                font-size: 1rem;
            }
            
            .waves {
                height: 25px;
            }
        }

        /* Touch device optimization */
        @media (hover: none) {
            .form-control, .btn-login {
                font-size: 16px; /* Prevents zoom on iOS */
            }
            
            .form-check {
                margin-bottom: 1rem;
            }
            
            .form-check input[type="checkbox"] {
                width: 18px;
                height: 18px;
            }
        }
    </style>
</head>
<body>
    <div class="left-section">
        <div class="logo-container">
            <h1 class="welcome-text">SELAMAT DATANG</h1>
            <img src="{{ asset('images/login.png') }}" alt="Logo Tirta Pakuan" class="logo">
            <h2 class="company-name">TIRTA PAKUAN BOGOR</h2>
        </div>

        <!-- Animated waves -->
        <svg class="waves" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"
            viewBox="0 24 150 28" preserveAspectRatio="none" shape-rendering="auto">
            <defs>
                <path id="gentle-wave" d="M-160 44c30 0 58-18 88-18s 58 18 88 18 58-18 88-18 58 18 88 18 v44h-352z" />
            </defs>
            <g class="parallax">
                <use xlink:href="#gentle-wave" x="48" y="0" fill="rgba(255,255,255,0.7" />
                <use xlink:href="#gentle-wave" x="48" y="3" fill="rgba(255,255,255,0.5)" />
                <use xlink:href="#gentle-wave" x="48" y="5" fill="rgba(255,255,255,0.3)" />
                <use xlink:href="#gentle-wave" x="48" y="7" fill="#fff" />
            </g>
        </svg>
    </div>

    <div class="right-section">
        <div class="login-container">
            <div class="login-header">
                <img src="{{ asset('images/login.png') }}" alt="Login Icon" class="login-icon">
                <h2 class="login-title">Login</h2>
            </div>

            @if(session('status'))
                <div class="alert alert-success">
                    {{ session('status') }}
                </div>
            @endif
            
            @if($errors->any())
                <div class="alert alert-danger">
                    {{ $errors->first() }}
                </div>
            @endif

            <form action="{{ route('login.submit') }}" method="POST">
                @csrf
                <div class="form-group">
                    <label for="nup">NUP</label>
                    <input type="text" 
                           id="nup"
                           name="nup" 
                           class="form-control @error('nup') is-invalid @enderror" 
                           placeholder="Masukkan NUP"
                           value="{{ old('nup') }}"
                           required
                           autocomplete="username"
                           autofocus>
                    @error('nup')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" 
                           id="password"
                           name="password" 
                           class="form-control @error('password') is-invalid @enderror" 
                           placeholder="Masukkan password"
                           autocomplete="current-password"
                           required>
                    @error('password')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </div>

                <div class="form-check">
                    <input type="checkbox" id="showPassword">
                    <label for="showPassword">Show Password</label>
                </div>

                <button type="submit" class="btn-login">
                    Masuk
                </button>
            </form>
        </div>
    </div>

    <script>
        // Show/Hide Password
        document.getElementById('showPassword').addEventListener('change', function() {
            const passwordInput = document.querySelector('input[name="password"]');
            passwordInput.type = this.checked ? 'text' : 'password';
        });
    </script>
</body>
</html> 