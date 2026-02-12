<?php

namespace App\Services\Tracking;

/**
 * Class LinkedinInsightService
 *
 * Handles the rendering of LinkedIn Insight Tag tracking code.
 *
 * @package App\Services\Tracking
 */
class LinkedinInsightService extends AbstractPixelService
{
    /**
     * @return string
     */
    protected function getServiceSlug(): string
    {
        return 'linkedin_insight';
    }

    /**
     * Render the LinkedIn Insight code.
     *
     * @return string
     */
    public function render(): string
    {
        if (!$this->isActive()) {
            return '';
        }

        $partnerId = $this->settings['partner_id'] ?? null;

        if (!$partnerId) {
            return '';
        }

        $safeId = e($partnerId);

        return "
            <!-- LinkedIn Insight Tag -->
            <script type='text/javascript'>
            _linkedin_partner_id = '{$safeId}';
            window._linkedin_data_partner_ids = window._linkedin_data_partner_ids || [];
            window._linkedin_data_partner_ids.push(_linkedin_partner_id);
            </script>
            <script type='text/javascript'>
            (function(l) {
            if (!l){window.lintrk = function(a,b){window.lintrk.q.push([a,b])};
            window.lintrk.q=[]}
            var s = document.getElementsByTagName('script')[0];
            var b = document.createElement('script');
            b.type = 'text/javascript';b.async = true;
            b.src = 'https://snap.licdn.com/li.lms-analytics/insight.min.js';
            s.parentNode.insertBefore(b, s);})(window.lintrk);
            </script>
            <noscript>
            <img height='1' width='1' style='display:none;' alt='' src='https://px.ads.linkedin.com/collect/?pid={$safeId}&fmt=gif' />
            </noscript>
            <!-- End LinkedIn Insight Tag -->
        ";
    }
}
