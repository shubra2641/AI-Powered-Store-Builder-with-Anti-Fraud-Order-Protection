<?php

namespace App\Http\View\Composers;

use App\Models\User;
use App\Services\Tracking\PixelManager;
use Illuminate\View\View;
use Illuminate\Support\Facades\Cache;

/**
 * Class PixelViewComposer
 *
 * Injects tracking pixels into platform-level layouts.
 *
 * @package App\Http\View\Composers
 */
class PixelViewComposer
{
    /**
     * @var PixelManager
     */
    protected PixelManager $pixelManager;

    /**
     * PixelViewComposer constructor.
     *
     * @param PixelManager $pixelManager
     */
    public function __construct(PixelManager $pixelManager)
    {
        $this->pixelManager = $pixelManager;
    }

    /**
     * Bind data to the view.
     *
     * @param View $view
     * @return void
     */
    public function compose(View $view): void
    {
        // Cache the admin user ID for performance
        $adminId = Cache::remember('ds_admin_id_for_pixels', now()->addDay(), function () {
            $admin = User::whereHas('role', function ($query) {
                $query->where('slug', 'admin');
            })->first();

            return $admin ? $admin->id : null;
        });

        if ($adminId) {
            $view->with('trackingPixels', $this->pixelManager->render($adminId));
        }
    }
}
