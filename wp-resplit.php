<?php
    /**
     * Plugin Name:       WP ReSplit
     * Plugin URI:        https://profiles.wordpress.org/iqbal1486/
     * Description:       WordPress A/B Testing Setup
     * Version:           1.0.0
     * Requires at least: 5.2
     * Requires PHP:      7.2
     * Author:            Geekerhub
     * Author URI:        https://profiles.wordpress.org/iqbal1486/
     * License:           GPL v2 or later
     * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
     * Text Domain:       wp-resplit
     * Domain Path:       /languages
     */

    /*
        When visitors visit https://socialself471.e.wpstage.net/i2i-masterclass-sl/ they have a 50% percent chance of being redirected to https://socialself471.e.wpstage.net/i2i-masterclass-sl-b/

        When visitors visit https://socialself471.e.wpstage.net/i2i-masterclass-video/ they have a 50% percent chance of being redirected to https://socialself471.e.wpstage.net/i2i-masterclass-video-b/

        When visitors visit https://socialself471.e.wpstage.net/i2i-masterclass-video-replay/ they have a 50% percent chance of being redirected to https://socialself471.e.wpstage.net/i2i-masterclass-video-replay-b/

        If they are served a b-variant on any of these pages, they always see the b-variant for the two other pages. If they are served the base variant (without the -b), they always see the base variant for the two other pages.
    */
    /*
        A
        https://circle.lifeandcanvas.com/affiliate-area/
        https://circle.lifeandcanvas.com/i2i-masterclass/
        https://circle.lifeandcanvas.com/friendship-academy-i2i/
        
        B
        https://circle.lifeandcanvas.com/friendship-academy-stoc/
        https://circle.lifeandcanvas.com/i2i-masterclass-b/
        https://circle.lifeandcanvas.com/friendship-academy-ttp/
    */
    if ( ! defined( 'ABSPATH' ) ) {
        exit( 'restricted access' );
    }

    define( 'WPR_VERSION', '1.0.0' );

    if (! defined('WPR_ADMIN_URL') ) {
        define('WPR_ADMIN_URL', get_admin_url());
    }

    if (! defined('WPR_PLUGIN_FILE') ) {
        define('WPR_PLUGIN_FILE', __FILE__);
    }

    if (! defined('WPR_PLUGIN_LOG') ) {
        define('WPR_PLUGIN_LOG', false);
    }

    if (! defined('WPR_PLUGIN_PATH') ) {
        define('WPR_PLUGIN_PATH', plugin_dir_path(WPR_PLUGIN_FILE));
    }

    if (! defined('WPR_PLUGIN_URL') ) {
        define('WPR_PLUGIN_URL', plugin_dir_url(WPR_PLUGIN_FILE));
    }

    function geek_resplit_plugin_loaded_callback() {
        require_once WPR_PLUGIN_PATH . 'admin/settings.php';
        require_once WPR_PLUGIN_PATH . 'public/resplit.php';
    }
    add_action('plugins_loaded', 'geek_resplit_plugin_loaded_callback');


    function geek_generate_log( $data = "" ){
        if(! WPR_PLUGIN_LOG ){
            return false;
        }
        
        $file = WPR_PLUGIN_PATH.'debug.log';
        $fileContents = file_get_contents($file);
        $time = "Time : ".date("F j, Y, g:i a")."\n";

        if( is_array($data)){
            $data = print_r($data, true);
        }

        $data = $time.$data."\n***************************************\n";
        file_put_contents($file, $data . $fileContents);
    }

    function geek_client_ip_address() {
        $ipaddress = '';
        if ($_SERVER['HTTP_X_REAL_IP']) {
            $ipaddress = $_SERVER['HTTP_X_REAL_IP'];
        } else if ($_SERVER['HTTP_CLIENT_IP']) {
            $ipaddress = $_SERVER['HTTP_CLIENT_IP'];
        } else if($_SERVER['HTTP_X_FORWARDED_FOR']) {
            $ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else if($_SERVER['HTTP_X_FORWARDED']) {
            $ipaddress = $_SERVER['HTTP_X_FORWARDED'];
        } else if(getenv('HTTP_FORWARDED_FOR')) {
            $ipaddress = $_SERVER['HTTP_FORWARDED_FOR'];
        } else if($_SERVER['HTTP_FORWARDED']) {
           $ipaddress = $_SERVER['HTTP_FORWARDED'];
        } else if($_SERVER['REMOTE_ADDR']){ 
            $ipaddress = $_SERVER['REMOTE_ADDR'];
        } else {
            $ipaddress = 'UNKNOWN';
        }
        return $ipaddress;
    }