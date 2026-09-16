<?php

namespace Database\Seeders;

use App\Models\Obat;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ObatSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $items = [
            [
                'code' => 'OBT-001',
                'name' => 'Paracetamol 500mg',
                'category' => 'Analgesik',
                'unit' => 'Strip',
                'purchase_price' => 5000,
                'selling_price' => 7500,
                'stock' => 150,
                'minimum_stock' => 20,
                'expired_date' => '2026-12-31',
                'notes' => 'Obat penurun panas dan pereda nyeri',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'code' => 'OBT-002',
                'name' => 'Amoxicillin 500mg',
                'category' => 'Antibiotik',
                'unit' => 'Strip',
                'purchase_price' => 10000,
                'selling_price' => 15000,
                'stock' => 80,
                'minimum_stock' => 15,
                'expired_date' => '2026-10-15',
                'notes' => 'Harus dengan resep dokter',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'code' => 'OBT-003',
                'name' => 'OBH Combi Sirup 100ml',
                'category' => 'Obat Batuk',
                'unit' => 'Botol',
                'purchase_price' => 18000,
                'selling_price' => 23000,
                'stock' => 45,
                'minimum_stock' => 10,
                'expired_date' => '2027-01-20',
                'notes' => 'Sirup batuk berdahak',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'code' => 'OBT-004',
                'name' => 'Vitamin C 500mg',
                'category' => 'Vitamin & Suplemen',
                'unit' => 'Botol',
                'purchase_price' => 25000,
                'selling_price' => 32000,
                'stock' => 60,
                'minimum_stock' => 10,
                'expired_date' => '2027-05-10',
                'notes' => 'Menjaga daya tahan tubuh',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'code' => 'OBT-005',
                'name' => 'Antasida Doen',
                'category' => 'Maag',
                'unit' => 'Strip',
                'purchase_price' => 3500,
                'selling_price' => 5500,
                'stock' => 120,
                'minimum_stock' => 25,
                'expired_date' => '2026-08-30',
                'notes' => 'Pereda asam lambung',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'code' => 'OBT-006',
                'name' => 'CTM 4mg',
                'category' => 'Antihistamin',
                'unit' => 'Strip',
                'purchase_price' => 2000,
                'selling_price' => 3500,
                'stock' => 200,
                'minimum_stock' => 30,
                'expired_date' => '2027-03-14',
                'notes' => 'Obat alergi dan gatal',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'code' => 'OBT-007',
                'name' => 'Ibuprofen 400mg',
                'category' => 'Analgesik',
                'unit' => 'Strip',
                'purchase_price' => 7000,
                'selling_price' => 10000,
                'stock' => 90,
                'minimum_stock' => 15,
                'expired_date' => '2026-11-20',
                'notes' => 'Pereda nyeri dan antiinflamasi',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'code' => 'OBT-008',
                'name' => 'Promag Tablet',
                'category' => 'Maag',
                'unit' => 'Strip',
                'purchase_price' => 8500,
                'selling_price' => 12000,
                'stock' => 110,
                'minimum_stock' => 20,
                'expired_date' => '2027-06-18',
                'notes' => 'Obat sakit maag dan kembung',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'code' => 'OBT-009',
                'name' => 'Minyak Kayu Putih 60ml',
                'category' => 'Minyak & Salep',
                'unit' => 'Botol',
                'purchase_price' => 19000,
                'selling_price' => 24500,
                'stock' => 50,
                'minimum_stock' => 10,
                'expired_date' => '2028-02-01',
                'notes' => 'Meringankan perut kembung dan masuk angin',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'code' => 'OBT-010',
                'name' => 'Betadine Antiseptic Solution 30ml',
                'category' => 'Antiseptik',
                'unit' => 'Botol',
                'purchase_price' => 16000,
                'selling_price' => 21000,
                'stock' => 40,
                'minimum_stock' => 10,
                'expired_date' => '2027-09-30',
                'notes' => 'Obat luar untuk luka ringan',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        // insertOrIgnore supaya aman dijalankan ulang (tidak duplikat OBT-001 dst)
        DB::table('obat')->insertOrIgnore($items);

        // 10.000 data dummy untuk uji search, pagination, queue & export.
        // Dibuat per 500 baris (20x) supaya hemat memori:
        // kalau langsung 10.000 sekaligus, PHP harus menampung 10.000 model di RAM.
        for ($i = 0; $i < 20; $i++) {
            Obat::factory()->count(500)->create();
        }
    }
}
