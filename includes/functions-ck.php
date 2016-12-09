<?php
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
			return $thankyoutext = '<p>You\'re in! Welcome. Check your inbox for 3 emails- one\'s a receipt, the next is your login, and the final is a note with juicy details you need to know.</p>';
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
