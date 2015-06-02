<?php

/*
 *  Injects content into page at different places
 */

// Prevent direct access to file
defined('ABSPATH') or die("Cheatin' uh?");

class AdPushup_Injector {

    function __construct() {
        add_action('wp_head', array($this, 'action_wp_head'));
        add_filter('the_content', array($this, 'filter_the_content'));
    }

    public function filter_the_content($content) {

        $pre = '<div id="_ap_wp_content_start" style="display:none"></div>';
        $post = '<div id="_ap_wp_content_end" style="display:none"></div>';
        return $pre . $content . $post;
    }

    /**
     * Runs at the end of <head> tag
     */
    public function action_wp_head() {
        global $wp;
        global $AdPushup;

        $adpushup_site_id = get_option('adpushup_site_id', '');

        $URI = home_url(add_query_arg(array(), $wp->request));
        $site_domain = cleanUrl(get_site_url());
        $plugin_version = $AdPushup->version;
        $page_group = ucwords(getPageType());
        $referer = '';

        if (!empty($_SERVER['HTTP_REFERER'])) {
            $ref_scheme = parse_url($_SERVER['HTTP_REFERER']);
            $site_scheme = parse_url(get_site_url());
            if ($ref_scheme['host'] != $site_scheme['host']) {
                $referer = $ref_scheme['host'];
            }
        }

        if (version_compare(PHP_VERSION, '5.4.0') >= 0) {
            $no_script_code = json_encode(array("success" => true, 'siteUrl' => cleanUrl(get_site_url()), "urls" => getUrlsForExperiment()), JSON_UNESCAPED_SLASHES);
        } else {
            $no_script_code = str_replace("\\/", "/", json_encode(array("success" => true, 'siteUrl' => cleanUrl(get_site_url()), "urls" => getUrlsForExperiment())));
        }


//                      AdPushup noscript code starts (Required for call by server)
        echo '
<noscript>
    _ap_ufes' . $no_script_code . '_ap_ufee
</noscript>
';
//                      AdPushup noscript code ends
//                      AdPushup script code starts
        echo '
<script data-cfasync="false" type="text/javascript">
(function () {
    var c = (window.adpushup = window.adpushup || {}).config = (window.adpushup.config || {});
    c.siteDomain= "' . $site_domain . '";
    c.pluginVer= ' . $plugin_version . ';
    c.cms = "wordpress";
    c.pageGroup = "' . $page_group . '";
    c.pageUrl = "' . esc_js($URI) . '";
    c.ref = "' . esc_js($referer) . '";
    c.siteId = "' . esc_js($adpushup_site_id) . '";        
    
    var s = document.createElement("script");
    s.type = "text/javascript";
    s.async = true;
    s.src = "//optimize.adpushup.com/' . esc_js($adpushup_site_id ? $adpushup_site_id . '/' : '') . 'ap.js";
    (document.getElementsByTagName("head")[0]||document.getElementsByTagName("body")[0]).appendChild(s);
})();
</script>
';
//                      AdPushup script code ends
    }

}

new AdPushup_Injector();
