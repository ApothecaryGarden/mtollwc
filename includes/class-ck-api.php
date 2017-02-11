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

	public static function add_tag( $tag_id, $email ) {
		if ( ! $email ) {
			self::log( 'no email for add_tag', '', '' );
			return;
		}
		$log  = "add_tag fired".PHP_EOL.
				"Date: ".date("F j, Y, g:i a").PHP_EOL.
				"tag_id: ".$tag_id.PHP_EOL.
				"email: ".$email.PHP_EOL.
				"-------------------------".PHP_EOL;
		file_put_contents( plugin_dir_path(__FILE__) . "log_ck.txt", $log, FILE_APPEND);
		$args = array();
		$args['email'] = $email;
		$request = 'v3/tags/' . $tag_id . '/subscribe';
		return self::make_request( $request, 'POST', $args );
	}

	public static function remove_tag( $tag_id, $email ) {
		if ( ! $email ) {
			self::log( 'no email for remove_tag', '', '' );
			return;
		} 
		$log  = "remove_tag fired".PHP_EOL.
				"Date: ".date("F j, Y, g:i a").PHP_EOL.
				"tag_id: ".$tag_id.PHP_EOL.
				"email: ".$email.PHP_EOL.
				"-------------------------".PHP_EOL;
		file_put_contents( plugin_dir_path(__FILE__) . "log_ck.txt", $log, FILE_APPEND);
		$subscriber_id = self::get_subscriber_id( $email );

		if ( ! $subscriber_id )
			return;
		$args = array(
			'email' => $email,
		);
		$request = 'v3/subscribers/' . $subscriber_id . '/tags/' . $tag_id;
		return self::make_request( $request, 'DELETE', $args );
	}

	public static function get_subscriber_id( $email ) {
		if ( ! $email )
			return;
		$args = array(
			'email_address' => $email,
		);

		$request = 'v3/subscribers';
		$response = self::make_request( $request, 'GET', $args );

		$subscriber_id = $response->subscribers['0']->id;

		$log  = "get_subscriber_id response".PHP_EOL.
				"Date: ".date("F j, Y, g:i a").PHP_EOL.
				"email: ".$email.PHP_EOL.
				"subscriber_id: ".$subscriber_id.PHP_EOL.
				"-------------------------".PHP_EOL;
		file_put_contents( plugin_dir_path(__FILE__) . "log_ck.txt", $log, FILE_APPEND);
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

		$args['api_key'] = $api_key;
		$args['api_secret'] = $api_secret;

		$url = 'https://api.convertkit.com/' . $request . '?' . http_build_query( $args );
	//	$url = 'https://api.convertkit.com/' . $request;
	//	$url = add_query_arg($args, $url);
	//	self::log( "API Request: ", $url, $args);

		$log  = "make_request fired".PHP_EOL.
				"Date: ".date("F j, Y, g:i a").PHP_EOL.
				"request: ".$request.PHP_EOL.
				"method: ".$method.PHP_EOL.
			//	"args: ".print_r($args).PHP_EOL.
				"-------------------------".PHP_EOL;
		file_put_contents( plugin_dir_path(__FILE__) . "log_ck1.txt", $log, FILE_APPEND);

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

	//	self::log( "API Response: ", $url, json_decode( $results ) );
		$log  = "curl closed".PHP_EOL.
				"Date: ".date("F j, Y, g:i a").PHP_EOL.
				"results: ".$results.PHP_EOL.
				"-------------------------".PHP_EOL;
		file_put_contents( plugin_dir_path(__FILE__) . "log_ck-results.txt", $log, FILE_APPEND);
		return json_decode($results);
	}

	/**
	 * @param $message
	 */
	public static function log( $title, $var1, $var2 ) {
		$log  = $title.PHP_EOL.
				"Date: ".date("F j, Y, g:i a").PHP_EOL.
				"var1: ".$var1.PHP_EOL.
				"var2: ".print_r($var2).PHP_EOL.
				"-------------------------".PHP_EOL;
		file_put_contents( plugin_dir_path(__FILE__) . "log_ck1.txt", $log, FILE_APPEND);

	}
}
