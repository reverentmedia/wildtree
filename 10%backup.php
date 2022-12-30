<?php

// shows discount instructions on cart/checkout pages, adds/removes coupon when qualified


// apply or remove coupon
add_action('woocommerce_add_to_cart', 'apply_or_remove_ten_percent_discount');
add_action('woocommerce_cart_item_removed', 'apply_or_remove_ten_percent_discount');
add_action('woocommerce_cart_updated', 'apply_or_remove_ten_percent_discount');

function apply_or_remove_ten_percent_discount( ) {
		
	// calculate total (excluding starter packs)
	$hasOrchard = 0;
	$orchardDollars = 0;
	foreach(WC()->cart->get_cart() as $cart_item){
		$pid = $cart_item['product_id'];
		if ($pid==1482 || $pid==1518 || $pid==1522 || $pid==1525 || $pid==1516) {
			$hasOrchard = 1;
			$orchardDollars += get_post_meta($cart_item['product_id'] , '_price', true);
		}
	}
	$total = floatval(WC()->cart->get_subtotal()) - floatval($orchardDollars);

	// add coupon
	if ($total>=1000 && !in_array('10off1k', WC()->cart->get_applied_coupons())) {
		WC()->cart->apply_coupon('10off1k');
	}

	// remove coupon
	if ($total<1000 && in_array('10off1k', WC()->cart->get_applied_coupons())) {
		WC()->cart->remove_coupon('10off1k');
	}
}



// show discount banner
add_action('woocommerce_before_checkout_form', 'add_ten_percent_discount');
add_action('woocommerce_cart_totals_after_order_total', 'add_ten_percent_discount');
function add_ten_percent_discount( ) {
	
	//if (get_current_user_id()==12) { // testing as erik
		
		// calculate total (excluding starter packs)
		$hasOrchard = 0;
		$orchardDollars = 0;
		foreach(WC()->cart->get_cart() as $cart_item){
			$pid = $cart_item['product_id'];
			if ($pid==1482 || $pid==1518 || $pid==1522 || $pid==1525 || $pid==1516) {
				$hasOrchard = 1;
				$orchardDollars += get_post_meta($cart_item['product_id'] , '_price', true);
			}
		}
		$total = floatval(WC()->cart->get_subtotal()) - floatval($orchardDollars);
		
		// spend more for discount
		if ($total<1000 && $hasOrchard==0) {
			echo '<div style="width:100%;background:#759072;color:#fff;box-sizing:border-box;padding:12px 20px;margin-bottom:20px;">You currently have $'.$total.' worth of product in your cart. Add another <strong>$'.floatval(1000-$total).'</strong> and receive 10% off your entire order!</div>';
		}
		
		// deer orchard not eligible
		else if ($total<1000 && $hasOrchard==1) {
			echo '<div style="width:100%;background:#759072;color:#fff;box-sizing:border-box;padding:12px 20px;margin-bottom:20px;">You currently have $'.$total.' worth of eligible product in your cart. Add another <strong>$'.floatval(1000-$total).'</strong> and receive 10% off your entire order! (<strong>Deer Orchard Starter Packs are already discounted and do not qualify for additional discounts.</strong>)</div>';
		}
		
		// order is eligible for additional 10% discount, apply it
		else if ($total>=1000) {
			echo '<div style="width:100%;background:#759072;color:#fff;box-sizing:border-box;padding:12px 20px;margin-bottom:20px;"><strong>Congrats!</strong> You\'ve qualified for 10% off your entire order!</div>';
		}
	
	//} // end testing as erik
}