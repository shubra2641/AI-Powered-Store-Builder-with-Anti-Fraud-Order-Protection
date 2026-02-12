<?php

namespace Database\Seeders;

use App\Models\DS_LandingPageComponent;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DS_LandingPageComponentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DS_LandingPageComponent::truncate();
        $components = [
            [
                'name' => 'Header - Elite Glass',
                'category' => 'header',
                'blade_template' => 'sections.landing.header',
                'thumbnail' => 'assets/section/header1.png',
                'config_schema' => [
                    'content' => [
                        'brand_name' => 'DropSaaS',
                        'logo' => '',
                        'cta_text' => 'Get Started',
                        'cta_url' => '/register',
                        'menu_items' => [
                            ['label' => 'Features', 'url' => '#features'],
                            ['label' => 'Pricing', 'url' => '#pricing'],
                            ['label' => 'Contact', 'url' => '#contact']
                        ]
                    ],
                    'style' => [
                        'padding' => 20,
                        'background' => 'rgba(255, 255, 255, 0.05)',
                        'color' => '#ffffff',
                        'btn_bg' => '#4F46E5',
                        'btn_text' => '#ffffff'
                    ]
                ]
            ],
            [
                'name' => 'Header - White Modern',
                'category' => 'header',
                'blade_template' => 'sections.landing.header_white',
                'thumbnail' => 'assets/section/header.png',
                'config_schema' => [
                    'content' => [
                        'brand_name' => 'Elite',
                        'brand_sub' => 'Market',
                        'logo' => '',
                        'cta_text' => 'Consultation',
                        'cta_url' => '#contact',
                        'menu_items' => [
                            ['label' => 'Services', 'url' => '#services'],
                            ['label' => 'About', 'url' => '#about'],
                            ['label' => 'Works', 'url' => '#works']
                        ]
                    ],
                    'style' => [
                        'padding' => 15,
                        'background' => '#ffffff',
                        'color' => '#1F2937',
                        'btn_bg' => '#10B981',
                        'btn_text' => '#ffffff'
                    ]
                ]
            ],
            [
                'name' => 'Hero - Centered Glass',
                'category' => 'hero',
                'blade_template' => 'sections.landing.hero',
                'thumbnail' => 'assets/section/hero1.png',
                'config_schema' => [
                    'content' => [
                        'tagline' => 'Modern Solutions',
                        'title' => 'The Future of <span class="gradient-text">No-Code Building</span>',
                        'subtitle' => 'Revolutionize your workflow with our interactive drag-and-drop landing page builder.',
                        'primary_btn_text' => 'Get Started',
                        'primary_btn_url' => '/register',
                        'secondary_btn_text' => 'Watch Demo',
                        'secondary_btn_url' => '#',
                        'trusted_by_text' => 'Trusted by 500+ Companies',
                        'trusted_by' => [
                            'https://upload.wikimedia.org/wikipedia/commons/2/2f/Google_2015_logo.svg',
                            'https://upload.wikimedia.org/wikipedia/commons/5/51/IBM_logo.svg',
                            'https://upload.wikimedia.org/wikipedia/commons/9/96/Microsoft_logo_%282012%29.svg'
                        ]
                    ],
                    'style' => [
                        'padding' => 120,
                        'background' => 'linear-gradient(to top left, #003366 0%, #000066 100%)',
                        'color' => '#ffffff',
                        'tagline_bg' => 'rgba(79, 70, 229, 0.1)',
                        'tagline_text' => '#4F46E5',
                        'btn_primary_bg' => '#4F46E5',
                        'btn_primary_text' => '#ffffff',
                        'btn_secondary_bg' => 'rgba(255, 255, 255, 0.1)',
                        'btn_secondary_text' => '#ffffff'
                    ]
                ]
            ],
            [
                'name' => 'Hero - Modern Design',
                'category' => 'hero',
                'blade_template' => 'sections.landing.hero_modern',
                'thumbnail' => 'assets/section/hero.png',
                'config_schema' => [
                    'content' => [
                        'tagline' => 'Your First Digital Partner',
                        'title' => 'We Transform Your Ideas into <span class="gradient-text">Tangible Results</span>',
                        'subtitle' => 'We are a digital marketing agency specializing in brand growth and sales increase through studied strategies.',
                        'primary_btn_text' => 'Start Project',
                        'primary_btn_url' => '#contact',
                        'secondary_btn_text' => 'Our Works',
                        'secondary_btn_url' => '#works',
                        'image_url' => 'https://images.unsplash.com/photo-1551434678-e076c223a692?ixlib=rb-1.2.1&auto=format&fit=crop&w=800&q=80'
                    ],
                    'style' => [
                        'padding' => 100,
                        'background' => '#ffffff',
                        'color' => '#111827',
                        'tagline_bg' => '#EEF2FF',
                        'tagline_text' => '#4F46E5',
                        'btn_primary_bg' => '#4F46E5',
                        'btn_primary_text' => '#ffffff',
                        'btn_secondary_bg' => '#ffffff',
                        'btn_secondary_text' => '#374151'
                    ]
                ]
            ],
            [
                'name' => 'Hero - Split Design',
                'category' => 'hero',
                'blade_template' => 'sections.landing.hero_split',
                'thumbnail' => 'assets/section/hero2.png',
                'config_schema' => [
                    'content' => [
                        'tagline' => 'AI Powered Platform',
                        'title' => 'Scale Your Business with <span class="gradient-text">Premium Features</span>',
                        'subtitle' => 'Our platform provides everything you need to build, launch, and grow your online presence.',
                        'primary_btn_text' => 'Free Trial',
                        'primary_btn_url' => '/register',
                        'secondary_btn_text' => 'Demo',
                        'secondary_btn_url' => '#',
                        'image_url' => 'https://images.unsplash.com/photo-1498050108023-c5249f4df085?auto=format&fit=crop&q=80&w=800'
                    ],
                    'style' => [
                        'padding' => 80,
                        'background' => '#060312',
                        'color' => '#ffffff',
                        'tagline_bg' => 'rgba(255, 255, 255, 0.05)',
                        'tagline_text' => '#7C3AED',
                        'btn_primary_bg' => '#4F46E5',
                        'btn_primary_text' => '#ffffff',
                        'btn_secondary_bg' => 'transparent',
                        'btn_secondary_text' => '#ffffff'
                    ]
                ]
            ],
            [
                'name' => 'Features - Card Grid',
                'category' => 'features',
                'blade_template' => 'sections.landing.features',
                'thumbnail' => 'https://images.unsplash.com/photo-1454165833767-152069e29a36?auto=format&fit=crop&q=80&w=800',
                'config_schema' => [
                    'content' => [
                        'title' => 'Features that Empower You',
                        'subtitle' => 'Discover the tools we provide to help you stay ahead of the competition.',
                        'items' => [
                            ['title' => 'AI Generation', 'description' => 'Generate content automatically with AI.', 'icon' => 'fas fa-brain'],
                            ['title' => 'Fast Performance', 'description' => 'Optimized for speed and SEO.', 'icon' => 'fas fa-bolt'],
                            ['title' => 'Secure Payments', 'description' => 'Global payment gateways integration.', 'icon' => 'fas fa-shield-alt']
                        ]
                    ],
                    'style' => [
                        'padding' => 80,
                        'background' => '#f9fafb',
                        'color' => '#1F2937',
                        'icon_color' => '#4F46E5'
                    ]
                ]
            ],
            [
                'name' => 'Statistics - Modern',
                'category' => 'stats',
                'blade_template' => 'sections.landing.statistics',
                'thumbnail' => 'https://images.unsplash.com/photo-1551288049-bebda4e38f71?auto=format&fit=crop&q=80&w=800',
                'config_schema' => [
                    'content' => [
                        'items' => [
                            ['value' => '15K+', 'label' => 'Happy Customers'],
                            ['value' => '50K+', 'label' => 'Pages Created'],
                            ['value' => '99%', 'label' => 'Success Rate'],
                            ['value' => '24/7', 'label' => 'Expert Support']
                        ]
                    ],
                    'style' => [
                        'padding' => 80,
                        'background' => '#ffffff',
                        'color' => '#1F2937',
                        'value_color' => '#4F46E5'
                    ]
                ]
            ],
            [
                'name' => 'Pricing - Elite Tiers',
                'category' => 'pricing',
                'blade_template' => 'sections.landing.pricing',
                'thumbnail' => 'https://images.unsplash.com/photo-1554224155-16974af9aebd?auto=format&fit=crop&q=80&w=800',
                'config_schema' => [
                    'content' => [
                        'title' => 'Simple, Transparent Pricing',
                        'subtitle' => 'No hidden fees. Choose the plan that works for you.',
                        'plans' => [
                            ['name' => 'Starter', 'price' => '19', 'btn_text' => 'Choose Starter', 'btn_url' => '/register', 'features' => ['3 Landing Pages', 'Basic AI', 'Community Support']],
                            ['name' => 'Professional', 'price' => '49', 'featured' => true, 'btn_text' => 'Go Professional', 'btn_url' => '/register', 'features' => ['Unlimited Pages', 'Full AI Suite', 'Priority Support']],
                            ['name' => 'Enterprise', 'price' => '99', 'btn_text' => 'Contact Sales', 'btn_url' => '/register', 'features' => ['Dedicated Manager', 'API Access', 'SSO Integration']]
                        ]
                    ],
                    'style' => [
                        'padding' => 100,
                        'background' => 'transparent',
                        'color' => '#ffffff',
                        'card_bg' => 'rgba(255, 255, 255, 0.05)',
                        'accent_color' => '#10B981'
                    ]
                ]
            ],
            [
                'name' => 'CTA - Gradient Wave',
                'category' => 'cta',
                'blade_template' => 'sections.landing.cta',
                'thumbnail' => 'https://images.unsplash.com/photo-1557683316-973673baf926?auto=format&fit=crop&q=80&w=800',
                'config_schema' => [
                    'content' => [
                        'title' => 'Ready to Transform Your Business?',
                        'subtitle' => 'Join thousands of satisfied users who are building their future with our platform.',
                        'btn_text' => 'Get Started Now',
                        'btn_url' => '/register'
                    ],
                    'style' => [
                        'padding' => 80,
                        'background' => 'linear-gradient(135deg, #4F46E5 0%, #10B981 100%)',
                        'color' => '#ffffff',
                        'btn_bg' => '#ffffff',
                        'btn_text' => '#4F46E5'
                    ]
                ]
            ],
            [
                'name' => 'Footer - Multi-column',
                'category' => 'footer',
                'blade_template' => 'sections.landing.footer',
                'thumbnail' => 'assets/section/footer.png',
                'config_schema' => [
                    'content' => [
                        'brand_name' => 'DropSaaS',
                        'description' => 'Empowering creators with AI-powered landing page tools to scale their business faster than ever.',
                        'socials' => [
                            ['icon' => 'fab fa-twitter', 'url' => '#'],
                            ['icon' => 'fab fa-facebook', 'url' => '#'],
                            ['icon' => 'fab fa-linkedin', 'url' => '#']
                        ],
                        'link_groups' => [
                            ['title' => 'Product', 'links' => [['label' => 'Features', 'url' => '#'], ['label' => 'Pricing', 'url' => '#']]],
                            ['title' => 'Support', 'links' => [['label' => 'Docs', 'url' => '#'], ['label' => 'Contact', 'url' => '#']]]
                        ]
                    ],
                    'style' => [
                        'padding' => 80,
                        'background' => '#060312',
                        'color' => '#9CA3AF',
                        'title_color' => '#ffffff'
                    ]
                ]
            ],
            [
                'name' => 'AI Raw - Custom HTML',
                'category' => 'custom',
                'blade_template' => 'sections.landing.ai_raw',
                'thumbnail' => 'https://images.unsplash.com/photo-1451187534963-566827652c8f?auto=format&fit=crop&q=80&w=800',
                'config_schema' => [
                    'content' => [
                        'html' => '<div class="text-center py-20 bg-gray-900 text-white rounded-3xl">
    <h2 class="text-4xl font-black mb-4">Custom AI Content</h2>
    <p class="opacity-70">This is a raw HTML container for AI generated masterpieces or custom code.</p>
</div>'
                    ],
                    'style' => [
                        'padding' => 40,
                        'background' => 'transparent'
                    ]
                ]
            ]
        ];

        foreach ($components as $comp) {
            DS_LandingPageComponent::updateOrCreate(
                ['blade_template' => $comp['blade_template']],
                $comp
            );
        }
    }
}
