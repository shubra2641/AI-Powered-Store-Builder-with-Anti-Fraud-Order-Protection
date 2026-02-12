<?php

namespace Database\Seeders;

use App\Models\EmailTemplate;
use App\Models\Language;
use Illuminate\Database\Seeder;

class EmailTemplateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $en = Language::where('code', 'en')->first();
        $ar = Language::where('code', 'ar')->first();
        if (!$en && !$ar) return;

        $templates = [
            [
                'slug' => 'activation_email',
                'name' => 'Account Activation',
                'subject' => 'Activate your account',
                'content' => '<h1>Welcome {{ $user->name }}</h1><p>Please activate your account by clicking the link below:</p><a href="{{ $activation_url }}">Activate Account</a>',
                'is_system' => true,
                'description' => 'Sent when a user registers to verify their email.',
                'language_id' => $en->id
            ],
            [
                'slug' => 'welcome_email',
                'name' => 'Welcome Message',
                'subject' => 'Welcome to DropSaaS',
                'content' => '<h1>Welcome aboard, {{ $user->name }}!</h1><p>We are glad to have you with us.</p>',
                'is_system' => true,
                'description' => 'Sent after a user activates their account.',
                'language_id' => $en->id
            ],
            [
                'slug' => 'password_reset_email',
                'name' => 'Password Reset',
                'subject' => 'Reset your password',
                'content' => '<h1>Password Reset</h1><p>Click the link below to reset your password:</p><a href="{{ $reset_url }}">Reset Password</a>',
                'is_system' => true,
                'description' => 'Sent when a user requests a password reset.',
                'language_id' => $en->id
            ],
            [
                'slug' => 'password_changed_notification',
                'name' => 'Password Changed Alert',
                'subject' => 'Password Changed',
                'content' => '<h1>Security Alert</h1><p>Your password has been changed successfully.</p>',
                'is_system' => true,
                'description' => 'Security notification after password change.',
                'language_id' => $en->id
            ],
            [
                'slug' => 'credit_added_notification',
                'name' => 'Credit Added Notification',
                'subject' => 'Credit Added to Your Account',
                'content' => '<h1>Hello {{ $user->name }}</h1><p>A credit of <strong>{{ $amount }}</strong> has been added to your account.</p><p>Your new balance is: <strong>{{ $balance }}</strong></p>',
                'is_system' => true,
                'description' => 'Sent when an admin adds credit to a user account.',
                'language_id' => $en?->id
            ],
            [
                'slug' => 'subscription_confirmation',
                'name' => 'Subscription Confirmation',
                'subject' => 'Subscription Activated: {{ $plan_name }}',
                'content' => '<p>Hello {{ $user_name }},</p><p>Your subscription to <strong>{{ $plan_name }}</strong> has been activated successfully.</p><p>Transaction ID: {{ $transaction_id }}</p><p>Expiry Date: {{ $ends_at }}</p>',
                'is_system' => true,
                'description' => 'Sent when a subscription is activated.',
                'language_id' => $en?->id
            ],
            [
                'slug' => 'smtp_test',
                'name' => 'SMTP Test Email',
                'subject' => 'SMTP Test Connection — {{ $site_name }}',
                'content' => '<p>This is a test email from <strong>{{ $site_name }}</strong> to verify your SMTP settings. Congratulations, it works!</p>',
                'is_system' => true,
                'description' => 'Sent when admin tests the SMTP connection.',
                'language_id' => $en?->id
            ],
            [
                'slug' => 'payment_rejected',
                'name' => 'Payment Rejected Notification',
                'subject' => 'Payment Rejected: {{ $plan_name }}',
                'content' => '<p>Hello {{ $user_name }},</p><p>We regret to inform you that your payment for the <strong>{{ $plan_name }}</strong> plan has been rejected.</p><p>Reason: Verification failed or invalid proof provided.</p><p>Please contact support for further details.</p>',
                'is_system' => true,
                'description' => 'Sent when admin rejects a bank transfer payment.',
                'language_id' => $en?->id
            ],
            [
                'slug' => 'subscription_invoice',
                'name' => 'Subscription Invoice/Receipt',
                'subject' => 'Invoice for Subscription — {{ $plan_name }}',
                'content' => '<h1>Invoice #{{ $transaction_id }}</h1><p>Hello {{ $user_name }},</p><p>Thank you for your payment for the <strong>{{ $plan_name }}</strong> plan.</p><p>Amount: {{ $amount }}</p><p>Status: Completed</p><p>Valid until: {{ $ends_at }}</p>',
                'is_system' => true,
                'description' => 'Sent after successful subscription payment.',
                'language_id' => $en?->id
            ],
            [
                'slug' => 'renewal_reminder',
                'name' => 'Renewal Reminder',
                'subject' => 'Your Subscription is Renewing Soon — {{ $plan_name }}',
                'content' => '<p>Hello {{ $user_name }},</p><p>This is a reminder that your subscription to <strong>{{ $plan_name }}</strong> will expire on <strong>{{ $ends_at }}</strong>.</p><p>Please ensure you have enough balance or active payment method for renewal.</p>',
                'is_system' => true,
                'description' => 'Sent X days before subscription expiry.',
                'language_id' => $en?->id
            ],
            [
                'slug' => 'renewal_reminder_urgent',
                'name' => 'Urgent Renewal Reminder',
                'subject' => 'Urgent: Your Subscription Expires Today — {{ $plan_name }}',
                'content' => '<p>Hello {{ $user_name }},</p><p>Your subscription to <strong>{{ $plan_name }}</strong> expires <strong>TODAY</strong>.</p><p>Please renew your subscription immediately to avoid any service interruption.</p>',
                'is_system' => true,
                'description' => 'Sent on the day of subscription expiry.',
                'language_id' => $en?->id
            ],
            [
                'slug' => 'grace_period_warning',
                'name' => 'Grace Period / Expiry Notification',
                'subject' => 'Subscription Expired: {{ $plan_name }}',
                'content' => '<p>Hello {{ $user_name }},</p><p>Your subscription to <strong>{{ $plan_name }}</strong> has expired.</p><p>We have granted you a grace period of <strong>{{ $grace_days }} days</strong> before your services are suspended. Please renew now to avoid interruption.</p>',
                'is_system' => true,
                'description' => 'Sent when a subscription expires, notifying about grace period.',
                'language_id' => $en?->id
            ],
            
            // Arabic Templates
            [
                'slug' => 'subscription_invoice',
                'name' => 'فاتورة الاشتراك',
                'subject' => 'فاتورة اشتراك — {{ $plan_name }}',
                'content' => '<h1>فاتورة رقم #{{ $transaction_id }}</h1><p>مرحباً {{ $user_name }}،</p><p>شكراً لشرائك باقة <strong>{{ $plan_name }}</strong>.</p><p>المبلغ: {{ $amount }}</p><p>الحالة: مكتملة</p><p>صالحة حتى: {{ $ends_at }}</p>',
                'is_system' => true,
                'description' => 'ترسل بعد نجاح عملية دفع الاشتراك.',
                'language_id' => $ar?->id
            ],
            [
                'slug' => 'renewal_reminder',
                'name' => 'تنبيه تجديد الاشتراك',
                'subject' => 'سيتم تجديد اشتراكك قريباً — {{ $plan_name }}',
                'content' => '<p>مرحباً {{ $user_name }}،</p><p>نود تذكيرك بأن اشتراكك في باقة <strong>{{ $plan_name }}</strong> سينتهي في <strong>{{ $ends_at }}</strong>.</p><p>يرجى التأكد من توفر رصيد كافٍ للتجديد التلقائي.</p>',
                'is_system' => true,
                'description' => 'ترسل قبل عدد معين من الأيام من انتهاء الاشتراك.',
                'language_id' => $ar?->id
            ],
            [
                'slug' => 'renewal_reminder_urgent',
                'name' => 'تنبيه تجديد عاجل',
                'subject' => 'عاجل: اشتراكك ينتهي اليوم — {{ $plan_name }}',
                'content' => '<p>مرحباً {{ $user_name }}،</p><p>اشتراكك في باقة <strong>{{ $plan_name }}</strong> ينتهي <strong>اليوم</strong>.</p><p>يرجى التجديد الآن لتجنب انقطاع الخدمة.</p>',
                'is_system' => true,
                'description' => 'ترسل في يوم انتهاء الاشتراك.',
                'language_id' => $ar?->id
            ],
            [
                'slug' => 'grace_period_warning',
                'name' => 'تنبيه فترة السماح / انتهاء الاشتراك',
                'subject' => 'انتهى الاشتراك: {{ $plan_name }}',
                'content' => '<p>مرحباً {{ $user_name }}،</p><p>لقد انتهى اشتراكك في باقة <strong>{{ $plan_name }}</strong>.</p><p>لقد منحناك فترة سماح لمدة <strong>{{ $grace_days }} أيام</strong> قبل إيقاف الخدمات. يرجى التجديد الآن لتجنب التعطيل.</p>',
                'is_system' => true,
                'description' => 'ترسل عند انتهاء الاشتراك لإبلاغ المستخدم بفترة السماح.',
                'language_id' => $ar?->id
            ]
        ];

        foreach ($templates as $template) {
            EmailTemplate::updateOrCreate(
                ['slug' => $template['slug'], 'language_id' => $template['language_id']],
                $template
            );
        }
    }
}
