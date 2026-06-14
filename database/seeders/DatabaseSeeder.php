<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Medicine;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Create users
        User::create([
            'name' => 'Admin',
            'username' => 'admin',
            'password' => bcrypt('admin123'),
            'role' => 'admin',
        ]);

        User::create([
            'name' => 'Petugas Pendaftaran',
            'username' => 'pendaftaran',
            'password' => bcrypt('123456'),
            'role' => 'pendaftaran',
        ]);

        User::create([
            'name' => 'Dr. Budi Santoso',
            'username' => 'dokter',
            'password' => bcrypt('123456'),
            'role' => 'dokter',
        ]);

        User::create([
            'name' => 'Kasir',
            'username' => 'kasir',
            'password' => bcrypt('123456'),
            'role' => 'kasir',
        ]);

        User::create([
            'name' => 'Apoteker',
            'username' => 'apoteker',
            'password' => bcrypt('123456'),
            'role' => 'apoteker',
        ]);

        User::create([
            'name' => 'Owner',
            'username' => 'owner',
            'password' => bcrypt('123456'),
            'role' => 'owner',
        ]);

        // Create sample service actions (Jasa/Tindakan)
        $serviceActions = [
            ['name' => 'Konsultasi Umum', 'description' => 'Konsultasi dokter umum', 'price' => 50000],
            ['name' => 'Injeksi', 'description' => 'Tindakan penyuntikan obat', 'price' => 30000],
            ['name' => 'Nebulizer', 'description' => 'Terapi uap untuk gangguan pernapasan', 'price' => 35000],
            ['name' => 'Rawat Luka', 'description' => 'Perawatan luka ringan/sedang', 'price' => 50000],
            ['name' => 'Jahit Luka', 'description' => 'Menjahit luka robek', 'price' => 75000],
            ['name' => 'Cabut Gigi', 'description' => 'Pencabutan gigi', 'price' => 100000],
            ['name' => 'EKG', 'description' => 'Rekam jantung', 'price' => 75000],
            ['name' => 'Lab Sederhana', 'description' => 'Pemeriksaan laboratorium dasar', 'price' => 50000],
            ['name' => 'Cek Gula Darah', 'description' => 'Pemeriksaan gula darah cepat', 'price' => 25000],
            ['name' => 'Cek Asam Urat', 'description' => 'Pemeriksaan asam urat', 'price' => 25000],
            ['name' => 'Cek Kolesterol', 'description' => 'Pemeriksaan kolesterol', 'price' => 30000],
            ['name' => 'Insisi Abses', 'description' => 'Pembukaan dan drainase abses', 'price' => 60000],
            ['name' => 'Pemasangan Infus', 'description' => 'Terapi cairan infus', 'price' => 40000],
            ['name' => 'Kuretase', 'description' => 'Tindakan kuretase ringan', 'price' => 150000],
            ['name' => 'Terapi Oksigen', 'description' => 'Pemberian oksigen', 'price' => 30000],
            ['name' => 'Tindik Telinga', 'description' => 'Tindik telinga medis', 'price' => 25000],
            ['name' => 'Pemeriksaan Mata', 'description' => 'Pemeriksaan visus mata', 'price' => 35000],
            ['name' => 'Pemeriksaan THT', 'description' => 'Pemeriksaan telinga hidung tenggorokan', 'price' => 40000],
            ['name' => 'Sirkumsisi', 'description' => 'Khitan medis', 'price' => 200000],
            ['name' => 'Pemasangan IUD', 'description' => 'Pemasangan alat kontrasepsi IUD', 'price' => 250000],
        ];

        foreach ($serviceActions as $action) {
            \App\Models\ServiceAction::create($action);
        }

        // Create sample medicines
        $medicines = [
            ['code' => 'OBT001', 'name' => 'Paracetamol 500mg', 'category' => 'Tablet', 'unit' => 'strip', 'stock' => 100, 'purchase_price' => 5000, 'selling_price' => 10000],
            ['code' => 'OBT002', 'name' => 'Amoxicillin 500mg', 'category' => 'Kapsul', 'unit' => 'strip', 'stock' => 80, 'purchase_price' => 8000, 'selling_price' => 15000],
            ['code' => 'OBT003', 'name' => 'Ibuprofen 400mg', 'category' => 'Tablet', 'unit' => 'strip', 'stock' => 60, 'purchase_price' => 6000, 'selling_price' => 12000],
            ['code' => 'OBT004', 'name' => 'Antasida DOEN', 'category' => 'Tablet', 'unit' => 'strip', 'stock' => 90, 'purchase_price' => 4000, 'selling_price' => 8000],
            ['code' => 'OBT005', 'name' => 'CTM 4mg', 'category' => 'Tablet', 'unit' => 'strip', 'stock' => 100, 'purchase_price' => 2000, 'selling_price' => 5000],
            ['code' => 'OBT006', 'name' => 'Salbutamol 2mg', 'category' => 'Tablet', 'unit' => 'strip', 'stock' => 50, 'purchase_price' => 10000, 'selling_price' => 20000],
            ['code' => 'OBT007', 'name' => 'Dextromethorphan', 'category' => 'Sirup', 'unit' => 'botol', 'stock' => 30, 'purchase_price' => 15000, 'selling_price' => 25000],
            ['code' => 'OBT008', 'name' => 'Betadine Solution', 'category' => 'Cairan', 'unit' => 'botol', 'stock' => 25, 'purchase_price' => 12000, 'selling_price' => 22000],
            ['code' => 'OBT009', 'name' => 'Ambroxol', 'category' => 'Sirup', 'unit' => 'botol', 'stock' => 35, 'purchase_price' => 18000, 'selling_price' => 30000],
            ['code' => 'OBT010', 'name' => 'Omeprazole 20mg', 'category' => 'Kapsul', 'unit' => 'strip', 'stock' => 70, 'purchase_price' => 12000, 'selling_price' => 25000],
        ];

        foreach ($medicines as $medicine) {
            Medicine::create($medicine);
        }

        $this->command->info('Database seeded successfully!');
        $this->command->info('Login credentials:');
        $this->command->info('Admin: admin / admin123');
        $this->command->info('Others: Role name / 123456');
    }
}