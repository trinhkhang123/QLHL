<?php
  
namespace App\Http\Controllers;

use App\Models\DonVi;
use App\Models\Donvi as ModelsDonvi;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Trainee;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
  
class AuthController extends Controller
{
    public function register()
    {
        return view('auth/register');
    }

    public function test () {
        $donvi = ModelsDonvi::get();
        return $donvi;
    }
  
    public function registerSave(Request $request)
    {
        
        $ans =  $request->all();
        $unitId = $ans["dropdown"][1];
        $year = $ans["dropdown"][0];

        Validator::make($request->all(), [
            'full_name' => 'required',
            'email' => 'required|email',
            'password' => 'required|confirmed',
            'class_name' => 'required'
        ])->validate();
        $user = User::create([
            'name' => $request->full_name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'type_user' => 'trainee'
        ]);
        
        $trainee = Trainee::create([
            'full_name' => $request->full_name,
            'rank' => 0,
            'unit_id' => $unitId, // ID đơn vị của học viên
            'class_name' => $request->class_name,
            'start_date' => now(),
            'year_id' => $year
        ]);
        
        

        $trainee->user()->associate($user);
        $trainee->save();
  
        $trainee = Trainee::with('unit')->get();
  
        return redirect()->route('products');
    }
  
    public function login()
    {
        return view('auth/login');
    }
  
    public function loginAction(Request $request)
    {
        Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required'
        ])->validate();
  
        if (!Auth::attempt($request->only('email', 'password'), $request->boolean('remember'))) {
            throw ValidationException::withMessages([
                'email' => trans('auth.failed')
            ]);
        }
  
        $request->session()->regenerate();
  
        return redirect()->route('dashboard');
    }
  
    public function logout(Request $request)
    {
        Auth::guard('web')->logout();
  
        $request->session()->invalidate();
  
        return redirect('/');
    }
 
    public function profile()
    {
        $user = Auth::user();
        $trainee = $user->trainee;
        return view('profile',compact('trainee'));
    }
}