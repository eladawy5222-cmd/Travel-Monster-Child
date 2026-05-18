<?php
/**
 * FTS Smart Search - Modern Search Tooltip
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class FTS_Smart_Search {

    public function __construct() {
        // Register Shortcode
        add_shortcode( 'fts_smart_search', array( $this, 'render_search_tool' ) );

        // Enqueue Assets
        add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_assets' ) );
    }

    public function enqueue_assets() {
        $css_file = get_stylesheet_directory() . '/fts-smart-search/assets/css/style.css';
        $js_file  = get_stylesheet_directory() . '/fts-smart-search/assets/js/script.js';
        $css_ver  = file_exists( $css_file ) ? (string) filemtime( $css_file ) : null;
        $js_ver   = file_exists( $js_file ) ? (string) filemtime( $js_file ) : null;
        wp_enqueue_style( 'fts-smart-search-style', get_stylesheet_directory_uri() . '/fts-smart-search/assets/css/style.css', array(), $css_ver );
        wp_enqueue_script( 'fts-smart-search-script', get_stylesheet_directory_uri() . '/fts-smart-search/assets/js/script.js', array( 'jquery' ), $js_ver, true );

        $archive_url = function_exists( 'get_post_type_archive_link' ) ? get_post_type_archive_link( 'trip' ) : '';
        if ( ! $archive_url ) {
            $archive_url = home_url( '/trips/' );
        }

        $currency = '$';
        if ( function_exists( 'fts_v2_get_active_currency_symbol' ) ) {
            $currency = (string) fts_v2_get_active_currency_symbol();
        } elseif ( function_exists( 'wp_travel_engine_get_currency_symbol' ) && function_exists( 'wp_travel_engine_get_currency_code' ) ) {
            $currency = html_entity_decode(
                (string) wp_travel_engine_get_currency_symbol( wp_travel_engine_get_currency_code() ),
                ENT_QUOTES | ENT_HTML5,
                'UTF-8'
            );
        }

        wp_localize_script( 'fts-smart-search-script', 'ftsSmartSearchData', array(
            'ajaxUrl'    => admin_url( 'admin-ajax.php' ),
            'nonce'      => wp_create_nonce( 'fts_hero_search' ),
            'archiveUrl' => $archive_url,
            'currency'   => $currency,
            'i18n'       => array(
                'searching'  => __( 'Searching...', 'fts' ),
                'no_results' => __( 'No trips found. Try different keywords.', 'fts' ),
                'view_all'   => __( 'View All %s Results', 'fts' ),
                'per_person' => __( '/person', 'fts' ),
            ),
        ) );
    }

    public function render_search_tool() {
        ob_start();
        $uid = function_exists( 'wp_unique_id' ) ? wp_unique_id( 'fts-ss-' ) : ( 'fts-ss-' . substr( md5( uniqid( '', true ) ), 0, 8 ) );
        $trigger_id = $uid . 'trigger';
        $tooltip_id = $uid . 'tooltip';
        $results_id = $uid . 'results';
        $input_id   = $uid . 'input';
        $btn_text   = function_exists( 'is_rtl' ) && is_rtl() ? 'بحث' : __( 'Search', 'fts' );
        ?>
        <div class="fts-smart-search-wrapper">
            <!-- Trigger Icon -->
            <button
                type="button"
                class="fts-ss-trigger"
                id="<?php echo esc_attr( $trigger_id ); ?>"
                aria-label="<?php echo esc_attr__( 'Search trips', 'fts' ); ?>"
                aria-expanded="false"
                aria-controls="<?php echo esc_attr( $tooltip_id ); ?>"
            >
                <svg class="fts-ss-icon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.3" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false"><circle cx="11" cy="11" r="7.5"></circle><path d="m21 21-4.6-4.6"></path></svg>
            </button>
            
            <!-- Tooltip Popup -->
            <div class="fts-ss-tooltip" id="<?php echo esc_attr( $tooltip_id ); ?>" role="dialog" aria-modal="true" aria-label="<?php echo esc_attr__( 'Search trips', 'fts' ); ?>" aria-hidden="true">
                <div class="fts-ss-tooltip-inner">
                    <!-- Close Button (Mobile Only) -->
                        <button type="button" class="fts-ss-close-btn" aria-label="<?php echo esc_attr__( 'Close search', 'fts' ); ?>"><svg class="fts-ss-icon" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false"><path d="M18 6 6 18"></path><path d="M6 6 18 18"></path></svg></button>

                    <!-- Search Form -->
                    <form role="search" method="get" class="fts-hero-search-form fts-ss-form" action="<?php echo esc_url( home_url( '/' ) ); ?>">
                        <input type="hidden" name="post_type" value="trip" />
                        <label for="<?php echo esc_attr( $input_id ); ?>" class="screen-reader-text"><?php esc_html_e( 'Search trips', 'fts' ); ?></label>
                        <div class="fts-hero-search-field">
                            <svg class="fts-hero-search-icon" width="20" height="20" viewBox="0 0 24 24" fill="none"
                                 stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
                            </svg>
                            <input
                                type="search"
                                id="<?php echo esc_attr( $input_id ); ?>"
                                class="fts-hero-search-input fts-ss-input"
                                placeholder="Where do you want to go?"
                                value="<?php echo get_search_query(); ?>"
                                name="s"
                                autocomplete="off"
                                aria-label="<?php echo esc_attr__( 'Search for a trip', 'fts' ); ?>"
                            />
                        </div>
                        <button type="submit" class="fts-hero-search-btn fts-ss-submit-btn" aria-label="<?php echo esc_attr__( 'Search', 'fts' ); ?>">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                 stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
                            </svg>
                            <?php echo esc_html( $btn_text ); ?>
                        </button>
                    </form>

                    <div class="fts-hero-results fts-ss-results" id="<?php echo esc_attr( $results_id ); ?>" role="listbox" aria-label="<?php echo esc_attr__( 'Search results', 'fts' ); ?>"></div>

                    <!-- Popular Tags (Activities) -->
                    <div class="fts-ss-popular">
                        <span class="fts-ss-label">Popular Activities</span>
                        <div class="fts-ss-tags">
                            <?php 
                            $activities = get_terms( array(
                                'taxonomy' => 'activities', // Changed to activities
                                'hide_empty' => true,
                                'number' => 8, // Limit to 8 popular items
                                'orderby' => 'count',
                                'order' => 'DESC'
                            ) );

                            if ( ! empty( $activities ) && ! is_wp_error( $activities ) ) {
                                foreach ( $activities as $activity ) {
                                    $link = get_term_link( $activity );
                                    echo '<a href="' . esc_url( $link ) . '" class="fts-ss-tag">' . esc_html( $activity->name ) . '</a>';
                                }
                            } else {
                                echo '<span class="fts-ss-empty">No popular activities found.</span>';
                            }
                            ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php
        return ob_get_clean();
    }
}

new FTS_Smart_Search();
