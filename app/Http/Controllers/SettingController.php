<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;

class SettingController extends Controller
{
    
    function provinces(){
        return apiSuccess("قائمة المحافظات", DB::table('provinces')->select("id" , "name")->get());
    }
}
