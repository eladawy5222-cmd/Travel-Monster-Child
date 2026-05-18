<?php
/**
 * Header V2 - Breadcrumbs + Hero Title + Rating + Meta Badges
 * Variables provided by layout-controller.php via extract()
 */
if ( ! defined( 'ABSPATH' ) ) exit;
?>

<?php
    $fts_has_price = ( isset( $display_price ) && floatval( $display_price ) > 0 );
    $fts_cancel_text = '';
    if ( isset( $free_cancellation_text ) && is_string( $free_cancellation_text ) && trim( $free_cancellation_text ) !== '' ) {
        $fts_cancel_text = trim( $free_cancellation_text );
    } elseif ( isset( $cancel_hours ) && intval( $cancel_hours ) > 0 ) {
        $fts_cancel_text = sprintf( esc_html__( 'Free cancellation up to %s hours in advance', 'fts' ), intval( $cancel_hours ) );
    }
    $fts_has_cancel = ( $fts_cancel_text !== '' );
    $fts_terms_url = ( isset( $terms_url ) && is_string( $terms_url ) && trim( $terms_url ) !== '' ) ? $terms_url : home_url( '/terms-and-conditions/' );

    $fts_has_pay_later = false;
    $fts_pay_later_text = '';
    if ( ! empty( $pp_eligible ) && isset( $pay_later_text ) && is_string( $pay_later_text ) && trim( $pay_later_text ) !== '' ) {
        $fts_has_pay_later = true;
        $fts_pay_later_text = trim( $pay_later_text );
    }

    $fts_subtitle = '';
    if ( isset( $overview_excerpt ) && is_string( $overview_excerpt ) ) $fts_subtitle = trim( $overview_excerpt );
    if ( $fts_subtitle === '' && isset( $bold_promise ) && is_string( $bold_promise ) ) $fts_subtitle = trim( $bold_promise );

    $fts_lang_value = '';
    $fts_pickup_value = '';
    if ( isset( $trip_facts_items ) && is_array( $trip_facts_items ) ) {
        foreach ( $trip_facts_items as $it ) {
            if ( ! is_array( $it ) ) continue;
            $lbl = strtolower( trim( (string) ( $it['label'] ?? '' ) ) );
            $val = trim( (string) ( $it['value'] ?? '' ) );
            if ( $lbl === '' || $val === '' ) continue;
            if ( $fts_lang_value === '' && ( strpos( $lbl, 'language' ) !== false || strpos( $lbl, 'languages' ) !== false || strpos( $lbl, 'لغة' ) !== false ) ) $fts_lang_value = $val;
            if ( $fts_pickup_value === '' && ( strpos( $lbl, 'pickup' ) !== false || strpos( $lbl, 'meeting' ) !== false || strpos( $lbl, 'start' ) !== false || strpos( $lbl, 'meet' ) !== false || strpos( $lbl, 'استلام' ) !== false || strpos( $lbl, 'التقاء' ) !== false || strpos( $lbl, 'نقطة' ) !== false ) ) $fts_pickup_value = $val;
            if ( $fts_lang_value !== '' && $fts_pickup_value !== '' ) break;
        }
    }
?>

<div class="fts-v2-hero">
    <div class="fts-v2-container">
        <nav class="fts-v2-breadcrumbs">
            <a href="<?php echo esc_url( home_url() ); ?>"><?php echo esc_html__( 'Home', 'fts' ); ?></a>
            <?php foreach ( $dest_chain as $dc ) : ?>
                <span class="fts-v2-bc-sep">/</span>
                <a href="<?php echo esc_url( $dc['url'] ); ?>"><?php echo esc_html( $dc['name'] ); ?></a>
            <?php endforeach; ?>
            <?php if ( $last_crumb ) : ?>
                <span class="fts-v2-bc-sep">/</span>
                <span class="fts-v2-bc-current"><?php echo esc_html( $last_crumb ); ?></span>
            <?php endif; ?>
        </nav>
    </div>

    <div class="fts-v2-hero-media" aria-label="<?php echo esc_attr__( 'Trip photos', 'fts' ); ?>">
        <?php
            $fts_gallery_mode = 'hero';
            include get_stylesheet_directory() . '/trip-design-v2/parts/gallery-v2.php';
        ?>
    </div>

    <div class="fts-v2-container">
        <div class="fts-v2-hero-body">
            <div class="fts-v2-hero-headline-row">
                <div class="fts-v2-hero-headline-text">
                    <h1 class="fts-v2-trip-title"><?php the_title(); ?></h1>

                    <?php if ( $avg_rating > 0 ) : ?>
                    <div class="fts-v2-hero-rating" aria-label="<?php echo esc_attr__( 'Customer rating', 'fts' ); ?>">
                        <div class="fts-v2-stars-inline" aria-hidden="true">
                            <?php for ( $i = 1; $i <= 5; $i++ ) : ?>
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="<?php echo $i <= round( $avg_rating ) ? '#FF8C00' : 'none'; ?>" stroke="#FF8C00" stroke-width="2"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
                            <?php endfor; ?>
                        </div>
                        <strong><?php echo number_format( $avg_rating, 1 ); ?></strong>
                        <a href="#fts-v2-sec-reviews" class="fts-v2-hero-reviews-link"><?php echo esc_html( sprintf( _n( '%s review', '%s reviews', $review_count, 'fts' ), number_format_i18n( $review_count ) ) ); ?></a>
                    </div>
                    <?php endif; ?>

                    <?php if ( $fts_subtitle !== '' ) : ?>
                    <p class="fts-v2-hero-subtitle"><?php echo esc_html( $fts_subtitle ); ?></p>
                    <?php endif; ?>
                </div>

                <?php if ( $fts_has_price ) : ?>
                <aside class="fts-v2-hero-price" aria-label="<?php echo esc_attr__( 'Price', 'fts' ); ?>">
                    <span class="fts-v2-hero-price-from"><?php echo esc_html__( 'From', 'fts' ); ?></span>
                    <div class="fts-v2-hero-price-main">
                        <?php if ( $old_price > 0 ) : ?>
                        <span class="fts-v2-hero-price-old"><?php echo esc_html( wte_get_formated_price( $old_price ) ); ?></span>
                        <?php endif; ?>
                        <span class="fts-v2-hero-price-current"><?php echo esc_html( wte_get_formated_price( $display_price ) ); ?></span>
                    </div>
                    <span class="fts-v2-hero-price-pp"><?php echo esc_html__( 'per person', 'fts' ); ?></span>
                    <?php if ( $discount_pct > 0 ) : ?>
                    <span class="fts-v2-hero-price-save"><?php echo esc_html__( 'SAVE', 'fts' ); ?> <?php echo intval( $discount_pct ); ?>%</span>
                    <?php endif; ?>
                    <button type="button" class="fts-v2-hero-price-cta fts-bm-trigger" aria-label="<?php echo esc_attr__( 'Check availability', 'fts' ); ?>">
                        <span><?php echo esc_html__( 'Check availability', 'fts' ); ?></span>
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <path d="M5 12h14"/>
                            <path d="m12 5 7 7-7 7"/>
                        </svg>
                    </button>
                </aside>
                <?php endif; ?>
            </div>

            <?php
                $about_items = array();

                if ( $fts_has_cancel ) {
                    $about_items[] = array(
                        'icon'  => '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2"/><path d="M16 2v4"/><path d="M8 2v4"/><path d="M3 10h18"/><path d="M9 16l2 2 4-4"/></svg>',
                        'title' => __( 'Free cancellation', 'fts' ),
                        'desc'  => $fts_cancel_text,
                    );
                }

                if ( $fts_has_pay_later ) {
                    $pl = isset( $fts_pay_later_text ) ? trim( (string) $fts_pay_later_text ) : '';
                    $pl_lc = strtolower( $pl );
                    if ( $pl_lc === strtolower( (string) __( 'Reserve now & pay later', 'fts' ) ) ) $pl = '';
                    $about_items[] = array(
                        'icon'  => '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="5" width="20" height="14" rx="2"/><path d="M2 10h20"/></svg>',
                        'title' => __( 'Reserve now & pay later', 'fts' ),
                        'desc'  => $pl !== '' ? $pl : __( 'Keep your travel plans flexible — book your spot and pay nothing today.', 'fts' ),
                    );
                }

                if ( ! empty( $duration_text ) ) {
                    $about_items[] = array(
                        'icon'  => '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>',
                        'title' => __( 'Duration', 'fts' ) . ' ' . esc_html( $duration_text ),
                        'desc'  => __( 'Check availability to see starting times', 'fts' ),
                    );
                }

                if ( $fts_lang_value !== '' ) {
                    $about_items[] = array(
                        'icon'  => '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 8l6 6"/><path d="M4 14l6-6 2-3"/><path d="M2 5h12"/><path d="M7 2h1"/><path d="M22 22l-5-10-5 10"/><path d="M14 18h6"/></svg>',
                        'title' => __( 'Live tour guide', 'fts' ),
                        'desc'  => $fts_lang_value,
                    );
                }

                if ( $fts_pickup_value !== '' ) {
                    $about_items[] = array(
                        'icon'  => '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 17h2c.6 0 1-.4 1-1v-3c0-.9-.7-1.7-1.5-1.9L18 10l-2-4H7L5 10l-2.5 1.1C1.7 11.3 1 12.1 1 13v3c0 .6.4 1 1 1h2"/><circle cx="7" cy="17" r="2"/><circle cx="17" cy="17" r="2"/><path d="M5 10h14"/></svg>',
                        'title' => __( 'Pickup included', 'fts' ),
                        'desc'  => __( 'Check availability for details', 'fts' ),
                    );
                }

                $ci_str_about = is_array( $cost_includes ?? '' ) ? implode( "\n", $cost_includes ) : (string) ( $cost_includes ?? '' );
                $ci_lc_about  = strtolower( $ci_str_about );

                $has_meals_about = false;
                $meals_title = '';
                if ( strpos( $ci_lc_about, 'lunch' ) !== false && strpos( $ci_lc_about, 'breakfast' ) !== false ) {
                    $has_meals_about = true; $meals_title = __( 'Breakfast & Lunch included', 'fts' );
                } elseif ( strpos( $ci_lc_about, 'lunch' ) !== false ) {
                    $has_meals_about = true; $meals_title = __( 'Lunch included', 'fts' );
                } elseif ( strpos( $ci_lc_about, 'breakfast' ) !== false ) {
                    $has_meals_about = true; $meals_title = __( 'Breakfast included', 'fts' );
                } elseif ( strpos( $ci_lc_about, 'meal' ) !== false ) {
                    $has_meals_about = true; $meals_title = __( 'Meals included', 'fts' );
                }
                if ( $has_meals_about ) {
                    $about_items[] = array(
                        'icon'  => '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 2v7c0 1.1.9 2 2 2h4a2 2 0 0 0 2-2V2"/><path d="M7 2v20"/><path d="M21 15V2a5 5 0 0 0-5 5v6c0 1.1.9 2 2 2h3zm0 0v7"/></svg>',
                        'title' => $meals_title,
                        'desc'  => '',
                    );
                }

                $about_items = array_slice( $about_items, 0, 6 );
            ?>
            <?php if ( ! empty( $about_items ) ) : ?>
            <div class="fts-v2-about-grid">
                <?php foreach ( $about_items as $ai ) : ?>
                <div class="fts-v2-about-item">
                    <div class="fts-v2-about-icon"><?php echo $ai['icon']; ?></div>
                    <div class="fts-v2-about-text">
                        <span class="fts-v2-about-title"><?php echo esc_html( $ai['title'] ); ?></span>
                        <?php if ( ! empty( $ai['desc'] ) ) : ?>
                        <span class="fts-v2-about-desc"><?php echo esc_html( $ai['desc'] ); ?></span>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>


        </div>
    </div>
</div>
