<?php

namespace App\Helpers;


class AIPromptHelper
{

    public static function getLandingPageSystemPrompt(string $userPrompt, string $style = 'Premium Modern'): string
    {
        $niche = self::detectNiche($userPrompt);
        
        // Removed array_rand for deterministic testing
        $styleGuide = "Use a {$style} style with sophisticated gradients and deep shadows.";

        return "Role: You are a World-Class UI/UX Designer & Senior Frontend Developer.
        
        ### MANDATORY TASK:
        Generate a complete Landing Page structure for '{$niche}' using ONLY Tailwind CSS utility classes.
        
        ### STRICT SECURITY RULES (HARD REJECTION IF VIOLATED):
        1. **NO <script> TAGS.** Zero JavaScript allowed.
        2. **NO <style> TAGS.** Zero custom CSS allowed.
        3. **NO External Links.** Do not link to external CSS/JS files.
        4. **USE ONLY TAILWIND.** All styling must be done via 'class=\"...\"' attributes.

        ### CREATIVE DIRECTION:
        - Style: {$styleGuide}
        - Theme: Match the '{$niche}' industry aesthetics.
        - Primary Color: '#4F46E5' (Indigo)
        - Secondary Color: '#10B981' (Emerald)
        - Font: 'Cairo' (Arabic/English)

        ### OUTPUT FORMAT (STRICT JSON):
        Return a single valid JSON object with a 'sections' array.
        NO markdown formatting. NO backticks. NO preamble.
        
        JSON Structure:
        {
            \"sections\": [
                {
                    \"id\": \"unique_id\",
                    \"name\": \"Section Name\",
                    \"type\": \"ai_raw\",
                    \"style\": { \"background\": \"#...\", \"padding\": 60, \"color\": \"#...\" },
                    \"html\": \"<div class='w-full py-20 bg-slate-900'>...Content with Tailwind classes...</div>\"
                }
            ]
        }
        
        ### CONTENT REQUIREMENTS:
        - Generate 5-7 High-Quality Sections (Hero, Features, Social Proof, How it Works, FAQ, Footer).
        - If Arabic is detected, use RTL and Arabic text. Otherwise use English.
        - Use modern Tailwind primitives (e.g., 'backdrop-blur', 'bg-gradient-to-r', 'shadow-2xl').";
    }

    private static function detectNiche(string $prompt): string
    {
        $prompt = mb_strtolower($prompt);
        if (preg_match('/مطعم|أكل|طعام|وجبات|برجر|بيتزا|burger|food|restaurant|pizza|cafe|مقهى/', $prompt)) return 'Restaurant & Food';
        if (preg_match('/تسويق|وكالة|سوشيال|marketing|agency|social|ads|إعلانات|consulting/', $prompt)) return 'Digital Marketing Agency';
        if (preg_match('/طبي|عيادة|دكتور|صحة|مستشفى|medical|health|clinic|doctor|dental/', $prompt)) return 'Medical & Healthcare';
        if (preg_match('/برمجة|تطبيق|ساف|tech|software|saas|app|ai|تقنية|ذكاء/', $prompt)) return 'SaaS & Technology';
        if (preg_match('/عقارات|شقة|فيلا|real estate|property|villa|apartment/', $prompt)) return 'Real Estate';
        if (preg_match('/جمال|تجميل|صالون|عطر|beauty|salon|spa|perfume/', $prompt)) return 'Beauty & Wellness';
        
        return 'General Business';
    }

    public static function getLandingPageSchema(): string
    {
        return json_encode([
            "sections" => [
                [
                    "id" => "hero_sec",
                    "name" => "Strategic Hero Section",
                    "type" => "ai_raw",
                    "style" => [
                        "background" => "#0f172a",
                        "padding" => 100,
                        "color" => "#ffffff"
                    ],
                    "html" => "<section class='relative overflow-hidden bg-slate-900 pt-20 pb-32'><div class='container mx-auto px-4'>...</div></section>"
                ]
            ]
        ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    }
}