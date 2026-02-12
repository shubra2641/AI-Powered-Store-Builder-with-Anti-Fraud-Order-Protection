<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Services\LanguageService;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    public function __construct(
        protected LanguageService $languageService
    ) {}

    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): Response  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $locale = config('app.locale');

        if (Session::has('locale')) {
            $locale = Session::get('locale');
        }

        if (auth()->check() && auth()->user()->language_id) {
            $locale = auth()->user()->language->code ?? $locale;
        }

        $languages = $this->languageService->getAllLanguages();
        $language = $languages->firstWhere('code', $locale)
                  ?? $languages->firstWhere('is_default', true);

        if ($language) {
            App::setLocale($language->code);

            view()->share('currentLang', $language);
        }

        return $next($request);
    }
}
