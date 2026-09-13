<?php

use App\Models\Period;
use App\Models\Setting;
use Illuminate\Support\Str;

if (! function_exists('getSetting')) {
    function getSetting($key, $default = null)
    {
        $setting = Setting::where('key', $key)->first();

        return $setting ? $setting->value : $default;
    }
}

if (! function_exists('setSetting')) {
    function setSetting($key, $value)
    {
        $setting = Setting::where('key', $key)->first();
        if ($setting) {
            $setting->update(['value' => $value]);
        } else {
            Setting::create(['key' => $key, 'value' => $value]);
        }
    }
}

if (! function_exists('getSettingDetail')) {
    function getSettingDetail($key)
    {
        $setting = Setting::where('key', $key)->first();

        return $setting;
    }
}

if (! function_exists('getOpenPeriod')) {
    function getOpenPeriod()
    {
        return Period::where('status', 'open')->first();
    }
}

if (! function_exists('stringLimit')) {
    function stringLimit($string, $length = 8)
    {
        return Str::limit($string, $length);
    }
}

if (! function_exists('getNavigations')) {
    function getNavigations()
    {
        $navigastions = [
            (object) [
                'name' => 'Dashboard',
                'url' => route('dashboard'),
                'icon' => 'fas fa-tachometer-alt fa-fw',
                'roles' => [
                    'admin',
                    'teller',
                    'investor',
                ],
            ],
            (object) [
                'name' => 'Laporan Keuangan',
                'icon' => 'fas fa-file-invoice-dollar fa-fw',
                'children' => [
                    (object) [
                        'name' => 'Catatan Keuangan',
                        'url' => route('journals.index'),
                        'icon' => 'fas fa-book fa-fw',
                        'roles' => [
                            'admin',
                            'teller',
                            'investor',
                        ],
                    ],
                    (object) [
                        'name' => 'Catatan Keuangan Yayasan',
                        'url' => route('foundation-journals.index'),
                        'icon' => 'fas fa-hand-holding-usd fa-fw',
                        'roles' => [
                            'admin',
                            'teller',
                        ],
                    ],
                    (object) [
                        'name' => 'Jaminan',
                        'url' => route('jaminan-journals.index'),
                        'icon' => 'fas fa-shield-halved fa-fw',
                        'roles' => [
                            'admin',
                            'teller',
                        ],
                    ],
                    (object) [
                        'name' => 'Pembayaran Manajemen',
                        'url' => route('payments.index', 'manajemen'),
                        'icon' => 'fas fa-file-invoice-dollar fa-fw',
                        'roles' => [
                            'admin',
                            'teller',
                        ],
                    ],
                    (object) [
                        'name' => 'Pembayaran Koperasi',
                        'url' => route('payments.index', 'koperasi'),
                        'icon' => 'fas fa-file-invoice-dollar fa-fw',
                        'roles' => [
                            'admin',
                            'teller',
                        ],
                    ],
                    (object) [
                        'name' => 'Pembayaran Zakat',
                        'url' => route('payments.index', 'zakat'),
                        'icon' => 'fas fa-file-invoice-dollar fa-fw',
                        'roles' => [
                            'admin',
                            'teller',
                        ],
                    ],
                    (object) [
                        'name' => 'Lap. Tutup Periode',
                        'url' => route('closings.index'),
                        'icon' => 'fas fa-calendar-check fa-fw',
                        'roles' => [
                            'admin',
                            'teller',
                            'investor',
                        ],
                    ],
                ],
                'roles' => [
                    'admin',
                    'teller',
                    'investor',
                ],
            ],
            (object) [
                'name' => 'Dividen',
                'icon' => 'fas fa-hand-holding-usd fa-fw',
                'children' => [
                    (object) [
                        'name' => 'Penarikan Dividen',
                        'url' => route('withdraws.index'),
                        'icon' => 'fas fa-hand-holding-usd fa-fw',
                        'roles' => [
                            'admin',
                            'teller',
                            'investor',
                        ],
                    ],
                    (object) [
                        'name' => 'Laporan Dividen',
                        'url' => route('dividends.index'),
                        'icon' => 'fas fa-file-invoice-dollar fa-fw',
                        // 'investor' temporarily removed — Laporan Dividen hidden from investors for now
                        'roles' => [
                            'admin',
                            'teller',
                        ],
                    ],

                    (object) [
                        'name' => 'Lap. Saldo Investor',
                        'url' => route('transactions.index'),
                        'icon' => 'fas fa-chart-line fa-fw',
                        'roles' => [
                            'admin',
                            'teller',
                            'investor',
                        ],
                    ],
                ],
                'roles' => [
                    'admin',
                    'teller',
                    'investor',
                ],
            ],
            // Akun Bank menu temporarily hidden from investors
            // (object) [
            //     'name' => 'Akun Bank',
            //     'url' => route('bank-account.index'),
            //     'icon' => 'fas fa-money-check-dollar fa-fw',
            //     'roles' => [
            //         'investor',
            //     ],
            // ],
            (object) [
                'name' => 'Periode',
                'url' => route('periods.index'),
                'icon' => 'fas fa-calendar-alt fa-fw',
                'roles' => [
                    'admin',
                    'teller',
                ],
            ],
            (object) [
                'name' => 'Investor',
                'url' => route('investors.index'),
                'icon' => 'fas fa-users fa-fw',
                'roles' => [
                    'admin',
                ],
            ],
            (object) [
                'name' => 'Lain-lain',
                'icon' => 'fas fa-user-lock fa-fw',
                'children' => [
                    (object) [
                        'name' => 'Admin',
                        'url' => route('admins.index'),
                        'icon' => 'fas fa-database fa-fw',
                        'roles' => [
                            'admin',
                        ],
                    ],
                    (object) [
                        'name' => 'Database Backup',
                        'url' => route('database-backups.index'),
                        'icon' => 'fas fa-database fa-fw',
                        'roles' => [
                            'admin',
                        ],
                    ],
                    (object) [
                        'name' => 'Activity Log',
                        'url' => route('activity-logs.index'),
                        'icon' => 'fas fa-user-lock fa-fw',
                        'roles' => [
                            'admin',
                        ],
                    ],
                    (object) [
                        'name' => 'Error Log',
                        'url' => route('log-viewer.index'),
                        'icon' => 'fas fa-user-lock fa-fw',
                        'roles' => [
                            'admin',
                        ],
                    ],
                    (object) [
                        'name' => 'Setting',
                        'url' => route('settings.index'),
                        'icon' => 'fas fa-user-lock fa-fw',
                        'roles' => [
                            'admin',
                        ],
                    ],
                ],
                'roles' => [
                    'admin',
                ],
            ],
        ];

        $routeName = request()->route()->getName();
        $params = request()->route()->parameters();
        $navs = [];
        foreach ($navigastions as $navigation) {
            $role = auth()->user()->roles->first();

            if (! in_array($role->name, $navigation->roles)) {
                continue;
            }

            if (isset($navigation->children)) {
                $navChildren = [];
                foreach ($navigation->children as $child) {
                    if (! in_array($role->name, $child->roles)) {
                        continue;
                    }

                    $child->active = $child->url === route($routeName, $params);

                    $navChildren[] = $child;
                }

                $navigation->children = $navChildren;
            } else {
                $navigation->active = $navigation->url === route($routeName, $params);
            }

            $navs[] = $navigation;
        }

        return $navs;
    }
}

if (! function_exists('thousandFormat')) {
    function thousandFormat($number)
    {
        return number_format($number, 0, ',', '.');
    }
}

if (! function_exists('idrFormat')) {
    function idrFormat($number, $withSymbol = true)
    {
        return $withSymbol ? 'Rp '.number_format($number, 0, ',', '.') : number_format($number, 0, ',', '.');
    }
}

if (! function_exists('terbilang')) {
    function terbilang($x)
    {
        $angka = ['', 'satu', 'dua', 'tiga', 'empat', 'lima', 'enam', 'tujuh', 'delapan', 'sembilan', 'sepuluh', 'sebelas'];

        if ($x < 12) {
            return ' '.$angka[$x];
        } elseif ($x < 20) {
            return terbilang($x - 10).' belas';
        } elseif ($x < 100) {
            return terbilang($x / 10).' puluh'.terbilang($x % 10);
        } elseif ($x < 200) {
            return 'seratus'.terbilang($x - 100);
        } elseif ($x < 1000) {
            return terbilang($x / 100).' ratus'.terbilang($x % 100);
        } elseif ($x < 2000) {
            return 'seribu'.terbilang($x - 1000);
        } elseif ($x < 1000000) {
            return terbilang($x / 1000).' ribu'.terbilang($x % 1000);
        } elseif ($x < 1000000000) {
            return terbilang($x / 1000000).' juta'.terbilang($x % 1000000);
        }

    }
}
