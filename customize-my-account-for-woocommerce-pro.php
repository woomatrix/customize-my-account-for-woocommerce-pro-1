<?php
/*
    Plugin Name: Customize My Account for WooCommerce Pro
    Plugin URI: https://www.sysbasics.com/product/woocommerce-customize-my-account-pro/
    Description: Customize My account page. Add/Edit/Remove Endpoints.
    Version: 3.3.0
    Author: SysBasics
    Author URI: https://woomatrix.com
    Domain Path: /languages
    Requires at least: 3.3
    Tested up to: 6.7.0
    WC requires at least: 3.0.0
    WC tested up to: 9.4.1
*/

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly


if( !defined( 'wcmamtx_PLUGIN_URL' ) )
    define( 'wcmamtx_PLUGIN_URL', plugin_dir_url( __FILE__ ) );


if( !defined( 'wcmamtx_PLUGIN_name' ) )
    define( 'wcmamtx_PLUGIN_name', esc_html__( 'Customize My Account Pro' ,'customize-my-account-for-woocommerce-pro') );



if( !defined( 'wcmamtx_doc_url' ) )
    define( 'wcmamtx_doc_url', 'https://www.sysbasics.com/knowledge-base/category/woocommerce-customize-my-account-pro/' );

if( !defined( 'pro_url' ) )
    define( 'pro_url', 'https://sysbasics.com/go/customize/' );

$mt_type = 'specific';

add_action( 'before_woocommerce_init', function() {
    if ( class_exists( \Automattic\WooCommerce\Utilities\FeaturesUtil::class ) ) {
        \Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility( 'custom_order_tables', __FILE__, true );
    }
} );

function wcmamtx_translate_test_load_plugin_textdomain() {
  load_plugin_textdomain( 'customize-my-account-for-woocommerce-pro', false, basename( dirname(__FILE__) ).'/languages' );

}

add_action( 'init', 'wcmamtx_translate_test_load_plugin_textdomain' );


/**
 * Check if elementor or elementor pro is active
 */


    include_once( ABSPATH . 'wp-admin/includes/plugin.php' );

    if( !defined( 'wcmamtx_elementor_mode' ) ) {

        if ( is_plugin_active( 'elementor/elementor.php' ) || is_plugin_active( 'elementor-pro/elementor-pro.php' )) {
            define( 'wcmamtx_elementor_mode', 'on' );
        } else {
            define( 'wcmamtx_elementor_mode', 'off' );
        }

    }

    if( !defined( 'wcmamtx_wpmlsticky_mode' ) ) {

        if ( is_plugin_active( 'wpml-sticky-links/plugin.php' )) {
            define( 'wcmamtx_wpmlsticky_mode', 'on' );
        } else {
            define( 'wcmamtx_wpmlsticky_mode', 'off' );
        }

    }


    if( !defined( 'sysbasics_checkout_mode' ) ) {

        if ( is_plugin_active( 'phppoet-checkout-fields/phppoet-checkout-fields.php' )) {
            define( 'sysbasics_checkout_mode', 'on' );
        } else {
            define( 'sysbasics_checkout_mode', 'off' );
        }

    }
    
   /**
    * check weather woocommerce is active or not
    */

   if (is_plugin_active( 'customize-my-account-for-woocommerce/customize-my-account-for-woocommerce.php' ) ) {


    function wcmamtx__installation_notice_free() {
       echo '<div class="updated" style="padding:15px; position:relative;">'.esc_html__( 'You must disable free version of customize my account for woocommerce to use pro version' ,'customize-my-account-for-woocommerce-pro').'</div>';
   }

   add_action('admin_notices', 'wcmamtx__installation_notice_free');

   return;

} else {

    /**
     * Display Notice if woocommerce is not installed
     */
     //include the classes


       /**
    * check weather woocommerce is active or not
    */

   if (is_plugin_active( 'woocommerce/woocommerce.php' ) ) {


        include dirname( __FILE__ ) . '/include/admin/admin_settings.php';
        include dirname( __FILE__ ) . '/include/frontend/frontend_functions.php';
        include dirname( __FILE__ ) . '/include/wcmamtx_extra_functions.php';
        include dirname( __FILE__ ) . '/include/sysbasics-avatar-upload.php';
        include dirname( __FILE__ ) . '/phppoet-checkout-fields/init.php';

    } else {

        /**
         * Display Notice if woocommerce is not installed
         */

        function wcmamtx__installation_notice_woocommerce() {
            echo '<div class="updated" style="padding:15px; position:relative;"><a href="http://wordpress.org/plugins/woocommerce/">'.esc_html__('customize-my-account-for-woocommerce-pro','customize-my-account-for-woocommerce-pro').'</a> '.esc_html__('must be activated before activating Customize My Account For WooCommerce Pro ','customize-my-account-for-woocommerce-pro').' </div>';
        }

        add_action('admin_notices', 'wcmamtx__installation_notice_woocommerce');

        return;

    }
    
}

register_activation_hook(__FILE__, 'wcmamtx_plugin_activation_hook_pro');

add_action('admin_init', 'wcmamtx_admin_plugin_redirect_pro');

/**
 * Get account menu item classes.
 *
 * @since 1.0.0
 */

if (!function_exists('wcmamtx_plugin_activation_hook_pro')) {

    function wcmamtx_plugin_activation_hook_pro() {

        // Don't forget to exit() because wp_redirect doesn't exit automatically
        add_option('wcmamtx_admin_plugin_redirect_pro', true);

    }

}


/**
 * Get account menu item classes.
 *
 * @since 1.0.0
 */

if (!function_exists('wcmamtx_admin_plugin_redirect_pro')) {

    function wcmamtx_admin_plugin_redirect_pro() {

        if (get_option('wcmamtx_admin_plugin_redirect_pro', false)) {
            delete_option('wcmamtx_admin_plugin_redirect_pro');
            wp_redirect("admin.php?page=wcmamtx_advanced_settings");
         //wp_redirect() does not exit automatically and should almost always be followed by exit.
            exit;
        }

    }

}



if (!function_exists('wcmamtx_plugin_add_settings_link_pro')) {

    function wcmamtx_plugin_add_settings_link_pro( $links ) {

        $mt_type = wcmamtx_get_version_type();

        $settings_link1 = '<a href="' . admin_url( '/admin.php?page=wcmamtx_advanced_settings' ) . '">' . esc_html__( 'Settings','customize-my-account-for-woocommerce-pro' ) . '</a>';

        array_push( $links, $settings_link1 );


      
        

        
        return $links;
    }
}

$plugin = plugin_basename( __FILE__ );

add_filter( "plugin_action_links_$plugin", 'wcmamtx_plugin_add_settings_link_pro' );






if (wcmamtx_elementor_mode !== null) {
    if  (wcmamtx_elementor_mode == "on") {
     include dirname( __FILE__ ) . '/elementor-addon/elementor-addon.php';
 }
}



if (!function_exists('wcmamtx_placeholder_img_src')) {
    function wcmamtx_placeholder_img_src() {
        return ''.wcmamtx_PLUGIN_URL.'assets/images/placeholder.png';
    }

}

require 'lib/lib.php';
use YahnisElsts\PluginUpdateChecker\v5\PucFactory;

$myUpdateChecker = PucFactory::buildUpdateChecker(
    'https://www.sysbasics.com/updates2/?action=get_metadata&slug=customize-my-account-for-woocommerce-pro',
    __FILE__, //Full path to the main plugin file or functions.php.
    'customize-my-account-for-woocommerce-pro',
    48
);




$myUpdateChecker->addFilter('pre_inject_update', function($metadata) { 

    $wcmamtx_install_e = get_option('wcmamtx_install_e');

    

    if (isset($wcmamtx_install_e) && ($wcmamtx_install_e == "64")) {
        
        $wcmamtx_license_settings    = (array) get_option('wcmamtx_license_settings');

        $license_key = '';

        if (isset($wcmamtx_license_settings['license_key']) ) { 
            $license_key=$wcmamtx_license_settings['license_key']; 
        }

        $input = $_SERVER['SERVER_NAME'];


        $input = trim($input, '/');


        if (!preg_match('#^http(s)?://#', $input)) {
            $input = 'http://' . $input;
        }

        $urlParts = parse_url($input);


        $domain_name = preg_replace('/^www\./', '', $urlParts['host']);


        $siteurl = wcmamtx_get_siteurl();


        
        $metadata->download_url = 'https://www.sysbasics.com/updates2/?action=download&slug=customize-my-account-for-woocommerce-pro&domain='.$domain_name.'&code='.$license_key.'&siteurl='.$siteurl.'';

        

        return $metadata;
    } else {
        //$metadata->download_url = '';
        return $metadata; 
    }

     
    return $metadata; 

    }
);

/**
 * Get woocommerce version 
 */

if (!function_exists('wcmamtx_get_woo_version_number')) {

    function wcmamtx_get_woo_version_number() {
       
       if ( ! function_exists( 'get_plugins' ) )
         require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
    
       
       $plugin_folder = get_plugins( '/' . 'customize-my-account-for-woocommerce-pro' );
       $plugin_file = 'woocommerce.php';
    
    
       if ( isset( $plugin_folder[$plugin_file]['Version'] ) ) {
          return $plugin_folder[$plugin_file]['Version'];

       } else {
    
        return NULL;
       }
    }
}


/**
 * Get woocommerce version 
 */

if (!function_exists('wcmamtx_get_woo_version_number_this')) {

    function wcmamtx_get_woo_version_number_this() {
       
       if ( ! function_exists( 'get_plugins' ) )
         require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
    
       
       $plugin_folder = get_plugins( '/' . 'customize-my-account-for-woocommerce-pro' );
       $plugin_file = 'customize-my-account-for-woocommerce-pro.php';
    
    
       if ( isset( $plugin_folder[$plugin_file]['Version'] ) ) {
          return $plugin_folder[$plugin_file]['Version'];

       } else {
    
        return NULL;
       }
    }
}




if (!function_exists('wcmamtx_plugin_row_meta')) {
    function wcmamtx_plugin_row_meta( $links, $file ) {    
        if ( plugin_basename( __FILE__ ) == $file ) {
            $row_meta = array(
                'docs'    => '<a href="' . esc_url( wcmamtx_doc_url ) . '" target="_blank" aria-label="' . esc_attr__( 'Docs', 'customize-my-account-for-woocommerce-pro' ) . '" style="color:green;">' . esc_html__( 'Docs', 'customize-my-account-for-woocommerce-pro' ) . '</a>',
                'support'    => '<a href="' . esc_url( 'https://sysbasics.com/support/' ) . '" target="_blank" aria-label="' . esc_attr__( 'Support', 'customize-my-account-for-woocommerce-pro' ) . '" style="color:green;">' . esc_html__( 'Support', 'customize-my-account-for-woocommerce-pro' ) . '</a>'
            );
            return array_merge( $links, $row_meta );
        }
        return (array) $links;
    }
}

add_filter( 'plugin_row_meta', 'wcmamtx_plugin_row_meta', 10, 2 );


if( !defined( 'wcmamtx_version_type' ) )
    define( 'wcmamtx_version_type', $mt_type );


if (!function_exists('wcmamtx_plugin_path')) {

    function wcmamtx_plugin_path() {
  
       return untrailingslashit( plugin_dir_path( __FILE__ ) );
    }

}


if (!function_exists('wcmamtx_get_version_type')) {

    function wcmamtx_get_version_type() {
        $plugin_path = plugin_dir_path( __FILE__ );

        if ((strpos($plugin_path, 'pro') !== false) && ( wcmamtx_version_type == "specific")) { 
            $dt_type = 'specific';
        } else {
            $dt_type = 'all';
        }
    
        return $dt_type;
    }
}

$mt_type = wcmamtx_get_version_type();


?>