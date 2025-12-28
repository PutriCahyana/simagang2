<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index(){
         $data = array(
            "judul"         => "Mentor",
            "menuAdminUser" => "active",
            'user'          => User::get(),
            'mentor'        => User::where('role','mentor')->with(['mentor.rooms'])->get(),
        );
        return view('admin/user/index', $data);
    }

    public function destroy($id)
    {
        try {
            $user = User::findOrFail($id);
            $user->delete();
            
            return redirect()->route('user')
                ->with('success', 'Mentor berhasil dihapus');
        } catch (\Exception $e) {
            return redirect()->route('user')
                ->with('error', 'Gagal menghapus mentor');
        }
    }
}
