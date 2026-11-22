<?php

declare(strict_types=1);

namespace FlipPlay\Services;

use FlipPlay\Models\Anime;
use FlipPlay\Models\Movie;

class CLIHelper
{
    public const RESET = "\033[0m";

    public const BOLD = "\033[1m";

    public const DIM = "\033[2m";

    public const RED = "\033[31m";

    public const GREEN = "\033[32m";

    public const YELLOW = "\033[33m";

    public const BLUE = "\033[34m";

    public const MAGENTA = "\033[35m";

    public const CYAN = "\033[36m";

    public const WHITE = "\033[37m";

    public const BG_BLUE = "\033[44m";

    public const BG_MAGENTA = "\033[45m";

    public const BG_CYAN = "\033[46m";

    public const BG_DARK = "\033[100m";

    public static function printBanner(): void
    {
        $banner = '
'.self::CYAN.self::BOLD.'  ███████╗██╗     ██╗██████╗ ██████╗ ██╗      █████╗ ██╗   ██╗
  ██╔════╝██║     ██║██╔══██╗██╔══██╗██║     ██╔══██╗╚██╗ ██╔╝
  █████╗  ██║     ██║██████╔╝██████╔╝██║     ███████║ ╚████╔╝ 
  ██╔══╝  ██║     ██║██╔═══╝ ██╔═══╝ ██║     ██╔══██║  ╚██╔╝  
  ██║     ███████╗██║██║     ██║     ███████╗██║  ██║   ██║   
  ╚═╝     ╚══════╝╚═╝╚═╝     ╚═╝     ╚══════╝╚═╝  ╚═╝   ╚═╝   '.self::RESET.'
'.self::MAGENTA.self::BOLD.'       ~ The Vibe Grid Media Engine (PHP OOP Simulation) ~'.self::RESET.'
'.self::DIM.'        Modul 3: Pemrograman Berorientasi Objek dengan PHP'.self::RESET."\n";

        echo $banner.PHP_EOL;
    }

    public static function printSectionHeader(string $stepNumber, string $title, string $description = ''): void
    {
        echo PHP_EOL;
        echo self::YELLOW.self::BOLD.'━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━'.self::RESET.PHP_EOL;
        echo self::BG_MAGENTA.self::WHITE.self::BOLD." TAHAP {$stepNumber} ".self::RESET.' '.self::BOLD.self::CYAN.$title.self::RESET.PHP_EOL;
        if (! empty($description)) {
            echo self::DIM.'  ℹ  '.$description.self::RESET.PHP_EOL;
        }
        echo self::YELLOW.self::BOLD.'━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━'.self::RESET.PHP_EOL;
    }

    public static function printSuccess(string $message): void
    {
        echo self::GREEN.self::BOLD.'  [✓] '.self::RESET.$message.PHP_EOL;
    }

    public static function printInfo(string $message): void
    {
        echo self::CYAN.'  [ℹ] '.self::RESET.$message.PHP_EOL;
    }

    public static function printWarning(string $message): void
    {
        echo self::YELLOW.'  [!] '.self::RESET.$message.PHP_EOL;
    }

    public static function formatAuraBadge(string $aura): string
    {
        return match (strtolower(trim($aura))) {
            'high-energy' => self::RED.self::BOLD.'[⚡ High-Energy]'.self::RESET,
            'melancholy' => self::BLUE.self::BOLD.'[🌧 Melancholy]'.self::RESET,
            'binge-mode' => self::MAGENTA.self::BOLD.'[🔥 Binge-Mode]'.self::RESET,
            'mind-bending' => self::CYAN.self::BOLD.'[🌀 Mind-Bending]'.self::RESET,
            default => self::WHITE."[{$aura}]".self::RESET,
        };
    }

    public static function printMediaTable(array $mediaList): void
    {
        if (empty($mediaList)) {
            echo self::YELLOW.'  (Tidak ada media yang ditemukan)'.self::RESET.PHP_EOL;

            return;
        }

        $header = sprintf(
            "┌──────┬─────────┬───────────────────────────────┬────────────────┬──────┬─────────┬──────────────────────┐\n".
            "│ %-4s │ %-7s │ %-29s │ %-14s │ %-4s │ %-7s │ %-20s │\n".
            '├──────┼─────────┼───────────────────────────────┼────────────────┼──────┼─────────┼──────────────────────┤',
            'ID',
            'Tipe',
            'Judul Media',
            'Aura (Vibe)',
            'Rilis',
            'Rating',
            'Spesifikasi'
        );
        echo $header.PHP_EOL;

        foreach ($mediaList as $media) {
            $title = mb_strimwidth($media->getTitle(), 0, 29, '...');
            $aura = $media->getAura();
            $spec = '';

            if ($media instanceof Anime) {
                $spec = mb_strimwidth("{$media->getEpisodeCount()} Eps | {$media->getStudio()}", 0, 20, '..');
            } elseif ($media instanceof Movie) {
                $spec = mb_strimwidth("{$media->getDurationMinutes()}m | {$media->getDirector()}", 0, 20, '..');
            }

            printf(
                "│ #%-3d │ %-7s │ %-29s │ %-14s │ %-4d │ %4.1f ★  │ %-20s │\n",
                $media->getId(),
                $media->getType(),
                $title,
                $aura,
                $media->getReleaseYear(),
                $media->getRating(),
                $spec
            );
        }

        echo '└──────┴─────────┴───────────────────────────────┴────────────────┴──────┴─────────┴──────────────────────┘'.PHP_EOL;
    }

    public static function printUserCollectionTable(array $collection): void
    {
        if (empty($collection)) {
            echo self::YELLOW.'  (Koleksi pengguna masih kosong)'.self::RESET.PHP_EOL;

            return;
        }

        $header = sprintf(
            "┌──────┬───────────────────────────────┬─────────┬──────────────────┬──────────────┬──────────────┐\n".
            "│ %-4s │ %-29s │ %-7s │ %-16s │ %-12s │ %-12s │\n".
            '├──────┼───────────────────────────────┼─────────┼──────────────────┼──────────────┼──────────────┤',
            'ID',
            'Judul Media',
            'Tipe',
            'Status Tonton',
            'User Rating',
            'Ditambahkan'
        );
        echo $header.PHP_EOL;

        foreach ($collection as $item) {
            $media = $item['media'];
            $title = mb_strimwidth($media->getTitle(), 0, 29, '...');
            $userRating = $item['userRating'] !== null ? sprintf('%.1f ★', $item['userRating']) : '-';

            printf(
                "│ #%-3d │ %-29s │ %-7s │ %-16s │ %-12s │ %-12s │\n",
                $media->getId(),
                $title,
                $media->getType(),
                $item['status'],
                $userRating,
                $item['addedAt']
            );
        }

        echo '└──────┴───────────────────────────────┴─────────┴──────────────────┴──────────────┴──────────────┘'.PHP_EOL;
    }
}
