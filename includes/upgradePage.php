<?php
 

if ( !defined( 'ABSPATH' ) ) { exit; }

class UpgradePage{
	public function __construct(){
		add_action( 'admin_menu', [$this, 'adminMenu'] );
	}

	function adminMenu(){
		add_submenu_page(
            'info-cards-dashboard',
			__( 'Info Cards - Upgrade', 'info-cards' ),
			__( 'Upgrade', 'info-cards' ),
			'manage_options', 
            'upgrade',
			[$this, 'upgradePage']
		);
	}

	function upgradePage(){ ?>
		<iframe src='https://checkout.freemius.com/plugin/17727/plan/29468/' width='100%' frameborder='0' style='width: calc(100% - 20px); height: calc(100vh - 60px); margin-top: 15px;'></iframe>
	<?php }
}
new UpgradePage;