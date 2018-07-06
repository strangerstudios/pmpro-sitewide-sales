<?php
/**
 * text-domain:
 * register_activation_hook( __FILE__, 'pmprobp_install' );
function pmprobp_install() {
	set_transient( 'pmprobp_activated', true, 30 );
}
 */

new Help_Welcome_Menus();
class Help_Welcome_Menus {

	/**
	 * Add the minimum capabilities used for the plugin
	 */
	const min_caps = 'manage_options';

	protected $add_on_name;
	protected $database_names;

	public function __construct() {
		add_action( 'admin_menu', array( $this, 'create_admin_menus' ) );
		// add_action( 'admin_init', array( $this, 'pmprobp_welcome' ), 11 );
		add_action( 'admin_head', array( $this, 'admin_head' ) );
		add_action( 'admin_menu', array( $this, 'pmpbro_admin_help_tab' ) );
	}

	/**
	 * Add the page to the admin area
	 */
	public function create_admin_menus() {
		add_dashboard_page(
			'PMPro BP page',
			'PMPro BP page',
			self::min_caps,
			'pmprobp-page.php',
			array( $this, 'pmprobp_message' )
		);

		// Remove the page from the menu
		remove_submenu_page( 'index.php', 'pmprobp-page.php' );
	}

	/**
	 * Display the plugin pmprobp message
	 */
	public function pmprobp_message() {
		echo '<div class="wrap">';
		echo '<h2>' . __FUNCTION__ . '</h2>';
		echo '<h3>' . __FILE__ . '</h3>';
		echo '</div>';
	}

	/**
	 * Check the plugin activated transient exists if does then redirect
	 */
	public function pmprobp_welcome() {
		if ( ! get_transient( 'pmprobp_activated' ) ) {
			return;
		}

		// Delete the plugin activated transient
		delete_transient( 'pmprobp_activated' );

		wp_safe_redirect(
			add_query_arg(
				array(
					'page' => 'pmprobp-page.php',
				), admin_url( 'index.php' )
			)
		);
		exit;
	}

	/**
	 *
	 */
	public function admin_head() {
		// Add custom styling to your page
	}

	public function pmpbro_admin_help_tab() {
		$pmpbro_help_page = add_dashboard_page( __( 'PMPBro Help Tab Page', 'pmpbro' ), __( 'PMPBro Help Tab Page', 'pmpbro' ), self::min_caps, 'pmpbro.php', array( $this, 'pmpbro_help_admin_page' ) );

		add_action( 'load-' . $pmpbro_help_page, array( $this, 'admin_add_help_tab' ) );
	}


	public function admin_add_help_tab() {
		global $pmpbro_help_page;
		$screen = get_current_screen();

		// Add my_help_tab if current screen is My Admin Page
		$screen->add_help_tab(
			array(
				'id'    => 'pmpbro_help_tab_1',
				'title' => __( 'PMPBro Help Tab One' ),
				'content'   => '<p>' . __( 'Use this field to describe to the user what text you want on the help tab.' ) . '</p>',
			)
		);
		$screen->add_help_tab(
			array(
				'id'    => 'pmpbro_help_tab_2',
				'title' => __( 'PMPBro Help Tab Two' ),
				'content'   => '<p>' . __( 'Use this field to describe to the user what text you want on the help tab.' ) . '</p>',
			)
		);
		$screen->add_help_tab(
			array(
				'id'    => 'pmpbro_help_tab_3',
				'title' => __( 'PMPBro Help Tab Three' ),
				'content'   => '<p>' . __( 'Use this field to describe to the user what text you want on the help tab.' ) . '</p>',
			)
		);
		$screen->add_help_tab(
			array(
				'id'    => 'pmpbro_help_tab_4',
				'title' => __( 'PMPBro Help Tab Four' ),
				'content'   => '<p>' . __( 'Use this field to describe to the user what text you want on the help tab.' ) . '</p>',
			)
		);
	}

	public function pmpbro_help_admin_page() {
		echo '<div class="wrap">';
		echo '<h2>' . __FUNCTION__ . '</h2>';
		echo '<h3>' . __FILE__ . '</h3>';
		echo '<h4>Page built with:</h4>';

		echo '<pre>
	add_action( \'admin_menu\', \'pmpbro_admin_help_tab\' );
	function pmpbro_admin_help_tab() {
	    $pmpbro_help_page = add_options_page( __( \'PMPBro Help Tab Page\', \'pmpbro\' ), __( \'PMPBro Help Tab Page\', \'pmpbro\' ),
	        \'manage_options\', \'pmpbro.php\', \'pmpbro_help_admin_page\' );
	    add_action( \'load-\' . $pmpbro_help_page, \'admin_add_help_tab\' );
	}</pre>';
		// function admin_add_help_tab() {
		// global $pmpbro_help_page;
		// $screen = get_current_screen();
		// Add my_help_tab if current screen is My Admin Page
		// $screen->add_help_tab(
		// array(
		// \'id\'    => \'pmpbro_help_tab\',
		// \'title\' => __( \'PMPBro Help Tab\' ),
		// \'content\'   => \'<p>\' . __( \'Use this field to describe to the user what text you want on the help tab.\' ) . \'</p>\',
		// )
		// );
		// }
		// function pmpbro_help_admin_page() {
		// echo \'<div class="wrap">\';
		// echo \'<h2>\' . __FUNCTION__ . \'</h2>\';
		// echo \'<h3>\' . __FILE__ . \'</h3>\';
		// echo \'</div>\';
		// }
		// </pre>';
		echo '</div>';
	}
}
