<?php

namespace Database\Seeders;

use App\Models\Language;
use Illuminate\Database\Seeder;

class LanguageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $languages = [
            ['name' => 'English', 'code' => 'en', 'direction' => 'ltr', 'is_default' => true],
            ['name' => 'Arabic', 'code' => 'ar', 'direction' => 'rtl', 'is_default' => false],
        ];

        foreach ($languages as $language) {
            Language::firstOrCreate(['code' => $language['code']], $language);
        }
    }
}
