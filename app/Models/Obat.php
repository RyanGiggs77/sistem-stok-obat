<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Obat extends Model
{
    use HasFactory;

    protected $table = 'obat';

    protected $fillable = [
        'code',
        'name',
        'category',
        'unit',
        'purchase_price',
        'selling_price',
        'stock',
        'minimum_stock',
        'expired_date',
        'notes',
    ];

    /**
     * Satu-satunya tempat aturan filter search/kategori/status stok.
     * Dipakai tabel (data), export Excel, dan export PDF supaya konsisten.
     */
    public function scopeFilter($query, array $filters)
    {
        if (! empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('code', 'like', "%{$search}%")
                    ->orWhere('category', 'like', "%{$search}%");
            });
        }

        if (! empty($filters['category'])) {
            $query->where('category', $filters['category']);
        }

        if (! empty($filters['stock_status'])) {
            $status = $filters['stock_status'];
            if ($status === 'out') {
                $query->where('stock', '<=', 0);
            } elseif ($status === 'low') {
                $query->whereColumn('stock', '<=', 'minimum_stock')
                    ->where('stock', '>', 0);
            } elseif ($status === 'expired') {
                $query->where('expired_date', '<', now()->toDateString());
            } elseif ($status === 'available') {
                $query->where('stock', '>', 0);
            }
        }

        return $query;
    }
}
