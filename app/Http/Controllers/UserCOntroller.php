<?php

namespace App\Http\Controllers;

use App\Models\User;
Use Illuminate\Http\Request;

class UserCOntroller extends Controller
{
    public function index()
    {
        $data = User::orderBy('id','desc')->where('role','Penyewa')->get();
        // dd($data);
        return view('user.index', compact('data'));
    }
    public function store(Request $request)
    {
        $data = new User();
        $data->name = $request->name;
        $data->email = $request->email;
        $data->tlp = $request->tlp;
        $data->alamat = $request->alamat;
        $data->role = 'Penyewa';

        $data->password = bcrypt('password');
        $data->save();
        return redirect()->back()->with(['t' =>  'success', 'm'=> 'Data berhasil ditambah']);;
    }
 
    public function update(Request $request)
    {
        $data =User::where('id', $request->get('id'))
        ->update([
            'name'=>$request->get('name'),
            'email'=>$request->get('email'),
            'tlp'=>$request->get('tlp'),
            'alamat'=>$request->get('alamat'),
        ]);
        return redirect()->back()
        ->with(['t' =>  'success', 'm'=> 'Data berhasil diupdate']);
    }
    public function destroy(Request $request)
    {
        $data =User::findorFail($request->id);
        $data->delete();
        return redirect()->back()
        ->with(['t' =>  'success', 'm'=> 'Data berhasil dihapus']);
    }
}
