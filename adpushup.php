<?php

/*
  Plugin Name: AdPushup
  Plugin URI: http://adpushup.com
  Description: Maximize your AdSense Ad Revenue!
  Version: 1.0
  Author: AdPushup
  Author URI: http://www.adpushup.com
  License: GPL2
  Updated: 02-04-2015
 */

// Prevent direct access to file
defined('ABSPATH') or die("Cheatin' uh?");

class AdPushup {

    public $plugin_dir;
    public $version = '1.0';
    public $update_url;
    public $platform;

    public function __construct() {

        $this->plugin_dir = plugin_dir_path(__FILE__);
        require_once 'misc.php';
        require_once 'injector.php';
        require_once 'settings.php';
    }

}

function AdPushup() {
    global $AdPushup;
    $AdPushup = new AdPushup();
}

add_action('plugins_loaded', 'AdPushup');
