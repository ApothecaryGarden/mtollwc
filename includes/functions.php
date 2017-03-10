<?php

if ( ! function_exists( 'woocommerce_order_again_button' ) ) {
	function woocommerce_order_again_button( $order ) {
		if ( ! $order || ! $order->has_status( 'completed' ) || ! is_user_logged_in() ) {
			return;
		}
		$items = $order->get_items();
		$exc = maiatoll_get_option( 'maiatoll_remove_order_again' );
		$exc = explode( ',', $exc );
		foreach ( $items as $item ) {
			if ( in_array($item['product_id'], $exc )  ) {
				return;
			}
		}
		wc_get_template( 'order/order-again.php', array(
			'order' => $order
		) );
	//	return;
	}
}

/**
 * Remove the "Change Payment Method" button from the My Subscriptions table.
 *
 * This isn't actually necessary because @see eg_subscription_payment_method_cannot_be_changed()
 * will prevent the button being displayed, however, it is included here as an example of how to
 * remove just the button but allow the change payment method process.
 *
 * @link( https://gist.github.com/thenbrent/8851287 )
 */
function eg_remove_my_subscriptions_button( $actions, $subscription ) {
	foreach ( $actions as $action_key => $action ) {
		switch ( $action_key ) {
//			case 'change_payment_method':	// Hide "Change Payment Method" button?
//			case 'change_address':		// Hide "Change Address" button?
//			case 'switch':			// Hide "Switch Subscription" button?
			case 'resubscribe':		// Hide "Resubscribe" button from an expired or cancelled subscription?
			case 'pay':			// Hide "Pay" button on subscriptions that are "on-hold" as they require payment?
			case 'reactivate':		// Hide "Reactive" button on subscriptions that are "on-hold"?
//			case 'cancel':			// Hide "Cancel" button on subscriptions that are "active" or "on-hold"?
				unset( $actions[ $action_key ] );
				break;
			default:
				error_log( '-- $action = ' . print_r( $action, true ) );
				break;
		}
	}
	return $actions;
}
add_filter( 'wcs_view_subscription_actions', 'eg_remove_my_subscriptions_button', 100, 2 );

/**
 * Do not allow a customer to resubscribe to an expired or cancelled subscription.
 */
// Just in case removing the button isn't enough
// https://gist.github.com/thenbrent/8851189
// add_filter( 'wcs_can_user_resubscribe_to_subscription', '__return_false', 100 );


/**
 * [autocomplete_virtual_orders description]
 * @param  [type] $order_status [description]
 * @param  [type] $order_id     [description]
 * @return [type]               [description]
 */
function autocomplete_virtual_orders( $order_status, $order_id ) {
	$order = new WC_Order( $order_id );
	if ( 'processing' == $order_status && ( 'on-hold' == $order->status || 'pending' == $order->status || 'failed' == $order->status ) ) {
		$virtual_order = null;
		if ( count( $order->get_items()) > 0 ) {
			foreach ( $order->get_items() as $item ) {
				if ( 'line_item' == $item['type'] ) {
					$_product = $order->get_product_from_item( $item );
					if ( !$_product->is_virtual() ) {
						$virtual_order = false;
						break;
					} else {
						$virtual_order = true;
					}
				}
			}
		}
		if ($virtual_order) {
			return 'completed';
		}
	}
	return $order_status;
}
add_filter( 'woocommerce_payment_complete_order_status', 'autocomplete_virtual_orders', 10, 2 );

function mt_funky_func() {
	global $post;

	if ( '24854' == $post->ID && ! current_user_can('administrator') ) {
		if ( ! wc_memberships_is_user_active_member( get_current_user_id(), '13845' ) ) {
			remove_action( 'sensei_single_course_content_inside_before', array( 'Sensei_Course', 'the_course_enrolment_actions' ), 30 );
		}
	}
}
add_action( 'template_redirect', 'mt_funky_func', 200 );

function mtollwc_ck_thankyou_hook( $order_id ) {

	// Lets grab the order
	$order = wc_get_order( $order_id );

	// This is how to grab line items from the order
	$line_items = $order->get_items();

	// This loops over line items
	foreach ( $line_items as $item ) {
  		// This will be a product
  		$product = $order->get_product_from_item( $item );

  		// This is the products SKU
		$sku = $product->get_sku();

		// Looking for 22688
		if ( '22688' == $sku ) {

		}
	}
}
// add_action( 'woocommerce_thankyou', 'mtollwc_ck_thankyou_hook' );

function mtoll_thank_you( $thankyoutext, $order ) {
	// Lets grab the order
//	$order = wc_get_order( $order_id );
//	echo '<pre>'; print_r($order); echo '</pre>';

	// get the order id
	$order_id = $order->id;

	// This is how to grab line items from the order
	$line_items = $order->get_items();
//	echo '<pre>'; print_r($line_items); echo '</pre>';

	// This loops over line items
	foreach ( $line_items as $item ) {
  		// This will be a product
  		$product = $order->get_product_from_item( $item );

  		// This is the products SKU
	//	$sku = $product->get_sku();
		$id = $product->id;
		// Looking for 22688
		if ( '22688' == $id ) {
			return $thankyoutext = '<p>You\'re in! Welcome. Check your inbox for 2 emails- one\'s a receipt, the other is a note with juicy details you need to know.</p>';
		}
	}

	return $thankyoutext ;
/*
	// This is how to grab line items from the order
	$line_items = $order->get_items();

	// This loops over line items
	foreach ( $line_items as $item ) {
  		// This will be a product
  		$product = $order->get_product_from_item( $item );

  		// This is the products SKU
		$sku = $product->get_sku();

		// Looking for 22688
		if ( '22688' == $sku ) {
			return $thankyoutext = '<p>You\'re in! Welcome. Check your inbox for 3 emails- one\'s a receipt, the next is your login, and the final is a note with juicy details you need to know.</p>';
		}
	}
	return $thankyoutext ;
*/
}
add_filter( 'woocommerce_thankyou_order_received_text', 'mtoll_thank_you', 10, 2 );
/**
 * BADGE -- badge_id -- ck_tag_to_add
 * Butterfly -- 13738 -- 94935
 * Dragonfly -- 13741 -- 94936
 *
 * @param  [type] $user_id       [description]
 * @param  [type] $achievment_id [description]
 * @return [type]                [description]
 */
function mtollwc_ck_tag_awards( $user_id, $achievment_id ) {

	$u = get_userdata( $user_id );
	$email = $u->user_email;
	$name = $u->first_name;

	if ( '13738' == $achievment_id ) {
		$tag = '94935';
	} elseif ( '13741' == $achievment_id ) {
		$tag = '94936';
	}

	return ckwc_convertkit_api_add_subscriber_to_tag( $tag, $email, $name );
}
add_action( 'badgeos_award_achievement', 'mtollwc_ck_tag_awards', 10, 2 );

//	remove_action( 'bp_setup_nav', array( 'BuddyPress_Sensei_Loader', 'bp_sensei_add_new_setup_nav' ), 100 );
//	remove_action( 'bp_setup_admin_bar', array( 'BuddyPress_Sensei_Loader', 'bp_sensei_add_new_admin_bar' ), 90 );

/**
 *
 *
 */
function mtollwc_woocommerce_subscription_status_cancelled( $data ) {
	$id = $data->order->id;
	// $order = wc_get_order( $id );
	$o2 = new WC_Order( $id );
	$uid = $o2->user_id;
	$ud = get_userdata( $uid );
	$email = $ud->user_email;

	$response = array();
	$response['add'] = M_CK_API::add_tag( '94585', $email );
	$response['remove'] = M_CK_API::remove_tag( '153249', $email );
}
add_action( 'woocommerce_subscription_status_cancelled', 'mtollwc_woocommerce_subscription_status_cancelled', 10, 1 );

function mtollwc_woocommerce_subscriptions_after_payment_retry( $last_retry, $last_order ) {
	$a = 'no';
	if ( '5' == WCS_Retry_Manager::store()->get_retry_count_for_order( $last_order->id ) && 'wc-failed' == $last_order->post_status ) {
		$a = 'yes';
		$l_o = $last_order;

		$renewal_order_id = $last_order->id;
		// Returns an array of subscriptions related to renewal order. currently there is only one subscription product available, 
		// but in the future we will need to make sure the sub is the correct one (since we're doing this for community membership).
		$subscription_array = wcs_get_subscriptions_for_renewal_order( $renewal_order_id );
		$subscription_id = key($subscription_array);
		$subscription = (object) $subscription_array["$subscription_id"];

	}

	if ( '5' == WCS_Retry_Manager::store()->get_retry_count_for_order( $last_order->id ) && 'wc-failed' == $last_order->post_status ) {
		// Update status of renewal order and subscription to cancelled
		$last_order->update_status( 'cancelled' );
		$subscription->update_status( 'cancelled' );
	}
}
add_action( 'woocommerce_subscriptions_after_payment_retry', 'mtollwc_woocommerce_subscriptions_after_payment_retry', 10, 2 );

/**
 * Add multiple products to cart 
 * @props   http://dsgnwrks.pro/snippets/woocommerce-allow-adding-multiple-products-to-the-cart-via-the-add-to-cart-query-string/
 */
function woocommerce_maybe_add_multiple_products_to_cart() {

	// Make sure WC is installed, and add-to-cart qauery arg exists, and contains at least one comma.
	if ( ! class_exists( 'WC_Form_Handler' ) || empty( $_REQUEST['add-to-cart'] ) || false === strpos( $_REQUEST['add-to-cart'], ',' ) ) {
	    return;
	}

	// Remove WooCommerce's hook, as it's useless (doesn't handle multiple products).
	remove_action( 'wp_loaded', array( 'WC_Form_Handler', 'add_to_cart_action' ), 20 );

	$product_ids = explode( ',', $_REQUEST['add-to-cart'] );
	$count       = count( $product_ids );
	$number      = 0;

    foreach ( $product_ids as $product_id ) {
		if ( ++$number === $count ) {
			// Ok, final item, let's send it back to woocommerce's add_to_cart_action method for handling.
			$_REQUEST['add-to-cart'] = $product_id;

			return WC_Form_Handler::add_to_cart_action();
		}

		$product_id        = apply_filters( 'woocommerce_add_to_cart_product_id', absint( $product_id ) );
		$was_added_to_cart = false;
		$adding_to_cart    = wc_get_product( $product_id );

		if ( ! $adding_to_cart ) {
			continue;
		}

		$add_to_cart_handler = apply_filters( 'woocommerce_add_to_cart_handler', $adding_to_cart->product_type, $adding_to_cart );

		/*
		 * Sorry.. if you want non-simple products, you're on your own.
		 *
		 * Related: WooCommerce has set the following methods as private:
		 * WC_Form_Handler::add_to_cart_handler_variable(),
		 * WC_Form_Handler::add_to_cart_handler_grouped(),
		 * WC_Form_Handler::add_to_cart_handler_simple()
		 *
		 * Why you gotta be like that WooCommerce?
		 */
//        if ( 'simple' !== $add_to_cart_handler ) {
//            continue;
//        }

		// For now, quantity applies to all products.. This could be changed easily enough, but I didn't need this feature.
		$quantity          = empty( $_REQUEST['quantity'] ) ? 1 : wc_stock_amount( $_REQUEST['quantity'] );
		$passed_validation = apply_filters( 'woocommerce_add_to_cart_validation', true, $product_id, $quantity );

		if ( $passed_validation && false !== WC()->cart->add_to_cart( $product_id, $quantity ) ) {
		//    wc_add_to_cart_message( array( $product_id => $quantity ), true );
		    add_filter( 'wc_add_to_cart_message', '__return_empty_string' );
		}
    }
}

// Fire before the WC_Form_Handler::add_to_cart_action callback.
add_action( 'wp_loaded', 'woocommerce_maybe_add_multiple_products_to_cart', 15 );

function mtollwc_woocommerce_clear_cart_url() {
	global $woocommerce;

	if ( isset( $_GET['empty-cart'] ) ) {
		$woocommerce->cart->empty_cart();
	}
}
add_action( 'init', 'mtollwc_woocommerce_clear_cart_url', 1);


add_filter( 'woocommerce_add_to_cart_redirect', 'mtollwc_add_to_cart_redirect', 100 );
/**
 * If a link has an add-to-cart param to the WooFunnels Checkout, after adding the product
 * to the cart, redirect to the page without the add-to-cart param to avoid adding the product
 * again if the customer refreshes the page.
 *
 * @since 0.1.0
 */
function mtollwc_add_to_cart_redirect( $url ) {

	if ( ! is_ajax() ) {
		$schema = is_ssl() ? 'https://' : 'http://';
		$url = explode('?', $schema . $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"] );
		$url = remove_query_arg( array( 'add-to-cart', 'variation_id', 'quantity', 'empty-cart', 'attribute_pa_*' ), $url[0] );
	}

	return $url;
}