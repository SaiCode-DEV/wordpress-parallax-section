<?php

if ( !defined( 'ABSPATH' ) ) { exit; }

class Menu {
	public function __construct() {
		add_action( 'admin_menu', [ $this, 'adminMenu' ] );
		add_action( 'admin_enqueue_scripts', [$this, 'adminEnqueueScripts'] );
		$this->isUserPremium = psIsPremium();
	}

	public function adminMenu() {

		if ($this->isUserPremium)  {
			add_menu_page(
				        __('Parallax Section', 'parallax-section'), // Page title
				        __('Parallax Section', 'parallax-section'), // Menu title
				        'manage_options',                           // Capability
				        'parallax-section-dashboard',               // Menu slug
				        [$this, 'renderDashboardPage'],             // Callback function
				        'dashicons-images-alt2',                    // Icon (Dashicons or URL)
				        20                                          // Position
				    );
		}else {
			add_submenu_page(
				'tools.php',
				__('Parallax Section', 'parallax-section'),
				__('Parallax Section', 'parallax-section'),
				'manage_options',
				'parallax-section-dashboard',
				[$this, 'renderDashboardPage'],
				0
			);
		}
	}

// 	public function adminMenu() {
//     add_menu_page(
//         __('Parallax Section', 'parallax-section'), // Page title
//         __('Parallax Section', 'parallax-section'), // Menu title
//         'manage_options',                           // Capability
//         'parallax-section-dashboard',               // Menu slug
//         [$this, 'renderDashboardPage'],             // Callback function
//         'dashicons-images-alt2',                    // Icon (Dashicons or URL)
//         20                                          // Position
//     );
// }


	public function renderDashboardPage(){ ?>
		<div
			id='apbDashboard'
			data-info='<?php echo esc_attr( wp_json_encode( [
				'version' => PSB_VERSION,
				'isPremium' => psIsPremium(),
				'hasPro' => PARALLAX_HAS_PRO
			] ) ); ?>'
		></div>
	<?php }

	function adminEnqueueScripts( $hook ) {
		if( strpos( $hook, 'parallax-section' ) ){
			wp_enqueue_style( 'apb-admin-dashboard', PSB_DIR_URL . 'build/admin-dashboard.css', [], PSB_VERSION );
			wp_enqueue_script( 'apb-admin-dashboard', PSB_DIR_URL . 'build/admin-dashboard.js', [ 'react', 'react-dom' ], PSB_VERSION, true );
			wp_set_script_translations( 'apb-admin-dashboard', 'parallax-section', PSB_DIR_PATH . 'languages' );
		}
	}
}
new Menu();
