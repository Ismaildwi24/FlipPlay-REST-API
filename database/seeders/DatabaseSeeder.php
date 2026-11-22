<?php

namespace Database\Seeders;

use App\Models\Media;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {

        $kenjiro = User::create([
            'name' => 'Kenjiro Sato',
            'username' => 'kenjiro',
            'email' => 'kenjiro@flipplay.test',
            'password' => Hash::make('password123'),
            'favorite_aura' => 'Binge-Mode',
        ]);

        $aoi = User::create([
            'name' => 'Aoi Hoshino',
            'username' => 'aoi_vibes',
            'email' => 'aoi@flipplay.test',
            'password' => Hash::make('password123'),
            'favorite_aura' => 'Melancholy',
        ]);

        $ren = User::create([
            'name' => 'Ren Amamiya',
            'username' => 'ren_runner',
            'email' => 'ren@flipplay.test',
            'password' => Hash::make('password123'),
            'favorite_aura' => 'High-Energy',
        ]);

        $aot = Media::create([
            'title' => 'Attack on Titan: Final Season',
            'type' => 'Anime',
            'genres' => ['Action', 'Fantasy', 'Mystery'],
            'rating' => 9.1,
            'release_year' => 2023,
            'aura' => 'Binge-Mode',
            'synopsis' => 'Perjuangan terakhir umat manusia menghadapi ancaman para Titan dan rahasia kelam dunia.',
            'episode_count' => 28,
            'studio' => 'MAPPA',
            'status' => 'Completed',
        ]);

        $cyberpunk = Media::create([
            'title' => 'Cyberpunk: Edgerunners',
            'type' => 'Anime',
            'genres' => ['Action', 'Sci-Fi', 'Psychological'],
            'rating' => 8.6,
            'release_year' => 2022,
            'aura' => 'High-Energy',
            'synopsis' => 'Seorang anak jalanan di kota masa depan terobsesi dengan teknologi modifikasi tubuh demi bertahan hidup.',
            'episode_count' => 10,
            'studio' => 'Studio Trigger',
            'status' => 'Completed',
        ]);

        $yourName = Media::create([
            'title' => 'Your Name (Kimi no Na wa)',
            'type' => 'Anime',
            'genres' => ['Drama', 'Romance', 'Supernatural'],
            'rating' => 8.9,
            'release_year' => 2016,
            'aura' => 'Melancholy',
            'synopsis' => 'Dua remaja terhubung secara misterius melalui pertukaran tubuh lintas ruang dan waktu.',
            'episode_count' => 1,
            'studio' => 'CoMix Wave Films',
            'status' => 'Completed',
        ]);

        $steinsGate = Media::create([
            'title' => 'Steins;Gate',
            'type' => 'Anime',
            'genres' => ['Sci-Fi', 'Suspense', 'Thriller'],
            'rating' => 9.1,
            'release_year' => 2011,
            'aura' => 'Mind-Bending',
            'synopsis' => 'Sekelompok ilmuwan muda tanpa sengaja menemukan mesin waktu dan memicu konspirasi global.',
            'episode_count' => 24,
            'studio' => 'White Fox',
            'status' => 'Completed',
        ]);

        $interstellar = Media::create([
            'title' => 'Interstellar',
            'type' => 'Movie',
            'genres' => ['Adventure', 'Drama', 'Sci-Fi'],
            'rating' => 8.7,
            'release_year' => 2014,
            'aura' => 'Mind-Bending',
            'synopsis' => 'Sebuah tim penjelajah luar angkasa melintasi lubang cacing untuk mencari rumah baru bagi umat manusia.',
            'duration_minutes' => 169,
            'director' => 'Christopher Nolan',
            'quality' => '4K Ultra HD (IMAX)',
        ]);

        $spiderVerse = Media::create([
            'title' => 'Spider-Man: Across the Spider-Verse',
            'type' => 'Movie',
            'genres' => ['Action', 'Animation', 'Adventure'],
            'rating' => 8.8,
            'release_year' => 2023,
            'aura' => 'High-Energy',
            'synopsis' => 'Miles Morales melintasi Multiverse dan bentrok dengan Spider-Society tentang takdir seorang pahlawan.',
            'duration_minutes' => 140,
            'director' => 'Joaquim Dos Santos, Kemp Powers',
            'quality' => '4K Ultra HD Dolby Vision',
        ]);

        $laLaLand = Media::create([
            'title' => 'La La Land',
            'type' => 'Movie',
            'genres' => ['Comedy', 'Drama', 'Music'],
            'rating' => 8.0,
            'release_year' => 2016,
            'aura' => 'Melancholy',
            'synopsis' => 'Kisah cinta seorang musisi jazz dan calon aktris di gemerlapnya kota Los Angeles.',
            'duration_minutes' => 128,
            'director' => 'Damien Chazelle',
            'quality' => '4K Ultra HD',
        ]);

        $kenjiro->media()->attach([
            $aot->id => [
                'status' => 'Completed',
                'user_rating' => 9.5,
                'note' => 'Ending yang luar biasa dramatis dan memuaskan.',
            ],
            $cyberpunk->id => [
                'status' => 'Watching',
                'user_rating' => 9.0,
                'note' => 'Animasi dan soundtrack sangat energik!',
            ],
        ]);

        $aoi->media()->attach([
            $yourName->id => [
                'status' => 'Completed',
                'user_rating' => 10.0,
                'note' => 'Film anime paling emosional dan visualnya luar biasa.',
            ],
            $laLaLand->id => [
                'status' => 'Completed',
                'user_rating' => 8.5,
                'note' => 'Lagu epilogue selalu bikin merinding.',
            ],
        ]);

        $ren->media()->attach([
            $interstellar->id => [
                'status' => 'Plan to Watch',
                'user_rating' => null,
                'note' => 'Direkomendasikan oleh Kenjiro.',
            ],
        ]);
    }
}
