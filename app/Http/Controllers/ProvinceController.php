<?php

namespace App\Http\Controllers;

use App\Models\Province;

class ProvinceController extends Controller{
    /**
     * قائمة المحافظات
     */
    public function provinces()
    {
        $provinces = Province::select('id', 'name')
            ->orderBy('name')
            ->get();

        return apiSuccess( "قائمة المحافظات", $provinces);
    }
}