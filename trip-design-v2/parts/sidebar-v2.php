<?php
/**
 * Sidebar V2 — Sticky Booking Sidebar (CRO Optimized)
 * Variables provided by layout-controller.php via extract()
 *
 * @var bool                              $use_frontend_view_model
 * @var array<string,mixed>               $vm_cta
 * @var array<string,mixed>               $vm_trust
 * @var float|int|string                  $avg_rating
 * @var int|string                        $review_count
 * @var float|int|string                  $old_price
 * @var float|int|string                  $display_price
 * @var int|string                        $discount_pct
 * @var string                            $free_cancellation_text
 * @var int|string                        $cancel_hours
 * @var bool                              $pp_eligible
 * @var string                            $pay_later_text
 * @var string                            $duration_text
 * @var array<int,array<string,mixed>>    $trip_facts_items
 * @var string|array                      $cost_includes
 * @var string                            $enquiry_enabled
 */
if ( ! defined( 'ABSPATH' ) ) exit;

$fts_use_frontend_vm = ! empty( $use_frontend_view_model );
$fts_vm_cta   = ( isset( $vm_cta ) && is_array( $vm_cta ) ) ? $vm_cta : array();
$fts_vm_trust = ( isset( $vm_trust ) && is_array( $vm_trust ) ) ? $vm_trust : array();

$fts_vm_scalar = static function( $source, $key, $fallback = '' ) {
    if ( ! is_array( $source ) || ! array_key_exists( $key, $source ) || ! is_scalar( $source[ $key ] ) ) {
        return $fallback;
    }

    $value = trim( (string) $source[ $key ] );
    return $value !== '' ? $value : $fallback;
};

$fts_vm_text_list = static function( $source, $key ) {
    $items = array();

    if ( ! is_array( $source ) || empty( $source[ $key ] ) || ! is_array( $source[ $key ] ) ) {
        return $items;
    }

    foreach ( $source[ $key ] as $raw_item ) {
        $text = '';

        if ( is_scalar( $raw_item ) ) {
            $text = trim( (string) $raw_item );
        } elseif ( is_array( $raw_item ) ) {
            foreach ( array( 'text', 'label', 'value', 'title' ) as $item_key ) {
                if ( isset( $raw_item[ $item_key ] ) && is_scalar( $raw_item[ $item_key ] ) ) {
                    $text = trim( (string) $raw_item[ $item_key ] );
                    if ( $text !== '' ) {
                        break;
                    }
                }
            }
        }

        if ( $text !== '' ) {
            $items[] = $text;
        }
    }

    return $items;
};

$fts_sidebar_rating = isset( $avg_rating ) ? floatval( $avg_rating ) : 0;
if ( $fts_use_frontend_vm && isset( $fts_vm_trust['rating'] ) && is_numeric( $fts_vm_trust['rating'] ) ) {
    $fts_vm_rating = floatval( $fts_vm_trust['rating'] );
    if ( $fts_vm_rating > 0 ) {
        $fts_sidebar_rating = $fts_vm_rating;
    }
}

$fts_sidebar_review_count = isset( $review_count ) ? max( 0, intval( $review_count ) ) : 0;
if ( $fts_use_frontend_vm && isset( $fts_vm_trust['reviews_count'] ) && is_numeric( $fts_vm_trust['reviews_count'] ) ) {
    $fts_vm_reviews_count = max( 0, intval( $fts_vm_trust['reviews_count'] ) );
    if ( $fts_vm_reviews_count > 0 || $fts_sidebar_rating > 0 ) {
        $fts_sidebar_review_count = $fts_vm_reviews_count;
    }
}

$fts_cta_badge = $fts_use_frontend_vm ? $fts_vm_scalar( $fts_vm_cta, 'badge', '' ) : '';
$fts_cta_headline = $fts_use_frontend_vm ? $fts_vm_scalar( $fts_vm_cta, 'price_text', '' ) : '';
if ( $fts_cta_headline === '' ) {
    $fts_cta_headline = $fts_use_frontend_vm ? $fts_vm_scalar( $fts_vm_cta, 'headline', '' ) : '';
}

$fts_cta_subheadline = $fts_use_frontend_vm ? $fts_vm_scalar( $fts_vm_cta, 'supporting_text', '' ) : '';
if ( $fts_cta_subheadline === '' ) {
    $fts_cta_subheadline = $fts_use_frontend_vm ? $fts_vm_scalar( $fts_vm_cta, 'subheadline', '' ) : '';
}

$fts_cta_primary_button_text = $fts_use_frontend_vm ? $fts_vm_scalar( $fts_vm_cta, 'primary', '' ) : '';
if ( $fts_cta_primary_button_text === '' ) {
    $fts_cta_primary_button_text = $fts_use_frontend_vm ? $fts_vm_scalar( $fts_vm_cta, 'primary_button_text', '' ) : '';
}

$fts_cta_secondary_button_text = $fts_use_frontend_vm ? $fts_vm_scalar( $fts_vm_cta, 'secondary', '' ) : '';
if ( $fts_cta_secondary_button_text === '' ) {
    $fts_cta_secondary_button_text = $fts_use_frontend_vm ? $fts_vm_scalar( $fts_vm_cta, 'secondary_button_text', '' ) : '';
}

$fts_cta_urgency = $fts_use_frontend_vm ? $fts_vm_scalar( $fts_vm_cta, 'urgency', '' ) : '';
$fts_cta_trust_points = $fts_use_frontend_vm ? $fts_vm_text_list( $fts_vm_trust, 'badges' ) : array();
if ( empty( $fts_cta_trust_points ) ) {
    $fts_cta_trust_points = $fts_use_frontend_vm ? $fts_vm_text_list( $fts_vm_cta, 'trust_points' ) : array();
}

$fts_sidebar_cancel_text = '';
if ( isset( $free_cancellation_text ) && is_string( $free_cancellation_text ) && trim( $free_cancellation_text ) !== '' ) {
    $fts_sidebar_cancel_text = trim( $free_cancellation_text );
} elseif ( isset( $cancel_hours ) && intval( $cancel_hours ) > 0 ) {
    $fts_sidebar_cancel_text = sprintf( esc_html__( 'Free cancellation up to %s hours in advance', 'fts' ), intval( $cancel_hours ) );
}

if ( $fts_cta_primary_button_text === '' ) {
    $fts_cta_primary_button_text = esc_html__( 'Check availability', 'fts' );
}

if ( $fts_cta_secondary_button_text === '' ) {
    $fts_cta_secondary_button_text = esc_html__( 'Support via WhatsApp', 'fts' );
}

$wa_raw    = trim( (string) apply_filters( 'fts_whatsapp_number', '+201000479285' ) );
$wa_digits = preg_replace( '/\D+/', '', $wa_raw );

$fts_sidebar_price_text = '';
if ( isset( $display_price ) && is_numeric( $display_price ) ) {
    $fts_sidebar_price_text = (string) wte_get_formated_price( $display_price );
}
$fts_sidebar_is_eur_price = ( $fts_sidebar_price_text !== '' )
    && ( strpos( $fts_sidebar_price_text, '€' ) !== false || stripos( $fts_sidebar_price_text, 'EUR' ) !== false );

if ( $fts_cta_subheadline !== '' && ( strpos( $fts_cta_subheadline, '€' ) !== false || stripos( $fts_cta_subheadline, 'EUR' ) !== false || stripos( $fts_cta_subheadline, '&euro;' ) !== false || stripos( $fts_cta_subheadline, '&#8364;' ) !== false || stripos( $fts_cta_subheadline, '&#x20ac;' ) !== false ) && ! $fts_sidebar_is_eur_price ) {
    $fts_cta_subheadline = '';
}

$fts_sidebar_duration_from_facts = '';
if ( isset( $trip_facts_items ) && is_array( $trip_facts_items ) ) {
    foreach ( $trip_facts_items as $sbit ) {
        if ( ! is_array( $sbit ) ) {
            continue;
        }
        $sblbl = strtolower( trim( (string) ( $sbit['label'] ?? '' ) ) );
        $sbval = trim( (string) ( $sbit['value'] ?? '' ) );
        if ( $sblbl === '' || $sbval === '' ) {
            continue;
        }
        if ( strpos( $sblbl, 'duration' ) !== false || strpos( $sblbl, 'مدة' ) !== false ) {
            $fts_sidebar_duration_from_facts = $sbval;
            break;
        }
    }
}

$fts_sidebar_duration_value = '';
if ( isset( $duration_text ) && is_string( $duration_text ) && trim( $duration_text ) !== '' ) {
    $fts_sidebar_duration_value = trim( $duration_text );
}
if ( $fts_sidebar_duration_from_facts !== '' ) {
    $d_main = strtolower( $fts_sidebar_duration_value );
    $d_fact = strtolower( $fts_sidebar_duration_from_facts );
    $fact_has_hours = ( strpos( $d_fact, 'hour' ) !== false || strpos( $d_fact, 'hours' ) !== false || strpos( $d_fact, 'ساعة' ) !== false );
    $main_has_days  = ( strpos( $d_main, 'day' ) !== false || strpos( $d_main, 'days' ) !== false || strpos( $d_main, 'يوم' ) !== false );
    if ( $fts_sidebar_duration_value === '' || ( $main_has_days && $fact_has_hours ) ) {
        $fts_sidebar_duration_value = $fts_sidebar_duration_from_facts;
    }
}
?>

<div class="fts-v2-sidebar-wrapper" id="fts-v2-booking-sidebar">
    <div class="fts-v2-sidebar-sticky">

        <div class="fts-v2-booking-card">

            <!-- 1. Price Header -->
            <div class="fts-v2-booking-price-top">
                <div class="fts-v2-booking-price-header">
                    <div class="fts-v2-booking-from"><?php echo esc_html__( 'From', 'fts' ); ?></div>
                    <?php if ( $fts_sidebar_rating > 0 ) : ?>
                    <a class="fts-v2-booking-rating fts-v2-meta-tidx" href="#fts-v2-sec-reviews">
                        <i class="fa fa-star"></i> <?php echo esc_html( number_format( $fts_sidebar_rating, 1 ) ); ?>
                        <span>(<?php echo intval( $fts_sidebar_review_count ); ?>)</span>
                    </a>
                    <?php endif; ?>
                </div>
                <div class="fts-v2-booking-price-row">
                    <?php if ( $old_price > 0 ) : ?>
                        <span class="fts-v2-booking-old-price"><?php echo esc_html( wte_get_formated_price( $old_price ) ); ?></span>
                    <?php endif; ?>
                    <span class="fts-v2-booking-current-price"><?php echo esc_html( wte_get_formated_price( $display_price ) ); ?></span>
                    <span class="fts-v2-booking-per-person"><?php echo esc_html__( '/ person', 'fts' ); ?></span>
                </div>
                <?php if ( $discount_pct > 0 ) : ?>
                <div class="fts-v2-booking-save-badge"><?php echo esc_html__( 'SAVE', 'fts' ); ?> <?php echo intval( $discount_pct ); ?>%</div>
                <?php endif; ?>
            </div>

            <!-- 2. Social Proof Bar (removed) -->

            <!-- 3. Cancellation Policy (removed) -->

            <!-- 4. Calendar Accordion -->
            <div class="fts-v2-calendar-section fts-v2-cal-collapsed" id="fts-v2-cal-accordion">
                <button type="button" class="fts-v2-cal-toggle" id="fts-v2-cal-toggle">
                    <div class="fts-v2-cal-toggle-left">
                        <div class="fts-v2-cal-icon-wrap">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                        </div>
                        <div class="fts-v2-cal-toggle-text">
                            <span class="fts-v2-cal-label"><?php echo esc_html__( 'Select Date', 'fts' ); ?></span>
                            <span class="fts-v2-cal-selected" id="fts-v2-cal-selected"><?php echo esc_html__( 'Tap to choose your travel date', 'fts' ); ?></span>
                        </div>
                    </div>
                    <svg class="fts-v2-cal-chevron" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m6 9 6 6 6-6"/></svg>
                </button>
                <div class="fts-v2-cal-body" id="fts-v2-cal-body">
                    <div id="fts-v2-datepicker"></div>
                    <div class="fts-v2-calendar-legend">
                        <span class="fts-v2-legend-item fts-v2-legend-low"><span class="fts-v2-legend-dot"></span> <?php echo esc_html__( 'Low availability', 'fts' ); ?></span>
                        <span class="fts-v2-legend-item fts-v2-legend-best"><span class="fts-v2-legend-dot"></span> <?php echo esc_html__( 'Best price', 'fts' ); ?></span>
                    </div>
                </div>
            </div>

            <!-- 5. Travelers Accordion -->
            <div class="fts-v2-travelers-accordion fts-v2-trav-collapsed" id="fts-v2-travelers-accordion">
                <button type="button" class="fts-v2-trav-toggle" id="fts-v2-trav-toggle">
                    <div class="fts-v2-trav-toggle-left">
                        <div class="fts-v2-trav-icon-wrap">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                        </div>
                        <div class="fts-v2-trav-toggle-text">
                            <span class="fts-v2-trav-label"><?php echo esc_html__( 'Travelers', 'fts' ); ?></span>
                            <span class="fts-v2-trav-summary" id="fts-v2-trav-summary"><?php echo esc_html__( '1 Adult', 'fts' ); ?></span>
                        </div>
                    </div>
                    <svg class="fts-v2-trav-chevron" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m6 9 6 6 6-6"/></svg>
                </button>
                <div class="fts-v2-trav-body" id="fts-v2-trav-body">
                    <div class="fts-v2-trav-row">
                        <div class="fts-v2-trav-row-info">
                            <span class="fts-v2-trav-row-label"><?php echo esc_html__( 'Adults', 'fts' ); ?></span>
                            <span class="fts-v2-trav-row-hint"><?php echo esc_html__( 'Age 12+', 'fts' ); ?></span>
                        </div>
                        <div class="fts-v2-trav-row-counter">
                            <button type="button" class="fts-v2-trav-btn" data-type="adults" data-dir="minus">−</button>
                            <span class="fts-v2-trav-num" id="fts-v2-adults-count">1</span>
                            <button type="button" class="fts-v2-trav-btn" data-type="adults" data-dir="plus">+</button>
                        </div>
                    </div>
                    <div class="fts-v2-trav-row">
                        <div class="fts-v2-trav-row-info">
                            <span class="fts-v2-trav-row-label"><?php echo esc_html__( 'Children', 'fts' ); ?></span>
                            <span class="fts-v2-trav-row-hint"><?php echo esc_html__( 'Age 2–11', 'fts' ); ?></span>
                        </div>
                        <div class="fts-v2-trav-row-counter">
                            <button type="button" class="fts-v2-trav-btn" data-type="children" data-dir="minus">−</button>
                            <span class="fts-v2-trav-num" id="fts-v2-children-count">0</span>
                            <button type="button" class="fts-v2-trav-btn" data-type="children" data-dir="plus">+</button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 6. Trip Highlights (mirrors the header about-grid, minimal icons) -->
            <?php
                $sb_items = array();

                if ( ! empty( $fts_cta_trust_points ) ) {
                    foreach ( $fts_cta_trust_points as $fts_cta_trust_point ) {
                        $sb_items[] = array(
                            'icon' => '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"/></svg>',
                            'text' => $fts_cta_trust_point,
                        );
                    }
                } else {
                    if ( $fts_sidebar_cancel_text !== '' ) {
                        $sb_items[] = array(
                            'icon' => '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2"/><path d="M16 2v4"/><path d="M8 2v4"/><path d="M3 10h18"/><path d="M9 16l2 2 4-4"/></svg>',
                            'text' => $fts_sidebar_cancel_text,
                        );
                    }

                    if ( ! empty( $pp_eligible ) && isset( $pay_later_text ) && is_string( $pay_later_text ) && trim( $pay_later_text ) !== '' ) {
                        $sb_pay_later_text = trim( $pay_later_text );
                        $sb_items[] = array(
                            'icon' => '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="5" width="20" height="14" rx="2"/><path d="M2 10h20"/></svg>',
                            'text' => $sb_pay_later_text,
                        );
                    }

                    if ( $fts_sidebar_duration_value !== '' ) {
                        $sb_items[] = array(
                            'icon' => '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>',
                            'text' => __( 'Duration', 'fts' ) . ' ' . esc_html( $fts_sidebar_duration_value ),
                        );
                    }

                    $sb_lang   = '';
                    $sb_pickup = '';
                    if ( isset( $trip_facts_items ) && is_array( $trip_facts_items ) ) {
                        foreach ( $trip_facts_items as $sbit ) {
                            if ( ! is_array( $sbit ) ) continue;
                            $sblbl = strtolower( trim( (string) ( $sbit['label'] ?? '' ) ) );
                            $sbval = trim( (string) ( $sbit['value'] ?? '' ) );
                            if ( $sblbl === '' || $sbval === '' ) continue;
                            if ( $sb_lang === '' && ( strpos( $sblbl, 'language' ) !== false || strpos( $sblbl, 'languages' ) !== false || strpos( $sblbl, 'لغة' ) !== false ) ) $sb_lang = $sbval;
                            if ( $sb_pickup === '' && ( strpos( $sblbl, 'pickup' ) !== false || strpos( $sblbl, 'meeting' ) !== false || strpos( $sblbl, 'start' ) !== false || strpos( $sblbl, 'استلام' ) !== false ) ) $sb_pickup = $sbval;
                        }
                    }

                    if ( $sb_lang !== '' ) {
                        $sb_items[] = array(
                            'icon' => '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 8l6 6"/><path d="M4 14l6-6 2-3"/><path d="M2 5h12"/><path d="M7 2h1"/><path d="M22 22l-5-10-5 10"/><path d="M14 18h6"/></svg>',
                            'text' => __( 'Live tour guide', 'fts' ) . ' — ' . $sb_lang,
                        );
                    }

                    if ( $sb_pickup !== '' ) {
                        $sb_items[] = array(
                            'icon' => '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 17h2c.6 0 1-.4 1-1v-3c0-.9-.7-1.7-1.5-1.9L18 10l-2-4H7L5 10l-2.5 1.1C1.7 11.3 1 12.1 1 13v3c0 .6.4 1 1 1h2"/><circle cx="7" cy="17" r="2"/><circle cx="17" cy="17" r="2"/><path d="M5 10h14"/></svg>',
                            'text' => __( 'Pickup included', 'fts' ),
                        );
                    }

                    $sb_ci = is_array( $cost_includes ?? '' ) ? implode( "\n", $cost_includes ) : (string) ( $cost_includes ?? '' );
                    $sb_ci_lc = strtolower( $sb_ci );
                    $sb_meal_title = '';
                    if ( strpos( $sb_ci_lc, 'lunch' ) !== false && strpos( $sb_ci_lc, 'breakfast' ) !== false ) {
                        $sb_meal_title = __( 'Breakfast & Lunch included', 'fts' );
                    } elseif ( strpos( $sb_ci_lc, 'lunch' ) !== false ) {
                        $sb_meal_title = __( 'Lunch included', 'fts' );
                    } elseif ( strpos( $sb_ci_lc, 'breakfast' ) !== false ) {
                        $sb_meal_title = __( 'Breakfast included', 'fts' );
                    } elseif ( strpos( $sb_ci_lc, 'meal' ) !== false ) {
                        $sb_meal_title = __( 'Meals included', 'fts' );
                    }
                    if ( $sb_meal_title !== '' ) {
                        $sb_items[] = array(
                            'icon' => '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 2v7c0 1.1.9 2 2 2h4a2 2 0 0 0 2-2V2"/><path d="M7 2v20"/><path d="M21 15V2a5 5 0 0 0-5 5v6c0 1.1.9 2 2 2h3zm0 0v7"/></svg>',
                            'text' => $sb_meal_title,
                        );
                    }
                }
            ?>
            <?php if ( ! empty( $sb_items ) ) : ?>
            <div class="fts-v2-booking-trust fts-v2-sidebar-highlights">
                <?php foreach ( $sb_items as $sbi ) : ?>
                <div class="fts-v2-booking-trust-item">
                    <?php echo $sbi['icon']; ?>
                    <?php echo esc_html( $sbi['text'] ); ?>
                </div>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>

            <!-- 7. CTA Button -->
            <div class="fts-v2-booking-cta">
                <?php if ( $fts_cta_badge !== '' ) : ?>
                <div class="fts-v2-booking-save-badge"><?php echo esc_html( $fts_cta_badge ); ?></div>
                <?php endif; ?>
                <?php if ( $fts_cta_headline !== '' ) : ?>
                <div class="fts-v2-booking-micro-title"><?php echo esc_html( $fts_cta_headline ); ?></div>
                <?php endif; ?>
                <?php if ( $fts_cta_subheadline !== '' ) : ?>
                <div class="fts-v2-booking-per-person"><?php echo esc_html( $fts_cta_subheadline ); ?></div>
                <?php endif; ?>
                <?php if ( $fts_cta_urgency !== '' ) : ?>
                <div class="fts-v2-booking-save-badge"><?php echo esc_html( $fts_cta_urgency ); ?></div>
                <?php endif; ?>
                <button type="button" class="fts-v2-check-btn fts-bm-trigger"><?php echo esc_html( $fts_cta_primary_button_text ); ?></button>
            </div>

            <!-- 8. Book with Confidence (WhatsApp + Duration only) -->
            <div class="fts-v2-booking-micro">
                <div class="fts-v2-booking-micro-title"><?php echo esc_html__( 'Book with confidence', 'fts' ); ?></div>
                <?php
                    if ( $fts_sidebar_duration_value !== '' ) :
                ?>
                <div class="fts-v2-booking-micro-item">
                    <i class="fa fa-clock-o"></i>
                    <span><?php echo esc_html( sprintf( __( 'Duration: %s', 'fts' ), $fts_sidebar_duration_value ) ); ?></span>
                </div>
                <?php endif; ?>
                <?php if ( $wa_digits ) : ?>
                <div class="fts-v2-booking-micro-item">
                    <i class="fa fa-whatsapp"></i>
                    <a class="fts-v2-booking-micro-link" href="<?php echo esc_url( 'https://wa.me/' . $wa_digits ); ?>" target="_blank" rel="noopener noreferrer nofollow" data-fts-wa-source="sidebar_micro">
                        <?php echo esc_html( $fts_cta_secondary_button_text ); ?>
                    </a>
                </div>
                <?php endif; ?>
            </div>

            <!-- 9. Payment Icons (SVG) -->
            <div class="fts-v2-payment-icons">
                <svg class="fts-v2-pay-icon" viewBox="0 0 38 24" width="38" height="24" role="img" aria-label="Visa"><rect width="38" height="24" rx="3" fill="#1a1f71"/><text x="19" y="15.5" text-anchor="middle" fill="#fff" font-family="Arial,sans-serif" font-size="10" font-weight="700" font-style="italic">VISA</text></svg>
                <svg class="fts-v2-pay-icon" viewBox="0 0 38 24" width="38" height="24" role="img" aria-label="Mastercard"><rect width="38" height="24" rx="3" fill="#252525"/><circle cx="15" cy="12" r="7" fill="#eb001b"/><circle cx="23" cy="12" r="7" fill="#f79e1b"/><path d="M19 6.7a7 7 0 0 1 0 10.6 7 7 0 0 1 0-10.6z" fill="#ff5f00"/></svg>
                <svg class="fts-v2-pay-icon" viewBox="0 0 38 24" width="38" height="24" role="img" aria-label="PayPal"><rect width="38" height="24" rx="3" fill="#f5f7fa"/><text x="19" y="15" text-anchor="middle" fill="#003087" font-family="Arial,sans-serif" font-size="8.5" font-weight="700">Pay<tspan fill="#009cde">Pal</tspan></text></svg>
                <svg class="fts-v2-pay-icon" viewBox="0 0 38 24" width="38" height="24" role="img" aria-label="Stripe"><rect width="38" height="24" rx="3" fill="#635bff"/><text x="19" y="15" text-anchor="middle" fill="#fff" font-family="Arial,sans-serif" font-size="9" font-weight="700">stripe</text></svg>
            </div>
        </div>

        <?php if ( $enquiry_enabled === 'on' ) : ?>
        <div class="fts-v2-enquiry-card">
            <h4><i class="fa fa-comments-o"></i> <?php echo esc_html__( 'Have a Question?', 'fts' ); ?></h4>
            <p><?php echo esc_html__( 'Our travel experts are here to help.', 'fts' ); ?></p>
            <?php echo do_shortcode( '[WP_TRAVEL_ENGINE_TRIP_ENQUIRY_FORM use_current="yes"]' ); ?>
        </div>
        <?php endif; ?>

    </div>
</div>
