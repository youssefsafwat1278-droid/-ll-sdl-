<?php
// app/Http/Controllers/AuthController.php
namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Scout;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function showLogin()
    {
        if (Auth::check()) {
            return $this->redirectBasedOnRole();
        }
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'scout_id' => 'required|exists:scouts,scout_id',
            'password' => 'required',
        ], [
            'scout_id.required' => 'رقم الكشاف مطلوب',
            'scout_id.exists' => 'رقم الكشاف غير موجود',
            'password.required' => 'كلمة المرور مطلوبة',
        ]);

        $user = User::where('scout_id', $request->scout_id)->first();

        if (!$user) {
            return back()->withErrors(['scout_id' => 'رقم الكشاف غير مسجل في النظام'])->withInput();
        }

        if (!$this->isBcryptHash($user->password)) {
            if ($request->password === $user->password) {
                $user->password = $request->password;
                $user->save();
            } else {
                return back()->withErrors(['password' => 'سجل اولا كلمه مرور جديدة'])->withInput();
            }
        }

        if (Auth::attempt(['scout_id' => $request->scout_id, 'password' => $request->password], $request->filled('remember'))) {
            $request->session()->regenerate();
            
            Auth::user()->update(['last_login' => now()]);

            return $this->redirectBasedOnRole();
        }

        return back()->withErrors(['password' => 'كلمة المرور غير صحيحة'])->withInput();
    }

    public function showRegister()
    {
        if (Auth::check()) {
            return $this->redirectBasedOnRole();
        }
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $request->validate([
            'scout_id' => 'required|exists:scouts,scout_id',
            'password' => 'required|min:6|confirmed',
        ], [
            'scout_id.required' => 'رقم الكشاف مطلوب',
            'scout_id.exists' => 'رقم الكشاف غير موجود',
            'scout_id.unique' => 'هذا الكشاف مسجل بالفعل',
            'password.required' => 'كلمة المرور مطلوبة',
            'password.min' => 'كلمة المرور يجب أن تكون 6 أحرف على الأقل',
            'password.confirmed' => 'كلمة المرور غير متطابقة',
        ]);

        $scout = Scout::find($request->scout_id);
        $user = User::where('scout_id', $request->scout_id)->first();

        if ($user && $user->password) {
            return back()->withErrors(['scout_id' => 'هذا الكشاف مسجل بالفعل.'])->withInput();
        }

        if ($user) {
            $user->update([
                'password' => $request->password,
                'first_name' => $user->first_name ?? $scout->first_name,
                'last_name' => $user->last_name ?? $scout->last_name,
                'patrol_id' => $user->patrol_id ?? $scout->patrol_id,
                'photo_url' => $user->photo_url ?? $scout->photo_url,
                'team_name' => $user->team_name ?? ($scout->first_name . ' Team'),
            ]);
        } else {
            $user = User::create([
                'scout_id' => $request->scout_id,
                'password' => $request->password,
                'first_name' => $scout->first_name,
                'last_name' => $scout->last_name,
                'patrol_id' => $scout->patrol_id,
                'photo_url' => $scout->photo_url,
                'team_name' => $scout->first_name . ' Team',
                'role' => 'user',
            ]);
        }

        Auth::login($user);

        return redirect('/')->with('success', 'تم التسجيل بنجاح! مرحباً بك في Scout Tanzania');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login')->with('success', 'تم تسجيل الخروج بنجاح');
    }

    public function me()
    {
        return response()->json([
            'user' => Auth::user()->load(['scout', 'patrol']),
        ]);
    }

    private function redirectBasedOnRole()
    {
        if (Auth::user()->role === 'admin') {
            return redirect('/admin/dashboard');
        }
        return redirect('/');
    }

    private function isBcryptHash(?string $value): bool
    {
        if ($value === null) {
            return false;
        }

        return str_starts_with($value, '$2y$')
            || str_starts_with($value, '$2a$')
            || str_starts_with($value, '$2b$');
    }
}
