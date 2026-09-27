<?php

namespace App\Http\Controllers;

use App\Models\ArContent;
use App\Models\Kelas;
use App\Models\Materi;
use App\Models\Module;
use App\Models\Question;
use App\Models\User;
use App\Models\QuestionOption;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class AdminDashboardController extends Controller
{
    public function index()
    {
        return view('admin.dashboard', [
            'totalKelas'      => Kelas::count(),
            'totalMahasiswa'  => User::where('role', 'mahasiswa')->count(),
            'totalModul'      => Module::count(),
            'totalMateri'     => Materi::count(),
            'totalAr'         => ArContent::count(),
            'totalQuestion'   => Question::count(),
        ]);
    }

    public function kelasIndex()
    {
        $items = Kelas::orderBy('nama_kelas')->get();
        $items = Kelas::withCount('mahasiswa')->orderBy('nama_kelas')->get();

        return view('admin.pages.kelas', compact('items'));
    }

    public function storeKelas(Request $request)
    {
        $data = $request->validate([
            'nama_kelas'   => 'required|string|max:50',
            'tahun_ajaran' => 'nullable|string|max:9',
        ]);

        Kelas::create($data);

        return back()->with('success', 'Kelas berhasil ditambahkan.');
    }

    public function updateKelas(Request $request, Kelas $kelas)
    {
        $data = $request->validate([
            'nama_kelas'   => 'required|string|max:50',
            'tahun_ajaran' => 'nullable|string|max:9',
        ]);

        $kelas->update($data);

        return back()->with('success', 'Kelas berhasil diperbarui.');
    }

    public function destroyKelas(Kelas $kelas)
    {
        $kelas->delete();

        return back()->with('success', 'Kelas berhasil dihapus.');
    }
    public function bulkDestroyKelas(Request $request)
    {
        $request->validate([
            'ids'   => 'required|array',
            'ids.*' => 'exists:kelas,kelas_id'
        ]);

        Kelas::whereIn('kelas_id', $request->ids)->delete();

        return back()->with('success', count($request->ids) . ' Kelas berhasil dihapus.');
    }

    public function mahasiswaIndex(Kelas $kelas)
    {
        // Ambil mahasiswa yang HANYA berada di kelas yang diklik
        $items = User::where('role', 'mahasiswa')
            ->where('kelas_id', $kelas->kelas_id)
            ->orderBy('nama')
            ->get();

        // Lempar variabel $items dan $kelas ke view
        return view('admin.pages.mahasiswa', compact('items', 'kelas'));
    }

    public function storeMahasiswa(Request $request)
    {
        $data = $request->validate([
            'nama'     => 'required|string|max:100',
            'email'    => 'required|email|max:100|unique:users,email',
            'password' => 'nullable|string|min:6',
            'kelas_id' => 'nullable|exists:kelas,kelas_id',
        ]);

        User::create([
            'user_id'       => (string) Str::uuid(),
            'role'          => 'mahasiswa',
            'nama'          => $data['nama'],
            'email'         => $data['email'],
            'password_hash' => Hash::make($data['password'] ?? 'password123'),
            'kelas_id'      => $data['kelas_id'] ?? null,
            'level'         => 1,
            'total_xp'      => 0,
        ]);

        return back()->with('success', 'Mahasiswa berhasil ditambahkan.');
    }

    public function updateMahasiswa(Request $request, User $user)
    {
        $data = $request->validate([
            'nama'     => 'required|string|max:100',
            'email'    => 'required|email|max:100|unique:users,email,' . $user->user_id . ',user_id',
            'password' => 'nullable|string|min:6',
            'kelas_id' => 'nullable|exists:kelas,kelas_id',
        ]);

        $user->nama = $data['nama'];
        $user->email = $data['email'];
        $user->kelas_id = $data['kelas_id'] ?? null;

        if (!empty($data['password'])) {
            $user->password_hash = Hash::make($data['password']);
        }

        $user->save();

        return back()->with('success', 'Mahasiswa berhasil diperbarui.');
    }

    public function destroyMahasiswa(User $user)
    {
        $user->delete();

        return back()->with('success', 'Mahasiswa berhasil dihapus.');
    }

    public function bulkDestroy(Request $request)
    {
        $request->validate([
            'ids'   => 'required|array',
            'ids.*' => 'exists:users,user_id'
        ]);

        // Hapus menggunakan query Eloquent
        User::whereIn('user_id', $request->ids)->delete();

        return back()->with('success', count($request->ids) . ' Mahasiswa berhasil dihapus.');
    }

    public function modulIndex()
    {
        $items = Module::orderBy('urutan')->get();

        return view('admin.pages.modul', compact('items'));
    }

    public function storeModul(Request $request)
    {
        $data = $request->validate([
            'kode_modul' => 'required|string|max:10',
            'judul'      => 'required|string|max:150',
            'sub_cpmk'   => 'nullable|string|max:50',
            'deskripsi'  => 'nullable|string',
        ]);

        Module::create([
            ...$data,
            'urutan' => (Module::max('urutan') ?? 0) + 1,
        ]);

        return back()->with('success', 'Modul berhasil ditambahkan.');
    }

    public function updateModul(Request $request, Module $module)
    {
        $data = $request->validate([
            'kode_modul' => 'required|string|max:10',
            'judul'      => 'required|string|max:150',
            'sub_cpmk'   => 'nullable|string|max:50',
            'deskripsi'  => 'nullable|string',
        ]);

        $module->update($data);

        return back()->with('success', 'Modul berhasil diperbarui.');
    }

    public function destroyModul(Module $module)
    {
        $module->delete();

        return back()->with('success', 'Modul berhasil dihapus.');
    }

    public function materiIndex(Module $module)
    {
        // Ambil materi yang HANYA dimiliki oleh modul yang diklik
        $items = Materi::where('module_id', $module->module_id)
            ->orderBy('urutan')
            ->get();

        return view('admin.pages.materi', compact('items', 'module'));
    }

    public function storeMateri(Request $request)
    {
        $request->validate([
            'module_id'   => 'required|exists:modules,module_id',
            'judul'       => 'required|string|max:150',
            'tipe'        => 'required|in:teks,video,pdf,pendahuluan',
            'urutan'      => 'required|integer',
            'konten_teks' => 'nullable|string',
            'konten_url'  => 'nullable|string|max:255',
            'konten_file' => 'nullable|file|mimes:pdf,mp4,mov,avi|max:51200',
        ]);

        $kontenUrl = null;
        $kontenTeks = null;

        // PERBAIKAN: Izinkan 'pendahuluan' untuk menyimpan teks
        if (in_array($request->tipe, ['teks', 'pendahuluan'])) {
            $kontenTeks = $request->konten_teks;
        } elseif (in_array($request->tipe, ['video', 'pdf'])) {
            if ($request->hasFile('konten_file')) {
                $path = $request->file('konten_file')->store('materi_files', 'public');
                $kontenUrl = 'storage/' . $path;
            } else {
                $kontenUrl = $request->konten_url;
            }
        }

        Materi::create([
            'module_id'   => $request->module_id,
            'judul'       => $request->judul,
            'tipe'        => $request->tipe,
            'urutan'      => $request->urutan,
            'konten_teks' => $kontenTeks,
            'konten_url'  => $kontenUrl,
        ]);

        return back()->with('success', 'Materi berhasil ditambahkan.');
    }

    public function updateMateri(Request $request, Materi $materi)
    {
        $request->validate([
            'module_id'   => 'required|exists:modules,module_id',
            'judul'       => 'required|string|max:150',
            'tipe'        => 'required|in:teks,video,pdf,pendahuluan',
            'urutan'      => 'required|integer',
            'konten_teks' => 'nullable|string',
            'konten_url'  => 'nullable|string|max:255',
            'konten_file' => 'nullable|file|mimes:pdf,mp4,mov,avi|max:51200',
        ]);

        $kontenUrl = $materi->konten_url;
        $kontenTeks = $materi->konten_teks;

        // PERBAIKAN: Izinkan 'pendahuluan' untuk menyimpan teks
        if (in_array($request->tipe, ['teks', 'pendahuluan'])) {
            $kontenTeks = $request->konten_teks;

            // Bersihkan URL/File lama jika sebelumnya bukan teks
            if ($kontenUrl && str_contains($kontenUrl, 'materi_files/')) {
                Storage::disk('public')->delete(str_replace('storage/', '', $kontenUrl));
            }
            $kontenUrl = null;
        } elseif (in_array($request->tipe, ['video', 'pdf'])) {
            $kontenTeks = null;

            if ($request->hasFile('konten_file')) {
                if ($kontenUrl && str_contains($kontenUrl, 'materi_files/')) {
                    Storage::disk('public')->delete(str_replace('storage/', '', $kontenUrl));
                }
                $path = $request->file('konten_file')->store('materi_files', 'public');
                $kontenUrl = 'storage/' . $path;
            } elseif ($request->filled('konten_url')) {
                if ($kontenUrl && str_contains($kontenUrl, 'materi_files/')) {
                    Storage::disk('public')->delete(str_replace('storage/', '', $kontenUrl));
                }
                $kontenUrl = $request->konten_url;
            }
        }

        $materi->update([
            'module_id'   => $request->module_id,
            'judul'       => $request->judul,
            'tipe'        => $request->tipe,
            'urutan'      => $request->urutan,
            'konten_teks' => $kontenTeks,
            'konten_url'  => $kontenUrl,
        ]);

        return back()->with('success', 'Materi berhasil diperbarui.');
    }

    public function destroyMateri(Materi $materi)
    {
        if ($materi->konten_url && str_contains($materi->konten_url, 'materi_files/')) {
            Storage::disk('public')->delete(str_replace('storage/', '', $materi->konten_url));
        }

        $materi->delete();

        return back()->with('success', 'Materi berhasil dihapus.');
    }

    public function arContentIndex()
    {
        $items = ArContent::with('module')
            ->orderBy('urutan')
            ->get();

        $modules = Module::orderBy('urutan')->get();

        return view('admin.pages.ar_content', compact('items', 'modules'));
    }

    public function storeArContent(Request $request)
    {
        $data = $request->validate([
            'module_id'        => 'required|exists:modules,module_id',
            'judul'            => 'required|string|max:150',
            'marker_image_url' => 'nullable|string|max:255',
            'model_3d_url'     => 'nullable|string|max:255',
            'penjelasan'       => 'nullable|string',
        ]);

        ArContent::create([
            ...$data,
            'urutan' => (ArContent::where('module_id', $data['module_id'])->max('urutan') ?? 0) + 1,
        ]);

        return back()->with('success', 'AR Content berhasil ditambahkan.');
    }

    public function updateArContent(Request $request, ArContent $arContent)
    {
        $data = $request->validate([
            'module_id'        => 'required|exists:modules,module_id',
            'judul'            => 'required|string|max:150',
            'marker_image_url' => 'nullable|string|max:255',
            'model_3d_url'     => 'nullable|string|max:255',
            'penjelasan'       => 'nullable|string',
        ]);

        $arContent->update($data);

        return back()->with('success', 'AR Content berhasil diperbarui.');
    }

    public function destroyArContent(ArContent $arContent)
    {
        $arContent->delete();

        return back()->with('success', 'AR Content berhasil dihapus.');
    }

    // ==========================================
    // QUESTIONS (SOAL KUIS) - Langsung ke Modul
    // ==========================================
    public function questionsIndex(Module $module)
    {
        // Ambil soal yang HANYA dimiliki oleh modul yang diklik
        $items = Question::where('module_id', $module->module_id)
            ->orderBy('urutan')
            ->get();

        // Lempar variabel $items dan $module ke view
        return view('admin.pages.questions', compact('items', 'module'));
    }

    public function storeQuestion(Request $request)
    {
        $data = $request->validate([
            'module_id'  => 'required|exists:modules,module_id',
            'pertanyaan' => 'required|string',
            'xp_reward'  => 'nullable|integer|min:0',
        ]);

        Question::create([
            ...$data,
            'urutan'    => (Question::where('module_id', $data['module_id'])->max('urutan') ?? 0) + 1,
            'xp_reward' => $data['xp_reward'] ?? 30,
        ]);

        return back()->with('success', 'Soal berhasil ditambahkan.');
    }

    public function updateQuestion(Request $request, Question $question)
    {
        $data = $request->validate([
            'module_id'  => 'required|exists:modules,module_id',
            'pertanyaan' => 'required|string',
            'xp_reward'  => 'nullable|integer|min:0',
        ]);

        $question->update([
            ...$data,
            'xp_reward' => $data['xp_reward'] ?? 30,
        ]);

        return back()->with('success', 'Soal berhasil diperbarui.');
    }

    public function destroyQuestion(Question $question)
    {
        $question->delete();

        return back()->with('success', 'Soal berhasil dihapus.');
    }
    public function optionsIndex(Question $question)
    {
        // Ambil opsi jawaban khusus untuk soal ini
        $items = QuestionOption::where('question_id', $question->question_id)->get();

        return view('admin.pages.options', compact('items', 'question'));
    }

    public function storeBulkOptions(Request $request)
    {
        $request->validate([
            'question_id'         => 'required|exists:questions,question_id',
            'options'             => 'required|array|size:4', // Pastikan selalu ada 4 opsi
            'options.*.teks_opsi' => 'required|string|max:255',
            'is_correct'          => 'required|integer|between:0,3', // Index dari 0 sampai 3
        ]);

        $correctIndex = (int) $request->is_correct;

        foreach ($request->options as $index => $opt) {
            $isCorrectStatus = ($index === $correctIndex);

            // Jika opsi sudah ada ID-nya, lakukan Update
            if (!empty($opt['option_id'])) {
                QuestionOption::where('option_id', $opt['option_id'])->update([
                    'teks_opsi'  => $opt['teks_opsi'],
                    'is_correct' => $isCorrectStatus,
                ]);
            }
            // Jika belum ada ID, buat data Create baru
            else {
                QuestionOption::create([
                    'question_id' => $request->question_id,
                    'teks_opsi'   => $opt['teks_opsi'],
                    'is_correct'  => $isCorrectStatus,
                ]);
            }
        }

        return back()->with('success', '4 Opsi jawaban berhasil disimpan.');
    }

    public function destroyOption(QuestionOption $option)
    {
        $option->delete();

        return back()->with('success', 'Opsi jawaban berhasil dihapus.');
    }
}
