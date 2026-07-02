<?php

namespace Database\Seeders;

use App\Models\Province;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProvinceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $provinces = [
            ['id' => 1,'name' =>  'دمشق'],
            ['id' => 2,'name' =>  'حلب'],
            ['id' => 3,'name' =>  'ريف دمشق'],
            ['id' => 4,'name' =>  'درعا'],
            ['id' => 5,'name' =>  'السويداء'],
            ['id' => 6,'name' =>  'القنيطرة'],
            ['id' => 7,'name' => 'اللاذقية'],
            ['id' => 8,'name' =>  'طرطوس'],
            ['id' => 9,'name' =>  'إدلب'],
            ['id' => 10,'name' => 'حماة'],
            ['id' => 11,'name' =>  'الحسكة'],
            ['id' => 12,'name' =>  'الرقة'],
            ['id' => 13,'name' =>  'ديرالزور'],
            ['id' => 14,'name' => 'حمص'],
        ];
        DB::table('provinces')->insert($provinces);
    }
}
