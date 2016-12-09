<?php
/**
 * Plugin Name: Mtollwc
 * Plugin URI:  http://github.com/oakwoodgates/mtollwc
 * Description: A radical new plugin for WordPress!
 * Version:     0.0.1
 * Author:      WPGuru4u
 * Author URI:  http://wpguru4u.com
 * Donate link: http://github.com/oakwoodgates/mtollwc
 * License:     GPLv2
 * Text Domain: mtollwc
 * Domain Path: /languages
 *
 * @link http://github.com/oakwoodgates/mtollwc
 *
 * @package Mtollwc
 * @version 0.0.1
 */

/**
 * Copyright (c) 2016 WPGuru4u (email : wpguru4u@gmail.com)
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License, version 2 or, at
 * your discretion, any later version, as published by the Free
 * Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 */

/**
 * Built using generator-plugin-wp
 */

/**
 * Autoloads files with classes when needed
 *
 * @since  0.0.1
 * @param  string $class_name Name of the class being requested.
 * @return void
 */
function mtollwc_autoload_classes( $class_name ) {
	if ( 0 !== strpos( $class_name, 'M_' ) ) {
		return;
	}

	$filename = strtolower( str_replace(
		'_', '-',
		substr( $class_name, strlen( 'M_' ) )
	) );

	Mtollwc::include_file( 'includes/class-' . $filename );
}
spl_autoload_register( 'mtollwc_autoload_classes' );

/**
 * Main initiation class
 *
 * @since  0.0.1
 */
final class Mtollwc {

	/**
	 * Current version
	 *
	 * @var  string
	 * @since  0.0.1
	 */
	const VERSION = '0.0.1';

	/**
	 * URL of plugin directory
	 *
	 * @var string
	 * @since  0.0.1
	 */
	protected $url = '';

	/**
	 * Path of plugin directory
	 *
	 * @var string
	 * @since  0.0.1
	 */
	protected $path = '';

	/**
	 * Plugin basename
	 *
	 * @var string
	 * @since  0.0.1
	 */
	protected $basename = '';

	/**
	 * Singleton instance of plugin
	 *
	 * @var Mtollwc
	 * @since  0.0.1
	 */
	protected static $single_instance = null;

	/**
	 * Creates or returns an instance of this class.
	 *
	 * @since  0.0.1
	 * @return Mtollwc A single instance of this class.
	 */
	public static function get_instance() {
		if ( null === self::$single_instance ) {
			self::$single_instance = new self();
		}

		return self::$single_instance;
	}

	/**
	 * Sets up our plugin
	 *
	 * @since  0.0.1
	 */
	protected function __construct() {
		$this->basename = plugin_basename( __FILE__ );
		$this->url      = plugin_dir_url( __FILE__ );
		$this->path     = plugin_dir_path( __FILE__ );

		$this->plugin_classes();
	}

	/**
	 * Attach other plugin classes to the base plugin class.
	 *
	 * @since  0.0.1
	 * @return void
	 */
	public function plugin_classes() {
		// Attach other plugin classes to the base plugin class.

	//	$this->maia_admin = new M_Maia_Admin( $this );
	//	require_once dirname(__FILE__) . '/vendor/cpt-core/CPT_Core.php';
	//	require_once dirname(__FILE__) . '/vendor/cmb2/init.php';
		require( self::dir( 'vendor/cpt-core/CPT_Core.php' ) );
		require( self::dir( 'vendor/cmb2/init.php' ) );

		// post types
		$this->lounge = new M_Lounge( $this );
		$this->premium = new M_Premium( $this );

		// widgets
		require( self::dir( 'includes/class-badge143.php' ) );
		require( self::dir( 'includes/class-dynamic-lounge-image.php' ) );
		require( self::dir( 'includes/class-moon-phase.php' ) );
		require( self::dir( 'includes/class-landing-login.php' ) );

		// options panel
		require( self::dir( 'includes/admin.php' ) );

		// some functions for woocommerce subscriptions
		require( self::dir( 'includes/subscription-functions.php' ) );

		// send stuff to convert kit
		require( self::dir( 'includes/functions-ck.php' ) );

		// should probably be in the theme
		$this->theme_settings = new M_Theme_Settings( $this );

		// magic
		$this->points = new M_Points( $this );
		M_Create_Relate_Points::get_instance();
		$this->luna_woofunnels = new M_Luna_Woofunnels( $this );
	} // END OF PLUGIN CLASSES FUNCTION

	/**
	 * Add hooks and filters
	 *
	 * @since  0.0.1
	 * @return void
	 */
	public function hooks() {

		add_action( 'init', array( $this, 'init' ) );
	}

	/**
	 * Activate the plugin
	 *
	 * @since  0.0.1
	 * @return void
	 */
	public function _activate() {
		// Make sure any rewrite functionality has been loaded.
		flush_rewrite_rules();
	}

	/**
	 * Deactivate the plugin
	 * Uninstall routines should be in uninstall.php
	 *
	 * @since  0.0.1
	 * @return void
	 */
	public function _deactivate() {}

	/**
	 * Init hooks
	 *
	 * @since  0.0.1
	 * @return void
	 */
	public function init() {
		if ( $this->check_requirements() ) {
			load_plugin_textdomain( 'mtollwc', false, dirname( $this->basename ) . '/languages/' );
		//	$this->plugin_classes();
		}
	}

	/**
	 * Check if the plugin meets requirements and
	 * disable it if they are not present.
	 *
	 * @since  0.0.1
	 * @return boolean result of meets_requirements
	 */
	public function check_requirements() {
		if ( ! $this->meets_requirements() ) {

			// Add a dashboard notice.
			add_action( 'all_admin_notices', array( $this, 'requirements_not_met_notice' ) );

			// Deactivate our plugin.
			add_action( 'admin_init', array( $this, 'deactivate_me' ) );

			return false;
		}

		return true;
	}

	/**
	 * Deactivates this plugin, hook this function on admin_init.
	 *
	 * @since  0.0.1
	 * @return void
	 */
	public function deactivate_me() {
		deactivate_plugins( $this->basename );
	}

	/**
	 * Check that all plugin requirements are met
	 *
	 * @since  0.0.1
	 * @return boolean True if requirements are met.
	 */
	public function meets_requirements() {
		// Do checks for required classes / functions
		// function_exists('') & class_exists('').
		// We have met all requirements.
		return true;
	}

	/**
	 * Adds a notice to the dashboard if the plugin requirements are not met
	 *
	 * @since  0.0.1
	 * @return void
	 */
	public function requirements_not_met_notice() {
		// Output our error.
		echo '<div id="message" class="error">';
		echo '<p>' . sprintf( __( 'Mtollwc is missing requirements and has been <a href="%s">deactivated</a>. Please make sure all requirements are available.', 'mtollwc' ), admin_url( 'plugins.php' ) ) . '</p>';
		echo '</div>';
	}

	/**
	 * Magic getter for our object.
	 *
	 * @since  0.0.1
	 * @param string $field Field to get.
	 * @throws Exception Throws an exception if the field is invalid.
	 * @return mixed
	 */
	public function __get( $field ) {
		switch ( $field ) {
			case 'version':
				return self::VERSION;
			case 'basename':
			case 'url':
			case 'path':
			case 'lounge':
			case 'premium':
			case 'luna_woofunnels':
			case 'theme_settings':
			case 'points':
			case 'create_relate_points':
				return $this->$field;
			default:
				throw new Exception( 'Invalid ' . __CLASS__ . ' property: ' . $field );
		}
	}

	/**
	 * Include a file from the includes directory
	 *
	 * @since  0.0.1
	 * @param  string $filename Name of the file to be included.
	 * @return bool   Result of include call.
	 */
	public static function include_file( $filename ) {
		$file = self::dir( $filename .'.php' );
		if ( file_exists( $file ) ) {
			return include_once( $file );
		}
		return false;
	}

	/**
	 * This plugin's directory
	 *
	 * @since  0.0.1
	 * @param  string $path (optional) appended path.
	 * @return string       Directory and path
	 */
	public static function dir( $path = '' ) {
		static $dir;
		$dir = $dir ? $dir : trailingslashit( dirname( __FILE__ ) );
		return $dir . $path;
	}

	/**
	 * This plugin's url
	 *
	 * @since  0.0.1
	 * @param  string $path (optional) appended path.
	 * @return string       URL and path
	 */
	public static function url( $path = '' ) {
		static $url;
		$url = $url ? $url : trailingslashit( plugin_dir_url( __FILE__ ) );
		return $url . $path;
	}
}

/**
 * Grab the Mtollwc object and return it.
 * Wrapper for Mtollwc::get_instance()
 *
 * @since  0.0.1
 * @return Mtollwc  Singleton instance of plugin class.
 */
function mtollwc() {
	return Mtollwc::get_instance();
}

// Kick it off.
add_action( 'plugins_loaded', array( mtollwc(), 'hooks' ) );

register_activation_hook( __FILE__, array( mtollwc(), '_activate' ) );
register_deactivation_hook( __FILE__, array( mtollwc(), '_deactivate' ) );

add_filter( 'woofunnels_checkout_page_templates', 'mllp_checkout_page_template' );
function mllp_checkout_page_template( $templates ) {
		$plugin_path  = plugin_dir_path( __FILE__ ) . 'templates/';
		$templates['maia-lunar-lounge'] = array(
		'label'       => __( 'Maia Lunar Lounge Signup', 'maiatoll' ),
			'description' => __( 'Signup Page', 'maiatoll' ),
			'path' => $plugin_path,
			'callback'		=> 'woofunnels_maia_lunar_lounge',
		);
		$templates['first-promo'] = array(
		'label'       => __( 'First Promo', 'maiatoll' ),
			'description' => __( 'Signup for the first promo', 'maiatoll' ),
			'path' => $plugin_path,
			'callback'		=> 'woofunnels_maia_lunar_lounge',
		);
		return $templates;
}

function woofunnels_maia_lunar_lounge() {
	new M_Luna_Woofunnels();
}


/**
 *
 */
add_filter( 'woofunnels_checkout_form_templates', 'mllp_checkout_form_template' );
function mllp_checkout_form_template( $templates ) {
		$plugin_path  = plugin_dir_path( __FILE__ ) . 'templates/';
		$templates['maia-lunar-lounge'] = array(
			'label'       => __( 'Lunar Lounge Form', 'maiatoll' ),
			'description' => __( 'for Maia Lunar Lounge Signup page template', 'maiatoll' ),
			'path' => $plugin_path,
			'callback'		=> 'woofunnels_maia_lunar_lounge_checkout_form',
		);
		$templates['first-promo'] = array(
			'label'       => __( 'First Promo Form', 'maiatoll' ),
			'description' => __( 'for the first promo', 'maiatoll' ),
			'path' => $plugin_path,
			'callback'		=> 'woofunnels_maia_lunar_lounge_checkout_form',
		);
		$templates['autumn-2016'] = array(
			'label'       => __( 'Autumn 2016', 'maiatoll' ),
			'description' => __( 'for the free class into wc', 'maiatoll' ),
			'path' => $plugin_path,
			'callback'		=> 'woofunnels_mtollwc_autumn_2016',
		);
		return $templates;
}

function woofunnels_maia_lunar_lounge_checkout_form(){
	remove_action( 'woocommerce_before_checkout_form', 'woocommerce_checkout_coupon_form', 10 );
}
function woofunnels_mtollwc_autumn_2016(){
	remove_action( 'woocommerce_before_checkout_form', 'woocommerce_checkout_coupon_form', 10 );
	add_filter( 'wc_get_template', 'wf_autumn_2016_billing', 10, 5 );
	remove_filter( 'wc_get_template', array( WooFunnels_Checkout_Form, 'order_review_template' ), 20, 5 );

}
function wf_autumn_2016_billing( $located, $template_name, $args, $template_path, $default_path ) {

	if ( 'checkout/form-billing.php' == $template_name
		&& $default_path !== WooFunnels::dir( 'templates/' )
		&& is_woofunnels() ) {
		$located = wc_locate_template( 'woofunnels-checkout-form/autumn-2016/form-billing.php', '', Mtollwc::dir( 'templates/' ) );
	}

	return $located;
}
/**
 *
 */
add_filter( 'woofunnels_product_block_templates', 'mllp_product_block_template' );
function mllp_product_block_template( $templates ) {
	$plugin_path  = plugin_dir_path( __FILE__ ) . 'templates/';
	$templates['maia-lunar-lounge-table'] = array(
		'label'       => __( 'Lunar Lounge Form', 'maiatoll' ),
		'description' => __( 'for Maia Lunar Lounge Signup page template', 'maiatoll' ),
		'path' => $plugin_path,
	);
	$templates['first-promo-block'] = array(
		'label'       => __( 'First Promo Block', 'maiatoll' ),
		'description' => __( '', 'maiatoll' ),
		'path' => $plugin_path,
	);
	return $templates;
}

/**
 *
 */
function maia_login_redirect_url() {
		$id  = get_queried_object_id();
		$url = wc_get_page_permalink( 'myaccount' );

		if ( is_singular() ) {
			$redirect_to = 'post';
		} elseif ( isset( get_queried_object()->term_id ) ) {
			$redirect_to = get_queried_object()->taxonomy;
		} else {
			$redirect_to = '';
		}

		if ( ! empty( $redirect_to ) ) {

			$url = add_query_arg( array(
				'wcm_redirect_to' => $redirect_to,
				'wcm_redirect_id' => $id,
			), $url );
		}

		return esc_url( $url );
}

add_filter( 'wc_memberships_content_restricted_message', 'mtoll_master_membership_filter', 10, 3 );
function mtoll_master_membership_filter( $message, $post_id, $access_time ) {
	if ( 'lounge' === get_post_type( $post_id )
		|| 'premium' === get_post_type( $post_id )
		|| $post_id == maiatoll_get_option( 'maiatoll_hub_page' ) ) {
			// WP_Query arguments
		// WP_Query arguments
		$block = maiatoll_get_option( 'maiatoll_witchcamp_sign_up_in' );
		$args = array (
			'p' => $block,
			'post_type' => 'page',
		);

		// The Query
		$query = new WP_Query( $args );

		// The Loop
		if ( $query->have_posts() ) {
			while ( $query->have_posts() ) {
				$query->the_post();
			//	the_content();
				$message = apply_filters( 'the_content', get_the_content() );
			}
		} else {

		}

		// Restore original Post Data
		wp_reset_postdata();

	//	$p = get_post(14526);
	//	$message = $p->post_title;
	//	$message .= 'abc';
	//	$message .= apply_filters('the_content', get_post_field('post_content', '14526'));
	//	$message = 'To access this content, you must <a href="http://staging.bizarre-cord.flywheelsites.com/woofunnels_checkout/lunar-lounge-signup/?empty-cart&add-to-cart=13594">signup for a free membership</a>, or <a href="' . maia_login_redirect_url() . '">log in</a> if you are a member.';

	//	if ( is_user_logged_in() ) {
		//	$message = 'To access this content, you must <a href="' . esc_url( get_permalink( maiatoll_get_option( 'maiatoll_witchcamp_signup_page' ) ) ) . '">sign up here</a>.';

	//	} else {
		//	$message = 'To access this content, you must <a href="' . esc_url( get_permalink( maiatoll_get_option( 'maiatoll_witchcamp_signup_page' ) ) ) . '">sign up</a>, or <a href="' . maia_login_redirect_url() . '">log in</a> if you are a member.';
	//	}
	//	$message = '';
	}
	return $message;
}

// add_filter( 'wc_memberships_content_restricted_message', 'sv_filter_content_delayed_message1', 10, 3 );
function sv_filter_content_delayed_message1( $message, $post_id, $access_time ) {
	if ( 'premium' === get_post_type( $post_id ) ) {

		$message = 'To access this content, you must <a href="http://staging.bizarre-cord.flywheelsites.com/woofunnels_checkout/lunar-lounge-signup/?empty-cart&add-to-cart=13593">purchase a premium membership</a>, or <a href="' . maia_login_redirect_url() . '">log in</a> if you are a member.';

	}
	return $message;
}

add_action('template_redirect', 'lounge_closed');
function lounge_closed(){
// echo maiatoll_get_option( 'radio' );
	if ( is_singular( 'lounge' ) && 'closed' === maiatoll_get_option( 'maiatoll_luna_lounge_open' ) ) {
		wp_redirect( esc_url( get_permalink( maiatoll_get_option( 'maiatoll_luna_lounge_closed_redirect' ) ) ) );
		exit;
	}
//	if ( ( is_singular( 'premium' ) || is_post_type_archive( 'premium' ) ) && 'closed' === maiatoll_get_option( 'maiatoll_luna_lounge_premium_open' ) ) {
//		wp_redirect( esc_url( get_permalink( maiatoll_get_option( 'maiatoll_luna_lounge_premium_closed_post' ) ) ) );
//		exit;
//	}
}


add_action( 'cmb2_admin_init', 'maia_landing_page_metabox' );
function maia_landing_page_metabox() {

	$prefix = '_maialpt_';

	$cmb = new_cmb2_box( array(
		'id'           => $prefix . 'metabox',
		'title'        => __( 'Landing Page Options', 'mtoll' ),
		'object_types' => array( 'page' ),
		'context'      => 'normal',
		'priority'     => 'default',
	) );

	$cmb->add_field( array(
		'name' => __( 'Header', 'mtoll' ),
		'id' => $prefix . 'header',
		'type' => 'radio',
		'options' => array(
			'header_with_menu' => __( 'Default Menu', 'mtoll' ),
			'header_no_menu' => __( 'No menu', 'mtoll' ),
		),
	) );

	$cmb->add_field( array(
		'name' => __( 'Footer', 'mtoll' ),
		'id' => $prefix . 'footer',
		'type' => 'radio',
		'options' => array(
			'footer_ig_soc_bot' => __( 'IG, Social, Bottom', 'mtoll' ),
			'footer_soc_bot' => __( 'Social, Bottom', 'mtoll' ),
			'footer_bot' => __( 'Bottom', 'mtoll' ),
		),
	) );

}

function no_self_ping( &$links ) {
	$home = get_option( 'home' );
	foreach ( $links as $l => $link )
		if ( 0 === strpos( $link, $home ) )
			unset($links[$l]);
}

add_action( 'pre_ping', 'no_self_ping' );

add_action( 'init', 'update_my_custom_type', 99 );
/**
 * update_my_custom_type
 *
 * @author  Joe Sexton <joe@webtipblog.com>
 */
function update_my_custom_type() {
	global $wp_post_types;

	if ( post_type_exists( 'point' ) ) {
		// exclude from search results
		$wp_post_types['point']->exclude_from_search = true;
		$wp_post_types['badges']->exclude_from_search = true;
	}
}

add_action( 'woocommerce_save_account_details', 'woocommerce_save_account_details' );
function woocommerce_save_account_details( $uid ) {
	$dname = get_user_meta( $uid, 'display_name', true );
	$o_user = get_user_by( 'id', $uid );
	$o_user->display_name = ! empty( $_POST[ 'account_display_name' ] ) ? wc_clean( $_POST[ 'account_display_name' ] ) : $dname;

	wp_update_user( $o_user );

}

add_action( 'woocommerce_edit_account_form_start', 'mtoll_woocommerce_edit_account_form_start' );
function mtoll_woocommerce_edit_account_form_start() {
	$user = wp_get_current_user();
	?>
	<p class="woocommerce-FormRow woocommerce-FormRow--wide form-row form-row-wide">
		<label for="account_display_name"><?php _e( 'Display name', 'woocommerce' ); ?> <span class="required">*</span></label>
		<input type="text" class="woocommerce-Input woocommerce-Input--text input-text" name="account_display_name" id="account_display_name" value="<?php echo esc_attr( $user->display_name ); ?>" />
	</p>
	<?php
}

add_action( 'wp_head', 'mtoll_fb_pixel_wp_head' );
function mtoll_fb_pixel_wp_head() {
	?>
	<!-- Facebook Pixel Code -->
	<script>
	!function(f,b,e,v,n,t,s){if(f.fbq)return;n=f.fbq=function(){n.callMethod?
	n.callMethod.apply(n,arguments):n.queue.push(arguments)};if(!f._fbq)f._fbq=n;
	n.push=n;n.loaded=!0;n.version='2.0';n.queue=[];t=b.createElement(e);t.async=!0;
	t.src=v;s=b.getElementsByTagName(e)[0];s.parentNode.insertBefore(t,s)}(window,
	document,'script','https://connect.facebook.net/en_US/fbevents.js');
	fbq('init', '367652926691662');
	fbq('track', "PageView");</script>
	<noscript><img height="1" width="1" style="display:none"
	src="https://www.facebook.com/tr?id=367652926691662&ev=PageView&noscript=1"
	/></noscript>
	<!-- End Facebook Pixel Code -->
	<?php
}

/**
 * Remove password strength meter
 * @link( https://nicolamustone.com/2016/01/27/remove-the-password-strength-meter-on-the-checkout-page/, link)
 * @return [type] [description]
 */
function wc_ninja_remove_password_strength() {
	if ( wp_script_is( 'wc-password-strength-meter', 'enqueued' ) ) {
		wp_dequeue_script( 'wc-password-strength-meter' );
	}
}
add_action( 'wp_print_scripts', 'wc_ninja_remove_password_strength', 100 );

/**
 * Filter the wp-login.php logo link
 * @return [type] [description]
 */
function oak_login_logo_url() {
	return home_url();
}
add_filter( 'login_headerurl', 'oak_login_logo_url' );

function oak_login_css() { ?>
	<?php
	//	$img = content_url() . '/uploads/2016/04/logo_btc.png';
	//	$img = get_stylesheet_directory_uri() . '/images/site-login-logo.png';
	$img = content_url() . '/uploads/2016/08/witchcamp_final-e1470748381421.png';
	?>
	<style type="text/css">
		body.login div#login h1 a {
			background-image: url(<?php echo $img ?>);
			background-size: 259px;
   			height: 75px;
   			width:100%;
		}
	</style>
<?php }
add_action( 'login_head', 'oak_login_css' );
