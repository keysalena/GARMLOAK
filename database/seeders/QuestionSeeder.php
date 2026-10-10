<?php

namespace Database\Seeders;

use App\Models\Module;
use App\Models\Question;
use App\Models\QuestionOption;
use Illuminate\Database\Seeder;

class QuestionSeeder extends Seeder
{
    public function run(): void
    {
        $questionsByModule = [
            'Modul 1' => [
                [
                    'pertanyaan' => 'Apa fungsi utama CPU dalam sistem komputer?',
                    'penjelasan' => 'CPU mengendalikan operasi komputer dan memproses instruksi program.',
                    'options' => [
                        ['teks_opsi' => 'Mengendalikan operasi dan memproses instruksi', 'is_correct' => true],
                        ['teks_opsi' => 'Menyimpan data secara permanen', 'is_correct' => false],
                        ['teks_opsi' => 'Menghubungkan komputer ke jaringan listrik', 'is_correct' => false],
                        ['teks_opsi' => 'Menampilkan keluaran visual', 'is_correct' => false],
                    ],
                ],
                [
                    'pertanyaan' => 'Bus yang membawa data antar komponen komputer disebut apa?',
                    'penjelasan' => 'Data bus menjadi jalur transmisi data antar modul komputer.',
                    'options' => [
                        ['teks_opsi' => 'Data bus', 'is_correct' => true],
                        ['teks_opsi' => 'Address bus', 'is_correct' => false],
                        ['teks_opsi' => 'Control bus', 'is_correct' => false],
                        ['teks_opsi' => 'Expansion bus', 'is_correct' => false],
                    ],
                ],
                [
                    'pertanyaan' => 'Apa informasi yang ditentukan oleh address bus?',
                    'penjelasan' => 'Address bus membawa alamat sumber atau tujuan data di memori maupun perangkat I/O.',
                    'options' => [
                        ['teks_opsi' => 'Alamat sumber atau tujuan data', 'is_correct' => true],
                        ['teks_opsi' => 'Nilai data yang sedang diproses', 'is_correct' => false],
                        ['teks_opsi' => 'Instruksi untuk mematikan CPU', 'is_correct' => false],
                        ['teks_opsi' => 'Kecepatan clock memori', 'is_correct' => false],
                    ],
                ],
            ],
            'Modul 2' => [
                [
                    'pertanyaan' => 'Apa tujuan utama cache memory?',
                    'penjelasan' => 'Cache menjembatani perbedaan kecepatan antara CPU dan memori utama.',
                    'options' => [
                        ['teks_opsi' => 'Mengurangi waktu akses data yang sering dibutuhkan CPU', 'is_correct' => true],
                        ['teks_opsi' => 'Menyimpan data secara permanen saat komputer mati', 'is_correct' => false],
                        ['teks_opsi' => 'Menggantikan seluruh kapasitas RAM', 'is_correct' => false],
                        ['teks_opsi' => 'Mengatur koneksi perangkat jaringan', 'is_correct' => false],
                    ],
                ],
                [
                    'pertanyaan' => 'Data yang baru saja diakses cenderung diakses kembali dalam waktu dekat. Prinsip apakah ini?',
                    'penjelasan' => 'Temporal locality adalah kecenderungan mengakses kembali data yang baru digunakan.',
                    'options' => [
                        ['teks_opsi' => 'Temporal locality', 'is_correct' => true],
                        ['teks_opsi' => 'Spatial locality', 'is_correct' => false],
                        ['teks_opsi' => 'Cache coherence', 'is_correct' => false],
                        ['teks_opsi' => 'Memory interleaving', 'is_correct' => false],
                    ],
                ],
                [
                    'pertanyaan' => 'Apa karakteristik RAM sebagai memori utama?',
                    'penjelasan' => 'RAM bersifat volatile, sehingga isinya hilang ketika daya listrik terputus.',
                    'options' => [
                        ['teks_opsi' => 'Volatile dan kehilangan data saat daya terputus', 'is_correct' => true],
                        ['teks_opsi' => 'Non-volatile dan menyimpan data permanen', 'is_correct' => false],
                        ['teks_opsi' => 'Hanya dapat dibaca dan tidak dapat ditulis', 'is_correct' => false],
                        ['teks_opsi' => 'Digunakan khusus sebagai media keluaran', 'is_correct' => false],
                    ],
                ],
            ],
            'Modul 3' => [
                [
                    'pertanyaan' => 'Metode I/O apa yang memungkinkan transfer data langsung antara perangkat I/O dan RAM dengan keterlibatan CPU yang minimal?',
                    'penjelasan' => 'Direct Memory Access (DMA) memindahkan data langsung ke atau dari memori utama.',
                    'options' => [
                        ['teks_opsi' => 'Direct Memory Access (DMA)', 'is_correct' => true],
                        ['teks_opsi' => 'Programmed I/O', 'is_correct' => false],
                        ['teks_opsi' => 'Polling', 'is_correct' => false],
                        ['teks_opsi' => 'Instruction pipelining', 'is_correct' => false],
                    ],
                ],
                [
                    'pertanyaan' => 'Pada interrupt-driven I/O, apa yang dilakukan modul I/O saat data siap diproses?',
                    'penjelasan' => 'Modul I/O mengirim interrupt agar CPU mengetahui bahwa data siap diproses.',
                    'options' => [
                        ['teks_opsi' => 'Menginterupsi CPU', 'is_correct' => true],
                        ['teks_opsi' => 'Mematikan memori utama', 'is_correct' => false],
                        ['teks_opsi' => 'Menghapus instruksi dari program', 'is_correct' => false],
                        ['teks_opsi' => 'Mengubah data menjadi alamat bus', 'is_correct' => false],
                    ],
                ],
                [
                    'pertanyaan' => 'Bagian instruksi yang menentukan operasi yang harus dilakukan CPU disebut apa?',
                    'penjelasan' => 'Opcode (Operation Code) menentukan jenis operasi seperti ADD, SUB, atau MOV.',
                    'options' => [
                        ['teks_opsi' => 'Opcode', 'is_correct' => true],
                        ['teks_opsi' => 'Operand', 'is_correct' => false],
                        ['teks_opsi' => 'Address bus', 'is_correct' => false],
                        ['teks_opsi' => 'Interrupt vector', 'is_correct' => false],
                    ],
                ],
            ],
            'Modul 4' => [
                [
                    'pertanyaan' => 'Apa fungsi utama Control Unit (CU) pada CPU?',
                    'penjelasan' => 'Control Unit mengatur urutan eksekusi instruksi dan menghasilkan sinyal kontrol.',
                    'options' => [
                        ['teks_opsi' => 'Mengatur eksekusi instruksi dan sinyal kontrol', 'is_correct' => true],
                        ['teks_opsi' => 'Menyimpan file pengguna secara permanen', 'is_correct' => false],
                        ['teks_opsi' => 'Menampilkan hasil pemrosesan ke monitor', 'is_correct' => false],
                        ['teks_opsi' => 'Menghubungkan komputer ke internet', 'is_correct' => false],
                    ],
                ],
                [
                    'pertanyaan' => 'Apa manfaat utama instruction pipelining?',
                    'penjelasan' => 'Pipelining meningkatkan throughput dengan menumpangtindihkan tahapan beberapa instruksi.',
                    'options' => [
                        ['teks_opsi' => 'Meningkatkan throughput dengan menjalankan tahapan instruksi secara tumpang tindih', 'is_correct' => true],
                        ['teks_opsi' => 'Menghilangkan kebutuhan akan memori utama', 'is_correct' => false],
                        ['teks_opsi' => 'Membuat setiap instruksi hanya memiliki satu tahap', 'is_correct' => false],
                        ['teks_opsi' => 'Mengubah semua instruksi menjadi operasi I/O', 'is_correct' => false],
                    ],
                ],
                [
                    'pertanyaan' => 'Apa yang dimaksud dengan parallel processing?',
                    'penjelasan' => 'Parallel processing menggunakan beberapa processor atau core untuk bekerja secara bersamaan.',
                    'options' => [
                        ['teks_opsi' => 'Menggunakan beberapa processor atau core secara bersamaan', 'is_correct' => true],
                        ['teks_opsi' => 'Menjalankan satu instruksi tanpa tahapan', 'is_correct' => false],
                        ['teks_opsi' => 'Menyimpan instruksi di secondary storage', 'is_correct' => false],
                        ['teks_opsi' => 'Mengirim data hanya melalui control bus', 'is_correct' => false],
                    ],
                ],
            ],
        ];

        foreach ($questionsByModule as $moduleCode => $questions) {
            $module = Module::where('kode_modul', $moduleCode)->firstOrFail();

            foreach ($questions as $index => $questionData) {
                $options = $questionData['options'];
                unset($questionData['options']);

                $question = Question::updateOrCreate(
                    [
                        'module_id' => $module->module_id,
                        'urutan' => $index + 1,
                    ],
                    [
                        ...$questionData,
                        'xp_reward' => 30,
                    ]
                );

                foreach ($options as $optionData) {
                    QuestionOption::updateOrCreate(
                        [
                            'question_id' => $question->question_id,
                            'teks_opsi' => $optionData['teks_opsi'],
                        ],
                        ['is_correct' => $optionData['is_correct']]
                    );
                }
            }
        }
    }
}
