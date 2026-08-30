<?php

namespace App\Http\Controllers;

use App\Models\ArContent;
use App\Models\Challenge;
use App\Models\Kelas;
use App\Models\Materi;
use App\Models\Module;
use App\Models\Question;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

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
            'totalChallenge'  => Challenge::count(),
            'totalQuestion'   => Question::count(),
        ]);
    }

    public function kelasIndex()
    {
        $items = Kelas::orderBy('nama_kelas')->get();

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

    public function mahasiswaIndex()
    {
        $items = User::with('kelas')
            ->where('role', 'mahasiswa')
            ->orderBy('nama')
            ->get();

        return view('admin.pages.mahasiswa', compact('items'));
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

    public function materiIndex()
    {
        $items = Materi::with('module')
            ->orderBy('urutan')
            ->get();

        $modules = Module::orderBy('urutan')->get();

        return view('admin.pages.materi', compact('items', 'modules'));
    }

    public function storeMateri(Request $request)
    {
        $data = $request->validate([
            'module_id'   => 'required|exists:modules,module_id',
            'judul'       => 'required|string|max:150',
            'tipe'        => 'required|in:teks,video,pdf,kuis_singkat',
            'konten_url'  => 'nullable|string|max:255',
        ]);

        Materi::create([
            ...$data,
            'urutan' => (Materi::where('module_id', $data['module_id'])->max('urutan') ?? 0) + 1,
        ]);

        return back()->with('success', 'Materi berhasil ditambahkan.');
    }

    public function updateMateri(Request $request, Materi $materi)
    {
        $data = $request->validate([
            'module_id'  => 'required|exists:modules,module_id',
            'judul'      => 'required|string|max:150',
            'tipe'       => 'required|in:teks,video,pdf,kuis_singkat',
            'konten_url' => 'nullable|string|max:255',
        ]);

        $materi->update($data);

        return back()->with('success', 'Materi berhasil diperbarui.');
    }

    public function destroyMateri(Materi $materi)
    {
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

    public function challengeIndex()
    {
        $items = Challenge::with('module')
            ->orderBy('challenge_id')
            ->get();

        $modules = Module::orderBy('urutan')->get();

        return view('admin.pages.challenge', compact('items', 'modules'));
    }

    public function storeChallenge(Request $request)
    {
        $data = $request->validate([
            'module_id' => 'required|exists:modules,module_id',
            'judul'     => 'required|string|max:150',
        ]);

        Challenge::create([
            ...$data,
            'jumlah_soal' => 0,
        ]);

        return back()->with('success', 'Challenge berhasil ditambahkan.');
    }

    public function updateChallenge(Request $request, Challenge $challenge)
    {
        $data = $request->validate([
            'module_id' => 'required|exists:modules,module_id',
            'judul'     => 'required|string|max:150',
        ]);

        $challenge->update($data);

        return back()->with('success', 'Challenge berhasil diperbarui.');
    }

    public function destroyChallenge(Challenge $challenge)
    {
        $challenge->delete();

        return back()->with('success', 'Challenge berhasil dihapus.');
    }

    public function questionsIndex()
    {
        $items = Question::with('challenge')
            ->orderBy('question_id')
            ->get();

        $challenges = Challenge::orderBy('challenge_id')->get();

        return view('admin.pages.questions', compact('items', 'challenges'));
    }

    public function storeQuestion(Request $request)
    {
        $data = $request->validate([
            'challenge_id' => 'required|exists:challenges,challenge_id',
            'pertanyaan'   => 'required|string',
            'xp_reward'    => 'nullable|integer|min:0',
        ]);

        Question::create([
            ...$data,
            'urutan'    => (Question::where('challenge_id', $data['challenge_id'])->max('urutan') ?? 0) + 1,
            'xp_reward' => $data['xp_reward'] ?? 30,
        ]);

        $this->updateJumlahSoal($data['challenge_id']);

        return back()->with('success', 'Question berhasil ditambahkan.');
    }

    public function updateQuestion(Request $request, Question $question)
    {
        $data = $request->validate([
            'challenge_id' => 'required|exists:challenges,challenge_id',
            'pertanyaan'   => 'required|string',
            'xp_reward'    => 'nullable|integer|min:0',
        ]);

        $oldChallengeId = $question->challenge_id;

        $question->update([
            ...$data,
            'xp_reward' => $data['xp_reward'] ?? 30,
        ]);

        $this->updateJumlahSoal($oldChallengeId);

        if ($oldChallengeId != $data['challenge_id']) {
            $this->updateJumlahSoal($data['challenge_id']);
        }

        return back()->with('success', 'Question berhasil diperbarui.');
    }

    public function destroyQuestion(Question $question)
    {
        $challengeId = $question->challenge_id;

        $question->delete();

        $this->updateJumlahSoal($challengeId);

        return back()->with('success', 'Question berhasil dihapus.');
    }

    private function updateJumlahSoal($challengeId)
    {
        Challenge::where('challenge_id', $challengeId)
            ->update([
                'jumlah_soal' => Question::where('challenge_id', $challengeId)->count()
            ]);
    }
}