<?php

namespace Database\Seeders;

use App\Models\Plan;
use Illuminate\Database\Seeder;

class PlanSeeder extends Seeder
{
    public function run(): void
    {
        $plans = [
            [
                'name' => ['en' => 'Free Plan', 'ar' => 'الباقة المجانية'],
                'description' => ['en' => 'Starter pack for new users', 'ar' => 'باقة للمبتدئين'],
                'price' => 0,
                'currency' => 'USD',
                'duration_days' => 30,
                'trial_days' => 0,
                'is_active' => true,
                'is_default' => true,
                'is_featured' => false,
                'quotas' => [
                    'ai_pages' => 2,
                    'drag_drop_pages' => 1,
                    'orders' => 10,
                    'products' => 5,
                    'support_messages' => 5,
                    'payment_gateways' => ['bank_transfer'],
                    'custom_domains' => 0,
                    'storage_gb' => 0.5,
                    'whatsapp' => 0,
                    'whatsapp_messages' => 0,
                    'fb_pixel' => 0,
                    'snap_pixel' => 0,
                    'twitter_pixel' => 0,
                    'tiktok_pixel' => 0,
                    'ga_pixel' => 1,
                    'google_merchant' => 0,
                    'seo' => 0,
                    'remove_branding' => 0,
                ]
            ],
            [
                'name' => ['en' => 'Professional Plan', 'ar' => 'الباقة الاحترافية'],
                'description' => ['en' => 'Perfect for growing businesses', 'ar' => 'مثالية للشركات الناشئة'],
                'price' => 2900,
                'currency' => 'USD',
                'duration_days' => 30,
                'trial_days' => 7,
                'is_active' => true,
                'is_default' => false,
                'is_featured' => true,
                'quotas' => [
                    'ai_pages' => 20,
                    'drag_drop_pages' => 10,
                    'orders' => 500,
                    'products' => 100,
                    'support_messages' => -1,
                    'payment_gateways' => ['bank_transfer', 'stripe', 'paypal'],
                    'custom_domains' => 3,
                    'storage_gb' => 10,
                    'whatsapp' => 1,
                    'whatsapp_messages' => 0,
                    'fb_pixel' => 1,
                    'snap_pixel' => 1,
                    'twitter_pixel' => 1,
                    'tiktok_pixel' => 1,
                    'ga_pixel' => 1,
                    'google_merchant' => 1,
                    'seo' => 1,
                    'remove_branding' => 1,
                ]
            ],
            [
                'name' => ['en' => 'Unlimited Plan', 'ar' => 'الباقة غير المحدودة'],
                'description' => ['en' => 'No limits for power users', 'ar' => 'بدون حدود للمستخدمين المميزين'],
                'price' => 9900,
                'currency' => 'USD',
                'duration_days' => 30,
                'trial_days' => 0,
                'is_active' => true,
                'is_default' => false,
                'is_featured' => false,
                'quotas' => [
                    'ai_pages' => -1,
                    'drag_drop_pages' => -1,
                    'orders' => -1,
                    'products' => -1,
                    'support_messages' => -1,
                    'payment_gateways' => ['bank_transfer', 'stripe', 'paypal', 'razorpay'],
                    'custom_domains' => -1,
                    'storage_gb' => 100,
                    'whatsapp' => 1,
                    'whatsapp_messages' => 1,
                    'fb_pixel' => 1,
                    'snap_pixel' => 1,
                    'twitter_pixel' => 1,
                    'tiktok_pixel' => 1,
                    'ga_pixel' => 1,
                    'google_merchant' => 1,
                    'seo' => 1,
                    'remove_branding' => 1,
                ]
            ]
        ];

        foreach ($plans as $planData) {
            Plan::updateOrCreate(
                ['name->en' => $planData['name']['en']],
                $planData
            );
        }
    }
}
