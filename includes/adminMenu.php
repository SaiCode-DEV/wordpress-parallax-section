<?php
if (!defined('ABSPATH')) {exit;}
if(!class_exists('bsbAdminMenu')) {

    class icbAdminMenu {

        public function __construct() {
            add_action( 'admin_enqueue_scripts', [$this, 'adminEnqueueScripts'] );
            add_action( 'admin_menu', [$this, 'adminMenu'] );

            add_action( 'wp_ajax_get_popular_plugins', [$this, 'get_popular_plugins'] );
            add_action( 'wp_ajax_get_active_plugins', [$this, 'get_active_plugins'] );
            add_action( 'admin_notices', [$this, 'display_activation_notice'] );
            add_action( 'wp_ajax_activated_plugin', [$this, 'activated_plugin'] );
             
        }

        public function adminEnqueueScripts($hook) {
            if ('tools_page_parallax-section-dashboard' === $hook) {
                wp_enqueue_style( 'icb-admin-style', PSB_DIR_URL . 'build/admin/admin.css', false, PSB_VERSION );
                wp_enqueue_script( 'icb-admin-script', PSB_DIR_URL . 'build/admin/admin.js', ['react', 'react-dom', 'wp-data', "wp-api", "wp-util", "wp-i18n"], PSB_VERSION, true );

                 wp_localize_script('icb-admin-script', 'pluginAction', [
                    'ajaxUrl' => admin_url('admin-ajax.php'),
                    'nonce' => wp_create_nonce('wp_rest'),
                ]);
            }
        }

        public function adminMenu(){
        

            add_submenu_page(
                'tools.php',
                __('Parallax Section', 'parallax-section'),
                __('Parallax Section', 'parallax-section'),
                'manage_options',
                'parallax-section-dashboard',
                [$this, 'icb_help_page'],
            );
        }

        public function icb_help_page(){
            ?>
                <div id='icbAdminHelpPage'></div>
            <?php 
        }

        public function get_popular_plugins () {

            if (!function_exists('plugins_api')) {
                require_once ABSPATH . 'wp-admin/includes/plugin-install.php';
            }
             
            $cached_plugins = plugins_api('query_plugins', array(
                    'author' => 'bplugins',
                    'per_page' => 100
                ));

             wp_send_json_success($cached_plugins->plugins); 
        }

        public function get_active_plugins() {
            if (!isset($_GET['nonce']) || !wp_verify_nonce(sanitize_text_field($_GET['nonce']), 'wp_rest')) {
                wp_send_json_error(['message' => 'Invalid nonce or request.'], 400);
            }
        
            // Get the list of all installed plugins
            if (!function_exists('get_plugins')) {
                include_once ABSPATH . '/wp-admin/includes/plugin.php';
            }
        
            $installed_plugins = get_plugins();
        
            // Return the plugin basenames as an array
            $installed_plugin_slugs = array_keys($installed_plugins);
        
            wp_send_json_success($installed_plugin_slugs);
        }

        public function display_activation_notice() {
            // Check if transient is set
            $plugin_slug = get_transient('bblocks_show_activation_notice');

            $first_part = explode("/", $plugin_slug)[0];
            $cleaned_string = str_replace("-", " ", $first_part);
             

            if ($plugin_slug) {
                // Remove transient after displaying the notice
                delete_transient('bblocks_show_activation_notice');
        
                // Generate activation URL
                $activation_url = wp_nonce_url(
                    admin_url('plugins.php?action=activate&plugin=' . $plugin_slug),
                    'activate-plugin_' . $plugin_slug
                );
        
                // Display notice with activation button
                ?>
                <div class="notice notice-success is-dismissible bblocks-notice">
                    <p><?php esc_html_e(" $cleaned_string plugin was successfully installed.", 'bblocks-admin-bar'); ?></p>
                    <p>
                        <a href="<?php echo esc_url($activation_url); ?>" class="button button-primary">
                            <?php esc_html_e('Activate Plugin', 'bblocks-admin-bar'); ?>
                        </a>
                    </p>
                </div>
                <?php
            }
        }

        public function activated_plugin() {
            // Verify nonce
            if (!isset($_GET['nonce']) || !wp_verify_nonce(sanitize_text_field($_GET['nonce']), 'wp_rest')) {
                wp_send_json_error(['message' => 'Invalid nonce or request.'], 400);
            }

            $plugin_name = sanitize_text_field($_GET['plugin_name']) ?? false;
        
            include_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';
            include_once ABSPATH . 'wp-admin/includes/plugin-install.php';
        
            try {
                // Fetch plugin information
                $api = plugins_api('plugin_information', ['slug' => $plugin_name, 'fields' => ['sections' => false]]);
                if (is_wp_error($api)) {
                    wp_send_json_error(['message' => 'Failed to fetch plugin information.']);
                }
        
                // Suppress unexpected output
                ob_start();
                $upgrader = new Plugin_Upgrader();
                $result = $upgrader->install($api->download_link);
                ob_end_clean();
            
                $plugin_slug = $plugin_name.'/'.$plugin_name.'.php';

                if ($result) {
                    // Set transient to show notice
                    set_transient('bblocks_show_activation_notice', $plugin_slug, 1000000); // Valid for 60 seconds
                    $redirect_url = admin_url('plugins.php?plugin_status=all');
                    wp_send_json_success(['message' => 'Plugin installed successfully.', 'redirectUrl' => $redirect_url]);

                } else {
                    wp_send_json_error(['message' => 'Plugin installation failed.']);
                }
            } catch (Exception $e) {
                wp_send_json_error(['message' => 'An unexpected error occurred: ' . $e->getMessage()]);
            }
        }
    }
    new icbAdminMenu();
}