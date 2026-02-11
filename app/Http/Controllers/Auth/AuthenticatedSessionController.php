<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\LoginAttempt;
use App\Models\OtpRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        // Update last login
        User::updateLastLogin(Auth::id());

        // Log successful login
        LoginAttempt::create([
            'destination' => $request->email,
            'channel' => 'password',
            'ip_address' => $request->ip(),
            'success' => true,
        ]);

        return redirect()->intended(route('dashboard'));
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }

    /**
     * Whatsapp.
     */
    public function showLoginForm()
    {
        return view('auth.whatsapp-login');
    }

    // 2. Proses Kirim OTP
    public function sendOtp(Request $request)
    {
        $request->validate([
            'phone' => 'required|numeric|digits_between:10,15',
        ]);

        // Format nomor ke 62 (hapus 0 di depan jika ada)
        $phone = $request->phone;
        if (substr($phone, 0, 1) == '0') {
            $phone = '62' . substr($phone, 1);
        }

        // Cek apakah user terdaftar
        $user = User::where('phone', $phone)->first();

        if (!$user) {
            return back()->withErrors(['phone' => 'Nomor WhatsApp belum terdaftar di sistem kami.']);
        }

        // PENGECEKAN ROLE 
        if ($user->role === 'patient') {
            return back()->withErrors(['phone' => 'Akses ditolak. Akun pasien tidak memiliki izin akses.']);
        }

        // Generate OTP 6 digit
        $otp = rand(100000, 999999);

        // Simpan ke database (Sesuai tabel otp_requests Anda)
        OtpRequest::create([
            'user_id' => $user->id,
            'destination' => $phone,
            'channel' => 'WHATSAPP',
            'otp_code_hash' => Hash::make($otp), // Hash OTP sesuai struktur DB
            'purpose' => 'LOGIN',
            'expired_at' => Carbon::now()->addMinutes(5),
        ]);

        // --- SIMULASI KIRIM WA (INTEGRASI API DISINI) ---
        // Contoh: Fonnte, Twilio, atau Wablas
        // Http::post('https://api.fonnte.com/send', ['target' => $phone, 'message' => "Kode OTP Liasdent Anda: $otp"]);

        // Untuk Development: Kita log dulu kodenya agar bisa login
        Log::info("Kode OTP untuk $phone: $otp");

        // Simpan nomor di session untuk halaman verifikasi
        session(['wa_phone' => $phone]);

        return redirect()->route('login.whatsapp.verify')->with('success', 'Kode OTP telah dikirim ke WhatsApp Anda.');
    }

    // 3. Tampilkan Form Input OTP
    public function showVerifyForm()
    {
        if (!session('wa_phone')) {
            return redirect()->route('login.whatsapp');
        }
        return view('auth.whatsapp-verify');
    }

    // 4. Proses Verifikasi OTP
    public function verifyOtp(Request $request)
    {
        $request->validate([
            'otp' => 'required|numeric|digits:6',
        ]);

        $phone = session('wa_phone');

        // Cari OTP valid terakhir yang belum expired dan belum diverifikasi
        $otpRecord = OtpRequest::where('destination', $phone)
            ->where('purpose', 'LOGIN')
            ->where('channel', 'WHATSAPP')
            ->whereNull('verified_at')
            ->where('expired_at', '>', Carbon::now())
            ->latest()
            ->first();

        // Cek kecocokan Hash
        if (!$otpRecord || !Hash::check($request->otp, $otpRecord->otp_code_hash)) {
            return back()->withErrors(['otp' => 'Kode OTP salah atau kedaluwarsa.']);
        }

        // Tandai OTP sudah dipakai
        $otpRecord->update(['verified_at' => Carbon::now()]);

        // Login User
        $user = User::find($otpRecord->user_id);
        Auth::login($user);

        // Hapus session phone
        $request->session()->forget('wa_phone');

        // Redirect sesuai Role
        return redirect()->route('nakes.dashboard');
    }
}
