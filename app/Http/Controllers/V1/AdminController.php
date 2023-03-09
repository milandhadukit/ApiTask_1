<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use app\User;
use Illuminate\Support\Facades\Hash;

class AdminController extends Controller
{
    public function loginProcess(Request $request)
    {
         if(Auth::attempt(['email'=>$request->email,'password'=>$request->password])) {
           echo $check = Session::put('checked','ok');
            return redirect('api/documentation');
         }  else {
            
            return redirect('/login')->with('message','invalid Username or Password');
         }
     }
     public function logout(){

        Session::flush();
        return redirect('/login');
     }
 
}
