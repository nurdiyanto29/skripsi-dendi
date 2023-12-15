<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use App\Rules\MatchOldPassword;
use Symfony\Component\Console\Input\Input;

class AuthController extends Controller
{
    public function authenticate(Request $request)
    {

        $validator = Validator::make(
            $request->all(),
            [
                'email' => 'required|email',
                'password' => 'required|string',
            ],
            [
                'password.required' => 'Password wajib diisi',
                'email.required' => 'email wajib diisi',
                'email.email' => 'format email tidak sesuai',

            ]

        );
        if ($validator->fails()) {
            return back()->withErrors($validator)
                ->withInput();
        }else {
            // dd($_POST);
            $credentials = $request->only('email', 'password');
            if (Auth::attempt($credentials)) {
                $request->session()->regenerate();
                if (Auth::user()->role == 'Pemilik') {
                    return redirect()->route('admin.index');
                }elseif (Auth::user()->role == 'Penyewa') {
                    return redirect()->route('home.index');
                } else {
                    return Redirect()->back()->with(
                        [
                            'error' => 'Anda tidak memiliki akses'
                        ]
                    );
                }
            } else {
                return Redirect()->back()->with(
                    [
                        'error' => 'Email atau Password Salah'
                    ]
                );
            }
        }
    }
    public function logout()
    {
        Auth::logout();
        return redirect('/login');
    }
    public function login()
    {
        return view('login');
    }

    public function register()
    {
        // return view('register');
        return view('blank');
    }
    public function register_store(Request $req)
    {

        $validator = Validator::make($req->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
        ]);
    
        if ($validator->fails()) {
            return redirect('register')
                ->withErrors($validator)
                ->withInput();
        }
        
        $user = new User();
        $user->name = $req->name;
        $user->email = $req->email;
        $user->password = bcrypt($req->password);
        $user->tlp = $req->tlp;
        $user->alamat = $req->alamat;
        $user->save();



        return redirect()->route('login')->with('success', 'register Berhasil.');
    }
    public function profile()
    {
        return view('profile');
    }
    public function ubahpwstore(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'password_lama' => ['required', new MatchOldPassword],
                'password_baru' => ['required'],
                'konfirmasi_password' => ['same:password_baru', 'required'],
            ],
            [
                'password_lama.required' => 'password lama harus diisi',
                'password_baru.required' => 'password baru harus diisi',
                'konfirmasi_password.required' => 'konfirmasi password harus diisi',
                'konfirmasi_password.same' => 'konfirmasi password harus sama dengan password baru',
            ]
        );

        if ($validator->fails()) {
            return back()->withErrors($validator)
                ->withInput();
        }
        User::find(Auth::user()->id)->update(['password' => Hash::make($request->password_baru)]);
        return redirect()->route('logout.auth')->with('success', 'Silahkan login ulang degan password yang baru.');
    }
}
