<?php

namespace Database\Seeders;

use App\Models\Carrier;
use Illuminate\Database\Seeder;

class CarrierSeeder extends Seeder
{
    /**
     * Seed global maritime carriers.
     */
    public function run(): void
    {
        $carriers = [
            [
                'name' => 'Maersk Line (A.P. Moller)',
                'code' => 'MSK',
                'contact_email' => 'ops.asia.europe@maersk.com',
                'contact_phone' => '+45 33 63 33 63',
                'is_active' => true,
            ],
            [
                'name' => 'MSC Mediterranean Shipping Company',
                'code' => 'MSC',
                'contact_email' => 'tracking.line@msc.com',
                'contact_phone' => '+41 22 703 88 88',
                'is_active' => true,
            ],
            [
                'name' => 'CMA CGM Group',
                'code' => 'CMA',
                'contact_email' => 'cargo.visibility@cma-cgm.com',
                'contact_phone' => '+33 4 88 91 90 00',
                'is_active' => true,
            ],
            [
                'name' => 'Hapag-Lloyd AG',
                'code' => 'HAG',
                'contact_email' => 'operations@hapag-lloyd.com',
                'contact_phone' => '+49 40 3001 0',
                'is_active' => true,
            ],
            [
                'name' => 'Ocean Network Express (ONE)',
                'code' => 'ONE',
                'contact_email' => 'sg.liner@one-line.com',
                'contact_phone' => '+65 6890 1000',
                'is_active' => true,
            ],
            [
                'name' => 'Evergreen Marine Corporation',
                'code' => 'EMC',
                'contact_email' => 'customer.service@evergreen-marine.com',
                'contact_phone' => '+886 2 2501 1111',
                'is_active' => true,
            ],
        ];

        foreach ($carriers as $carrier) {
            Carrier::firstOrCreate(
                ['code' => $carrier['code']],
                $carrier
            );
        }
    }
}
