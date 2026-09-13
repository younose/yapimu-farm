<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;

class SettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Cache::forget('settings');
        $databaseDefault = config('database.default');
        $databaseConfig = config("database.connections.{$databaseDefault}");
        $logoFull = public_path('images/dividen-full.png');
        $logo = public_path('images/dividen-logo.png');
        $favicon = $logo;

        if (file_exists($logoFull)) {
            $logoFull = file_get_contents($logoFull);
            $logoFullPath = 'settings/logo-full.png';
            Storage::put($logoFullPath, $logoFull);
        } else {
            $logoFullPath = 'images/logo-full.png';
        }

        if (file_exists($logo)) {
            $logo = file_get_contents($logo);
            $logoPath = 'settings/logo.png';
            Storage::put($logoPath, $logo);
        } else {
            $logoPath = 'images/logo.png';
        }

        if (file_exists($favicon)) {
            $favicon = file_get_contents($favicon);
            $faviconPath = 'settings/favicon.png';
            Storage::put($faviconPath, $favicon);
        } else {
            $faviconPath = 'images/favicon.png';
        }

        $settings = [
            [
                'key' => 'app_name',
                'name' => 'Nama Aplikasi',
                'value' => config('app.name') ?? 'Laravel',
                'group' => 'app',
            ],
            [
                'key' => 'app_url',
                'name' => 'URL Aplikasi',
                'value' => config('app.url') ?? 'http://localhost',
                'group' => 'app',
            ],
            [
                'key' => 'app_description',
                'name' => 'Deskripsi Aplikasi',
                'value' => 'Dividen Ayam Broiler, aplikasi mudah untuk melacak transaksi dan hasil panen ayam broiler Anda. Pantau keuangan Anda dengan cepat dan mudah!',
                'group' => 'app',
            ],
            [
                'key' => 'app_favicon',
                'name' => 'Icon Aplikasi',
                'value' => $faviconPath,
                'group' => 'app',
            ],
            [
                'key' => 'app_logo_full',
                'name' => 'Logo Aplikasi Full',
                'value' => $logoFullPath,
                'group' => 'app',
            ],
            [
                'key' => 'app_logo',
                'name' => 'Logo Aplikasi',
                'value' => $logoPath,
                'group' => 'app',
            ],
            [
                'key' => 'database_driver',
                'name' => 'Database Driver',
                'value' => $databaseDefault,
                'group' => 'database',
            ],
            [
                'key' => 'database_host',
                'name' => 'Database Host',
                'value' => $databaseConfig['host'] ?? 'localhost',
                'group' => 'database',
            ],
            [
                'key' => 'database_port',
                'name' => 'Database Port',
                'value' => $databaseConfig['port'] ?? '3306',
                'group' => 'database',
            ],
            [
                'key' => 'database_database',
                'name' => 'Database Name',
                'value' => $databaseConfig['database'] ?? 'si_dividen',
                'group' => 'database',
            ],
            [
                'key' => 'database_username',
                'name' => 'Database Username',
                'value' => $databaseConfig['username'] ?? 'root',
                'group' => 'database',
            ],
            [
                'key' => 'database_password',
                'name' => 'Database Password',
                'value' => $databaseConfig['password'] ?? '',
                'group' => 'database',
            ],
            // [
            //     'key' => 'mail_mailer',
            //     'name' => 'Mail Mailer',
            //     'value' => config('mail.mailer') ?? 'smtp',
            //     'group' => 'mail',
            // ],
            // [
            //     'key' => 'mail_host',
            //     'name' => 'Mail Host',
            //     'value' => config('mail.host') ?? 'smtp.mailtrap.io',
            //     'group' => 'mail',
            // ],
            // [
            //     'key' => 'mail_port',
            //     'name' => 'Mail Port',
            //     'value' => config('mail.port') ?? '2525',
            //     'group' => 'mail',
            // ],
            // [
            //     'key' => 'mail_username',
            //     'name' => 'Mail Username',
            //     'value' => config('mail.username') ?? null,
            //     'group' => 'mail',
            // ],
            // [
            //     'key' => 'mail_password',
            //     'name' => 'Mail Password',
            //     'value' => config('mail.password') ?? null,
            //     'group' => 'mail',
            // ],
            // [
            //     'key' => 'mail_encryption',
            //     'name' => 'Mail Encryption',
            //     'value' => config('mail.encryption') ?? null,
            //     'group' => 'mail',
            // ],
            // [
            //     'key' => 'mail_from_address',
            //     'name' => 'Mail From Address',
            //     'value' => config('mail.from.address') ?? 'hello@example.com',
            //     'group' => 'mail',
            // ],
            [
                'key' => 'default_zakat_percentage',
                'name' => 'Nilai default persentase zakat',
                'value' => '2.5',
                'group' => 'default',
            ],
            [
                'key' => 'default_cooperative_percentage',
                'name' => 'Nilai default persentase koperasi',
                'value' => '2',
                'group' => 'default',
            ],
            [
                'key' => 'default_management_fee',
                'name' => 'Nilai default biaya manajemen',
                'value' => '6500000',
                'group' => 'default',
            ],
            [
                'key' => 'default_share_price',
                'name' => 'Harga saham perlembar',
                'value' => '1500000',
                'group' => 'default',
            ],
            [
                'key' => 'default_share_count',
                'name' => 'Jumlah saham dibuka',
                'value' => '200',
                'group' => 'default',
            ],

        ];

        foreach ($settings as $setting) {
            Setting::create($setting);
        }
    }
}
