<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login - Klinik Pratama Ramah</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <script src="https://cdn.tailwindcss.com"></script>

    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        body, html {
            height: 100%;
            font-family: 'Roboto', sans-serif;
            overflow: hidden;
        }

        .login-container {
            display: flex;
            width: 100vw;
            height: 100vh;
        }

        /* Bagian Kiri (Form) */
        .left-section {
            flex: 0 0 450px;
            background: #ffffff;
            display: flex;
            flex-direction: column;
            padding: 50px;
            z-index: 10;
            box-shadow: 10px 0 30px rgba(0, 0, 0, 0.05);
        }

        .form-wrapper {
            margin: auto 0; /* Membuat form berada di tengah secara vertikal di sisi kiri */
        }

        .logo-section {
            text-align: center;
            margin-bottom: 30px;
        }

        .logo-title {
            font-size: 26px;
            font-weight: 700;
            color: #16a34a; /* Hijau sesuai tema klinik */
            margin-top: 10px;
        }

        .divider {
            height: 1px;
            background: #f1f5f9;
            margin: 25px 0;
        }

        .form-group {
            position: relative;
            margin-bottom: 25px;
        }

        .form-control {
            width: 100%;
            padding: 12px 15px;
            padding-right: 45px;
            border: none;
            border-bottom: 2px solid #e2e8f0;
            font-size: 15px;
            transition: all 0.3s ease;
            outline: none;
        }

        .form-control:focus {
            border-bottom-color: #5DB8D8;
        }

        .input-icon {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #94a3b8;
            font-size: 18px;
            cursor: pointer;
        }

        .btn-login {
            width: 100%;
            padding: 14px;
            background: linear-gradient(135deg, #5DB8D8 0%, #4da7c7 100%);
            border: none;
            border-radius: 12px;
            color: white;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(93, 184, 216, 0.3);
        }

        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(93, 184, 216, 0.4);
        }

        /* Bagian Kanan (Background) */
        .right-section {
            flex: 1;
            position: relative;
            background-image: url('{{ asset("images/bg.jpg") }}'); /* Sesuaikan path file asli */
            background-size: cover;
            background-position: center;
        }

        .overlay {
            position: absolute;
            inset: 0;
            background: linear-gradient(135deg, rgba(22, 163, 74, 0.3) 0%, rgba(0, 0, 0, 0.2) 100%);
        }

        .info-card {
            position: absolute;
            bottom: 40px;
            left: 40px;
            right: 40px;
            max-width: 500px;
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(10px);
            padding: 30px;
            border-radius: 20px;
            box-shadow: 0 20px 50px rgba(0,0,0,0.2);
        }

        /* Responsive Mobile */
        @media (max-width: 992px) {
            .right-section { display: none; }
            .left-section { flex: 1; width: 100%; padding: 30px; }
        }
    </style>
</head>
<body>

<div class="login-container">
    <div class="left-section">
        <div class="form-wrapper">
            <div class="logo-section">
                <img src="{{ asset('images/logo-new.png') }}" alt="Logo" style="height: 90px; margin: 0 auto;" onerror="this.src='https://placehold.co/200x70?text=LOGO+KLINIK'">
                <div class="logo-title">Klinik Pratama Ramah</div>
                <p class="text-xs text-gray-400 uppercase tracking-widest mt-1">Monitoring Pasien</p>
            </div>

            <div class="divider"></div>

            <div class="mb-6">
                <h3 class="text-2xl font-bold text-gray-800">Login</h3>
                <p class="text-sm text-gray-500">Silahkan masuk ke akun Anda</p>
            </div>

            @if($errors->any())
                <div class="mb-4 text-red-600 text-sm bg-red-50 p-3 rounded-lg border border-red-100">
                    {{ $errors->first() }}
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}">
                @csrf
                
                <div class="form-group">
                    <input type="email" name="email" class="form-control" placeholder="Email Address" value="{{ old('email') }}" required autofocus>
                    <span class="input-icon"><i class="bi bi-envelope"></i></span>
                </div>

                <div class="form-group">
                    <input type="password" name="password" id="password" class="form-control" placeholder="Password" required>
                    <span class="input-icon" onclick="togglePass()">
                        <i class="bi bi-eye" id="toggleIcon"></i>
                    </span>
                </div>

                <div class="flex items-center justify-between mb-6">
                    <label class="flex items-center text-sm text-gray-600 cursor-pointer">
                        <input type="checkbox" name="remember" class="mr-2 rounded border-gray-300"> Ingat saya
                    </label>
                    <a href="{{ route('password.request') }}" class="text-sm text-sky-600 hover:underline">Lupa Password?</a>
                </div>

                <button type="submit" class="btn-login">MASUK</button>
            </form>
        </div>

        <div class="mt-auto text-center text-xs text-gray-500 py-6">
            <div class="mb-2">
                <i class="bi bi-geo-alt-fill text-green-600"></i> 
                Jl. Lintas Seraya No. 104, RT 009/RW 004, Desa Bukit Menanti, Kecamatan Hulu Percha, Kota Serambi Tinggi, Sumatera Tengah, 39981.
            </div>
            <div class="text-gray-400">
                &copy; {{ date('Y') }} Klinik Pratama Ramah. All rights reserved.
            </div>
        </div>
    </div>

    <div class="right-section">
        <div class="overlay"></div>
        
        <div class="info-card">
            <h4 class="text-xl font-bold text-green-700 mb-2 uppercase">Sistem Monitoring</h4>
            <p class="text-gray-600 text-sm leading-relaxed">
                Platform terintegrasi untuk pemantauan data pasien secara real-time. 
                Pastikan privasi data tetap terjaga selama penggunaan sistem.
            </p>
        </div>
    </div>
</div>

<script>
    function togglePass() {
        const p = document.getElementById('password');
        const icon = document.getElementById('toggleIcon');
        if (p.type === "password") {
            p.type = "text";
            icon.classList.replace('bi-eye', 'bi-eye-slash');
        } else {
            p.type = "password";
            icon.classList.replace('bi-eye-slash', 'bi-eye');
        }
    }
</script>

</body>
</html>