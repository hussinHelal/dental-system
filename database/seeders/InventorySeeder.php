<?php

namespace Database\Seeders;

use App\Models\InventoryItem;
use Illuminate\Database\Seeder;

class InventorySeeder extends Seeder
{
    public function run(): void
    {
        $items = [
            ['name' => 'Nitrile Gloves (M)', 'quantity' => 40, 'unit' => 'box', 'category' => 'PPE', 'low_stock_threshold' => 10],
            ['name' => 'Local Anesthetic Cartridges', 'quantity' => 3, 'unit' => 'box', 'category' => 'Pharmacy', 'low_stock_threshold' => 5],
            ['name' => 'Composite Resin', 'quantity' => 15, 'unit' => 'piece', 'category' => 'Restorative', 'low_stock_threshold' => 5],
            ['name' => 'Sterilization Pouches', 'quantity' => 200, 'unit' => 'piece', 'category' => 'Sterilization', 'low_stock_threshold' => 50],
            ['name' => 'Fluoride Gel', 'quantity' => 6, 'unit' => 'ml', 'category' => 'Preventive', 'low_stock_threshold' => 10],
            ['name' => 'Dental Floss Spools', 'quantity' => 30, 'unit' => 'piece', 'category' => 'Preventive', 'low_stock_threshold' => 10],
        ];

        foreach ($items as $item) {
            InventoryItem::firstOrCreate(['name' => $item['name']], $item);
        }
    }
}
