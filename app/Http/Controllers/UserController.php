<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function getUsernameEmail(Request $request){
        $users  =   User::where('display_name', 'LIKE', '%'.$request->keyword.'%')->orWhere('user_email', 'LIKE', '%'.$request->keyword.'%')->select('ID as value', 'display_name', 'user_email')->limit(10)->get();

        return $users;
    }
}
