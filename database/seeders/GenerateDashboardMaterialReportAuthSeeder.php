<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class GenerateDashboardMaterialReportAuthSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $matchThese = ['name' => 'Dashboard Material Report', 'parent_id' => 90];
        DB::table('authorization_module')->updateOrInsert($matchThese, [...$matchThese, 'order' => 3, 'status' => 'A']);
    }
}
