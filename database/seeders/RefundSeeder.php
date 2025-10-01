<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Refund;

class RefundSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $refunds = [
            [
                'bast_id' => 'BAST-001',
                'total_refund' => 150000.00,
                'status_refund' => 'Pending',
            ],
            [
                'bast_id' => 'BAST-002',
                'total_refund' => 250000.00,
                'status_refund' => 'Diproses',
            ],
            [
                'bast_id' => 'BAST-003',
                'total_refund' => 300000.00,
                'status_refund' => 'Selesai',
            ],
            [
                'bast_id' => 'BAST-004',
                'total_refund' => 75000.00,
                'status_refund' => 'Dibatalkan',
            ],
            [
                'bast_id' => 'BAST-005',
                'total_refund' => 500000.00,
                'status_refund' => 'Pending',
            ],
        ];

        foreach ($refunds as $refund) {
            Refund::create($refund);
        }
    }
}
