<?php

/**
 * Plugin Name: Parallax Section - Block
 * Description: Makes background element scrolls slower than foreground content.
 * Version: 2.0.0
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

define( 'PSB_VERSION', ( isset( $_SERVER['HTTP_HOST'] ) && 'localhost' === $_SERVER['HTTP_HOST'] ? time() : '2.0.0' ) );
define( 'PSB_DIR_URL', plugin_dir_url( __FILE__ ) );
define( 'PSB_DIR_PATH', plugin_dir_path( __FILE__ ) );

function psIsPremium() {
    return true;
}

require_once PSB_DIR_PATH . 'includes/GetCSS.php';

if ( !class_exists( 'PSBPlugin' ) ) {
    class PSBPlugin {
        function __construct() {
            add_action( 'init', [$this, 'onInit'] );
            add_action( 'enqueue_block_editor_assets', [$this, "enqueueBlockEditorAssets"] );
        }

        function enqueueBlockEditorAssets() {
            wp_add_inline_script( 'psb-parallax-editor-script', 'const psbpipecheck = true;', 'before' );
        }

        function onInit() {
            register_block_type( __DIR__ . '/build' );
        }
    }

    new PSBPlugin();
}

require_once PSB_DIR_PATH . '/includes/Menu.php';