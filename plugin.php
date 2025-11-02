<?php

/**
 * Plugin Name: Parallax Section - Block
 * Description: Makes background element scrolls slower than foreground content.
 * Version: 1.0.9
 * Author: bPlugins
 * Author URI: https://bplugins.com
 * License: GPLv3
 * License URI: https://www.gnu.org/licenses/gpl-3.0.txt
 * Text Domain: parallax-section
 */
// ABS PATH
if ( !defined( 'ABSPATH' ) ) {
    exit;
}
if ( function_exists( 'ps_fs' ) ) {
    register_activation_hook( __FILE__, function () {
        // 'swiper-slider/swiper-slider.php' ---> ai line er prothom ta slug r porer ta php file er name
        if ( is_plugin_active( 'parallax-section/plugin.php' ) ) {
            deactivate_plugins( 'parallax-section/plugin.php' );
        }
        if ( is_plugin_active( 'parallax-section-pro/plugin.php' ) ) {
            deactivate_plugins( 'parallax-section-pro/plugin.php' );
        }
    } );
} else {
    /**
     * DO NOT REMOVE THIS IF, IT IS ESSENTIAL FOR THE
     * `function_exists` CALL ABOVE TO PROPERLY WORK.
     */
    define( 'PSB_VERSION', ( isset( $_SERVER['HTTP_HOST'] ) && 'localhost' === $_SERVER['HTTP_HOST'] ? time() : '1.0.9' ) );
    define( 'PSB_DIR_URL', plugin_dir_url( __FILE__ ) );
    define( 'PSB_DIR_PATH', plugin_dir_path( __FILE__ ) );
    define( 'PARALLAX_HAS_PRO', file_exists( dirname( __FILE__ ) . '/freemius/start.php' ) );
    if ( !function_exists( 'ps_fs' ) ) {
        // Create a helper function for easy SDK access.
        function ps_fs() {
            global $ps_fs;
            if ( !isset( $ps_fs ) ) {
                if ( PARALLAX_HAS_PRO ) {
                    require_once dirname( __FILE__ ) . '/freemius/start.php';
                } else {
                    require_once dirname( __FILE__ ) . '/freemius-lite/start.php';
                }
                $apbConfig = array(
                    'id'                  => '19833',
                    'slug'                => 'parallax-section',
                    'type'                => 'plugin',
                    'public_key'          => 'pk_b230abfa498765ac9fd6a75cdfde2',
                    'is_premium'          => true,
                    'premium_suffix'      => 'Pro',
                    'has_premium_version' => true,
                    'has_addons'          => false,
                    'has_paid_plans'      => true,
                    'trial'               => array(
                        'days'               => 7,
                        'is_require_payment' => false,
                    ),
                    'menu'                => array(
                        'slug'       => 'parallax-section-dashboard',
                        'first-path' => 'tools.php?page=parallax-section-dashboard#/welcome',
                        'support'    => false,
                        'parent'     => array(
                            'slug' => 'tools.php',
                        ),
                    ),
                );
                $ps_fs = ( PARALLAX_HAS_PRO ? fs_dynamic_init( $apbConfig ) : fs_lite_dynamic_init( $apbConfig ) );
            }
            return $ps_fs;
        }

        // Init Freemius.
        ps_fs();
        // Signal that SDK was initiated.
        do_action( 'ps_fs_loaded' );
    }
    function psIsPremium() {
        return ( PARALLAX_HAS_PRO ? ps_fs()->can_use_premium_code() : false );
    }

    // ... Your plugin's main file logic ...
    require_once PSB_DIR_PATH . 'includes/GetCSS.php';
    if ( !class_exists( 'PSBPlugin' ) ) {
        class PSBPlugin {
            function __construct() {
                add_action( 'init', [$this, 'onInit'] );
                add_action( 'enqueue_block_editor_assets', [$this, "enqueueBlockEditorAssets"] );
                // freemius codes
                // add_action('wp_ajax_ssbPremiumChecker', [$this, 'ssbPremiumChecker']);
                // add_action('wp_ajax_nopriv_ssbPremiumChecker', [$this, 'ssbPremiumChecker']);
                // add_action('admin_init', [$this, 'registerSettings']);
                // add_action('rest_api_init', [$this, 'registerSettings']);
            }

            function enqueueBlockEditorAssets() {
                wp_add_inline_script( 'psb-parallax-editor-script', 'const psbpipecheck = ' . wp_json_encode( psIsPremium() ) . ';', 'before' );
            }

            // function ssbPremiumChecker(){
            // 	$nonce = sanitize_text_field($_POST['_wpnonce'] ?? null);
            // 	if (!wp_verify_nonce($nonce, 'wp_ajax')) {
            // 		wp_send_json_error('Invalid Request');
            // 	}
            // 	wp_send_json_success([
            // 		'isPipe' => psIsPremium()
            // 		//  'isPipe' => true
            // 	]);
            // }
            // function registerSettings(){
            // 	register_setting('ssbUtils', 'ssbUtils', [
            // 		'show_in_rest' => [
            // 			'name' => 'ssbUtils',
            // 			'schema' => ['type' => 'string']
            // 		],
            // 		'type' => 'string',
            // 		'default' => wp_json_encode(['nonce' => wp_create_nonce('wp_ajax')]),
            // 		'sanitize_callback' => 'sanitize_text_field'
            // 	]);
            // }
            function onInit() {
                register_block_type( __DIR__ . '/build' );
            }

        }

        new PSBPlugin();
    }
}
require_once PSB_DIR_PATH . '/includes/Menu.php';
// Constant