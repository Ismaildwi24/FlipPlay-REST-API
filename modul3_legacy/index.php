<?php

declare(strict_types=1);

require_once __DIR__.'/app/bootstrap.php';

use FlipPlay\Models\Anime;
use FlipPlay\Models\Movie;
use FlipPlay\Models\User;
use FlipPlay\Services\CLIHelper;
use FlipPlay\Services\Library;

CLIHelper::printBanner();

CLIHelper::printSectionHeader(
    '1',
    'Menambahkan Media ke Dalam Library',
    'Instansiasi objek Anime & Movie lalu didaftarkan ke dalam Library'
);

$library = new Library('FlipPlay Vibe Catalog');

$mediaItems = [
    new Anime(
        id: 1,
        title: 'Attack on Titan: Final Season',
        genres: ['Action', 'Fantasy', 'Mystery'],
        rating: 9.1,
        releaseYear: 2023,
        aura: 'Binge-Mode',
        synopsis: 'Perjuangan terakhir umat manusia menghadapi ancaman para Titan dan rahasia kelam dunia.',
        episodeCount: 28,
        studio: 'MAPPA',
        status: 'Completed'
    ),
    new Anime(
        id: 2,
        title: 'Cyberpunk: Edgerunners',
        genres: ['Action', 'Sci-Fi', 'Psychological'],
        rating: 8.6,
        releaseYear: 2022,
        aura: 'High-Energy',
        synopsis: 'Seorang anak jalanan di kota masa depan terobsesi dengan teknologi modifikasi tubuh demi bertahan hidup.',
        episodeCount: 10,
        studio: 'Studio Trigger',
        status: 'Completed'
    ),
    new Anime(
        id: 3,
        title: 'Your Name (Kimi no Na wa)',
        genres: ['Drama', 'Romance', 'Supernatural'],
        rating: 8.9,
        releaseYear: 2016,
        aura: 'Melancholy',
        synopsis: 'Dua remaja terhubung secara misterius melalui pertukaran tubuh lintas ruang dan waktu.',
        episodeCount: 1,
        studio: 'CoMix Wave Films',
        status: 'Completed'
    ),
    new Anime(
        id: 4,
        title: 'Steins;Gate',
        genres: ['Sci-Fi', 'Suspense', 'Thriller'],
        rating: 9.1,
        releaseYear: 2011,
        aura: 'Mind-Bending',
        synopsis: 'Sekelompok ilmuwan muda tanpa sengaja menemukan mesin waktu dan memicu konspirasi global.',
        episodeCount: 24,
        studio: 'White Fox',
        status: 'Completed'
    ),
    new Movie(
        id: 5,
        title: 'Interstellar',
        genres: ['Adventure', 'Drama', 'Sci-Fi'],
        rating: 8.7,
        releaseYear: 2014,
        aura: 'Mind-Bending',
        synopsis: 'Sebuah tim penjelajah luar angkasa melintasi lubang cacing untuk mencari rumah baru bagi umat manusia.',
        durationMinutes: 169,
        director: 'Christopher Nolan',
        quality: '4K Ultra HD (IMAX)'
    ),
    new Movie(
        id: 6,
        title: 'Spider-Man: Across the Spider-Verse',
        genres: ['Action', 'Animation', 'Adventure'],
        rating: 8.8,
        releaseYear: 2023,
        aura: 'High-Energy',
        synopsis: 'Miles Morales melintasi Multiverse dan bentrok dengan Spider-Society tentang takdir seorang pahlawan.',
        durationMinutes: 140,
        director: 'Joaquim Dos Santos, Kemp Powers',
        quality: '4K Ultra HD Dolby Vision'
    ),
    new Movie(
        id: 7,
        title: 'La La Land',
        genres: ['Comedy', 'Drama', 'Music'],
        rating: 8.0,
        releaseYear: 2016,
        aura: 'Melancholy',
        synopsis: 'Kisah cinta antara musisi jazz dan calon aktris di tengah ambisi karier di Los Angeles.',
        durationMinutes: 128,
        director: 'Damien Chazelle',
        quality: '1080p BluRay'
    ),
    new Movie(
        id: 8,
        title: 'The Dark Knight',
        genres: ['Action', 'Crime', 'Drama'],
        rating: 9.0,
        releaseYear: 2008,
        aura: 'Binge-Mode',
        synopsis: 'Batman menghadapi teror psikologis dan kekacauan tak terkendali dari musuh bebuyutannya, The Joker.',
        durationMinutes: 152,
        director: 'Christopher Nolan',
        quality: '4K Remastered'
    ),
];

foreach ($mediaItems as $media) {
    $library->addMedia($media);
    CLIHelper::printSuccess(sprintf(
        'Berhasil menambahkan [%s] #%d: %s (%s)',
        $media->getType(),
        $media->getId(),
        $media->getTitle(),
        $media->getAura()
    ));
}

$stats = $library->getStatistics();
echo PHP_EOL;
CLIHelper::printInfo(sprintf(
    'Total Koleksi: %d media | Anime: %d judul | Film: %d judul | Rata-rata Rating: %.2f★',
    $stats['total'],
    $stats['animeCount'],
    $stats['movieCount'],
    $stats['averageRating']
));

CLIHelper::printSectionHeader(
    '2',
    'Menampilkan Daftar Media yang Tersedia',
    'Mengambil seluruh media dari katalog dan menyajikannya dalam tabel rapi'
);

$allMedia = $library->getAllMedia();
CLIHelper::printMediaTable($allMedia);

echo PHP_EOL;
CLIHelper::printInfo("Contoh Filter Berdasarkan Aura: 'Mind-Bending'");
$mindBendingMedia = $library->filterByAura('Mind-Bending');
CLIHelper::printMediaTable($mindBendingMedia);

CLIHelper::printSectionHeader(
    '3',
    'Mencari Media Berdasarkan Judul',
    'Menjalankan query pencarian kata kunci pada judul dan genre'
);

$searchQuery1 = 'Spider';
CLIHelper::printInfo("Melakukan pencarian dengan kata kunci: '{$searchQuery1}'");
$results1 = $library->searchByTitle($searchQuery1);
CLIHelper::printMediaTable($results1);

$searchQuery2 = 'Titan';
echo PHP_EOL;
CLIHelper::printInfo("Melakukan pencarian dengan kata kunci: '{$searchQuery2}'");
$results2 = $library->searchByTitle($searchQuery2);
CLIHelper::printMediaTable($results2);

$searchQuery3 = 'Avengers 2099';
echo PHP_EOL;
CLIHelper::printInfo("Melakukan pencarian dengan kata kunci: '{$searchQuery3}'");
$results3 = $library->searchByTitle($searchQuery3);
if (empty($results3)) {
    CLIHelper::printWarning("Tidak ada media yang cocok dengan kata kunci '{$searchQuery3}'.");
} else {
    CLIHelper::printMediaTable($results3);
}

CLIHelper::printSectionHeader(
    '4',
    'Menampilkan Informasi Detail Suatu Media',
    'Memanggil method getDetails() yang dioverride oleh masing-masing subclass'
);

$animeMedia = $library->findById(1);
if ($animeMedia !== null) {
    echo $animeMedia->getDetails().PHP_EOL;
}

echo PHP_EOL;

$movieMedia = $library->findById(5);
if ($movieMedia !== null) {
    echo $movieMedia->getDetails().PHP_EOL;
}

CLIHelper::printSectionHeader(
    '5',
    'Menampilkan Koleksi Media Milik Pengguna',
    'Pengguna menyimpan media ke Watchlist/Koleksi dengan status dan rating pribadi'
);

$user = new User(
    id: 101,
    username: 'ismail_vibes',
    name: 'Ismail Nurhadi',
    favoriteAura: 'Binge-Mode'
);

CLIHelper::printInfo($user->getProfileSummary());
echo PHP_EOL;

$user->addToCollection(
    media: $library->findById(1),
    status: 'Completed',
    userRating: 9.5,
    note: 'Masterpiece anime terbaik sepanjang masa!'
);
CLIHelper::printSuccess("Menambahkan 'Attack on Titan' ke koleksi [Status: Selesai Tonton | Rating: 9.5]");

$user->addToCollection(
    media: $library->findById(2),
    status: 'Completed',
    userRating: 9.0,
    note: 'Visual spektakuler dan soundtrack luar biasa.'
);
CLIHelper::printSuccess("Menambahkan 'Cyberpunk: Edgerunners' ke koleksi [Status: Selesai Tonton | Rating: 9.0]");

$user->addToCollection(
    media: $library->findById(5),
    status: 'Watching',
    userRating: null,
    note: 'Sedang ditonton ulang di akhir pekan.'
);
CLIHelper::printSuccess("Menambahkan 'Interstellar' ke koleksi [Status: Sedang Ditonton]");

$user->addToCollection(
    media: $library->findById(6),
    status: 'Plan to Watch',
    userRating: null,
    note: 'Masuk daftar antrean tontonan berikutnya.'
);
CLIHelper::printSuccess("Menambahkan 'Spider-Man: Across the Spider-Verse' ke koleksi [Status: Rencana Tonton]");

$duplicateAttempt = $user->addToCollection($library->findById(1));
if (! $duplicateAttempt) {
    CLIHelper::printWarning('Validasi: Gagal menambahkan media #1 karena sudah ada dalam koleksi pengguna.');
}

echo PHP_EOL;
CLIHelper::printInfo('Menampilkan tabel daftar koleksi milik @'.$user->getUsername().':');
CLIHelper::printUserCollectionTable($user->getCollection());

echo PHP_EOL;
echo CLIHelper::GREEN.CLIHelper::BOLD.'============================================================================'.CLIHelper::RESET.PHP_EOL;
echo CLIHelper::GREEN.CLIHelper::BOLD.'  [✓] SIMULASI BACKEND OOP FLIPPLAY SELESAI BERJALAN DENGAN SUKSES!        '.CLIHelper::RESET.PHP_EOL;
echo CLIHelper::GREEN.CLIHelper::BOLD.'============================================================================'.CLIHelper::RESET.PHP_EOL;
echo PHP_EOL;

$isInteractiveFlag = isset($argv) && (in_array('--interactive', $argv, true) || in_array('-i', $argv, true));

if ($isInteractiveFlag) {
    runInteractiveMode($library, $user);
} else {
    echo CLIHelper::CYAN.'💡 Tips Praktikum:'.CLIHelper::RESET.' Jalankan '.CLIHelper::BOLD.'php index.php --interactive'.CLIHelper::RESET." untuk mencoba eksplorasi menu interaktif CLI secara langsung.\n".PHP_EOL;
}

function runInteractiveMode(Library $library, User $user): void
{
    while (true) {
        echo PHP_EOL;
        echo CLIHelper::BG_BLUE.CLIHelper::WHITE.CLIHelper::BOLD.' 🎮 MENU INTERAKTIF FLIPPLAY '.CLIHelper::RESET.PHP_EOL;
        echo "1. Tampilkan Semua Media di Library\n";
        echo "2. Cari Media Berdasarkan Judul\n";
        echo "3. Lihat Detail Lengkap Media (by ID)\n";
        echo "4. Tambah Media ke Koleksi Saya\n";
        echo "5. Tampilkan Koleksi Saya\n";
        echo "6. Filter Media Berdasarkan Aura\n";
        echo "0. Keluar dari Program\n";
        echo 'Pilih opsi [0-6]: ';

        $choice = trim((string) fgets(STDIN));

        switch ($choice) {
            case '1':
                CLIHelper::printMediaTable($library->getAllMedia());
                break;

            case '2':
                echo 'Masukkan kata kunci judul: ';
                $query = trim((string) fgets(STDIN));
                $results = $library->searchByTitle($query);
                CLIHelper::printMediaTable($results);
                break;

            case '3':
                echo 'Masukkan ID Media: ';
                $id = (int) trim((string) fgets(STDIN));
                $media = $library->findById($id);
                if ($media !== null) {
                    echo $media->getDetails().PHP_EOL;
                } else {
                    CLIHelper::printWarning("Media dengan ID #{$id} tidak ditemukan!");
                }
                break;

            case '4':
                echo 'Masukkan ID Media yang ingin ditambahkan: ';
                $id = (int) trim((string) fgets(STDIN));
                $media = $library->findById($id);
                if ($media === null) {
                    CLIHelper::printWarning('Media tidak ditemukan.');
                    break;
                }
                echo 'Status (1. Plan to Watch / 2. Watching / 3. Completed) [Default: 2]: ';
                $statusInput = trim((string) fgets(STDIN));
                $status = match ($statusInput) {
                    '1' => 'Plan to Watch',
                    '3' => 'Completed',
                    default => 'Watching'
                };
                echo 'Beri Rating pribadi (0.0 - 10.0, kosongkan jika belum dinilai): ';
                $rateInput = trim((string) fgets(STDIN));
                $userRate = is_numeric($rateInput) ? (float) $rateInput : null;

                $added = $user->addToCollection($media, $status, $userRate);
                if ($added) {
                    CLIHelper::printSuccess("Berhasil menambahkan '{$media->getTitle()}' ke koleksi Anda!");
                } else {
                    CLIHelper::printWarning('Media sudah ada di dalam koleksi Anda.');
                }
                break;

            case '5':
                CLIHelper::printInfo($user->getProfileSummary());
                CLIHelper::printUserCollectionTable($user->getCollection());
                break;

            case '6':
                echo 'Pilih Aura (1: High-Energy, 2: Melancholy, 3: Binge-Mode, 4: Mind-Bending): ';
                $auraChoice = trim((string) fgets(STDIN));
                $auraName = match ($auraChoice) {
                    '1' => 'High-Energy',
                    '2' => 'Melancholy',
                    '3' => 'Binge-Mode',
                    '4' => 'Mind-Bending',
                    default => ''
                };
                if (! empty($auraName)) {
                    $filtered = $library->filterByAura($auraName);
                    CLIHelper::printMediaTable($filtered);
                } else {
                    CLIHelper::printWarning('Pilihan aura tidak valid.');
                }
                break;

            case '0':
                echo CLIHelper::CYAN."Terima kasih telah menggunakan FlipPlay Engine!\n".CLIHelper::RESET;

                return;

            default:
                CLIHelper::printWarning('Pilihan tidak valid, silakan coba lagi.');
                break;
        }
    }
}
