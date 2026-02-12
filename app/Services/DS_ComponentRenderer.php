<?php

namespace App\Services;

use Illuminate\Support\Str;

/**
 * Class DS_ComponentRenderer
 * 
 * Orchestrates the preparation and transformation of component data 
 * for dynamic section rendering within the landing page builder.
 */
class DS_ComponentRenderer
{
    /**
     * Prepare section data for rendering.
     *
     * @param array $section
     * @param bool $isRtl
     * @return array
     */
    public function prepare(array $section, bool $isRtl): array
    {
        $template = $section['template'] ?? null;
        $content = $section['content'] ?? [];
        $style = $section['style'] ?? [];
        $attributes = $section['attributes'] ?? [];

        $id = $attributes['id'] ?? (Str::slug($template ?? 'section') . '-' . Str::random(6));
        
        $filteredContent = array_filter($content, function ($value) {
            return !is_null($value) && $value !== '';
        });
        $imageKeys = ['image_url', 'logo', 'background_image', 'icon_url', 'avatar'];
        foreach ($filteredContent as $key => &$value) {
            if (in_array($key, $imageKeys) && is_string($value) && !empty($value)) {
                if (!filter_var($value, FILTER_VALIDATE_URL) && !str_starts_with($value, 'data:')) {
                    $value = asset($value);
                }
            }
        }

        $css = $this->generateCss($id, $template, $style);

        return [
            'id' => $id,
            'content' => $filteredContent,
            'css' => $css,
            'template' => $template,
            'attributes' => $attributes,
            'isRtl' => $isRtl
        ];
    }

    /**
     * Sanitizes a CSS value for safe output.
     * Ensures it's either a valid color, numeric, or a safe string.
     *
     * @param mixed $value
     * @param string $default
     * @return string
     */
    protected function safeStyle(mixed $value, string $default): string
    {
        if (empty($value) && $value !== 0 && $value !== '0') {
            return $default;
        }

        $value = (string) $value;

        // Valid Hex: #abc or #abcdef
        if (preg_match('/^#(?:[0-9a-fA-F]{3}){1,2}$/', $value)) {
            return $value;
        }

        // Valid RGBA/RGB: rgb(0,0,0) or rgba(0,0,0,0)
        if (preg_match('/^rgba?\([0-9\s,.]+\)$/', $value)) {
            return $value;
        }

        if (is_numeric($value)) {
            return $value;
        }

        if (Str::startsWith($value, 'linear-gradient')) {
            if (preg_match('/[;{}]/', $value)) {
                return $default;
            }
            return $value;
        }

        $keywords = ['transparent', 'inherit', 'initial', 'none', 'auto', 'center', 'left', 'right'];
        if (in_array(strtolower($value), $keywords)) {
            return $value;
        }

        return $default;
    }

    /**
     * Generate component-specific CSS based on the template and style data.
     *
     * @param string $id
     * @param string|null $template
     * @param array $style
     * @return string
     */
    protected function generateCss(string $id, ?string $template, array $style): string
    {
        $css = "";
        
        $paddingTop = (int) $this->safeStyle($style['padding'] ?? 80, '80');
        if ($template === 'sections.landing.hero_modern') {
            $paddingTop += 40;
        }
        $paddingBottom = (int) $this->safeStyle($style['padding'] ?? 80, '80');
        
        $bg = $this->safeStyle($style['background'] ?? $style['bg_color'] ?? 'transparent', 'transparent');
        $textColor = $this->safeStyle($style['color'] ?? $style['text_color'] ?? 'inherit', 'inherit');

        $css .= "#{$id} { background: {$bg}; color: {$textColor}; padding-top: {$paddingTop}px; padding-bottom: {$paddingBottom}px; }\n";

        if ($template === 'sections.landing.hero_modern' || $template === 'sections.landing.hero' || $template === 'sections.landing.hero_split') {
            $tagBg = $this->safeStyle($style['tagline_bg'] ?? '#EEF2FF', '#EEF2FF');
            $tagText = $this->safeStyle($style['tagline_text'] ?? '#4F46E5', '#4F46E5');
            $btnBg = $this->safeStyle($style['btn_primary_bg'] ?? '#4F46E5', '#4F46E5');
            $btnText = $this->safeStyle($style['btn_primary_text'] ?? '#ffffff', '#ffffff');
            $btnSBg = $this->safeStyle($style['btn_secondary_bg'] ?? '#ffffff', '#ffffff');
            $btnSText = $this->safeStyle($style['btn_secondary_text'] ?? '#374151', '#374151');

            $css .= "#{$id} .tagline { background: {$tagBg}; color: {$tagText}; }\n";
            $css .= "#{$id} .btn-primary { background: {$btnBg}; color: {$btnText}; }\n";
            $css .= "#{$id} .btn-secondary { background: {$btnSBg}; color: {$btnSText}; }\n";
        }

        if (Str::contains($template, 'header')) {
            $btnBg = $this->safeStyle($style['btn_bg'] ?? '#4F46E5', '#4F46E5');
            $btnText = $this->safeStyle($style['btn_text'] ?? '#ffffff', '#ffffff');
            $css .= "#{$id} .btn-cta { background: {$btnBg}; color: {$btnText}; }\n";
            $css .= "#{$id} .nav-link, #{$id} .brand-text { color: {$textColor}; }\n";
        }

        if ($template === 'sections.landing.cta') {
            $btnBg = $this->safeStyle($style['btn_bg'] ?? '#ffffff', '#ffffff');
            $btnText = $this->safeStyle($style['btn_text'] ?? '#4F46E5', '#4F46E5');
            $css .= "#{$id} .btn-action { background: {$btnBg}; color: {$btnText}; }\n";
        }

        if (Str::contains($template, 'features')) {
            $iconColor = $this->safeStyle($style['icon_color'] ?? '#4F46E5', '#4F46E5');
            $css .= "#{$id} .feature-icon { color: {$iconColor}; }\n";
        }

        if (Str::contains($template, 'statistics')) {
            $valueColor = $this->safeStyle($style['value_color'] ?? '#4F46E5', '#4F46E5');
            $css .= "#{$id} .stat-value { color: {$valueColor}; }\n";
        }

        if (Str::contains($template, 'pricing')) {
            $cardBg = $this->safeStyle($style['card_bg'] ?? '#ffffff', '#ffffff');
            $accent = $this->safeStyle($style['accent_color'] ?? '#4F46E5', '#4F46E5');
            $css .= "#{$id} .pricing-card { background: {$cardBg}; }\n";
            $css .= "#{$id} .accent-text { color: {$accent}; }\n";
            $css .= "#{$id} .btn-pricing { background: {$accent}; color: #ffffff; }\n";
        }

        if (Str::contains($template, 'footer')) {
            $titleColor = $this->safeStyle($style['title_color'] ?? '#ffffff', '#ffffff');
            $css .= "#{$id} .footer-title { color: {$titleColor}; }\n";
        }

        return "<style>\n{$css}</style>";
    }
}
