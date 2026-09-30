<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>E-Aspirasi Siswa - Platform Pengaduan & Aspirasi Sekolah</title>
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Alpine.js CDN -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Google Fonts Inter -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                    },
                }
            }
        }
    </script>
    <style>
        [x-cloak] { display: none !important; }
        /* Custom smooth scroll & transition */
        html { scroll-behavior: smooth; }
    </style>
</head>
<body class="h-full bg-slate-50 text-slate-800 font-sans antialiased flex flex-col justify-between" 
      x-data="aspirasiApp()" x-init="initData()">

    <!-- Navigation Bar -->
    <header class="bg-white border-b border-slate-200 sticky top-0 z-40 shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
            <div class="flex items-center space-x-3 cursor-pointer" @click="currentTab = 'home'">
                <div class="w-10 h-10 rounded-xl bg-blue-600 text-white flex items-center justify-center shadow-md shadow-blue-200">
                    <i class="fa-solid fa-bullhorn text-lg"></i>
                </div>
                <div>
                    <span class="text-lg font-bold text-slate-900 tracking-tight">E-Aspirasi</span>
                    <span class="text-xs block text-blue-600 font-medium">Sistem Pengaduan Siswa</span>
                </div>
            </div>
            
            <nav class="hidden md:flex items-center space-x-1">
                <button @click="currentTab = 'home'" :class="currentTab === 'home' ? 'bg-blue-50 text-blue-700 font-semibold' : 'text-slate-600 hover:bg-slate-100'" class="px-3 py-2 rounded-lg text-sm transition">Beranda</button>
                <button @click="currentTab = 'form'" :class="currentTab === 'form' ? 'bg-blue-50 text-blue-700 font-semibold' : 'text-slate-600 hover:bg-slate-100'" class="px-3 py-2 rounded-lg text-sm transition">Buat Pengaduan</button>
                <button @click="currentTab = 'tracking'" :class="currentTab === 'tracking' ? 'bg-blue-50 text-blue-700 font-semibold' : 'text-slate-600 hover:bg-slate-100'" class="px-3 py-2 rounded-lg text-sm transition">Lacak Status</button>
            </nav>

            <div class="flex items-center space-x-2">
                <template x-if="!isAdminLoggedIn">
                    <button @click="openAdminModal = true" class="inline-flex items-center space-x-2 px-4 py-2 bg-slate-900 hover:bg-slate-800 text-white text-sm font-medium rounded-xl shadow-sm transition">
                        <i class="fa-solid fa-lock text-xs"></i>
                        <span>Admin Login</span>
                    </button>
                </template>
                <template x-if="isAdminLoggedIn">
                    <div class="flex items-center space-x-3">
                        <button @click="currentTab = 'admin'" :class="currentTab === 'admin' ? 'bg-blue-600 text-white' : 'bg-slate-100 text-slate-700 hover:bg-slate-200'" class="px-3 py-2 rounded-xl text-sm font-medium transition flex items-center space-x-2">
                            <i class="fa-solid fa-gauge"></i>
                            <span>Dashboard Admin</span>
                        </button>
                        <button @click="logoutAdmin()" class="px-3 py-2 bg-red-50 hover:bg-red-100 text-red-600 rounded-xl text-sm font-medium transition" title="Keluar">
                            <i class="fa-solid fa-right-from-bracket"></i>
                        </button>
                    </div>
                </template>
            </div>
        </div>
        <!-- Mobile nav bar bottom -->
        <div class="md:hidden flex border-t border-slate-100 bg-white px-2 py-2 justify-around">
            <button @click="currentTab = 'home'" :class="currentTab === 'home' ? 'text-blue-600 font-bold' : 'text-slate-500'" class="flex flex-col items-center text-xs space-y-1">
                <i class="fa-solid fa-house text-base"></i>
                <span>Beranda</span>
            </button>
            <button @click="currentTab = 'form'" :class="currentTab === 'form' ? 'text-blue-600 font-bold' : 'text-slate-500'" class="flex flex-col items-center text-xs space-y-1">
                <i class="fa-solid fa-pen-to-square text-base"></i>
                <span>Lapor</span>
            </button>
            <button @click="currentTab = 'tracking'" :class="currentTab === 'tracking' ? 'text-blue-600 font-bold' : 'text-slate-500'" class="flex flex-col items-center text-xs space-y-1">
                <i class="fa-solid fa-magnifying-glass text-base"></i>
                <span>Lacak</span>
            </button>
            <template x-if="isAdminLoggedIn">
                <button @click="currentTab = 'admin'" :class="currentTab === 'admin' ? 'text-blue-600 font-bold' : 'text-slate-500'" class="flex flex-col items-center text-xs space-y-1">
                    <i class="fa-solid fa-gauge text-base"></i>
                    <span>Admin</span>
                </button>
            </template>
        </div>
    </header>

    <!-- Main Content Container -->
    <main class="flex-grow max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-8">

        <!-- Toast Notification -->
        <div x-show="toast.show" x-transition.opacity 
             class="fixed bottom-5 right-5 z-50 flex items-center space-x-3 px-4 py-3 rounded-xl shadow-xl text-white text-sm"
             :class="toast.type === 'success' ? 'bg-emerald-600' : (toast.type === 'error' ? 'bg-rose-600' : 'bg-slate-800')">
            <i class="fa-solid" :class="toast.type === 'success' ? 'fa-circle-check' : 'fa-circle-exclamation'"></i>
            <span x-text="toast.message"></span>
        </div>

        <!-- VIEW: HOME / BERANDA -->
        <div x-show="currentTab === 'home'" x-transition class="space-y-10">
            <!-- Hero Banner -->
            <div class="relative bg-gradient-to-br from-blue-700 via-blue-600 to-indigo-800 rounded-3xl p-8 sm:p-12 text-white shadow-xl overflow-hidden">
                <div class="absolute right-0 bottom-0 translate-x-10 translate-y-10 opacity-10 pointer-events-none">
                    <i class="fa-solid fa-comments text-[280px]"></i>
                </div>
                <div class="max-w-2xl relative z-10 space-y-6">
                    <span class="inline-flex items-center space-x-2 bg-blue-500/40 border border-blue-400/30 px-3 py-1 rounded-full text-xs font-medium text-blue-100">
                        <i class="fa-solid fa-shield-halved"></i>
                        <span>Transparan & Aman untuk Siswa</span>
                    </span>
                    <h1 class="text-3xl sm:text-5xl font-extrabold tracking-tight leading-tight">
                        Suarakan Aspirasi & Perbaikan Fasilitas Sekolahmu
                    </h1>
                    <p class="text-blue-100 text-base sm:text-lg">
                        Platform resmi internal sekolah untuk menampung kritik, saran, dan laporan kerusakan fasilitas secara transparan dengan pelacakan status real-time.
                    </p>
                    <div class="flex flex-wrap gap-4 pt-2">
                        <button @click="currentTab = 'form'" class="px-6 py-3 bg-white text-blue-700 font-semibold rounded-xl shadow-lg hover:bg-blue-50 transition transform hover:-translate-y-0.5">
                            <i class="fa-solid fa-plus-circle mr-2"></i> Buat Pengaduan Baru
                        </button>
                        <button @click="currentTab = 'tracking'" class="px-6 py-3 bg-blue-600/60 border border-blue-400/50 text-white font-semibold rounded-xl backdrop-blur-sm hover:bg-blue-600/80 transition">
                            <i class="fa-solid fa-magnifying-glass mr-2"></i> Lacak Status Laporan
                        </button>
                    </div>
                </div>
            </div>

            <!-- Quick Metrics / Summary Grid -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 sm:gap-6">
                <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm flex items-center space-x-4">
                    <div class="w-12 h-12 bg-blue-50 text-blue-600 rounded-xl flex items-center justify-center text-xl font-bold">
                        <i class="fa-solid fa-file-lines"></i>
                    </div>
                    <div>
                        <p class="text-xs font-medium text-slate-500 uppercase tracking-wider">Total Laporan</p>
                        <h3 class="text-2xl font-bold text-slate-900 mt-1" x-text="stats.total"></h3>
                    </div>
                </div>

                <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm flex items-center space-x-4">
                    <div class="w-12 h-12 bg-amber-50 text-amber-600 rounded-xl flex items-center justify-center text-xl font-bold">
                        <i class="fa-solid fa-clock"></i>
                    </div>
                    <div>
                        <p class="text-xs font-medium text-slate-500 uppercase tracking-wider">Menunggu</p>
                        <h3 class="text-2xl font-bold text-slate-900 mt-1" x-text="stats.menunggu"></h3>
                    </div>
                </div>

                <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm flex items-center space-x-4">
                    <div class="w-12 h-12 bg-sky-50 text-sky-600 rounded-xl flex items-center justify-center text-xl font-bold">
                        <i class="fa-solid fa-spinner"></i>
                    </div>
                    <div>
                        <p class="text-xs font-medium text-slate-500 uppercase tracking-wider">Diproses</p>
                        <h3 class="text-2xl font-bold text-slate-900 mt-1" x-text="stats.diproses"></h3>
                    </div>
                </div>

                <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm flex items-center space-x-4">
                    <div class="w-12 h-12 bg-emerald-50 text-emerald-600 rounded-xl flex items-center justify-center text-xl font-bold">
                        <i class="fa-solid fa-circle-check"></i>
                    </div>
                    <div>
                        <p class="text-xs font-medium text-slate-500 uppercase tracking-wider">Selesai</p>
                        <h3 class="text-2xl font-bold text-slate-900 mt-1" x-text="stats.selesai"></h3>
                    </div>
                </div>
            </div>

            <!-- Progress Bar Completion -->
            <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm space-y-3">
                <div class="flex justify-between items-center text-sm font-semibold text-slate-700">
                    <span>Tingkat Penyelesaian Pengaduan</span>
                    <span x-text="stats.completionRate + '%'"></span>
                </div>
                <div class="w-full bg-slate-100 rounded-full h-3 overflow-hidden">
                    <div class="bg-blue-600 h-3 rounded-full transition-all duration-500" :style="`width: ${stats.completionRate}%`"></div>
                </div>
                <p class="text-xs text-slate-400">Persentase laporan yang telah berhasil diselesaikan oleh pihak sekolah.</p>
            </div>

            <!-- Workflow & Features Explanation -->
            <div class="grid md:grid-cols-3 gap-6">
                <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm space-y-3">
                    <div class="w-10 h-10 bg-indigo-50 text-indigo-600 rounded-xl flex items-center justify-center font-bold">1</div>
                    <h3 class="text-lg font-bold text-slate-900">Kirim Aspirasi</h3>
                    <p class="text-slate-600 text-sm">Isi form pengaduan dengan memilih kategori dan opsi identitas (bisa anonim). Dapatkan kode token unik otomatis.</p>
                </div>
                <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm space-y-3">
                    <div class="w-10 h-10 bg-indigo-50 text-indigo-600 rounded-xl flex items-center justify-center font-bold">2</div>
                    <h3 class="text-lg font-bold text-slate-900">Lacak Real-Time</h3>
                    <p class="text-slate-600 text-sm">Gunakan kode token laporan di halaman pelacakan untuk memantau status: Menunggu ➔ Diproses ➔ Selesai.</p>
                </div>
                <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm space-y-3">
                    <div class="w-10 h-10 bg-indigo-50 text-indigo-600 rounded-xl flex items-center justify-center font-bold">3</div>
                    <h3 class="text-lg font-bold text-slate-900">Tanggapan Admin</h3>
                    <p class="text-slate-600 text-sm">Pihak sekolah/admin akan meninjau, mengubah status, serta memberikan tanggapan resmi secara transparan.</p>
                </div>
            </div>
        </div>

        <!-- VIEW: FORM PENGADUAN -->
        <div x-show="currentTab === 'form'" x-transition class="max-w-2xl mx-auto bg-white p-6 sm:p-8 rounded-3xl border border-slate-200 shadow-sm space-y-6">
            <div class="border-b border-slate-100 pb-4">
                <h2 class="text-2xl font-bold text-slate-900">Form Pengaduan & Aspirasi</h2>
                <p class="text-slate-500 text-sm mt-1">Sampaikan kritik, saran, atau laporan kerusakan fasilitas sekolah secara bijak.</p>
            </div>

            <form @submit.prevent="submitComplaint" class="space-y-5">
                <!-- Anonymous Toggle -->
                <div class="bg-blue-50/60 p-4 rounded-2xl border border-blue-100 flex items-center justify-between">
                    <div>
                        <span class="font-semibold text-sm text-slate-900 block">Kirim sebagai Anonim?</span>
                        <span class="text-xs text-slate-500">Identitas Anda akan disembunyikan dari publik & admin sekolah.</span>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" x-model="form.isAnonymous" class="sr-only peer">
                        <div class="w-11 h-6 bg-slate-300 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                    </label>
                </div>

                <!-- Identity fields (conditionally shown or disabled) -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4" x-show="!form.isAnonymous" x-transition>
                    <div class="space-y-1">
                        <label class="text-xs font-semibold uppercase tracking-wider text-slate-600">Nama Lengkap</label>
                        <input type="text" x-model="form.nama" :required="!form.isAnonymous" placeholder="Cth: Ahmad Fauzi" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-600 text-sm">
                    </div>
                    <div class="space-y-1">
                        <label class="text-xs font-semibold uppercase tracking-wider text-slate-600">Kelas</label>
                        <input type="text" x-model="form.kelas" :required="!form.isAnonymous" placeholder="Cth: XI IPA 2" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-600 text-sm">
                    </div>
                </div>

                <div class="space-y-1">
                    <label class="text-xs font-semibold uppercase tracking-wider text-slate-600">Kategori Laporan</label>
                    <select x-model="form.kategori" required class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-600 text-sm bg-white">
                        <option value="">-- Pilih Kategori --</option>
                        <option value="Fasilitas">Fasilitas Rusak / Sarana</option>
                        <option value="Akademik">Akademik & Pembelajaran</option>
                        <option value="Kebersihan">Kebersihan & Lingkungan</option>
                        <option value="Keamanan">Keamanan & Ketertiban</option>
                        <option value="Lainnya">Lainnya / Saran Umum</option>
                    </select>
                </div>

                <div class="space-y-1">
                    <label class="text-xs font-semibold uppercase tracking-wider text-slate-600">Judul Pengaduan / Lokasi</label>
                    <input type="text" x-model="form.judul" required placeholder="Cth: Kerusakan proyektor di ruang kelas XI IPA 2" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-600 text-sm">
                </div>

                <div class="space-y-1">
                    <label class="text-xs font-semibold uppercase tracking-wider text-slate-600">Deskripsi Detail</label>
                    <textarea x-model="form.deskripsi" required rows="4" placeholder="Jelaskan kronologi atau detail masalah secara lengkap..." class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-600 text-sm"></textarea>
                </div>

                <button type="submit" class="w-full py-3 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-xl shadow-lg transition flex items-center justify-center space-x-2">
                    <i class="fa-solid fa-paper-plane"></i>
                    <span>Kirim Pengaduan</span>
                </button>
            </form>
        </div>

        <!-- VIEW: TRACKING / PELACAKAN -->
        <div x-show="currentTab === 'tracking'" x-transition class="max-w-2xl mx-auto space-y-6">
            <div class="bg-white p-6 sm:p-8 rounded-3xl border border-slate-200 shadow-sm space-y-4">
                <div class="border-b border-slate-100 pb-4">
                    <h2 class="text-2xl font-bold text-slate-900">Lacakan Status Pengaduan</h2>
                    <p class="text-slate-500 text-sm mt-1">Masukkan kode unik atau token (Cth: ASP-10293) untuk memeriksa status terbaru.</p>
                </div>

                <div class="flex gap-2">
                    <div class="relative flex-grow">
                        <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center text-slate-400">
                            <i class="fa-solid fa-ticket"></i>
                        </span>
                        <input type="text" x-model="trackingTokenInput" @keyup.enter="searchTracking" placeholder="Masukkan Kode Token (ASP-XXXXX)" class="w-full pl-10 pr-4 py-3 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-600 text-sm font-mono uppercase">
                    </div>
                    <button @click="searchTracking" class="px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-xl shadow transition text-sm">
                        Cari
                    </button>
                </div>
            </div>

            <!-- Result Card -->
            <template x-if="trackedComplaint">
                <div class="bg-white p-6 sm:p-8 rounded-3xl border border-slate-200 shadow-sm space-y-6">
                    <div class="flex flex-wrap items-center justify-between gap-2 border-b border-slate-100 pb-4">
                        <div>
                            <span class="text-xs font-mono bg-blue-50 text-blue-700 px-3 py-1 rounded-full border border-blue-100 font-semibold" x-text="trackedComplaint.token"></span>
                            <span class="text-xs text-slate-400 ml-2" x-text="trackedComplaint.tanggal"></span>
                        </div>
                        <div>
                            <span class="px-3 py-1 rounded-full text-xs font-semibold"
                                  :class="{
                                      'bg-amber-100 text-amber-700': trackedComplaint.status === 'Menunggu',
                                      'bg-sky-100 text-sky-700': trackedComplaint.status === 'Diproses',
                                      'bg-emerald-100 text-emerald-700': trackedComplaint.status === 'Selesai'
                                  }" x-text="trackedComplaint.status"></span>
                        </div>
                    </div>

                    <div class="space-y-3">
                        <div class="flex items-center space-x-2">
                            <span class="text-xs uppercase font-semibold px-2.5 py-0.5 bg-slate-100 text-slate-600 rounded-md" x-text="trackedComplaint.kategori"></span>
                            <span class="text-xs text-slate-500 font-medium" x-text="trackedComplaint.isAnonymous ? 'Oleh: Anonim' : 'Oleh: ' + trackedComplaint.nama + ' (' + trackedComplaint.kelas + ')'"></span>
                        </div>
                        <h3 class="text-lg font-bold text-slate-900" x-text="trackedComplaint.judul"></h3>
                        <p class="text-slate-600 text-sm whitespace-pre-line bg-slate-50 p-4 rounded-xl border border-slate-100" x-text="trackedComplaint.deskripsi"></p>
                    </div>

                    <!-- Workflow Timeline -->
                    <div class="space-y-3 pt-2">
                        <h4 class="text-xs font-semibold uppercase tracking-wider text-slate-500">Alur Status Data (Workflow)</h4>
                        <div class="grid grid-cols-3 gap-2 text-center text-xs">
                            <div class="p-3 rounded-xl border transition" :class="getTimelineClass(trackedComplaint.status, 'Menunggu')">
                                <i class="fa-solid fa-clock mb-1 text-base"></i>
                                <div class="font-semibold">1. Menunggu</div>
                            </div>
                            <div class="p-3 rounded-xl border transition" :class="getTimelineClass(trackedComplaint.status, 'Diproses')">
                                <i class="fa-solid fa-spinner mb-1 text-base"></i>
                                <div class="font-semibold">2. Diproses</div>
                            </div>
                            <div class="p-3 rounded-xl border transition" :class="getTimelineClass(trackedComplaint.status, 'Selesai')">
                                <i class="fa-solid fa-circle-check mb-1 text-base"></i>
                                <div class="font-semibold">3. Selesai</div>
                            </div>
                        </div>
                    </div>

                    <!-- Admin Feedback Response -->
                    <div class="space-y-2 pt-2 border-t border-slate-100">
                        <h4 class="text-xs font-semibold uppercase tracking-wider text-slate-500">Tanggapan Resmi Pihak Sekolah</h4>
                        <template x-if="trackedComplaint.tanggapan">
                            <div class="bg-blue-50/70 border border-blue-100 p-4 rounded-2xl text-sm text-blue-900 space-y-1">
                                <div class="flex items-center space-x-2 text-xs font-bold text-blue-700">
                                    <i class="fa-solid fa-circle-check"></i>
                                    <span>Respon Admin Sekolah</span>
                                </div>
                                <p x-text="trackedComplaint.tanggapan"></p>
                            </div>
                        </template>
                        <template x-if="!trackedComplaint.tanggapan">
                            <div class="bg-slate-50 border border-slate-200/60 p-4 rounded-2xl text-sm text-slate-500 italic">
                                Belum ada tanggapan resmi dari admin sekolah untuk laporan ini.
                            </div>
                        </template>
                    </div>
                </div>
            </template>
        </div>

        <!-- VIEW: ADMIN DASHBOARD -->
        <div x-show="currentTab === 'admin' && isAdminLoggedIn" x-transition class="space-y-6">
            <div class="flex flex-wrap items-center justify-between gap-4 bg-white p-6 rounded-3xl border border-slate-200 shadow-sm">
                <div>
                    <h2 class="text-2xl font-bold text-slate-900">Dashboard Admin Sekolah</h2>
                    <p class="text-slate-500 text-sm mt-0.5">Kelola status aspirasi, berikan tanggapan resmi, dan manajemen data.</p>
                </div>
                <div class="flex items-center space-x-3">
                    <button @click="resetToDefaultData()" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl text-sm font-medium transition flex items-center space-x-2">
                        <i class="fa-solid fa-rotate-right"></i>
                        <span>Muat Data Demo</span>
                    </button>
                </div>
            </div>

            <!-- Filters & Search -->
            <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-sm flex flex-wrap items-center justify-between gap-4">
                <div class="flex flex-wrap items-center gap-3 flex-grow">
                    <div class="relative min-w-[220px] flex-grow sm:flex-grow-0">
                        <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-slate-400">
                            <i class="fa-solid fa-magnifying-glass text-xs"></i>
                        </span>
                        <input type="text" x-model="adminSearch" placeholder="Cari judul / token..." class="w-full pl-9 pr-4 py-2 rounded-xl border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-blue-600">
                    </div>

                    <select x-model="adminFilterStatus" class="px-3 py-2 rounded-xl border border-slate-200 text-sm bg-white focus:outline-none focus:ring-2 focus:ring-blue-600">
                        <option value="">Semua Status</option>
                        <option value="Menunggu">Menunggu</option>
                        <option value="Diproses">Diproses</option>
                        <option value="Selesai">Selesai</option>
                    </select>

                    <select x-model="adminFilterKategori" class="px-3 py-2 rounded-xl border border-slate-200 text-sm bg-white focus:outline-none focus:ring-2 focus:ring-blue-600">
                        <option value="">Semua Kategori</option>
                        <option value="Fasilitas">Fasilitas</option>
                        <option value="Akademik">Akademik</option>
                        <option value="Kebersihan">Kebersihan</option>
                        <option value="Keamanan">Keamanan</option>
                        <option value="Lainnya">Lainnya</option>
                    </select>
                </div>
                <div class="text-xs font-semibold text-slate-500">
                    Menampilkan <span x-text="filteredComplaints.length"></span> laporan
                </div>
            </div>

            <!-- Complaints Table -->
            <div class="bg-white rounded-3xl border border-slate-200 shadow-sm overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse text-sm">
                        <thead>
                            <tr class="bg-slate-50 border-b border-slate-200 text-slate-600 text-xs font-semibold uppercase tracking-wider">
                                <th class="p-4">Token & Tanggal</th>
                                <th class="p-4">Pelapor</th>
                                <th class="p-4">Kategori & Judul</th>
                                <th class="p-4">Status</th>
                                <th class="p-4 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            <template x-for="c in filteredComplaints" :key="c.id">
                                <tr class="hover:bg-slate-50/50 transition">
                                    <td class="p-4">
                                        <span class="font-mono text-xs font-bold text-blue-600 block" x-text="c.token"></span>
                                        <span class="text-xs text-slate-400" x-text="c.tanggal"></span>
                                    </td>
                                    <td class="p-4">
                                        <template x-if="c.isAnonymous">
                                            <span class="px-2.5 py-1 bg-slate-100 text-slate-600 rounded-full text-xs font-medium">Anonim</span>
                                        </template>
                                        <template x-if="!c.isAnonymous">
                                            <div>
                                                <span class="font-medium text-slate-800 block" x-text="c.nama"></span>
                                                <span class="text-xs text-slate-400" x-text="c.kelas"></span>
                                            </div>
                                        </template>
                                    </td>
                                    <td class="p-4 max-w-xs">
                                        <span class="inline-block px-2 py-0.5 bg-indigo-50 text-indigo-700 text-xs rounded mb-1 font-medium" x-text="c.kategori"></span>
                                        <p class="font-semibold text-slate-900 truncate" x-text="c.judul"></p>
                                    </td>
                                    <td class="p-4">
                                        <span class="px-3 py-1 rounded-full text-xs font-semibold"
                                              :class="{
                                                  'bg-amber-100 text-amber-700': c.status === 'Menunggu',
                                                  'bg-sky-100 text-sky-700': c.status === 'Diproses',
                                                  'bg-emerald-100 text-emerald-700': c.status === 'Selesai'
                                              }" x-text="c.status"></span>
                                    </td>
                                    <td class="p-4 text-right space-x-2">
                                        <button @click="openManageModal(c)" class="px-3 py-1.5 bg-blue-50 hover:bg-blue-100 text-blue-600 rounded-xl text-xs font-semibold transition">
                                            <i class="fa-solid fa-pen-to-square mr-1"></i> Kelola
                                        </button>
                                        <button @click="deleteComplaint(c.id)" class="px-3 py-1.5 bg-red-50 hover:bg-red-100 text-red-600 rounded-xl text-xs font-semibold transition" title="Hapus Laporan">
                                            <i class="fa-solid fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            </template>
                            <template x-if="filteredComplaints.length === 0">
                                <tr>
                                    <td colspan="5" class="p-8 text-center text-slate-400 italic">
                                        Tidak ada data pengaduan yang ditemukan.
                                    </td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </main>

    <!-- MODAL: Admin Login -->
    <div x-show="openAdminModal" x-transition.opacity class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4">
        <div @click.away="openAdminModal = false" class="bg-white rounded-3xl max-w-md w-full p-6 sm:p-8 space-y-6 shadow-2xl relative">
            <div class="flex justify-between items-center border-b border-slate-100 pb-4">
                <div class="flex items-center space-x-2 text-slate-900 font-bold text-lg">
                    <i class="fa-solid fa-lock text-blue-600"></i>
                    <span>Login Admin Sekolah</span>
                </div>
                <button @click="openAdminModal = false" class="text-slate-400 hover:text-slate-600">
                    <i class="fa-solid fa-xmark text-lg"></i>
                </button>
            </div>

            <form @submit.prevent="loginAdmin" class="space-y-4">
                <div class="space-y-1">
                    <label class="text-xs font-semibold uppercase tracking-wider text-slate-600">Username / PIN</label>
                    <input type="text" x-model="adminLoginForm.username" required placeholder="Cth: admin" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-600 text-sm">
                </div>
                <div class="space-y-1">
                    <label class="text-xs font-semibold uppercase tracking-wider text-slate-600">Password</label>
                    <input type="password" x-model="adminLoginForm.password" required placeholder="Cth: 12345" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-600 text-sm">
                </div>
                <p class="text-xs text-slate-400">Demo Login: Username <code class="bg-slate-100 px-1 py-0.5 rounded font-bold text-slate-700">admin</code> | Password <code class="bg-slate-100 px-1 py-0.5 rounded font-bold text-slate-700">12345</code></p>
                <button type="submit" class="w-full py-3 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-xl shadow transition">
                    Masuk Dashboard
                </button>
            </form>
        </div>
    </div>

    <!-- MODAL: Manage Complaint (Status & Tanggapan) -->
    <div x-show="openManageModalState" x-transition.opacity class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4">
        <div @click.away="openManageModalState = false" class="bg-white rounded-3xl max-w-lg w-full p-6 sm:p-8 space-y-6 shadow-2xl relative max-h-[90vh] overflow-y-auto">
            <div class="flex justify-between items-center border-b border-slate-100 pb-4">
                <div>
                    <span class="text-xs font-mono font-bold text-blue-600 bg-blue-50 px-2.5 py-1 rounded-full" x-text="activeComplaint ? activeComplaint.token : ''"></span>
                    <h3 class="text-lg font-bold text-slate-900 mt-1">Kelola Pengaduan</h3>
                </div>
                <button @click="openManageModalState = false" class="text-slate-400 hover:text-slate-600">
                    <i class="fa-solid fa-xmark text-lg"></i>
                </button>
            </div>

            <template x-if="activeComplaint">
                <form @submit.prevent="saveComplaintChanges" class="space-y-4">
                    <div class="space-y-1 bg-slate-50 p-3 rounded-2xl border border-slate-200/60 text-xs text-slate-600">
                        <span class="font-bold text-slate-800 block" x-text="activeComplaint.judul"></span>
                        <p class="text-slate-500 mt-1" x-text="activeComplaint.deskripsi"></p>
                    </div>

                    <div class="space-y-1">
                        <label class="text-xs font-semibold uppercase tracking-wider text-slate-600">Status Laporan</label>
                        <select x-model="activeComplaint.status" required class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm bg-white focus:outline-none focus:ring-2 focus:ring-blue-600">
                            <option value="Menunggu">Menunggu</option>
                            <option value="Diproses">Diproses</option>
                            <option value="Selesai">Selesai</option>
                        </select>
                    </div>

                    <div class="space-y-1">
                        <label class="text-xs font-semibold uppercase tracking-wider text-slate-600">Tanggapan Resmi Admin Sekolah</label>
                        <textarea x-model="activeComplaint.tanggapan" rows="4" placeholder="Berikan tanggapan, instruksi perbaikan, atau hasil penyelesaian..." class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-blue-600"></textarea>
                    </div>

                    <div class="flex space-x-3 pt-2">
                        <button type="button" @click="openManageModalState = false" class="flex-1 py-3 bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold rounded-xl text-sm transition">
                            Batal
                        </button>
                        <button type="submit" class="flex-1 py-3 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-xl text-sm shadow transition">
                            Simpan Perubahan
                        </button>
                    </div>
                </form>
            </template>
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-white border-t border-slate-200 mt-16 py-6 text-center text-xs text-slate-400">
        <div class="max-w-7xl mx-auto px-4 space-y-1">
            <p>&copy; 2026 E-Aspirasi Siswa &bull; Platform Internal Transparansi Sekolah.</p>
            <p>Keamanan dasar teruji &bull; Penyimpanan LocalStorage Persisten</p>
        </div>
    </footer>

    <!-- Application JavaScript Logic -->
    <script>
        function aspirasiApp() {
            return {
                currentTab: 'home',
                isAdminLoggedIn: false,
                openAdminModal: false,
                openManageModalState: false,
                activeComplaint: null,
                adminSearch: '',
                adminFilterStatus: '',
                adminFilterKategori: '',
                trackingTokenInput: '',
                trackedComplaint: null,
                
                toast: {
                    show: false,
                    message: '',
                    type: 'success'
                },

                adminLoginForm: {
                    username: '',
                    password: ''
                },

                form: {
                    isAnonymous: false,
                    nama: '',
                    kelas: '',
                    kategori: '',
                    judul: '',
                    deskripsi: ''
                },

                complaints: [],

                initData() {
                    const saved = localStorage.getItem('e_aspirasi_complaints');
                    if (saved) {
                        try {
                            this.complaints = JSON.parse(saved);
                        } catch (e) {
                            this.loadDefaultComplaints();
                        }
                    } else {
                        this.loadDefaultComplaints();
                    }

                    // Check admin session
                    if (localStorage.getItem('e_aspirasi_admin') === 'true') {
                        this.isAdminLoggedIn = true;
                    }
                },

                loadDefaultComplaints() {
                    this.complaints = [
                        {
                            id: 1,
                            token: 'ASP-83921',
                            isAnonymous: false,
                            nama: 'Budi Santoso',
                            kelas: 'XI IPA 1',
                            kategori: 'Fasilitas',
                            judul: 'AC di Ruang Kelas XI IPA 1 Tidak Dingin',
                            deskripsi: 'AC sudah dinyalakan dari pagi tapi hanya mengeluarkan angin biasa dan tidak mendinginkan ruangan.',
                            status: 'Diproses',
                            tanggapan: 'Laporan diterima. Tim sarpras telah menjadwalkan pengecekan teknisi AC hari ini.',
                            tanggal: '29 Sep 2026, 10:15'
                        },
                        {
                            id: 2,
                            token: 'ASP-54910',
                            isAnonymous: true,
                            nama: 'Anonim',
                            kelas: '-',
                            kategori: 'Kebersihan',
                            judul: 'Pintu Toilet Lantai 2 Rusak',
                            deskripsi: 'Pintu toilet siswa sebelah timur lantai 2 kancing selotnya patah sehingga tidak bisa dikunci.',
                            status: 'Selesai',
                            tanggapan: 'Perbaikan selot pintu toilet telah diselesaikan oleh petugas kebersihan dan pertukangan.',
                            tanggal: '28 Sep 2026, 14:30'
                        },
                        {
                            id: 3,
                            token: 'ASP-90234',
                            isAnonymous: false,
                            nama: 'Siti Rahma',
                            kelas: 'X IPS 3',
                            kategori: 'Akademik',
                            judul: 'Penambahan Buku Paket Ekonomi di Perpus',
                            deskripsi: 'Mohon penambahan eksemplar buku paket ekonomi kurikulum terbaru karena jumlahnya kurang untuk kelompok belajar.',
                            status: 'Menunggu',
                            tanggapan: '',
                            tanggal: '30 Sep 2026, 08:00'
                        }
                    ];
                    this.saveToStorage();
                },

                saveToStorage() {
                    localStorage.setItem('e_aspirasi_complaints', JSON.stringify(this.complaints));
                },

                showToast(message, type = 'success') {
                    this.toast.message = message;
                    this.toast.type = type;
                    this.toast.show = true;
                    setTimeout(() => {
                        this.toast.show = false;
                    }, 3500);
                },

                // XSS Sanitization basic protection helper
                sanitize(str) {
                    if (!str) return '';
                    return str.replace(/&/g, "&amp;")
                              .replace(/</g, "&lt;")
                              .replace(/>/g, "&gt;")
                              .replace(/"/g, "&quot;")
                              .replace(/'/g, "&#039;");
                },

                submitComplaint() {
                    const tokenNum = Math.floor(10000 + Math.random() * 90000);
                    const token = `ASP-${tokenNum}`;
                    const now = new Date();
                    const tanggal = now.toLocaleDateString('id-ID', { day: '2-digit', month: 'short', year: 'numeric' }) + ', ' + now.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' });

                    const newC = {
                        id: Date.now(),
                        token: token,
                        isAnonymous: this.form.isAnonymous,
                        nama: this.form.isAnonymous ? 'Anonim' : this.sanitize(this.form.nama),
                        kelas: this.form.isAnonymous ? '-' : this.sanitize(this.form.kelas),
                        kategori: this.form.kategori,
                        judul: this.sanitize(this.form.judul),
                        deskripsi: this.sanitize(this.form.deskripsi),
                        status: 'Menunggu',
                        tanggapan: '',
                        tanggal: tanggal
                    };

                    this.complaints.unshift(newC);
                    this.saveToStorage();

                    // Reset form
                    this.form = { isAnonymous: false, nama: '', kelas: '', kategori: '', judul: '', deskripsi: '' };

                    // Show success and redirect to tracking with token filled
                    this.showToast(`Pengaduan berhasil dikirim! Token Anda: ${token}`);
                    this.trackingTokenInput = token;
                    this.currentTab = 'tracking';
                    this.searchTracking();
                },

                searchTracking() {
                    const query = this.trackingTokenInput.trim().toUpperCase();
                    if (!query) {
                        this.trackedComplaint = null;
                        this.showToast('Masukkan kode token terlebih dahulu', 'error');
                        return;
                    }
                    const found = this.complaints.find(c => c.token.toUpperCase() === query);
                    if (found) {
                        this.trackedComplaint = found;
                        this.showToast('Data laporan ditemukan', 'success');
                    } else {
                        this.trackedComplaint = null;
                        this.showToast('Laporan dengan token tersebut tidak ditemukan', 'error');
                    }
                },

                getTimelineClass(currentStatus, targetStatus) {
                    const order = { 'Menunggu': 1, 'Diproses': 2, 'Selesai': 3 };
                    const currentVal = order[currentStatus] || 1;
                    const targetVal = order[targetStatus] || 1;

                    if (currentVal >= targetVal) {
                        return 'bg-blue-50 border-blue-200 text-blue-700 font-bold';
                    }
                    return 'bg-slate-50 border-slate-200 text-slate-400';
                },

                loginAdmin() {
                    if (this.adminLoginForm.username === 'admin' && this.adminLoginForm.password === '12345') {
                        this.isAdminLoggedIn = true;
                        localStorage.setItem('e_aspirasi_admin', 'true');
                        this.openAdminModal = false;
                        this.adminLoginForm.username = '';
                        this.adminLoginForm.password = '';
                        this.showToast('Login Admin berhasil');
                        this.currentTab = 'admin';
                    } else {
                        this.showToast('Username atau password salah (Gunakan admin / 12345)', 'error');
                    }
                },

                logoutAdmin() {
                    this.isAdminLoggedIn = false;
                    localStorage.removeItem('e_aspirasi_admin');
                    this.showToast('Logout Admin berhasil');
                    this.currentTab = 'home';
                },

                openManageModal(complaint) {
                    this.activeComplaint = JSON.parse(JSON.stringify(complaint));
                    this.openManageModalState = true;
                },

                saveComplaintChanges() {
                    const index = this.complaints.findIndex(c => c.id === this.activeComplaint.id);
                    if (index !== -1) {
                        this.complaints[index].status = this.activeComplaint.status;
                        this.complaints[index].tanggapan = this.sanitize(this.activeComplaint.tanggapan);
                        this.saveToStorage();
                        this.showToast('Perubahan pengaduan berhasil disimpan');
                        this.openManageModalState = false;

                        // Also update tracked complaint if active
                        if (this.trackedComplaint && this.trackedComplaint.id === this.activeComplaint.id) {
                            this.trackedComplaint = JSON.parse(JSON.stringify(this.complaints[index]));
                        }
                    }
                },

                deleteComplaint(id) {
                    if (confirm('Apakah Anda yakin ingin menghapus laporan ini?')) {
                        this.complaints = this.complaints.filter(c => c.id !== id);
                        this.saveToStorage();
                        this.showToast('Laporan berhasil dihapus');
                        if (this.trackedComplaint && this.trackedComplaint.id === id) {
                            this.trackedComplaint = null;
                        }
                    }
                },

                resetToDefaultData() {
                    this.loadDefaultComplaints();
                    this.showToast('Data demo berhasil dimuat ulang');
                },

                get filteredComplaints() {
                    return this.complaints.filter(c => {
                        const matchSearch = c.judul.toLowerCase().includes(this.adminSearch.toLowerCase()) || 
                                          c.token.toLowerCase().includes(this.adminSearch.toLowerCase()) ||
                                          c.nama.toLowerCase().includes(this.adminSearch.toLowerCase());
                        const matchStatus = this.adminFilterStatus ? c.status === this.adminFilterStatus : true;
                        const matchKategori = this.adminFilterKategori ? c.kategori === this.adminFilterKategori : true;
                        return matchSearch && matchStatus && matchKategori;
                    });
                },

                get stats() {
                    const total = this.complaints.length;
                    const menunggu = this.complaints.filter(c => c.status === 'Menunggu').length;
                    const diproses = this.complaints.filter(c => c.status === 'Diproses').length;
                    const selesai = this.complaints.filter(c => c.status === 'Selesai').length;
                    const completionRate = total > 0 ? Math.round((selesai / total) * 100) : 0;
                    return { total, menunggu, diproses, selesai, completionRate };
                }
            }
        }
    </script>
</body>
</html>