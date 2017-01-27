<?php
/**
 * Mtollwc Ck Api
 *
 * @since NEXT
 * @package Mtollwc
 */

/**
 * Mtollwc Ck Api.
 *
 * @since NEXT
 */
class M_CK_API {
	/**
	 * Parent plugin class
	 *
	 * @var   Mtollwc
	 * @since NEXT
	 */
	protected static $plugin = null;

	/**
	 * Constructor
	 *
	 * @since  NEXT
	 * @param  Mtollwc $plugin Main plugin object.
	 * @return void
	 */
	public function __construct( $plugin ) {
		self::$plugin = $plugin;
	//	$this->api = new ConvertKitAPI($api_key,$api_secret,$debug);
		self::hooks();
	}

	/**
	 * Initiate our hooks
	 *
	 * @since  NEXT
	 * @return void
	 */
	public static function hooks() {

	}

	public static function add_tag( $tag_id, $options = array() ) {
		if ( ! array_key_exists( 'email', $options ) ) {
			self::log( 'no email' );
			return;
		} 
		$request = 'v3/tags/' . $tag_id . '/subscribe';
		return self::make_request( $request, 'POST', $args );
	}

	public static function remove_tag( $tag_id, $options = array() ) {
		if ( ! array_key_exists( 'email', $options ) ) {
			self::log( 'no email' );
			return;
		} 

		$subscriber_id = self::get_subscriber_id( $options['email'] );

		if ( ! $subscriber_id )
			return;

		$request = 'v3/subscribers/' . $subscriber_id . '/tags/' . $tag_id;
		return self::make_request( $request, 'DELETE', $args );
	}

	public static function get_subscriber_id( $email ) {
		if ( ! $email )
			return;
		$args = array(
			'email' => $email,
		);

		$request = 'v3/subscribers';
		$response = self::make_request( $request, 'GET', $args );
		$subscriber_id = $response->subscribers->id;
		return $subscriber_id;
	}

	/**
	 * Make a request to the ConvertKit API
	 *
	 * @param string $request Request string
	 * @param string $method HTTP Method
	 * @param array $args Request arguments
	 * @return object Response object
	 */
	private static function make_request( $request, $method = 'GET', $args = array() ) {

		$general_options = get_option('_wp_convertkit_settings');
		$api_key = $general_options && array_key_exists('api_key', $general_options) ? $general_options['api_key'] : null;
		$api_secret = $general_options && array_key_exists('api_secret', $general_options) ? $general_options['api_secret'] : null;
		$debug = $general_options && array_key_exists('debug', $general_options) ? $general_options['debug'] : null;

		$args['api_key'] = $api_key;

		$url = 'https://api.convertkit.com/' . $request . '?' . http_build_query( $args );
		self::log( "API Request: " . $url );

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_HEADER, false);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
		if ( 'PUT' == $method ){
			curl_setopt($ch, CURLOPT_PUT, true);
		}

		$results = curl_exec($ch);
		curl_close($ch);

		self::log( "API Response: " . print_r( json_decode( $results ), true) );

		return json_decode( $results );
	}

	/**
	 * @param $message
	 */
	public static function log( $message ) {

		if ( 'on' == self::$debug ) {
			$dir = dirname( __FILE__ );

			$handle = fopen( trailingslashit( $dir ) . 'log.txt', 'a' );
			if ( $handle ) {
				$time   = date_i18n( 'm-d-Y @ H:i:s -' );
				fwrite( $handle, $time . " " . $message . "\n" );
				fclose( $handle );
			}
		}
	}
}
