<?php

namespace Database\Seeders;

use App\Models\Kelas;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class KelasMahasiswaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Buat Data Kelas
        $kelasA = Kelas::create([
            'nama_kelas'   => 'TI-A',
            'tahun_ajaran' => '2026',
        ]);

        $kelasB = Kelas::create([
            'nama_kelas'   => 'TI-B',
            'tahun_ajaran' => '2026',
        ]);

        // 2. Data Mahasiswa
        $mahasiswaList = [
            [
                'nama'     => 'Ahmad Rizky',
                'email'    => 'keysalenamis@gmail.com',
                'kelas_id' => $kelasA->kelas_id,
            ],
            [
                'nama'     => 'Siti Nurhaliza',
                'email'    => 'siti.nurhaliza@example.com',
                'kelas_id' => $kelasA->kelas_id,
            ],
            [
                'nama'     => 'Budi Santoso',
                'email'    => 'budi.santoso@example.com',
                'kelas_id' => $kelasB->kelas_id,
            ],
            [
                'nama'     => 'Dewi Lestari',
                'email'    => 'dewi.lestari@example.com',
                'kelas_id' => $kelasB->kelas_id,
            ],
            [
                'nama'     => 'Eko Prasetyo',
                'email'    => 'eko.prasetyo@example.com',
                'kelas_id' => null, // Mahasiswa tanpa kelas
            ],
        ];

        // 3. Simpan Mahasiswa ke Tabel Users
        foreach ($mahasiswaList as $mhs) {
            User::create([
                'user_id'       => (string) Str::uuid(),
                'role'          => 'mahasiswa',
                'nama'          => $mhs['nama'],
                'email'         => $mhs['email'],
                'password_hash' => Hash::make('123456'),
                'kelas_id'      => $mhs['kelas_id'],
                'level'         => 1,
                'total_xp'      => 0,
            ]);
        }
    }
}