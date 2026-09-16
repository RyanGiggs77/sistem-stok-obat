<?php

namespace Database\Factories;

use App\Models\Obat;
use Illuminate\Database\Eloquent\Factories\Factory;

class ObatFactory extends Factory
{
    protected $model = Obat::class;

    public function definition(): array
    {
        $categories = [
            'Analgesik', 'Antibiotik', 'Obat Batuk', 'Vitamin & Suplemen',
            'Maag', 'Antihistamin', 'Minyak & Salep', 'Antiseptik',
        ];

        $units = ['Strip', 'Botol', 'Tablet', 'Kapsul', 'Sirup', 'Tube'];

        $names = [
            'Paracetamol', 'Amoxicillin', 'Ibuprofen', 'Vitamin C', 'Antasida',
            'CTM', 'OBH Combi', 'Promag', 'Betadine', 'Kayu Putih',
            'Amlodipine', 'Metformin', 'Omeprazole', 'Cefixime', 'Loratadine',
        ];

        $name = $this->faker->randomElement($names)
            .' '.$this->faker->randomElement(['100mg', '250mg', '500mg', '60ml', '100ml', '10 Tablet']);

        $purchase = $this->faker->numberBetween(2000, 50000);
        $stock = $this->faker->numberBetween(0, 300);
        $minimum = $this->faker->numberBetween(10, 30);

        return [
            // OBT- + 6 digit acak, unique agar tidak tabrakan dengan OBT-001 manual
            'code' => $this->faker->unique()->bothify('OBT-######'),
            'name' => $name,
            'category' => $this->faker->randomElement($categories),
            'unit' => $this->faker->randomElement($units),
            'purchase_price' => $purchase,
            'selling_price' => $purchase + $this->faker->numberBetween(1000, 15000),
            // Sengaja buat variasi: ada yang habis (0), menipis, dan tersedia
            'stock' => $stock,
            'minimum_stock' => $minimum,
            // 10% kadaluarsa (tanggal lewat), sisanya masa depan
            'expired_date' => $this->faker->boolean(10)
                ? $this->faker->dateTimeBetween('-1 year', '-1 day')->format('Y-m-d')
                : $this->faker->dateTimeBetween('+1 month', '+3 years')->format('Y-m-d'),
            'notes' => $this->faker->optional(0.5)->sentence(6),
        ];
    }
}
