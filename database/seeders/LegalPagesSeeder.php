<?php

namespace Database\Seeders;

use App\Models\Language;
use App\Models\DS_Page;
use App\Models\DS_PageTranslation;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class LegalPagesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $languages = Language::all();
        $en = $languages->where('code', 'en')->first();
        $ar = $languages->where('code', 'ar')->first();

        $pages = [
            [
                'slug' => 'terms-and-conditions',
                'translations' => [
                    'en' => [
                        'title' => 'Terms & Conditions',
                        'content' => '<h1>Terms of Service</h1><p>Please read these terms carefully before using our services...</p>',
                    ],
                    'ar' => [
                        'title' => 'الشروط والأحكام',
                        'content' => '<h1>شروط الخدمة</h1><p>يرجى قراءة هذه الشروط بعناية قبل استخدام خدماتنا...</p>',
                    ]
                ]
            ],
            [
                'slug' => 'privacy-policy',
                'translations' => [
                    'en' => [
                        'title' => 'Privacy Policy',
                        'content' => '<h1>Privacy Policy</h1><p>Your privacy is important to us. This policy explains how we handle your data...</p>',
                    ],
                    'ar' => [
                        'title' => 'سياسة الخصوصية',
                        'content' => '<h1>سياسة الخصوصية</h1><p>خصوصيتك تهمنا. توضح هذه السياسة كيفية تعاملنا مع بياناتك...</p>',
                    ]
                ]
            ],
            [
                'slug' => 'refund-policy',
                'translations' => [
                    'en' => [
                        'title' => 'Refund Policy',
                        'content' => '<h1>Refund Policy</h1><p>Our goal is your satisfaction. Here is how our refund process works...</p>',
                    ],
                    'ar' => [
                        'title' => 'سياسة الاسترجاع',
                        'content' => '<h1>سياسة الاسترجاع</h1><p>هدفنا هو رضاكم. إليكم كيفية عمل عملية الاسترجاع...</p>',
                    ]
                ]
            ],
            [
                'slug' => 'cookie-policy',
                'translations' => [
                    'en' => [
                        'title' => 'Cookie Policy',
                        'content' => '<h1>Cookie Policy</h1><p>We use cookies to improve your experience. This policy explains why...</p>',
                    ],
                    'ar' => [
                        'title' => 'سياسة ملفات الارتباط',
                        'content' => '<h1>سياسة ملفات الارتباط</h1><p>نحن نستخدم ملفات الارتباط لتحسين تجربتكم. توضح هذه السياسة السبب...</p>',
                    ]
                ]
            ],
        ];

        foreach ($pages as $pageData) {
            $page = DS_Page::updateOrCreate(
                ['slug' => $pageData['slug']],
                ['is_active' => true]
            );

            foreach ($pageData['translations'] as $langCode => $transData) {
                $lang = $languages->where('code', $langCode)->first();
                if ($lang) {
                    DS_PageTranslation::updateOrCreate(
                        ['page_id' => $page->id, 'language_id' => $lang->id],
                        [
                            'title' => $transData['title'],
                            'content' => $transData['content'],
                        ]
                    );
                }
            }
        }
    }
}
