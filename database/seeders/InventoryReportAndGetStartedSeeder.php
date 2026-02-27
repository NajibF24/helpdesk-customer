<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class InventoryReportAndGetStartedSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $modules = collect(['Inventory Report', 'Inventory Transaction Report']);
        $parent = DB::table('authorization_module')->where('name', 'Inventory Transaction')->first(['id']);

        DB::table('authorization_module')->insert($modules->map(function($row, $key) use($parent) {
            return [
                'name' => $row,
                'parent_id' => $parent->id,
                'order' => $key + 1,
                'status' => 'A'
            ];
        })->toArray());
    
    }
}
