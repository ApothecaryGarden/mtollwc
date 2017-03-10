<?php
/**
 * M_BuddyPress
 *
 * @since 0.0.5
 * @package RH2
 */

/**
 * M_BuddyPress
 *
 * @since 0.0.5
 */
class M_BuddyPress {
	/**
	 * Parent plugin class
	 *
	 * @var   RH2
	 * @since 0.0.5
	 */
	protected $plugin = null;

	/**
	 * Constructor
	 *
	 * @since  0.0.5
	 * @param  Mtollwc $plugin Main plugin object.
	 * @return void
	 */
	public function __construct( $plugin ) {
		$this->plugin = $plugin;
		$this->hooks();

	}


	/**
	 * Initiate our hooks
	 *
	 * @since  0.0.5
	 * @return void
	 */
	public function hooks() {
		add_action( 'bp_setup_nav', 		array( $this, 'bp_setup_nav' ), 				99 	);
		add_action( 'bp_init', 				array( $this, 'change_bp_default_component' ), 	4	);
	}

	public function change_bp_default_component() {
		if ( bp_is_my_profile() ) {
			define( 'BP_DEFAULT_COMPONENT', 'start' );
		} 
	}

	/**
	 * bp_setup_nav function.
	 * Add Start Here link to BP nav
	 *
	 * @return void
	 * @since  0.0.3 
	 * @todo  set default sub nav
	 */
	public function bp_setup_nav() {
	//	global $bp;

		$args = array(
			'name'						=> __( 'Start Here', 'mtollwc' ),
			'slug'						=> 'start',
			'default_subnav_slug'		=> 'start',
			'position'					=> 0,
			'show_for_displayed_user'	=> false,
			'screen_function' 			=> array( $this, 'user_home_screen' ),
			'item_css_id' 				=> 'start'
		);

		bp_core_new_nav_item( $args );
	}

	/**
	 * user_home_screen 
	 * @see   $this->bp_setup_nav()
	 * @since 0.0.5
	 */
	function user_home_screen() {
		add_action( 'bp_template_title', array( $this, 'user_home_screen_title') );
		add_action( 'bp_template_content', array( $this, 'user_home_screen_content') );
		bp_core_load_template( apply_filters( 'bp_core_template_plugin', 'members/single/plugins' ) );
	}

	/**
	 * user_home_screen_title action to show custom page in bp tab
	 * @see  $this->bp_setup_nav()
	 * @since 0.0.5
	 */
	function user_home_screen_title() {
	//	echo 'the welcome content';
	}

	/**
	 * user_home_screen_content action to show custom page in bp tab
	 * @see  $this->bp_setup_nav()
	 * @since 0.0.5
	 */
	function user_home_screen_content() {
		$p = get_post('20496');
	//	$post = &get_post($post_id);
		setup_postdata( $p );
		the_content();
		wp_reset_postdata( $p );
	//	echo 'the welcome content';
	}
}
