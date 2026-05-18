<?php
/**
 * Content cart summary — FTS sidebar: coupon panel + pricing panel (child theme override).
 *
 * @var array $cart_line_items
 * @var float $deposit_amount
 * @var float $due_amount
 * @var bool $is_partial_payment
 * @var bool $show_title
 * @var bool $show_coupon_form
 * @var Checkout $checkout
 * @var \WPTravelEngine\Core\Cart\Cart $cart
 * @package FTS_Checkout
 */

use WPTravelEngine\Pages\Checkout;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

global $wte_cart;

$cart     = $_wte_cart ?? $wte_cart;
$checkout = new Checkout( $cart );
if ( ! isset( $coupons ) ) {
	$coupons = array();
}
?>
<?php if ( $show_coupon_form || ! empty( $coupons ) ) : ?>
<div class="fts-checkout-tour-details__panel fts-checkout-tour-details__panel--coupon">
	<?php wptravelengine_get_template( 'template-checkout/content-coupon-form.php' ); ?>
</div>
<?php endif; ?>
<div class="fts-checkout-tour-details__panel fts-checkout-tour-details__panel--pricing">
	<div class="wpte-checkout__booking-summary">
		<?php if ( $show_title ) : ?>
			<h5 class="wpte-checkout__booking-summary-title"><?php esc_html_e( 'Package', 'wp-travel-engine' ); ?></h5>
		<?php endif; ?>
		<?php
			$fts_pp_text = (string) apply_filters( 'fts_v2_pay_later_text', '' );
			$fts_pp_text = is_string( $fts_pp_text ) ? trim( $fts_pp_text ) : '';
			$fts_pp_terms = home_url( '/terms-and-conditions/' );
			$fts_pp_cookie = isset( $_COOKIE['ftsPaymentPlan'] ) ? sanitize_text_field( wp_unslash( $_COOKIE['ftsPaymentPlan'] ) ) : '';
			$fts_pp_plan = ( $fts_pp_cookie === 'full' || $fts_pp_cookie === 'deposit' ) ? $fts_pp_cookie : ( $is_partial_payment ? 'deposit' : 'full' );
			$fts_pp_error = isset( $_COOKIE['ftsPaymentPlanError'] ) ? sanitize_text_field( wp_unslash( $_COOKIE['ftsPaymentPlanError'] ) ) : '';
		?>
		<?php if ( $fts_pp_text !== '' ) : ?>
			<div class="fts-pp-toggle" data-current="<?php echo esc_attr( $fts_pp_plan ); ?>">
				<?php if ( $fts_pp_error === '1' ) : ?>
					<div class="fts-pp-toggle__error">
						<?php echo esc_html__( 'Pay later is not available for this booking right now. The full amount will be charged at checkout.', 'fts' ); ?>
					</div>
					<script>
						(function(){try{document.cookie='ftsPaymentPlanError=; path=/; max-age=0; samesite=lax';}catch(e){}})();
					</script>
				<?php endif; ?>
				<div class="fts-pp-toggle__head">
					<strong><?php echo esc_html__( 'Payment option', 'fts' ); ?></strong>
					<a href="<?php echo esc_url( $fts_pp_terms ); ?>" target="_blank" rel="noopener"><?php echo esc_html__( 'Terms', 'fts' ); ?></a>
				</div>
				<label class="fts-pp-toggle__opt">
					<input type="radio" name="ftsPaymentPlanChoice" value="deposit" <?php checked( $fts_pp_plan, 'deposit' ); ?>>
					<span class="fts-pp-toggle__title"><?php echo esc_html__( 'Pay deposit today', 'fts' ); ?></span>
					<span class="fts-pp-toggle__desc"><?php echo esc_html( $fts_pp_text ); ?></span>
				</label>
				<label class="fts-pp-toggle__opt">
					<input type="radio" name="ftsPaymentPlanChoice" value="full" <?php checked( $fts_pp_plan, 'full' ); ?>>
					<span class="fts-pp-toggle__title"><?php echo esc_html__( 'Pay full amount now', 'fts' ); ?></span>
					<span class="fts-pp-toggle__desc"><?php echo esc_html__( 'Pay the full total at checkout.', 'fts' ); ?></span>
				</label>
				<script>
					(function(){
						function setCookie(name,value,maxAge){try{document.cookie=name+'='+encodeURIComponent(String(value||''))+'; path=/; max-age='+(maxAge||86400)+'; samesite=lax';}catch(e){}}
						var inputs = document.querySelectorAll('input[name=\"ftsPaymentPlanChoice\"]');
						for (var i=0;i<inputs.length;i++){
							inputs[i].addEventListener('change', function(){
								var v = String(this.value||'');
								if (v !== 'deposit' && v !== 'full') return;
								setCookie('ftsPaymentPlan', v, 86400);
								location.reload();
							});
						}
					})();
				</script>
			</div>
		<?php endif; ?>
		<div class="wpte-checkout__table-wrap">
			<table class="wpte-checkout__booking-summary-table">
				<?php
				do_action( 'wptravelengine_cart_before_line_items', $cart_line_items );
				foreach ( $cart_line_items as $key => $lines ) {
					do_action( "wptravelengine_cart_before_{$key}_line_items", $cart_line_items );
					if ( 'line_items' === $key ) {
						foreach ( $lines as $line ) {
							foreach ( $line as $_key => $row ) {
								do_action( "wptravelengine_cart_before_{$_key}_line_items", $cart_line_items );
								echo wp_kses(
									is_array( $row ) ? implode( '', $row ) : $row,
									array_merge(
										wp_kses_allowed_html( 'post' ),
										array( 'svg' => array() ),
										array( 'use' => array( 'xlink:href' => array() ) )
									)
								);
								do_action( "wptravelengine_cart_after_{$_key}_line_items", $cart_line_items );
							}
						}
						continue;
					}
					echo wp_kses(
						is_array( $lines ) ? implode( '', $lines ) : $lines,
						array_merge(
							wp_kses_allowed_html( 'post' ),
							array( 'svg' => array() ),
							array( 'use' => array( 'xlink:href' => array() ) )
						)
					);
					do_action( "wptravelengine_cart_after_{$key}_line_items", $cart_line_items );
				}
				do_action( 'wptravelengine_cart_after_line_items', $cart_line_items );

				if ( $wte_cart->is_curr_cart() ) :
					$rows_after_line_items = $checkout->get_fragments_after_line_items();
					foreach ( $rows_after_line_items as $row ) {
						echo wp_kses(
							is_array( $row ) ? implode( '', $row ) : $row,
							array_merge(
								wp_kses_allowed_html( 'post' ),
								array( 'svg' => array() ),
								array( 'use' => array( 'xlink:href' => array() ) )
							)
						);
					}
				elseif ( $is_partial_payment ) :
					if ( 'due' === $wte_cart->get_payment_type() ) :
						?>
						<tr class="wpte-checkout__booking-summary-deposit">
							<td><strong><?php echo esc_html__( 'Deposited:', 'wp-travel-engine' ); ?></strong></td>
							<td><strong>- <?php wptravelengine_the_price( $deposit_amount ); ?></strong></td>
						</tr>
					<?php else : ?>
						<tr class="wpte-checkout__booking-summary-deposit">
							<td><strong><?php echo esc_html__( 'Deposit Today:', 'wp-travel-engine' ); ?></strong></td>
							<td><strong>- <?php wptravelengine_the_price( $deposit_amount ); ?></strong></td>
						</tr>
					<?php endif; ?>
					<tr>
						<td><strong><?php echo esc_html__( 'Amount Due:', 'wp-travel-engine' ); ?></strong></td>
						<td><strong><?php wptravelengine_the_price( $due_amount ); ?></strong></td>
					</tr>
					<?php
				endif;
				do_action( 'wptravelengine_at_cart_summary_end' );
				?>
			</table>
		</div>
	</div>
</div>
