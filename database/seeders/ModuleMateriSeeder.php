<?php

namespace Database\Seeders;

use App\Models\Module;
use App\Models\Materi;
use Illuminate\Database\Seeder;

class ModuleMateriSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $modules = [
            // ==========================================
            // MODUL 1
            // ==========================================
            [
                'kode_modul' => 'Modul 1',
                'judul'      => 'Computer System & Interconnection',
                'sub_cpmk'   => 'Sub-CPMK 1-2',
                'urutan'     => 1,
                'deskripsi'  => 'Modul ini membahas komponen dasar arsitektur komputer (CPU, Memori, I/O) serta mekanisme komunikasi antar komponen menggunakan struktur interkoneksi bus.',
                'materis'    => [
                    [
                        'judul'       => 'Pengenalan Computer System & Interconnection',
                        'tipe'        => 'pendahuluan',
                        'urutan'      => 1,
                        'konten_teks' => '<p>Selamat datang di <strong>Modul 1: Computer System & Interconnection</strong>. Pada modul ini Anda akan mempelajari dasar-dasar arsitektur komputer dan bagaimana komponen internal saling berkomunikasi secara efisien.</p><blockquote><p><strong>Tujuan Pembelajaran:</strong> Memahami fungsi umum CPU, memori utama, modul I/O, serta struktur interkoneksi sistem (System Bus).</p></blockquote>',
                        'konten_url'  => null,
                    ],
                    [
                        'judul'       => 'Komponen Utama & Arsitektur Komputer',
                        'tipe'        => 'teks',
                        'urutan'      => 2,
                        'konten_teks' => '<p>Secara umum, sistem komputer terdiri dari 3 blok utama:</p><ol><li><strong>CPU (Central Processing Unit):</strong> Pusat kendali dan pemrosesan instruksi program.</li><li><strong>Memori Utama (RAM):</strong> Tempat penyimpanan sementara data dan instruksi yang sedang dieksekusi.</li><li><strong>Modul Input/Output (I/O):</strong> Antarmuka komunikasi antara komputer dengan perangkat eksternal.</li></ol>',
                        'konten_url'  => null,
                    ],
                    [
                        'judul'       => 'Struktur Interkoneksi & System Bus',
                        'tipe'        => 'teks',
                        'urutan'      => 3,
                        'konten_teks' => '<p>Lalu lintas data antar komponen terhubung melalui <strong>System Bus</strong> yang terbagi menjadi tiga jenis lintasan:</p><ul><li><strong>Data Bus:</strong> Jalur transmisi data antar modul.</li><li><strong>Address Bus:</strong> Menentukan alamat sumber atau tujuan data di memori/I/O.</li><li><strong>Control Bus:</strong> Mengirimkan sinyal kontrol dan timing transaksi data.</li></ul>',
                        'konten_url'  => null,
                    ],
                    [
                        'judul'       => 'Buku Panduan: Bus Architecture',
                        'tipe'        => 'pdf',
                        'urutan'      => 4,
                        'konten_teks' => null,
                        'konten_url'  => 'storage/materi_files/modul1_bus_architecture.pdf',
                    ],
                    [
                        'judul'       => 'Video Visualisasi: Cara Kerja System Bus',
                        'tipe'        => 'video',
                        'urutan'      => 5,
                        'konten_teks' => null,
                        'konten_url'  => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
                    ],
                ]
            ],

            // ==========================================
            // MODUL 2
            // ==========================================
            [
                'kode_modul' => 'Modul 2',
                'judul'      => 'Memory & Storage',
                'sub_cpmk'   => 'Sub-CPMK 3',
                'urutan'     => 2,
                'deskripsi'  => 'Modul ini membahas hierarki memori komputer, prinsip kerja Cache Memory, perbedaan RAM/ROM, serta teknologi penyimpanan sekunder (SSD & HDD).',
                'materis'    => [
                    [
                        'judul'       => 'Pengenalan Memory & Storage Hierarchy',
                        'tipe'        => 'pendahuluan',
                        'urutan'      => 1,
                        'konten_teks' => '<p>Pada <strong>Modul 2</strong>, kita akan mengulas bagaimana memori komputer dirancang berjenjang untuk menyeimbangkan antara <i>kecepatan</i>, <i>kapasitas</i>, dan <i>harga</i>.</p><blockquote><p><strong>Fokus Utama:</strong> Konsep Cache Memory, SRAM vs DRAM, dan media penyimpanan massal (Secondary Storage).</p></blockquote>',
                        'konten_url'  => null,
                    ],
                    [
                        'judul'       => 'Prinsip Kerja Cache Memory',
                        'tipe'        => 'teks',
                        'urutan'      => 2,
                        'konten_teks' => '<p>Cache Memory berfungsi menjembatani perbedaan kecepatan antara CPU berkecepatan tinggi dan Memori Utama yang lebih lambat.</p><h3>Prinsip Lokalitas:</h3><ul><li><strong>Temporal Locality:</strong> Data yang baru diakses cenderung akan diakses kembali dalam waktu dekat.</li><li><strong>Spatial Locality:</strong> Data yang lokasinya berdekatan dengan data yang baru diakses cenderung akan diakses berikutnya.</li></ul>',
                        'konten_url'  => null,
                    ],
                    [
                        'judul'       => 'Memori Internal vs Penyimpanan Eksternal',
                        'tipe'        => 'teks',
                        'urutan'      => 3,
                        'konten_teks' => '<p>Perbandingan antara memori utama dan penyimpanan sekunder:</p><ol><li><strong>Memori Utama (RAM/SRAM/DRAM):</strong> Bersifat <i>volatile</i> (data hilang saat mati listrik) dan berkecepatan tinggi.</li><li><strong>Secondary Storage (SSD/HDD):</strong> Bersifat <i>non-volatile</i> dan memiliki kapasitas besar untuk penyimpanan permanen.</li></ol>',
                        'konten_url'  => null,
                    ],
                    [
                        'judul'       => 'Modul Bacaan: Cache Memory & Mapping',
                        'tipe'        => 'pdf',
                        'urutan'      => 4,
                        'konten_teks' => null,
                        'konten_url'  => 'storage/materi_files/modul2_cache_memory.pdf',
                    ],
                    [
                        'judul'       => 'Video Penjelasan: Cara Kerja Cache L1, L2, L3',
                        'tipe'        => 'video',
                        'urutan'      => 5,
                        'konten_teks' => null,
                        'konten_url'  => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
                    ],
                ]
            ],

            // ==========================================
            // MODUL 3
            // ==========================================
            [
                'kode_modul' => 'Modul 3',
                'judul'      => 'I/O, Arithmetic & Instruction',
                'sub_cpmk'   => 'Sub-CPMK 4-5',
                'urutan'     => 3,
                'deskripsi'  => 'Membahas teknik input/output (Interrupt, DMA), representasi data aritmatika komputer (ALU), serta struktur set instruksi (ISA).',
                'materis'    => [
                    [
                        'judul'       => 'Pengenalan I/O, Arithmetic & Instruction Set',
                        'tipe'        => 'pendahuluan',
                        'urutan'      => 1,
                        'konten_teks' => '<p>Selamat datang di <strong>Modul 3</strong>. Modul ini berfokus pada bagaimana CPU memproses instruksi aritmatika/logika serta bagaimana CPU menangani komunikasi I/O tingkat tinggi.</p>',
                        'konten_url'  => null,
                    ],
                    [
                        'judul'       => 'Teknik Transfer Data I/O & DMA',
                        'tipe'        => 'teks',
                        'urutan'      => 2,
                        'konten_teks' => '<p>Ada tiga metode utama operasi I/O pada komputer:</p><ul><li><strong>Programmed I/O:</strong> CPU menunggu secara aktif hingga proses I/O selesai (polling).</li><li><strong>Interrupt-Driven I/O:</strong> Modul I/O menginterupsi CPU saat data siap diproses.</li><li><strong>Direct Memory Access (DMA):</strong> Modul I/O mentransfer data langsung ke RAM tanpa keterlibatan penuh CPU.</li></ul>',
                        'konten_url'  => null,
                    ],
                    [
                        'judul'       => 'Set Instruksi & Format Instruction Set Architecture (ISA)',
                        'tipe'        => 'teks',
                        'urutan'      => 3,
                        'konten_teks' => '<p><strong>Instruction Set Architecture (ISA)</strong></p><p>Set Instruksi adalah kumpulan perintah yang dipahami langsung oleh CPU. Setiap instruksi terdiri dari:</p><ol><li><strong>Opcode (Operation Code):</strong> Menentukan jenis operasi (misal: ADD, SUB, MOV).</li><li><strong>Operand:</strong> Data atau alamat memori/register yang akan dioperasikan.</li></ol>',
                        'konten_url'  => null,
                    ],
                    [
                        'judul'       => 'Buku Panduan: DMA & ISA Architecture',
                        'tipe'        => 'pdf',
                        'urutan'      => 4,
                        'konten_teks' => null,
                        'konten_url'  => 'storage/materi_files/modul3_io_isa.pdf',
                    ],
                    [
                        'judul'       => 'Video Pembelajaran: Konsep DMA & Interrupts',
                        'tipe'        => 'video',
                        'urutan'      => 5,
                        'konten_teks' => null,
                        'konten_url'  => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
                    ],
                ]
            ],

            // ==========================================
            // MODUL 4
            // ==========================================
            [
                'kode_modul' => 'Modul 4',
                'judul'      => 'CPU, Control & Parallel Processing',
                'sub_cpmk'   => 'Sub-CPMK 6-7',
                'urutan'     => 4,
                'deskripsi'  => 'Modul lanjutan yang mempelajari struktur internal CPU, Unit Kontrol (Control Unit), teknik Pipelining instruksi, dan Pemrosesan Paralel (Multicore & Supercomputing).',
                'materis'    => [
                    [
                        'judul'       => 'Pengenalan CPU & Parallel Processing',
                        'tipe'        => 'pendahuluan',
                        'urutan'      => 1,
                        'konten_teks' => '<p>Selamat datang di <strong>Modul 4</strong>. Modul ini merupakan puncaknya pembahasan arsitektur komputer, berfokus pada pengoptimalkan eksekusi instruksi melalui pemrosesan paralel.</p>',
                        'konten_url'  => null,
                    ],
                    [
                        'judul'       => 'Organisasi CPU & Control Unit',
                        'tipe'        => 'teks',
                        'urutan'      => 2,
                        'konten_teks' => '<p><strong>Organisasi CPU dan Unit Kontrol</strong></p><p>CPU terdiri dari dua komponen inti pemrosesan:</p><ul><li><strong>ALU (Arithmetic Logic Unit):</strong> Melakukan perhitungan matematika dan operasi logika.</li><li><strong>Control Unit (CU):</strong> Mengatur eksekusi instruksi, pembangkitan sinyal kontrol, dan siklus <i>fetch-decode-execute</i>.</li></ul>',
                        'konten_url'  => null,
                    ],
                    [
                        'judul'       => 'Instruction Pipelining & Pemrosesan Paralel',
                        'tipe'        => 'teks',
                        'urutan'      => 3,
                        'konten_teks' => '<p><strong>Teknik Pipelining & Parallelism</strong></p><p>Untuk meningkatkan performa eksekusi instruksi digunakan dua pendekatan:</p><ol><li><strong>Pipelining:</strong> Membagi eksekusi instruksi menjadi beberapa tahapan (Fetch, Decode, Execute, Writeback) yang dijalankan secara berselang-seling.</li><li><strong>Parallel Processing:</strong> Menggunakan banyak processor/core secara bersamaan (Symmetric Multiprocessing & GPU Computing).</li></ol>',
                        'konten_url'  => null,
                    ],
                    [
                        'judul'       => 'Slide Presentasi: Parallel Processing & Pipelining',
                        'tipe'        => 'pdf',
                        'urutan'      => 4,
                        'konten_teks' => null,
                        'konten_url'  => 'storage/materi_files/modul4_pipelining.pdf',
                    ],
                    [
                        'judul'       => 'Video Visualisasi: Cara Kerja Instruction Pipelining',
                        'tipe'        => 'video',
                        'urutan'      => 5,
                        'konten_teks' => null,
                        'konten_url'  => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
                    ],
                ]
            ],
        ];

        foreach ($modules as $modData) {
            $materis = $modData['materis'];
            unset($modData['materis']);

            // Simpan atau update modul
            $module = Module::updateOrCreate(
                ['kode_modul' => $modData['kode_modul']],
                $modData
            );

            // Simpan materi untuk setiap modul
            foreach ($materis as $materiData) {
                $materiData['module_id'] = $module->module_id; // Sesuaikan dengan PK tabel modules
                
                Materi::updateOrCreate(
                    [
                        'module_id' => $module->module_id,
                        'urutan'    => $materiData['urutan'],
                    ],
                    $materiData
                );
            }
        }
    }
}