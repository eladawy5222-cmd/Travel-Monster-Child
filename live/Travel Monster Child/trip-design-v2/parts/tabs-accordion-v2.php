<?php
/**
 * Content Sections V2 - Collapsible Accordion (Single Open)
 * Variables provided by layout-controller.php via extract()
 */
if ( ! defined( 'ABSPATH' ) ) exit;
?>

<?php
/* ── Gallery + Description + About-items (above accordion) ── */

// Compute description/subtitle
$fts_content_subtitle = '';
if ( isset( $overview_excerpt ) && is_string( $overview_excerpt ) ) $fts_content_subtitle = trim( $overview_excerpt );
if ( $fts_content_subtitle === '' && isset( $bold_promise ) && is_string( $bold_promise ) ) $fts_content_subtitle = trim( $bold_promise );

// Compute about-items
$fts_col_cancel_text = '';
if ( isset( $free_cancellation_text ) && is_string( $free_cancellation_text ) && trim( $free_cancellation_text ) !== '' ) {
    $fts_col_cancel_text = trim( $free_cancellation_text );
} elseif ( isset( $cancel_hours ) && intval( $cancel_hours ) > 0 ) {
    $fts_col_cancel_text = sprintf( esc_html__( 'Free cancellation up to %s hours in advance', 'fts' ), intval( $cancel_hours ) );
}
if ( $fts_col_cancel_text === '' && function_exists( 'fts_v2_resolve_cancellation_desc' ) ) {
    $fts_col_cancel_text = fts_v2_resolve_cancellation_desc(
        intval( $trip_id ?? get_the_ID() ),
        (string) ( $free_cancellation_text ?? '' ),
        intval( $cancel_hours ?? 0 ),
        isset( $trip_facts_items ) && is_array( $trip_facts_items ) ? $trip_facts_items : array(),
        isset( $at_a_glance ) && is_array( $at_a_glance ) ? $at_a_glance : array()
    );
}
$fts_col_has_pay_later = ! empty( $pp_eligible ) && isset( $pay_later_text ) && trim( (string) $pay_later_text ) !== '';
$fts_col_pay_later_text = $fts_col_has_pay_later ? trim( (string) $pay_later_text ) : '';

$fts_col_lang_value   = '';
$fts_col_pickup_value = '';
if ( isset( $trip_facts_items ) && is_array( $trip_facts_items ) ) {
    foreach ( $trip_facts_items as $it ) {
        if ( ! is_array( $it ) ) continue;
        $lbl = strtolower( trim( (string) ( $it['label'] ?? '' ) ) );
        $val = trim( (string) ( $it['value'] ?? '' ) );
        if ( $lbl === '' || $val === '' ) continue;
        if ( $fts_col_lang_value === '' && ( strpos( $lbl, 'language' ) !== false || strpos( $lbl, 'languages' ) !== false || strpos( $lbl, 'لغة' ) !== false ) ) $fts_col_lang_value = $val;
        if ( $fts_col_pickup_value === '' && ( strpos( $lbl, 'pickup' ) !== false || strpos( $lbl, 'meeting' ) !== false || strpos( $lbl, 'start' ) !== false || strpos( $lbl, 'meet' ) !== false || strpos( $lbl, 'استلام' ) !== false || strpos( $lbl, 'التقاء' ) !== false || strpos( $lbl, 'نقطة' ) !== false ) ) $fts_col_pickup_value = $val;
        if ( $fts_col_lang_value !== '' && $fts_col_pickup_value !== '' ) break;
    }
}

$fts_col_about_items = array();
if ( $fts_col_cancel_text !== '' ) {
    $fts_col_about_items[] = array(
        'icon'  => '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2"/><path d="M16 2v4"/><path d="M8 2v4"/><path d="M3 10h18"/><path d="M9 16l2 2 4-4"/></svg>',
        'title' => __( 'Free cancellation', 'fts' ),
        'desc'  => $fts_col_cancel_text,
    );
}
if ( $fts_col_has_pay_later ) {
    $pl_text = $fts_col_pay_later_text;
    if ( strtolower( $pl_text ) === strtolower( (string) __( 'Reserve now & pay later', 'fts' ) ) ) {
        $pl_text = '';
    }
    $fts_col_about_items[] = array(
        'icon'  => '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="5" width="20" height="14" rx="2"/><path d="M2 10h20"/></svg>',
        'title' => __( 'Reserve now & pay later', 'fts' ),
        'desc'  => $pl_text !== '' ? $pl_text : __( 'Keep your travel plans flexible — book your spot and pay nothing today.', 'fts' ),
    );
}
if ( ! empty( $duration_text ) ) {
    $fts_col_about_items[] = array(
        'icon'  => '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>',
        'title' => __( 'Duration', 'fts' ) . ' ' . esc_html( $duration_text ),
        'desc'  => __( 'Check availability to see starting times', 'fts' ),
    );
}
if ( $fts_col_lang_value !== '' ) {
    $fts_col_about_items[] = array(
        'icon'  => '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 8l6 6"/><path d="M4 14l6-6 2-3"/><path d="M2 5h12"/><path d="M7 2h1"/><path d="M22 22l-5-10-5 10"/><path d="M14 18h6"/></svg>',
        'title' => __( 'Live tour guide', 'fts' ),
        'desc'  => $fts_col_lang_value,
    );
}
if ( $fts_col_pickup_value !== '' ) {
    $fts_col_about_items[] = array(
        'icon'  => '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 17h2c.6 0 1-.4 1-1v-3c0-.9-.7-1.7-1.5-1.9L18 10l-2-4H7L5 10l-2.5 1.1C1.7 11.3 1 12.1 1 13v3c0 .6.4 1 1 1h2"/><circle cx="7" cy="17" r="2"/><circle cx="17" cy="17" r="2"/><path d="M5 10h14"/></svg>',
        'title' => __( 'Pickup included', 'fts' ),
        'desc'  => __( 'Check availability for details', 'fts' ),
    );
}
$ci_str_col = is_array( $cost_includes ?? '' ) ? implode( "\n", $cost_includes ) : (string) ( $cost_includes ?? '' );
$ci_lc_col  = strtolower( $ci_str_col );
$meals_col_title = '';
if ( strpos( $ci_lc_col, 'lunch' ) !== false && strpos( $ci_lc_col, 'breakfast' ) !== false ) {
    $meals_col_title = __( 'Breakfast & Lunch included', 'fts' );
} elseif ( strpos( $ci_lc_col, 'lunch' ) !== false ) {
    $meals_col_title = __( 'Lunch included', 'fts' );
} elseif ( strpos( $ci_lc_col, 'breakfast' ) !== false ) {
    $meals_col_title = __( 'Breakfast included', 'fts' );
} elseif ( strpos( $ci_lc_col, 'meal' ) !== false ) {
    $meals_col_title = __( 'Meals included', 'fts' );
}
if ( $meals_col_title !== '' ) {
    $fts_col_about_items[] = array(
        'icon'  => '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 2v7c0 1.1.9 2 2 2h4a2 2 0 0 0 2-2V2"/><path d="M7 2v20"/><path d="M21 15V2a5 5 0 0 0-5 5v6c0 1.1.9 2 2 2h3zm0 0v7"/></svg>',
        'title' => $meals_col_title,
        'desc'  => '',
    );
}
$fts_col_about_items = array_slice( $fts_col_about_items, 0, 6 );
?>

<!-- ════ Gallery ════ -->
<div class="fts-v2-col-gallery" id="fts-v2-col-gallery">
    <?php
        $fts_gallery_mode = 'hero';
        include get_stylesheet_directory() . '/trip-design-v2/parts/gallery-v2.php';
    ?>
</div>

<!-- ════ Mobile: Title + Rating (below gallery) ════ -->
<div class="fts-v2-mob-title-block">
    <h1 class="fts-v2-trip-title"><?php the_title(); ?></h1>
    <?php if ( $avg_rating > 0 ) : ?>
    <div class="fts-v2-hero-rating" aria-label="<?php echo esc_attr__( 'Customer rating', 'fts' ); ?>">
        <div class="fts-v2-stars-inline" aria-hidden="true">
            <?php for ( $i = 1; $i <= 5; $i++ ) : ?>
                <svg width="14" height="14" viewBox="0 0 24 24" fill="<?php echo $i <= round( $avg_rating ) ? '#FF8C00' : 'none'; ?>" stroke="#FF8C00" stroke-width="2"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
            <?php endfor; ?>
        </div>
        <strong><?php echo number_format( $avg_rating, 1 ); ?></strong>
        <?php if ( $review_count > 0 ) : ?>
        <a href="#fts-v2-sec-reviews" class="fts-v2-hero-reviews-link"><?php echo esc_html( sprintf( _n( '%s review', '%s reviews', $review_count, 'fts' ), number_format_i18n( $review_count ) ) ); ?></a>
        <?php endif; ?>
    </div>
    <?php endif; ?>
</div>

<!-- ════ Description ════ -->
<?php if ( $fts_content_subtitle !== '' ) : ?>
<p class="fts-v2-col-description"><?php echo esc_html( $fts_content_subtitle ); ?></p>
<?php endif; ?>

<!-- ════ About / Trip-facts icons ════ -->
<?php if ( ! empty( $fts_col_about_items ) ) : ?>
<div class="fts-v2-about-grid">
    <?php foreach ( $fts_col_about_items as $ai ) : ?>
    <div class="fts-v2-about-item">
        <div class="fts-v2-about-icon<?php echo ! empty( $ai['icon_class'] ) ? ' ' . esc_attr( $ai['icon_class'] ) : ''; ?>"><?php echo $ai['icon']; ?></div>
        <div class="fts-v2-about-text">
            <span class="fts-v2-about-title"><?php echo esc_html( $ai['title'] ); ?></span>
            <?php if ( ! empty( $ai['desc'] ) ) : ?>
            <span class="fts-v2-about-desc">
                <?php echo esc_html( $ai['desc'] ); ?>
                <?php if ( ! empty( $ai['read_more'] ) && ! empty( $terms_url ) ) : ?>
                <a href="<?php echo esc_url( $terms_url ); ?>" class="fts-v2-about-read-more" target="_blank" rel="noopener noreferrer"><?php echo esc_html__( 'Read more', 'fts' ); ?></a>
                <?php endif; ?>
            </span>
            <?php endif; ?>
        </div>
    </div>
    <?php endforeach; ?>
</div>
<?php endif; ?>

<?php include get_stylesheet_directory() . '/trip-design-v2/parts/quick-info-v2.php'; ?>

<div class="fts-v2-content-sections fts-v2-accordion" data-single-open="true">

    <!-- ==================== ITINERARY ==================== -->
    <?php if ( $has_itinerary ) : ?>
    <?php
        $gyg_icons = array(
            'pickup'   => '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="10" r="3"/><path d="M12 2a8 8 0 0 0-8 8c0 5.4 7.05 11.5 7.35 11.76a1 1 0 0 0 1.3 0C13 21.5 20 15.4 20 10a8 8 0 0 0-8-8z"/></svg>',
            'dropoff'  => '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="10" r="3"/><path d="M12 2a8 8 0 0 0-8 8c0 5.4 7.05 11.5 7.35 11.76a1 1 0 0 0 1.3 0C13 21.5 20 15.4 20 10a8 8 0 0 0-8-8z"/></svg>',
            'transfer' => '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 17h2c.6 0 1-.4 1-1v-3c0-.9-.7-1.7-1.5-1.9L18 10l-2-4H7L5 10l-2.5 1.1C1.7 11.3 1 12.1 1 13v3c0 .6.4 1 1 1h2"/><circle cx="7" cy="17" r="2"/><circle cx="17" cy="17" r="2"/><path d="M5 10h14"/></svg>',
            'visit'    => '<svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 1 1 16 0z"/><circle cx="12" cy="10" r="3"/></svg>',
            'food'     => '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 2v7c0 1.1.9 2 2 2h4a2 2 0 0 0 2-2V2"/><path d="M7 2v20"/><path d="M21 15V2v0a5 5 0 0 0-5 5v6c0 1.1.9 2 2 2h3zm0 0v7"/></svg>',
            'optional' => '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="M8 12h8"/></svg>',
        );

        if ( ! function_exists( 'fts_v2_detect_stop_type' ) ) :
        function fts_v2_detect_stop_type( $title, $content, $is_first, $is_last, $total ) {
            $haystack = strtolower( $title . ' ' . wp_strip_all_tags( $content ) );

            if ( $is_first && preg_match( '/pickup|pick[- ]?up|meeting\s*point|collect|start\s*point|departure/i', $haystack ) ) return 'pickup';
            if ( $is_last && preg_match( '/drop[- ]?off|end\s*point|return|back\s*to|final\s*stop/i', $haystack ) ) return 'dropoff';
            if ( preg_match( '/drop[- ]?off|end\s*point|return\s*to/i', $haystack ) && $is_last ) return 'dropoff';
            if ( preg_match( '/\boptional\b|free\s*time|leisure|at\s*your\s*own/i', $haystack ) ) return 'optional';
            if ( preg_match( '/\b(van|bus|drive|transfer|taxi|boat|ferry|flight|train|metro|tuk[- ]?tuk|ride)\b/i', $title ) ) return 'transfer';
            if ( preg_match( '/\b(lunch|dinner|breakfast|brunch|meal|restaurant|café|cafe|dining|food)\b/i', $title ) ) return 'food';
            return 'visit';
        }
        endif;

        $total_items = 0;
        foreach ( $itin_titles as $__t ) { if ( ! empty( $__t ) ) $total_items++; }
    ?>
    <section id="fts-v2-sec-itinerary" class="fts-v2-section fts-v2-accordion-item is-open">
        <div class="fts-v2-accordion-header">
            <h2 class="fts-v2-section-title"><?php echo esc_html__( 'Itinerary', 'fts' ); ?></h2>
            <span class="fts-v2-accordion-icon"><i class="fa fa-chevron-down"></i></span>
        </div>
        <div class="fts-v2-accordion-body">
            <div class="fts-v2-itinerary-timeline fts-v2-gyg-timeline">
                <?php $stop_num = 0; foreach ( $itin_titles as $key => $title ) : if ( empty( $title ) ) continue; $stop_num++;
                    $is_first  = ( $stop_num === 1 );
                    $is_last   = ( $stop_num === $total_items );
                    $desc_raw  = $itin_content[ $key ] ?? '';
                    $has_desc  = ! empty( trim( wp_strip_all_tags( $desc_raw ) ) );
                    $stop_type = fts_v2_detect_stop_type( $title, $desc_raw, $is_first, $is_last, $total_items );
                    $icon_svg  = $gyg_icons[ $stop_type ] ?? $gyg_icons['visit'];

                    $item_classes = array( 'fts-v2-tl-item', 'fts-v2-tl-type-' . $stop_type );
                    if ( $is_first ) $item_classes[] = 'fts-v2-tl-first';
                    if ( $is_last ) $item_classes[] = 'fts-v2-tl-last';
                    if ( $is_first && $has_desc ) $item_classes[] = 'active';

                    $label = $itin_days_label[ $key ] ?? '';
                ?>
                <div class="<?php echo esc_attr( implode( ' ', $item_classes ) ); ?>">
                    <div class="fts-v2-tl-rail">
                        <div class="fts-v2-tl-line fts-v2-tl-line-top"></div>
                        <span class="fts-v2-tl-icon"><?php echo $icon_svg; ?></span>
                        <div class="fts-v2-tl-line fts-v2-tl-line-bottom"></div>
                    </div>
                    <div class="fts-v2-tl-body">
                        <div class="fts-v2-tl-header">
                            <div class="fts-v2-tl-header-text">
                                <h3 class="fts-v2-tl-title"><?php echo wp_kses_post( $title ); ?></h3>
                                <?php if ( ! empty( $label ) ) : ?>
                                    <span class="fts-v2-tl-subtitle"><?php echo esc_html( $label ); ?></span>
                                <?php endif; ?>
                                <?php if ( $stop_type === 'optional' ) : ?>
                                    <span class="fts-v2-tl-optional-tag"><?php echo esc_html__( 'Optional', 'fts' ); ?></span>
                                <?php endif; ?>
                            </div>
                            <?php if ( $has_desc ) : ?>
                            <span class="fts-v2-tl-toggle"><i class="fa fa-chevron-down"></i></span>
                            <?php endif; ?>
                        </div>
                        <?php if ( $has_desc ) : ?>
                        <div class="fts-v2-tl-desc"<?php if ( $is_first ) echo ' style="display:block"'; ?>><?php echo wp_kses_post( $desc_raw ); ?></div>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
    <?php endif; ?>

    <!-- ==================== OVERVIEW ==================== -->
    <?php if ( $has_overview_text ) : ?>
    <section id="fts-v2-sec-overview" class="fts-v2-section fts-v2-accordion-item">
        <div class="fts-v2-accordion-header">
            <h2 class="fts-v2-section-title"><?php echo esc_html__( 'Trip Overview', 'fts' ); ?></h2>
            <span class="fts-v2-accordion-icon"><i class="fa fa-chevron-down"></i></span>
        </div>
        <div class="fts-v2-accordion-body">
            <div class="fts-v2-overview-text"><?php echo wp_kses_post( $overview_content ); ?></div>
        </div>
    </section>
    <?php endif; ?>

    <!-- ==================== TRIP HIGHLIGHTS ==================== -->
    <?php if ( ! empty( $highlights ) ) :
        $hl_icon_map = array(
            array( 'keys' => array( 'flight', 'fly', 'plane', 'air', 'domestic' ), 'icon' => 'fa-plane',      'color' => '#e67e22' ),
            array( 'keys' => array( 'pyramid', 'giza', 'sphinx', 'ancient' ),      'icon' => 'fa-university', 'color' => '#2c3e50' ),
            array( 'keys' => array( 'museum', 'gem', 'exhibit' ),                   'icon' => 'fa-building',   'color' => '#2980b9' ),
            array( 'keys' => array( 'nile', 'boat', 'river', 'cruise', 'sail' ),    'icon' => 'fa-ship',       'color' => '#27ae60' ),
            array( 'keys' => array( 'lunch', 'food', 'meal', 'dinner', 'cuisine' ), 'icon' => 'fa-cutlery',    'color' => '#e67e22' ),
            array( 'keys' => array( 'guide', 'expert', 'egyptologist' ),            'icon' => 'fa-user',       'color' => '#c0392b' ),
            array( 'keys' => array( 'transfer', 'vehicle', 'car', 'bus', 'van' ),   'icon' => 'fa-car',        'color' => '#27ae60' ),
            array( 'keys' => array( 'hotel', 'accommodation', 'stay', 'resort' ),   'icon' => 'fa-bed',        'color' => '#8e44ad' ),
            array( 'keys' => array( 'snorkel', 'dive', 'sea', 'beach', 'swim' ),    'icon' => 'fa-tint',       'color' => '#2980b9' ),
            array( 'keys' => array( 'photo', 'camera', 'picture' ),                 'icon' => 'fa-camera',     'color' => '#e67e22' ),
            array( 'keys' => array( 'temple', 'church', 'mosque', 'tomb' ),         'icon' => 'fa-university', 'color' => '#c0392b' ),
            array( 'keys' => array( 'mountain', 'trek', 'hike', 'climb' ),          'icon' => 'fa-flag',       'color' => '#27ae60' ),
            array( 'keys' => array( 'safari', 'desert', 'camel', 'quad' ),          'icon' => 'fa-sun-o',      'color' => '#e67e22' ),
            array( 'keys' => array( 'ticket', 'entry', 'fee', 'inclusive' ),         'icon' => 'fa-ticket',     'color' => '#2980b9' ),
        );
        $hl_fallback_icons = array(
            array( 'icon' => 'fa-star',  'color' => '#e67e22' ),
            array( 'icon' => 'fa-gem',   'color' => '#2980b9' ),
            array( 'icon' => 'fa-leaf',  'color' => '#27ae60' ),
            array( 'icon' => 'fa-heart', 'color' => '#c0392b' ),
        );
        $hl_idx = 0;
    ?>
    <section id="fts-v2-sec-highlights" class="fts-v2-section fts-v2-accordion-item">
        <div class="fts-v2-accordion-header">
            <h2 class="fts-v2-section-title"><?php echo esc_html( $highlights_title ); ?></h2>
            <span class="fts-v2-accordion-icon"><i class="fa fa-chevron-down"></i></span>
        </div>
        <div class="fts-v2-accordion-body">
            <div class="fts-v2-highlights">
                <div class="fts-v2-highlights-grid">
                    <?php foreach ( $highlights as $h ) : if ( empty( $h ) ) continue;
                        $lower = strtolower( $h );
                        $matched_icon  = null;
                        $matched_color = null;
                        foreach ( $hl_icon_map as $map ) {
                            foreach ( $map['keys'] as $kw ) {
                                if ( strpos( $lower, $kw ) !== false ) {
                                    $matched_icon  = $map['icon'];
                                    $matched_color = $map['color'];
                                    break 2;
                                }
                            }
                        }
                        if ( ! $matched_icon ) {
                            $fb = $hl_fallback_icons[ $hl_idx % count( $hl_fallback_icons ) ];
                            $matched_icon  = $fb['icon'];
                            $matched_color = $fb['color'];
                        }
                        $hl_idx++;
                    ?>
                    <div class="fts-v2-highlight-item">
                        <span class="fts-v2-hl-icon" style="color:<?php echo esc_attr( $matched_color ); ?>"><i class="fa <?php echo esc_attr( $matched_icon ); ?>"></i></span>
                        <span><?php echo esc_html( $h ); ?></span>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </section>
    <?php endif; ?>

    <!-- ==================== INCLUDES / EXCLUDES ==================== -->
    <?php if ( $has_cost ) : ?>
    <section id="fts-v2-sec-includes" class="fts-v2-section fts-v2-accordion-item">
        <div class="fts-v2-accordion-header">
            <h2 class="fts-v2-section-title"><?php echo esc_html__( "What's Included", 'fts' ); ?></h2>
            <span class="fts-v2-accordion-icon"><i class="fa fa-chevron-down"></i></span>
        </div>
        <div class="fts-v2-accordion-body">
            <div class="fts-v2-includes-grid">
                <?php if ( ! empty( trim( $cost_includes ) ) ) :
                    $inc_list = preg_split( '/\r\n|[\r\n]/', $cost_includes );
                ?>
                <div class="fts-v2-includes-col fts-v2-col-included">
                    <h3><i class="fa fa-check-circle"></i> <?php echo esc_html__( 'Included', 'fts' ); ?></h3>
                    <ul>
                        <?php foreach ( $inc_list as $item ) : if ( empty( trim( $item ) ) ) continue; ?>
                        <li><i class="fa fa-check"></i> <?php echo esc_html( $item ); ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
                <?php endif; ?>

                <?php if ( ! empty( trim( $cost_excludes ) ) ) :
                    $exc_list = preg_split( '/\r\n|[\r\n]/', $cost_excludes );
                ?>
                <div class="fts-v2-includes-col fts-v2-col-excluded">
                    <h3><i class="fa fa-times-circle"></i> <?php echo esc_html__( 'Not Included', 'fts' ); ?></h3>
                    <ul>
                        <?php foreach ( $exc_list as $item ) : if ( empty( trim( $item ) ) ) continue; ?>
                        <li><i class="fa fa-times"></i> <?php echo esc_html( $item ); ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </section>
    <?php endif; ?>

    <!-- ==================== TRIP FACTS ==================== -->
    <?php if ( ! empty( $has_trip_facts ) && ! empty( $trip_facts_items ) ) : ?>
    <section id="fts-v2-sec-facts" class="fts-v2-section fts-v2-accordion-item is-open">
        <div class="fts-v2-accordion-header">
            <h2 class="fts-v2-section-title"><?php echo esc_html( ! empty( $trip_facts_title ) ? $trip_facts_title : __( 'Trip Facts', 'ftstravels' ) ); ?></h2>
            <span class="fts-v2-accordion-icon"><i class="fa fa-chevron-down"></i></span>
        </div>
        <div class="fts-v2-accordion-body">
            <div class="fts-v2-facts-grid">
                <?php foreach ( $trip_facts_items as $tf ) :
                    $lbl = isset( $tf['label'] ) ? trim( (string) $tf['label'] ) : '';
                    $val = isset( $tf['value'] ) ? trim( (string) $tf['value'] ) : '';
                    if ( $lbl === '' || $val === '' ) continue;
                    $icon = isset( $tf['icon'] ) ? trim( (string) $tf['icon'] ) : 'fa-info-circle';
                ?>
                    <div class="fts-v2-fact-card">
                        <div class="fts-v2-fact-card-icon"><i class="fa <?php echo esc_attr( $icon ); ?>"></i></div>
                        <div class="fts-v2-fact-card-body">
                            <div class="fts-v2-fact-card-label"><?php echo esc_html( $lbl ); ?></div>
                            <div class="fts-v2-fact-card-value"><?php echo esc_html( $val ); ?></div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
    <?php endif; ?>

    <!-- ==================== MAP ==================== -->
    <?php
        $fts_map_query = '';
        if ( isset( $location ) && is_string( $location ) ) $fts_map_query = trim( $location );
        if ( $fts_map_query === '' ) $fts_map_query = trim( (string) get_the_title() );

        $map_locations = array();
        $map_seen = array();
        if ( ! empty( $itin_titles ) ) {
            foreach ( $itin_titles as $mk => $mt ) {
                $mt = trim( (string) $mt );
                if ( $mt === '' ) continue;
                $clean = preg_replace( '/^(morning|afternoon|evening|late\s*morning|late\s*afternoon|early\s*morning|day\s*\d+)\s*:\s*/i', '', $mt );
                $clean = preg_replace( '/\s*\|.*$/', '', $clean );
                $clean = trim( $clean );
                if ( $clean === '' ) continue;
                $key = strtolower( $clean );
                if ( isset( $map_seen[ $key ] ) ) continue;
                if ( preg_match( '/\b(van|bus|transfer|drive|taxi|flight|fly|return|pickup|pick.up|drop.off|hotel|lunch|breakfast|dinner|meal)\b/i', $clean ) ) continue;
                $map_seen[ $key ] = true;
                $map_locations[] = array( 'name' => $clean, 'query' => $clean . ', ' . $fts_map_query );
            }
        }

        $embed_url = 'https://www.google.com/maps?q=' . rawurlencode( $fts_map_query ) . '&output=embed';
        if ( count( $map_locations ) >= 2 ) {
            $dir_parts = array();
            foreach ( $map_locations as $ml ) {
                $dir_parts[] = rawurlencode( $ml['query'] );
            }
            $gmaps_link = 'https://www.google.com/maps/dir/' . implode( '/', $dir_parts );
        } else {
            $gmaps_link = 'https://www.google.com/maps?q=' . rawurlencode( $fts_map_query );
        }
    ?>
    <?php if ( $fts_map_query !== '' ) : ?>
    <section id="fts-v2-sec-map" class="fts-v2-section fts-v2-accordion-item">
        <div class="fts-v2-accordion-header">
            <h2 class="fts-v2-section-title"><?php echo esc_html__( 'Meeting point & map', 'fts' ); ?></h2>
            <span class="fts-v2-accordion-icon"><i class="fa fa-chevron-down"></i></span>
        </div>
        <div class="fts-v2-accordion-body">
            <div class="fts-v2-map-card">
                <div class="fts-v2-map-embed" id="fts-v2-map-embed">
                    <iframe
                        id="fts-v2-map-iframe"
                        title="<?php echo esc_attr__( 'Map', 'fts' ); ?>"
                        loading="lazy"
                        referrerpolicy="no-referrer-when-downgrade"
                        src="<?php echo esc_url( $embed_url ); ?>"
                    ></iframe>
                </div>
                <?php if ( count( $map_locations ) > 1 ) : ?>
                <div class="fts-v2-map-chips" id="fts-v2-map-chips">
                    <span class="fts-v2-map-chips-title"><?php echo esc_html__( 'Stops on this trip', 'fts' ); ?></span>
                    <div class="fts-v2-map-chips-list">
                        <?php foreach ( $map_locations as $mi => $ml ) : ?>
                        <button type="button" class="fts-v2-map-chip<?php echo $mi === 0 ? ' active' : ''; ?>" data-query="<?php echo esc_attr( $ml['query'] ); ?>">
                            <span class="fts-v2-map-chip-num"><?php echo intval( $mi + 1 ); ?></span>
                            <?php echo esc_html( $ml['name'] ); ?>
                        </button>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endif; ?>
                <div class="fts-v2-map-actions">
                    <a class="fts-v2-map-link" id="fts-v2-map-gmaps-link" target="_blank" rel="noopener noreferrer nofollow" href="<?php echo esc_url( $gmaps_link ); ?>">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/><polyline points="15 3 21 3 21 9"/><line x1="10" y1="14" x2="21" y2="3"/></svg>
                        <?php echo esc_html__( 'Open in Google Maps', 'fts' ); ?>
                    </a>
                </div>
            </div>
        </div>
    </section>
    <?php endif; ?>

    <!-- ==================== WHY PEOPLE LOVE THIS TRIP ==================== -->
    <?php if ( $has_why_love ) : ?>
    <section id="fts-v2-sec-why-love" class="fts-v2-section fts-v2-accordion-item">
        <div class="fts-v2-accordion-header">
            <h2 class="fts-v2-section-title"><?php echo esc_html( ! empty( $why_love_tab_title ) ? $why_love_tab_title : __( 'Why People Love This Trip', 'fts' ) ); ?></h2>
            <span class="fts-v2-accordion-icon"><i class="fa fa-chevron-down"></i></span>
        </div>
        <div class="fts-v2-accordion-body">
            <div class="fts-v2-why-love-content"><?php echo wp_kses_post( $why_love_content ); ?></div>
        </div>
    </section>
    <?php endif; ?>

    <!-- ==================== PRICING / PACKAGES ==================== -->
    <section id="fts-v2-sec-pricing" class="fts-v2-section fts-v2-accordion-item is-open">
        <div class="fts-v2-accordion-header">
            <h2 class="fts-v2-section-title"><?php echo esc_html__( 'Choose Your Package', 'fts' ); ?></h2>
            <span class="fts-v2-accordion-icon"><i class="fa fa-chevron-down"></i></span>
        </div>
        <div class="fts-v2-accordion-body">
            <?php
            $has_packages = false;
            $packages     = null;
            $primary_id   = 0;
            if ( $trip_obj && method_exists( $trip_obj, 'has_package' ) ) {
                try {
                    $has_packages = $trip_obj->has_package();
                    if ( $has_packages && method_exists( $trip_obj, 'packages' ) ) {
                        $packages   = $trip_obj->packages();
                        $primary_id = $trip_obj->get_meta( 'primary_package' );
                    }
                } catch ( \Throwable $e ) {
                    $has_packages = false;
                }
            }
            ?>
            <?php if ( ! empty( $packages_list ) ) : ?>
            <?php
                $pkg_min_price = null;
                foreach ( (array) $packages_list as $p0 ) {
                    $p0_price = floatval( $p0['display_price'] ?? 0 );
                    if ( $p0_price <= 0 ) continue;
                    if ( $pkg_min_price === null || $p0_price < $pkg_min_price ) $pkg_min_price = $p0_price;
                }
            ?>
            <div class="fts-v2-packages-grid">
                <?php foreach ( $packages_list as $pkg ) :
                    $card_cls = 'fts-v2-package-card';
                    if ( $pkg['badge'] === 'most_popular' ) $card_cls .= ' fts-v2-package-popular';
                    if ( $pkg['badge'] === 'best_value' )   $card_cls .= ' fts-v2-package-best-value';
                    $pkg_dp = floatval( $pkg['display_price'] ?? 0 );
                    $is_lowest = ( $pkg_min_price !== null && $pkg_dp > 0 && ( $pkg_dp - floatval( $pkg_min_price ) ) <= 0.01 );
                    if ( $is_lowest ) $card_cls .= ' fts-v2-package-lowest';
                ?>
                <div class="<?php echo esc_attr( $card_cls ); ?>">
                    <?php if ( $is_lowest ) : ?>
                    <div class="fts-v2-package-badge fts-v2-badge-lowest"><span>&#9660;</span> <?php echo esc_html__( 'Lowest Price', 'fts' ); ?></div>
                    <?php elseif ( $pkg['badge'] === 'most_popular' ) : ?>
                    <div class="fts-v2-package-badge fts-v2-badge-popular"><span>&#9733;</span> <?php echo esc_html__( 'Most Popular', 'fts' ); ?></div>
                    <?php elseif ( $pkg['badge'] === 'best_value' ) : ?>
                    <div class="fts-v2-package-badge fts-v2-badge-value"><span>&#9889;</span> <?php echo esc_html__( 'Best Value', 'fts' ); ?></div>
                    <?php endif; ?>

                    <h3 class="fts-v2-package-name"><?php echo esc_html( $pkg['name'] ); ?></h3>
                    <?php
                        $pkg_short_desc = '';
                        if ( ! empty( $pkg['card_short_description'] ) ) {
                            $pkg_short_desc = (string) $pkg['card_short_description'];
                        } elseif ( ! empty( $pkg['excerpt'] ) ) {
                            $pkg_short_desc = (string) $pkg['excerpt'];
                        } elseif ( ! empty( $pkg['description'] ) ) {
                            $pkg_short_desc = (string) $pkg['description'];
                        }

                        $pkg_full_desc = '';
                        if ( ! empty( $pkg['card_full_description'] ) ) {
                            $pkg_full_desc = (string) $pkg['card_full_description'];
                        } elseif ( ! empty( $pkg['description_full'] ) ) {
                            $pkg_full_desc = (string) $pkg['description_full'];
                        } else {
                            $pkg_full_desc = $pkg_short_desc;
                        }

                        if ( function_exists( 'fts_v2_clean_package_card_text_for_display' ) ) {
                            $pkg_short_desc = fts_v2_clean_package_card_text_for_display( $pkg_short_desc, (string) ( $pkg['name'] ?? '' ) );
                            $pkg_full_desc  = fts_v2_clean_package_card_text_for_display( $pkg_full_desc, (string) ( $pkg['name'] ?? '' ) );
                        }

                        $pkg_short_desc = trim( preg_replace( '/\s+/u', ' ', $pkg_short_desc ) );
                        $pkg_full_desc  = trim( preg_replace( '/\s+/u', ' ', $pkg_full_desc ) );

                        if ( $pkg_short_desc === '' && $pkg_full_desc !== '' ) {
                            $pkg_short_desc = wp_trim_words( $pkg_full_desc, 24, '…' );
                        }

                        $has_more_desc = ( $pkg_full_desc !== '' && $pkg_short_desc !== '' && $pkg_full_desc !== $pkg_short_desc && strlen( $pkg_full_desc ) > strlen( $pkg_short_desc ) + 20 );
                    ?>
                    <?php if ( $pkg_short_desc !== '' || $pkg_full_desc !== '' ) : ?>
                    <div class="fts-v2-package-desc-wrap">
                        <p class="fts-v2-package-desc fts-v2-desc-clamped" data-short="<?php echo esc_attr( $pkg_short_desc ); ?>" data-full="<?php echo esc_attr( $pkg_full_desc ); ?>"><?php echo esc_html( $pkg_short_desc ); ?></p>
                        <?php if ( $has_more_desc ) : ?>
                        <button type="button" class="fts-v2-desc-toggle" aria-expanded="false" style="display:none" data-more="<?php echo esc_attr__( 'Read more', 'fts' ); ?>" data-less="<?php echo esc_attr__( 'Show less', 'fts' ); ?>"><?php echo esc_html__( 'Read more', 'fts' ); ?></button>
                        <?php endif; ?>
                    </div>
                    <?php endif; ?>

                    <div class="fts-v2-package-price">
                        <?php if ( $pkg['old_price'] > 0 ) : ?>
                        <span class="fts-v2-pkg-old"><?php echo function_exists( 'fts_v2_format_converted_price_for_display' ) ? fts_v2_format_converted_price_for_display( $pkg['old_price'] ) : wte_get_formated_price( $pkg['old_price'] ); ?></span>
                        <?php endif; ?>
                        <span class="fts-v2-pkg-current"><?php echo function_exists( 'fts_v2_format_converted_price_for_display' ) ? fts_v2_format_converted_price_for_display( $pkg['display_price'] ) : wte_get_formated_price( $pkg['display_price'] ); ?></span>
                        <span class="fts-v2-pkg-per"><?php echo esc_html__( '/ person', 'fts' ); ?></span>
                    </div>

                    <?php if ( ! empty( $pkg['discount_pct'] ) && intval( $pkg['discount_pct'] ) > 0 ) : ?>
                        <div class="fts-v2-pkg-save"><?php echo esc_html( sprintf( __( 'Save %s%%', 'fts' ), intval( $pkg['discount_pct'] ) ) ); ?></div>
                    <?php endif; ?>

                    <?php
                        $pb_list = ! empty( $pkg['price_breakdown'] ) && is_array( $pkg['price_breakdown'] ) ? $pkg['price_breakdown'] : array();
                    ?>
                    <?php if ( ! empty( $pb_list ) ) : ?>
                    <ul class="fts-v2-package-features fts-v2-package-price-breakdown">
                        <?php foreach ( $pb_list as $pb ) :
                            $pb_label = trim( (string) ( is_array( $pb ) ? ( $pb['label'] ?? '' ) : '' ) );
                            $pb_price = floatval( is_array( $pb ) ? ( $pb['price'] ?? 0 ) : 0 );
                            if ( $pb_label === '' || $pb_price <= 0 ) continue;
                            $pb_display = function_exists( 'fts_v2_format_converted_price_for_display' ) ? fts_v2_format_converted_price_for_display( $pb_price ) : wte_get_formated_price( $pb_price );
                        ?>
                        <li>
                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="#38a169" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"/></svg>
                            <?php echo esc_html( $pb_label ); ?>: <?php echo esc_html( $pb_display ); ?>
                        </li>
                        <?php endforeach; ?>
                    </ul>
                    <?php endif; ?>

                    <?php
                        $all_feats = ! empty( $pkg['features_all'] ) ? $pkg['features_all'] : ( ! empty( $pkg['features'] ) ? $pkg['features'] : array() );
                    ?>
                    <?php if ( ! empty( $all_feats ) ) : ?>
                    <ul class="fts-v2-package-features">
                        <?php foreach ( $all_feats as $feat ) : ?>
                        <li>
                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="#38a169" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"/></svg>
                            <?php echo esc_html( $feat ); ?>
                        </li>
                        <?php endforeach; ?>
                    </ul>
                    <?php endif; ?>

                    <a href="#" class="fts-v2-package-select-btn fts-bm-trigger" data-package-id="<?php echo esc_attr( $pkg['id'] ); ?>"><?php echo esc_html__( 'Select Package', 'fts' ); ?></a>
                </div>
                <?php endforeach; ?>
            </div>

            <?php else : ?>
            <div class="fts-v2-single-price-card">
                <div class="fts-v2-booking-form-wrap">
                    <?php do_action( 'wp_travel_engine_trip_price' ); ?>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </section>

    <!-- ==================== PHOTO GALLERY ==================== -->
    <?php if ( $has_gallery ) : ?>
    <section id="fts-v2-sec-gallery" class="fts-v2-section fts-v2-accordion-item">
        <div class="fts-v2-accordion-header">
            <h2 class="fts-v2-section-title"><?php echo esc_html__( 'Photo Gallery', 'fts' ); ?></h2>
            <span class="fts-v2-accordion-icon"><i class="fa fa-chevron-down"></i></span>
        </div>
        <div class="fts-v2-accordion-body">
            <div class="fts-v2-photo-grid">
                <?php foreach ( array_slice( $all_images, 0, 8 ) as $gi => $gimg_id ) :
                    $gimg_url  = wp_get_attachment_image_url( $gimg_id, 'medium_large' );
                    $gimg_full = wp_get_attachment_image_url( $gimg_id, 'full' );
                    $gimg_alt  = get_post_meta( $gimg_id, '_wp_attachment_image_alt', true ) ?: '';
                    if ( ! $gimg_url ) continue;
                ?>
                <div class="fts-v2-photo-item" data-full="<?php echo esc_url( $gimg_full ); ?>">
                    <img src="<?php echo esc_url( $gimg_url ); ?>" alt="<?php echo esc_attr( $gimg_alt ); ?>" loading="lazy">
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
    <?php endif; ?>

    <!-- ==================== REVIEWS ==================== -->
    <?php if ( $has_reviews ) : ?>
    <section id="fts-v2-sec-reviews" class="fts-v2-section fts-v2-accordion-item">
        <div class="fts-v2-accordion-header">
            <h2 class="fts-v2-section-title"><?php echo esc_html__( 'What Travelers Say', 'fts' ); ?></h2>
            <span class="fts-v2-accordion-icon"><i class="fa fa-chevron-down"></i></span>
        </div>
        <div class="fts-v2-accordion-body">
            <?php if ( $review_count > 0 ) : ?>
            <?php
                $stars_dist = array( 1 => 0, 2 => 0, 3 => 0, 4 => 0, 5 => 0 );
                $topic_defs = array(
                    'guide' => array(
                        'label' => esc_html__( 'Guide', 'fts' ),
                        'keywords' => array( 'guide', 'guid', 'egyptologist', 'مرشد', 'المرشد', 'دليل', 'الراوي', 'شرح', 'explain', 'knowledge', 'knowledgeable' ),
                    ),
                    'transportation' => array(
                        'label' => esc_html__( 'Transportation', 'fts' ),
                        'keywords' => array( 'driver', 'driving', 'car', 'van', 'bus', 'transport', 'transportation', 'vehicle', 'pickup', 'drop', 'on-time', 'punctual', 'سائق', 'سواق', 'مركبة', 'سيارة', 'فان', 'اتوبيس', 'استلام', 'التقاط', 'موعد' ),
                    ),
                    'value' => array(
                        'label' => esc_html__( 'Value for money', 'fts' ),
                        'keywords' => array( 'value', 'worth', 'money', 'price', 'cost', 'expensive', 'cheap', 'deal', 'great value', 'قيمة', 'مستحق', 'السعر', 'التكلفة', 'غالي', 'رخيص', 'صفقة' ),
                    ),
                    'communication' => array(
                        'label' => esc_html__( 'Communication', 'fts' ),
                        'keywords' => array( 'communication', 'communicat', 'whatsapp', 'message', 'responsive', 'respond', 'contact', 'تواصل', 'واتساب', 'رسالة', 'رد' ),
                    ),
                );
                $topic_stats = array();
                foreach ( $topic_defs as $slug => $def ) {
                    $topic_stats[ $slug ] = array( 'count' => 0, 'sum' => 0.0 );
                }

                $structured_topic_map = array();
                if ( isset( $review_data ) && is_array( $review_data ) ) {
                    $candidates = array( 'ratings_by_topic', 'ratingsByTopic', 'topic_ratings', 'ratings_by_category', 'ratingsByCategory' );
                    foreach ( $candidates as $ck ) {
                        if ( isset( $review_data[ $ck ] ) && is_array( $review_data[ $ck ] ) ) {
                            $structured_topic_map = $review_data[ $ck ];
                            break;
                        }
                    }
                }

                $reviews_topics_cache = array();
                foreach ( (array) $reviews as $rev0_idx => $rev0 ) {
                    $s0 = isset( $rev0['stars'] ) ? intval( $rev0['stars'] ) : 0;
                    if ( $s0 < 1 || $s0 > 5 ) {
                        $reviews_topics_cache[ $rev0_idx ] = array();
                        continue;
                    }
                    $stars_dist[ $s0 ]++;

                    $c0 = isset( $rev0['content'] ) ? (string) $rev0['content'] : '';
                    $c0 = trim( wp_strip_all_tags( $c0 ) );
                    $lc = '';
                    if ( $c0 !== '' ) {
                        $lc = function_exists( 'mb_strtolower' ) ? mb_strtolower( $c0 ) : strtolower( $c0 );
                    }
                    $rev_topics = array();
                    if ( $lc !== '' ) {
                        foreach ( $topic_defs as $slug => $def ) {
                            $kws = isset( $def['keywords'] ) && is_array( $def['keywords'] ) ? $def['keywords'] : array();
                            foreach ( $kws as $kw ) {
                                $kw = trim( (string) $kw );
                                if ( $kw === '' ) continue;
                                $pos = function_exists( 'mb_strpos' ) ? mb_strpos( $lc, $kw ) : strpos( $lc, $kw );
                                if ( $pos !== false ) {
                                    $rev_topics[] = $slug;
                                    break;
                                }
                            }
                        }
                    }
                    $reviews_topics_cache[ $rev0_idx ] = $rev_topics;
                    foreach ( $rev_topics as $slug ) {
                        if ( ! isset( $topic_stats[ $slug ] ) ) continue;
                        $topic_stats[ $slug ]['count']++;
                        $topic_stats[ $slug ]['sum'] += (float) $s0;
                    }
                }

                $topic_rows = array();
                $filter_topics = array();
                foreach ( $topic_defs as $slug => $def ) {
                    $cnt = isset( $topic_stats[ $slug ]['count'] ) ? intval( $topic_stats[ $slug ]['count'] ) : 0;
                    if ( $cnt > 0 ) $filter_topics[] = $slug;

                    $avg = 0.0;
                    $has_structured = false;
                    if ( is_array( $structured_topic_map ) && ! empty( $structured_topic_map ) ) {
                        foreach ( $structured_topic_map as $k => $v ) {
                            $k0 = function_exists( 'mb_strtolower' ) ? mb_strtolower( (string) $k ) : strtolower( (string) $k );
                            $k0 = trim( preg_replace( '/[^a-z0-9]+/i', ' ', $k0 ) );
                            if ( $slug === 'guide' && strpos( $k0, 'guide' ) !== false ) { $avg = floatval( $v ); $has_structured = true; break; }
                            if ( $slug === 'transportation' && ( strpos( $k0, 'transport' ) !== false || strpos( $k0, 'driver' ) !== false ) ) { $avg = floatval( $v ); $has_structured = true; break; }
                            if ( $slug === 'value' && ( strpos( $k0, 'value' ) !== false || strpos( $k0, 'money' ) !== false ) ) { $avg = floatval( $v ); $has_structured = true; break; }
                            if ( $slug === 'communication' && ( strpos( $k0, 'communication' ) !== false || strpos( $k0, 'whatsapp' ) !== false ) ) { $avg = floatval( $v ); $has_structured = true; break; }
                        }
                    }
                    if ( ! $has_structured && $cnt > 0 ) {
                        $avg = (float) $topic_stats[ $slug ]['sum'] / $cnt;
                    }
                    if ( $avg <= 0 || $avg > 5 ) continue;
                    $topic_rows[] = array(
                        'slug' => $slug,
                        'label' => (string) ( $def['label'] ?? $slug ),
                        'avg' => $avg,
                        'count' => $cnt,
                        'structured' => $has_structured,
                    );
                }
                $filter_topics = array_values( array_unique( $filter_topics ) );
            ?>
            <div class="fts-v2-reviews-shell">
                <div class="fts-v2-reviews-summary">
                    <div class="fts-v2-reviews-scoreline">
                        <div class="fts-v2-score-big"><?php echo number_format( (float) $avg_rating, 1 ); ?></div>
                        <div class="fts-v2-score-meta">
                            <div class="fts-v2-score-stars">
                                <?php for ( $i = 1; $i <= 5; $i++ ) : ?>
                                    <i class="fa fa-star<?php echo $i <= round( (float) $avg_rating ) ? '' : '-o'; ?>"></i>
                                <?php endfor; ?>
                            </div>
                            <div class="fts-v2-score-count"><?php echo esc_html( sprintf( _n( '%s review', '%s reviews', (int) $review_count, 'fts' ), number_format_i18n( (int) $review_count ) ) ); ?></div>
                        </div>
                    </div>
                    <div class="fts-v2-reviews-breakdown">
                        <?php for ( $r = 5; $r >= 1; $r-- ) : ?>
                            <?php
                                $cnt = isset( $stars_dist[ $r ] ) ? intval( $stars_dist[ $r ] ) : 0;
                                $pct = $review_count > 0 ? ( $cnt * 100.0 / $review_count ) : 0;
                            ?>
                            <div class="fts-v2-break-row">
                                <div class="fts-v2-break-label"><?php echo esc_html( $r ); ?></div>
                                <div class="fts-v2-break-stars"><i class="fa fa-star"></i></div>
                                <div class="fts-v2-break-bar" aria-hidden="true">
                                    <span class="fts-v2-break-fill" style="width: <?php echo esc_attr( max( 0, min( 100, $pct ) ) ); ?>%;"></span>
                                </div>
                                <div class="fts-v2-break-count"><?php echo esc_html( number_format_i18n( $cnt ) ); ?></div>
                            </div>
                        <?php endfor; ?>
                    </div>

                    <?php if ( ! empty( $topic_rows ) ) : ?>
                    <div class="fts-v2-topic-block" aria-label="<?php echo esc_attr__( 'Ratings by topic', 'fts' ); ?>">
                        <div class="fts-v2-topic-title"><?php echo esc_html__( 'Ratings by topic', 'fts' ); ?></div>
                        <div class="fts-v2-topic-rows">
                            <?php foreach ( $topic_rows as $tr ) : ?>
                                <?php
                                    $t_avg = floatval( $tr['avg'] ?? 0 );
                                    $t_pct = max( 0, min( 100, ( $t_avg / 5.0 ) * 100.0 ) );
                                    $t_cnt = intval( $tr['count'] ?? 0 );
                                    $t_structured = ! empty( $tr['structured'] );
                                ?>
                                <div class="fts-v2-topic-row" data-topic="<?php echo esc_attr( (string) $tr['slug'] ); ?>">
                                    <div class="fts-v2-topic-label"><?php echo esc_html( (string) $tr['label'] ); ?></div>
                                    <div class="fts-v2-topic-bar" aria-hidden="true"><span class="fts-v2-topic-fill" style="width: <?php echo esc_attr( $t_pct ); ?>%;"></span></div>
                                    <div class="fts-v2-topic-score"><?php echo esc_html( number_format( $t_avg, 1 ) ); ?>/5</div>
                                    <div class="fts-v2-topic-count"><?php echo esc_html( $t_structured ? __( '(platform score)', 'fts' ) : sprintf( _n( '(%s review)', '(%s reviews)', $t_cnt, 'fts' ), number_format_i18n( $t_cnt ) ) ); ?></div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
                <?php $fts_reviews_limit = 6; ?>
                <div class="fts-v2-reviews-items" data-limit="<?php echo esc_attr( (int) $fts_reviews_limit ); ?>">
                    <?php if ( ! empty( $filter_topics ) ) : ?>
                    <div class="fts-v2-review-filters" role="group" aria-label="<?php echo esc_attr__( 'Filter reviews', 'fts' ); ?>">
                        <button type="button" class="fts-v2-review-filter is-active" data-topic="all" aria-pressed="true"><?php echo esc_html__( 'All', 'fts' ); ?></button>
                        <?php foreach ( $topic_defs as $slug => $def ) : if ( ! in_array( $slug, $filter_topics, true ) ) continue; ?>
                            <button type="button" class="fts-v2-review-filter" data-topic="<?php echo esc_attr( (string) $slug ); ?>" aria-pressed="false">
                                <?php echo esc_html( (string) ( $def['label'] ?? $slug ) ); ?>
                            </button>
                        <?php endforeach; ?>
                        <span class="fts-v2-review-filter-status" aria-live="polite"></span>
                    </div>
                    <?php endif; ?>
                    <?php foreach ( (array) $reviews as $idx => $rev ) : ?>
                        <?php
                            $author = isset( $rev['title'] ) ? (string) $rev['title'] : '';
                            $author = trim( $author ) !== '' ? $author : __( 'Traveler', 'fts' );
                            $first  = mb_strtoupper( mb_substr( $author, 0, 1 ) );
                            $stars  = isset( $rev['stars'] ) ? intval( $rev['stars'] ) : 5;
                            $stars  = $stars >= 1 && $stars <= 5 ? $stars : 5;
                            $d_raw  = isset( $rev['date'] ) ? (string) $rev['date'] : '';
                            $d_txt  = '';
                            if ( $d_raw !== '' ) {
                                $ts = strtotime( $d_raw );
                                if ( $ts ) $d_txt = date_i18n( 'M j, Y', $ts );
                            }
                            $content = isset( $rev['content'] ) ? (string) $rev['content'] : '';
                            $rev_topics_attr = '';
                            if ( isset( $reviews_topics_cache[ $idx ] ) && is_array( $reviews_topics_cache[ $idx ] ) && ! empty( $reviews_topics_cache[ $idx ] ) ) {
                                $rev_topics_attr = implode( ' ', array_values( array_unique( array_map( 'strval', $reviews_topics_cache[ $idx ] ) ) ) );
                            }
                        ?>
                        <article class="fts-v2-review-item<?php echo $idx >= $fts_reviews_limit ? ' is-hidden' : ''; ?>"<?php echo $rev_topics_attr !== '' ? ' data-topics="' . esc_attr( $rev_topics_attr ) . '"' : ''; ?>>
                            <div class="fts-v2-review-top">
                                <div class="fts-v2-review-avatar"><?php echo esc_html( $first !== '' ? $first : 'R' ); ?></div>
                                <div class="fts-v2-review-head">
                                    <div class="fts-v2-review-headline">
                                        <strong class="fts-v2-review-name"><?php echo esc_html( $author ); ?></strong>
                                        <?php if ( $d_txt !== '' ) : ?>
                                            <span class="fts-v2-review-date"><?php echo esc_html( $d_txt ); ?></span>
                                        <?php endif; ?>
                                    </div>
                                    <div class="fts-v2-review-stars">
                                        <?php for ( $s = 1; $s <= 5; $s++ ) : ?>
                                            <i class="fa fa-star<?php echo $s <= $stars ? '' : '-o'; ?>"></i>
                                        <?php endfor; ?>
                                    </div>
                                </div>
                            </div>
                            <div class="fts-v2-review-body">
                                <p class="fts-v2-review-text"><?php echo esc_html( wp_trim_words( $content, 45 ) ); ?></p>
                            </div>
                        </article>
                    <?php endforeach; ?>
                    <?php if ( $review_count > $fts_reviews_limit ) : ?>
                        <div class="fts-v2-reviews-actions">
                            <button type="button" class="fts-v2-reviews-toggle" data-state="collapsed" data-more="<?php echo esc_attr__( 'View all reviews', 'fts' ); ?>" data-less="<?php echo esc_attr__( 'Show less', 'fts' ); ?>">
                                <?php echo esc_html__( 'View all reviews', 'fts' ); ?>
                            </button>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
            <?php endif; ?>

            <?php if ( $review_count <= 0 && ! empty( $reviews_tab_content ) ) : ?>
            <div class="fts-v2-reviews-tab-content">
                <?php echo do_shortcode( $reviews_tab_content ); ?>
            </div>
            <?php endif; ?>
        </div>
    </section>
    <?php endif; ?>

    <!-- ==================== FAQ ==================== -->
    <?php if ( $has_faq ) : ?>
    <section id="fts-v2-sec-faq" class="fts-v2-section fts-v2-accordion-item">
        <div class="fts-v2-accordion-header">
            <h2 class="fts-v2-section-title"><?php echo esc_html__( 'Frequently Asked Questions', 'fts' ); ?></h2>
            <span class="fts-v2-accordion-icon"><i class="fa fa-chevron-down"></i></span>
        </div>
        <div class="fts-v2-accordion-body">
            <div class="fts-v2-faq-list">
                <?php foreach ( $faq_titles as $key => $faq_title ) : if ( empty( $faq_title ) ) continue; ?>
                <div class="fts-v2-faq-item">
                    <div class="fts-v2-faq-question">
                        <span><?php echo esc_html( $faq_title ); ?></span>
                        <i class="fa fa-chevron-down"></i>
                    </div>
                    <?php if ( ! empty( $faq_content[ $key ] ) ) : ?>
                    <div class="fts-v2-faq-answer">
                        <?php echo wp_kses_post( $faq_content[ $key ] ); ?>
                    </div>
                    <?php endif; ?>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
    <?php endif; ?>

</div>
