<?php
// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

// BEGIN ENQUEUE PARENT ACTION
// AUTO GENERATED - Do not modify or remove comment markers above or below:

if ( !function_exists( 'chld_thm_cfg_locale_css' ) ):
    function chld_thm_cfg_locale_css( $uri ){
        if ( empty( $uri ) && is_rtl() && file_exists( get_template_directory() . '/rtl.css' ) )
            $uri = get_template_directory_uri() . '/rtl.css';
        return $uri;
    }
endif;
add_filter( 'locale_stylesheet_uri', 'chld_thm_cfg_locale_css' );
         
if ( !function_exists( 'child_theme_configurator_css' ) ):
    function child_theme_configurator_css() {
        $dependencies = array( 'travel-monster-style' );
        if ( wp_style_is( 'travel-monster-elementor', 'registered' ) ) {
            $dependencies[] = 'travel-monster-elementor';
        }
        wp_enqueue_style( 'chld_thm_cfg_child', trailingslashit( get_stylesheet_directory_uri() ) . 'style.css', $dependencies );
    }
endif;
add_action( 'wp_enqueue_scripts', 'child_theme_configurator_css', 20 );

// END ENQUEUE PARENT ACTION

add_action( 'wp_enqueue_scripts', function() {
    if ( ! is_singular( 'trip' ) ) return;
    if ( has_action( 'wp_enqueue_scripts', array( 'FTS_Trip_Redesign_V2', 'enqueue_assets' ) ) ) return;
    if ( ! function_exists( 'get_field' ) ) return;
    $trip_id = get_the_ID();
    if ( ! $trip_id ) return;

    $code = (string) get_field( 'trustindex_code', $trip_id );
    if ( trim( $code ) === '' ) return;

    wp_enqueue_script(
        'fts-trustindex-loader-legacy',
        'https://cdn.trustindex.io/loader.js?49f81de492564412a126bfa9e75',
        array(),
        null,
        array( 'in_footer' => true, 'strategy' => 'defer' )
    );
}, 99 );

add_filter( 'template_include', function( $template ) {
    if ( ! is_singular( 'trip' ) ) return $template;
    $custom = get_stylesheet_directory() . '/wp-travel-engine/single-trip.php';
    if ( file_exists( $custom ) ) return $custom;
    return $template;
}, 999 );

add_action( 'wp_enqueue_scripts', function() {
    if ( ! is_singular( 'trip' ) ) return;
    if ( ! function_exists( 'get_rocket_option' ) ) return;
    add_filter( 'pre_get_rocket_option_async_css', '__return_zero' );
    add_filter( 'pre_get_rocket_option_minify_css', '__return_zero' );
    add_filter( 'pre_get_rocket_option_combine_css', '__return_zero' );
    add_filter( 'pre_get_rocket_option_minify_js', '__return_zero' );
    add_filter( 'pre_get_rocket_option_combine_js', '__return_zero' );
    add_filter( 'pre_get_rocket_option_defer_all_js', '__return_zero' );
    add_filter( 'pre_get_rocket_option_delay_js', '__return_zero' );
}, 0 );

function fts_secure_external_requests_enabled() {
    return (bool) get_option( 'fts_secure_external_requests_enabled', 1 );
}

function fts_https_upgrade_url( $url ) {
    if ( ! is_string( $url ) ) return $url;
    $url = trim( $url );
    if ( $url === '' ) return $url;
    if ( stripos( $url, 'http://' ) === 0 ) {
        return 'https://' . substr( $url, 7 );
    }
    if ( strpos( $url, '//' ) === 0 ) {
        return 'https:' . $url;
    }
    return $url;
}

function fts_is_local_host( $host ) {
    $host = strtolower( trim( (string) $host ) );
    if ( $host === '' ) return true;
    if ( $host === 'localhost' || $host === '127.0.0.1' ) return true;
    $site_host = wp_parse_url( home_url(), PHP_URL_HOST );
    $site_host = strtolower( trim( (string) $site_host ) );
    return $site_host !== '' && $host === $site_host;
}

function fts_is_webfont_host( $host ) {
    $host = strtolower( trim( (string) $host ) );
    if ( $host === '' ) return false;
    $hosts = array(
        'fonts.googleapis.com',
        'fonts.gstatic.com',
        'fonts.bunny.net',
        'use.typekit.net',
        'use.fontawesome.com',
        'fonts.bootstrapcdn.com',
    );
    return in_array( $host, $hosts, true );
}

function fts_allowed_content_types_for_url( $url ) {
    $path = (string) wp_parse_url( $url, PHP_URL_PATH );
    $ext  = strtolower( pathinfo( $path, PATHINFO_EXTENSION ) );
    if ( $ext === 'css' ) {
        return array( 'text/css' );
    }
    if ( in_array( $ext, array( 'woff', 'woff2', 'ttf', 'otf', 'eot' ), true ) ) {
        return array(
            'font/',
            'application/font-woff',
            'application/font-woff2',
            'application/octet-stream',
        );
    }
    return null;
}

function fts_external_remote_request( $url, $args = array(), $allowed_content_types = null ) {
    $url = fts_https_upgrade_url( $url );
    if ( ! is_array( $args ) ) $args = array();
    $args['fts_internal_request'] = true;

    $timeout = isset( $args['timeout'] ) ? floatval( $args['timeout'] ) : 6.0;
    if ( $timeout <= 0 ) $timeout = 6.0;
    $args['timeout'] = $timeout;

    if ( ! isset( $args['redirection'] ) ) $args['redirection'] = 3;

    $response = wp_safe_remote_request( $url, $args );
    if ( is_wp_error( $response ) ) return $response;

    $code = (int) wp_remote_retrieve_response_code( $response );
    if ( $code < 200 || $code > 299 ) {
        $filename = isset( $response['filename'] ) ? (string) $response['filename'] : '';
        if ( $filename !== '' && file_exists( $filename ) ) {
            @unlink( $filename );
        }
        return new WP_Error(
            'fts_external_http_status',
            'Non-2xx response from external request.',
            array(
                'url'  => $url,
                'code' => $code,
            )
        );
    }

    if ( is_array( $allowed_content_types ) && ! empty( $allowed_content_types ) ) {
        $content_type = '';
        if ( isset( $response['headers'] ) ) {
            $content_type = (string) wp_remote_retrieve_header( $response, 'content-type' );
        }
        $content_type_lc = strtolower( trim( explode( ';', $content_type )[0] ?? '' ) );
        $ok = false;
        foreach ( $allowed_content_types as $allowed ) {
            $allowed = strtolower( (string) $allowed );
            if ( $allowed === '' ) continue;
            if ( substr( $allowed, -1 ) === '/' ) {
                if ( strpos( $content_type_lc, $allowed ) === 0 ) {
                    $ok = true;
                    break;
                }
            } elseif ( $content_type_lc === $allowed ) {
                $ok = true;
                break;
            }
        }
        if ( ! $ok ) {
            $filename = isset( $response['filename'] ) ? (string) $response['filename'] : '';
            if ( $filename !== '' && file_exists( $filename ) ) {
                @unlink( $filename );
            }
            return new WP_Error(
                'fts_external_unexpected_content_type',
                'Unexpected content-type from external request.',
                array(
                    'url'          => $url,
                    'content_type' => $content_type,
                )
            );
        }
    }

    $is_stream = ! empty( $args['stream'] );
    if ( ! $is_stream ) {
        $body = wp_remote_retrieve_body( $response );
        if ( is_string( $body ) && strlen( $body ) > 3 * 1024 * 1024 ) {
            return new WP_Error(
                'fts_external_body_too_large',
                'External response body too large.',
                array( 'url' => $url )
            );
        }
    } else {
        $filename = isset( $response['filename'] ) ? (string) $response['filename'] : '';
        if ( $filename !== '' && file_exists( $filename ) && filesize( $filename ) > 10 * 1024 * 1024 ) {
            @unlink( $filename );
            return new WP_Error(
                'fts_external_file_too_large',
                'External streamed file too large.',
                array( 'url' => $url )
            );
        }
    }

    return $response;
}

function fts_external_get_json( $url, $args = array() ) {
    $response = fts_external_remote_request( $url, $args, array( 'application/json' ) );
    if ( is_wp_error( $response ) ) return $response;
    $body = wp_remote_retrieve_body( $response );
    if ( ! is_string( $body ) || trim( $body ) === '' ) {
        return new WP_Error( 'fts_external_empty_json', 'Empty JSON response.', array( 'url' => $url ) );
    }
    $data = json_decode( $body );
    if ( json_last_error() !== JSON_ERROR_NONE ) {
        return new WP_Error(
            'fts_external_bad_json',
            'Invalid JSON response.',
            array(
                'url'   => $url,
                'error' => json_last_error_msg(),
            )
        );
    }
    return $data;
}

add_filter( 'pre_http_request', function( $preempt, $r, $url ) {
    if ( ! fts_secure_external_requests_enabled() ) return $preempt;
    if ( is_array( $r ) && ! empty( $r['fts_internal_request'] ) ) return $preempt;

    $host = wp_parse_url( $url, PHP_URL_HOST );
    if ( $host && fts_is_local_host( $host ) ) return $preempt;

    $upgraded   = fts_https_upgrade_url( $url );
    $is_webfont = $host && fts_is_webfont_host( $host );
    if ( $upgraded === $url && ! $is_webfont ) return $preempt;

    $args = is_array( $r ) ? $r : array();
    $allowed = null;
    if ( $is_webfont ) {
        $allowed = fts_allowed_content_types_for_url( $upgraded );
        if ( $allowed === null ) {
            $allowed = array( 'text/css', 'font/', 'application/octet-stream' );
        }
    }

    $resp = fts_external_remote_request( $upgraded, $args, $allowed );
    return $resp;
}, 1, 3 );

add_action( 'admin_init', function() {
    register_setting(
        'fts_security',
        'fts_secure_external_requests_enabled',
        array(
            'type'              => 'boolean',
            'sanitize_callback' => function( $v ) { return (int) (bool) $v; },
            'default'           => 1,
        )
    );
} );

add_action( 'admin_menu', function() {
    add_options_page(
        'FTS Security',
        'FTS Security',
        'manage_options',
        'fts-security',
        function() {
            if ( ! current_user_can( 'manage_options' ) ) return;

            $test = '';
            if ( isset( $_GET['fts_security_test'] ) && wp_verify_nonce( (string) $_GET['_wpnonce'], 'fts_security_test' ) ) {
                $results = array();

                $vimeo_test_url = add_query_arg(
                    array( 'url' => 'https://vimeo.com/76979871' ),
                    'https://vimeo.com/api/oembed.json'
                );
                $vimeo = fts_external_get_json( $vimeo_test_url, array( 'timeout' => 6 ) );
                $results[] = array(
                    'label' => 'Vimeo oEmbed',
                    'ok'    => ! is_wp_error( $vimeo ) && isset( $vimeo->thumbnail_url ),
                    'msg'   => is_wp_error( $vimeo ) ? $vimeo->get_error_message() : 'OK',
                );

                $font_test_url = 'https://fonts.googleapis.com/css2?family=Roboto:wght@400&display=swap';
                $font_resp = fts_external_remote_request( $font_test_url, array( 'timeout' => 6 ), array( 'text/css' ) );
                $results[] = array(
                    'label' => 'Webfont CSS',
                    'ok'    => ! is_wp_error( $font_resp ),
                    'msg'   => is_wp_error( $font_resp ) ? $font_resp->get_error_message() : 'OK',
                );

                ob_start();
                echo '<div class="notice notice-info"><p><strong>FTS Security Tests</strong></p><ul style="margin:0.5em 0 0 1.2em;">';
                foreach ( $results as $row ) {
                    echo '<li>' . esc_html( $row['label'] . ': ' . ( $row['ok'] ? 'PASS' : 'FAIL' ) . ' — ' . $row['msg'] ) . '</li>';
                }
                echo '</ul></div>';
                $test = ob_get_clean();
            }

            echo '<div class="wrap"><h1>FTS Security</h1>';
            echo $test;
            echo '<form method="post" action="options.php">';
            settings_fields( 'fts_security' );
            echo '<table class="form-table" role="presentation"><tbody>';
            echo '<tr><th scope="row">Secure external requests</th><td>';
            echo '<label><input type="checkbox" name="fts_secure_external_requests_enabled" value="1" ' . checked( 1, get_option( 'fts_secure_external_requests_enabled', 1 ), false ) . '> Enable HTTPS upgrade + strict handling for webfonts/Vimeo</label>';
            echo '</td></tr>';
            echo '</tbody></table>';
            submit_button();
            echo '</form>';

            $test_url = wp_nonce_url( admin_url( 'options-general.php?page=fts-security&fts_security_test=1' ), 'fts_security_test' );
            echo '<p><a class="button button-secondary" href="' . esc_url( $test_url ) . '">Run tests</a></p>';
            echo '</div>';
        }
    );
} );

function fts_parse_float( $value ) {
    if ( is_float( $value ) ) return $value;
    if ( is_int( $value ) ) return floatval( $value );
    if ( is_numeric( $value ) ) return floatval( $value );
    if ( ! is_string( $value ) ) return 0.0;

    $s = trim( $value );
    if ( $s === '' ) return 0.0;
    $s = wp_strip_all_tags( $s );
    $s = html_entity_decode( $s, ENT_QUOTES | ENT_HTML5, 'UTF-8' );
    $s = preg_replace( '/[^\d,\.\-]/', '', $s );
    $s = trim( (string) $s );
    if ( $s === '' || $s === '-' ) return 0.0;

    $has_dot = strpos( $s, '.' ) !== false;
    $has_comma = strpos( $s, ',' ) !== false;
    if ( $has_dot && $has_comma ) {
        $s = str_replace( ',', '', $s );
    } elseif ( $has_comma && ! $has_dot ) {
        $s = str_replace( ',', '.', $s );
    }

    return floatval( $s );
}

add_filter( 'wptravelengine_package_traveler_price', function( $price, $args ) {
    return fts_parse_float( $price );
}, 1, 2 );


function fts_v2_default_free_cancellation_text( $trip_id = 0 ) {

    $v = '';
    if ( $trip_id > 0 ) {
        if ( function_exists( 'get_field' ) ) {
            $v = (string) get_field( 'free_cancellation_text', $trip_id );
        }
        if ( trim( $v ) === '' ) {
            $v = (string) get_post_meta( $trip_id, 'free_cancellation_text', true );
        }
    }

    $v = trim( $v );
    if ( $v !== '' ) return $v;

    $h = $trip_id > 0 ? intval( get_post_meta( $trip_id, 'fts_cancel_hours', true ) ) : 0;
    if ( $h > 0 ) {
        return 'Free cancellation up to ' . $h . ' hours before the tour start time (unless otherwise stated).';
    }
    return '';
}

add_filter( 'fts_v2_free_cancellation_text', function( $text, $trip_id, $settings ) {
    $text = is_string( $text ) ? trim( $text ) : '';
    if ( $text !== '' ) return $text;
    return fts_v2_default_free_cancellation_text( $trip_id );
}, 10, 3 );

add_filter( 'fts_v2_sidebar_trust_items', function( $items, $trip_id, $settings ) {
    $items = is_array( $items ) ? $items : array();
    $out = array();

    foreach ( $items as $it ) {
        if ( ! is_array( $it ) ) continue;
        $t = isset( $it['text'] ) ? trim( (string) $it['text'] ) : '';
        $lc = strtolower( $t );
        if ( $t !== '' && strpos( $lc, 'cancellation' ) !== false ) continue;
        $out[] = $it;
    }

    $fc = fts_v2_default_free_cancellation_text( $trip_id );
    if ( is_string( $fc ) && trim( $fc ) !== '' ) {
        $out[] = array( 'type' => 'clock', 'text' => trim( $fc ) );
    }

    return $out;
}, 10, 3 );

function fts_v2_get_trip_last_booked_timestamp( $trip_id = 0 ) {
    $trip_id = intval( $trip_id );
    if ( $trip_id <= 0 ) return 0;

    $ts = intval( get_post_meta( $trip_id, 'fts_last_booked_ts', true ) );
    if ( $ts > 0 ) return $ts;

    $q = new WP_Query( array(
        'post_type'              => 'booking',
        'posts_per_page'         => 1,
        'post_status'            => 'any',
        'orderby'                => 'date',
        'order'                  => 'DESC',
        'no_found_rows'          => true,
        'update_post_meta_cache' => false,
        'update_post_term_cache' => false,
        'meta_query'             => array(
            array(
                'key'     => 'trip_id',
                'value'   => $trip_id,
                'compare' => '=',
                'type'    => 'NUMERIC',
            ),
        ),
    ) );

    if ( ! empty( $q->posts ) ) {
        $p = $q->posts[0];
        if ( $p && isset( $p->post_date_gmt ) && $p->post_date_gmt ) {
            $t = strtotime( $p->post_date_gmt . ' GMT' );
            if ( $t ) return intval( $t );
        }
        if ( $p && isset( $p->post_date ) && $p->post_date ) {
            $t = strtotime( $p->post_date );
            if ( $t ) return intval( $t );
        }
    }

    return 0;
}

function fts_v2_get_trip_last_booked_minutes( $trip_id = 0 ) {
    $ts = fts_v2_get_trip_last_booked_timestamp( $trip_id );
    if ( $ts <= 0 ) return 0;
    $now = time();
    if ( $now < $ts ) return 0;
    $mins = intval( floor( ( max( 0, $now - $ts ) ) / 60 ) );
    if ( $mins < 1 ) $mins = 1;
    if ( $mins > 10080 ) return 0;
    return $mins;
}

add_filter( 'fts_v2_social_proof', function( $data, $trip_id, $settings ) {
    $data = is_array( $data ) ? $data : array();
    $mins = fts_v2_get_trip_last_booked_minutes( $trip_id );
    if ( $mins > 0 ) $data['last_booked_minutes'] = $mins;
    if ( ! isset( $data['viewer_count'] ) ) $data['viewer_count'] = 0;
    return $data;
}, 10, 3 );

add_filter( 'fts_v2_enable_social_proof', function( $enabled, $trip_id, $settings ) {
    return fts_v2_get_trip_last_booked_timestamp( $trip_id ) > 0;
}, 10, 3 );


/* ================ بيانات الريڤيو اليدوية ================ */
function ft_static_trip_reviews() {
    return [
        // اضف سطر لكل رحلة (الـ slug = آخر جزء من الرابط بدون slash)
        'hurghada-luxor-valley-of-the-kings-tutankhamun-tomb-day-trip' => [
            'rating' => 4.5,
            'count'  => 958,
        ],
        // 'slug-تاني' => ['rating'=>4.2,'count'=>120],
    ];
}

function ft_get_trip_review_data( $post_id ) {
    // slug بدون slash فى آخره
    $slug = untrailingslashit( basename( get_permalink( $post_id ) ) );
    $data = ft_static_trip_reviews();
    return isset( $data[ $slug ] ) ? $data[ $slug ] : false;
}

add_action('wp_travel_engine_after_booking_inserted', function($booking_id, $booking_data) {
    $trip_id = isset($booking_data['trip_id']) ? intval($booking_data['trip_id']) : 0;

    if ($trip_id > 0) {
        update_post_meta($trip_id, 'fts_last_booked_ts', time());

        // 1. نحاول أولًا جلب Trip Code من حقل ACF أو WP Travel Engine
        $trip_code = '';
        
        // لو في WP Travel Engine أو ACF، غالبًا يكون اسمه 'trip_code'
        if (function_exists('get_field')) {
            $trip_code = get_field('trip_code', $trip_id);
        }

        // 2. لو لم تنجح نحاول من الحقول العادية
        if (!$trip_code) {
            $trip_code = get_post_meta($trip_id, 'trip_code', true);
        }

        // 3. كخطوة أخيرة نجرب post meta باسم 'code'
        if (!$trip_code) {
            $trip_code = get_post_meta($trip_id, 'code', true);
        }

        // 4. نحفظها في الحجز إن وجدت
        if ($trip_code) {
            update_post_meta($booking_id, 'trip_code', sanitize_text_field($trip_code));
        }
    }
}, 10, 2);

function fts_v2_touch_last_booked_ts_on_inventory_change( $meta_id, $object_id, $meta_key, $_meta_value ) {
    if ( $meta_key !== '_booking_inventory' ) return;
    $object_id = intval( $object_id );
    if ( $object_id <= 0 ) return;
    if ( get_post_type( $object_id ) !== 'trip' ) return;
    update_post_meta( $object_id, 'fts_last_booked_ts', time() );
}

add_action( 'added_post_meta', 'fts_v2_touch_last_booked_ts_on_inventory_change', 10, 4 );
add_action( 'updated_post_meta', 'fts_v2_touch_last_booked_ts_on_inventory_change', 10, 4 );

function fts_v2_sync_last_booked_ts_from_inventory( $trip_id = 0 ) {
    $trip_id = intval( $trip_id );
    if ( $trip_id <= 0 ) return;

    $inv = get_post_meta( $trip_id, '_booking_inventory', true );
    $has_inv = is_array( $inv ) ? ! empty( $inv ) : ( trim( (string) $inv ) !== '' );
    if ( ! $has_inv ) return;

    $hash = md5( maybe_serialize( $inv ) );
    $prev = (string) get_post_meta( $trip_id, 'fts_booking_inventory_hash', true );

    if ( $prev !== $hash ) {
        update_post_meta( $trip_id, 'fts_booking_inventory_hash', $hash );
        update_post_meta( $trip_id, 'fts_last_booked_ts', time() );
        return;
    }

    $ts = intval( get_post_meta( $trip_id, 'fts_last_booked_ts', true ) );
    if ( $ts <= 0 ) {
        update_post_meta( $trip_id, 'fts_last_booked_ts', time() );
    }
}

add_action( 'wp', function() {
    if ( ! is_singular( 'trip' ) ) return;
    $trip_id = get_queried_object_id();
    if ( ! $trip_id ) return;
    fts_v2_sync_last_booked_ts_from_inventory( $trip_id );
}, 9 );

function fts_v2_trip_viewers_route() {
    register_rest_route(
        'fts/v1',
        '/trip-viewers',
        array(
            'methods'             => 'GET',
            'permission_callback' => '__return_true',
            'callback'            => function( WP_REST_Request $req ) {
                $trip_id   = intval( $req->get_param( 'trip_id' ) );
                $viewer_id = (string) $req->get_param( 'viewer_id' );
                $viewer_id = preg_replace( '/[^a-zA-Z0-9_-]/', '', $viewer_id );
                if ( $trip_id <= 0 || $viewer_id === '' || strlen( $viewer_id ) > 64 ) {
                    return new WP_REST_Response( array( 'viewer_count' => 0 ), 200 );
                }

                $key = 'fts_v2_viewers_' . $trip_id;
                $now = time();

                $data = get_site_transient( $key );
                if ( ! is_array( $data ) ) $data = array();

                $cutoff = $now - 120;
                foreach ( $data as $k => $ts ) {
                    if ( intval( $ts ) < $cutoff ) unset( $data[ $k ] );
                }

                $data[ $viewer_id ] = $now;

                if ( count( $data ) > 250 ) {
                    asort( $data );
                    $data = array_slice( $data, -200, null, true );
                }

                set_site_transient( $key, $data, 10 * MINUTE_IN_SECONDS );

                return new WP_REST_Response(
                    array(
                        'viewer_count' => count( $data ),
                        'window_sec'   => 120,
                    ),
                    200
                );
            },
        )
    );
}

add_action( 'rest_api_init', 'fts_v2_trip_viewers_route' );
// Register the "trip_code" meta to appear in the REST API response
function register_trip_code_api_field() {
    register_rest_field('booking', 'trip_code', array(
        'get_callback'    => function($object) {
            return get_post_meta($object['id'], 'trip_code', true);
        },
        'update_callback' => null,
        'schema'          => null,
    ));
}
add_action('rest_api_init', 'register_trip_code_api_field');

/* ================ Package Taxonomy & Functionality ================ */

// 1. إنشاء Taxonomy للـ Package
function create_package_taxonomy() {
    $labels = array(
        'name'              => 'Packages',
        'singular_name'     => 'Package',
        'search_items'      => 'Search Packages',
        'all_items'         => 'All Packages',
        'edit_item'         => 'Edit Package',
        'update_item'       => 'Update Package',
        'add_new_item'      => 'Add New Package',
        'new_item_name'     => 'New Package Name',
        'menu_name'         => 'Packages',
    );

    $args = array(
        'hierarchical'      => true,
        'labels'            => $labels,
        'show_ui'           => true,
        'show_admin_column' => true,
        'query_var'         => true,
        'rewrite'           => array(
            'slug' => 'packages',
            'with_front' => false,
            'hierarchical' => true,
        ),
        'show_in_rest'      => true,
        'public'            => true,
        'show_in_nav_menus' => true,
        'publicly_queryable' => true,
    );

    register_taxonomy('packages', 'trip', $args);
}
add_action('init', 'create_package_taxonomy');

// إضافة Packages للفلتر الأصلي في WP Travel Engine
function add_packages_to_archive_filter($taxonomies) {
    $taxonomies['packages'] = array(
        'taxonomy' => 'packages',
        'title' => __('Packages', 'wp-travel-engine'),
    );
    return $taxonomies;
}
add_filter('wte_advanced_search_filter_taxonomies', 'add_packages_to_archive_filter');

// AJAX handler لفلترة جميع رحلات الـ Packages
function filter_all_package_trips_ajax() {
    // تحقق من الأمان
    if (!wp_verify_nonce($_POST['nonce'], 'filter_all_package_trips')) {
        wp_die('Security check failed');
    }
    
    $filters = $_POST['filters'];
    
    // بناء الـ tax_query
    $tax_query = array('relation' => 'AND');
    
    // إضافة شرط أن الرحلة لازم تكون في أي package (فقط لو مفيش packages محددة)
    if (empty($filters['packages'])) {
        $tax_query[] = array(
            'taxonomy' => 'packages',
            'operator' => 'EXISTS',
        );
    }
    
    // إضافة فلاتر إضافية
    if (!empty($filters['destination']) && is_array($filters['destination'])) {
        $tax_query[] = array(
            'taxonomy' => 'destination',
            'field' => 'slug',
            'terms' => $filters['destination'],
        );
    }
    
    if (!empty($filters['activities']) && is_array($filters['activities'])) {
        $tax_query[] = array(
            'taxonomy' => 'activities',
            'field' => 'slug',
            'terms' => $filters['activities'],
        );
    }
    
    if (!empty($filters['trip_types']) && is_array($filters['trip_types'])) {
        $tax_query[] = array(
            'taxonomy' => 'trip_types',
            'field' => 'slug',
            'terms' => $filters['trip_types'],
        );
    }
    
    if (!empty($filters['packages']) && is_array($filters['packages'])) {
        $tax_query[] = array(
            'taxonomy' => 'packages',
            'field' => 'slug',
            'terms' => $filters['packages'],
        );
    }
    
    if (!empty($filters['difficulty']) && is_array($filters['difficulty'])) {
        $tax_query[] = array(
            'taxonomy' => 'difficulty',
            'field' => 'slug',
            'terms' => $filters['difficulty'],
        );
    }
    
    // إضافة meta query للـ Duration والـ Price
    $meta_query = array('relation' => 'AND');
    
    // فلتر المدة
    if (!empty($filters['duration']) && is_array($filters['duration'])) {
        $duration_conditions = array('relation' => 'OR');
        foreach ($filters['duration'] as $duration_range) {
            switch ($duration_range) {
                case '1-3':
                    $duration_conditions[] = array(
                        'key' => 'wp_travel_engine_setting',
                        'value' => array(1, 2, 3),
                        'compare' => 'IN',
                        'type' => 'NUMERIC'
                    );
                    break;
                case '4-7':
                    $duration_conditions[] = array(
                        'key' => 'wp_travel_engine_setting',
                        'value' => array(4, 5, 6, 7),
                        'compare' => 'IN',
                        'type' => 'NUMERIC'
                    );
                    break;
                case '8-14':
                    $duration_conditions[] = array(
                        'relation' => 'AND',
                        array(
                            'key' => 'wp_travel_engine_setting',
                            'value' => 8,
                            'compare' => '>=',
                            'type' => 'NUMERIC'
                        ),
                        array(
                            'key' => 'wp_travel_engine_setting',
                            'value' => 14,
                            'compare' => '<=',
                            'type' => 'NUMERIC'
                        )
                    );
                    break;
                case '15+':
                    $duration_conditions[] = array(
                        'key' => 'wp_travel_engine_setting',
                        'value' => 15,
                        'compare' => '>=',
                        'type' => 'NUMERIC'
                    );
                    break;
            }
        }
        if (count($duration_conditions) > 1) {
            $meta_query[] = $duration_conditions;
        }
    }
    
    
    $paged = max( 1, intval( $_POST['paged'] ?? 1 ) );

    // Query للرحلات
    $args = array(
        'post_type' => 'trip',
        'post_status' => 'publish',
        'posts_per_page' => 24,
        'paged' => $paged,
        'tax_query' => $tax_query,
    );
    
    if (count($meta_query) > 1) {
        $args['meta_query'] = $meta_query;
    }
    
    $trips = new WP_Query($args);
    
    ob_start();
    if ($trips->have_posts()) :
        while ($trips->have_posts()) : $trips->the_post();
            if (function_exists('wptravelengine_get_template')) {
                // Initialize WP Travel Engine variables properly
                global $post;
                $all_args = wte_get_trip_details( $post->ID );
                $all_args['user_wishlists'] = wptravelengine_user_wishlists();
                $all_args['related_query'] = true;
                foreach ( $all_args as $key => $value ) {
                    wptravelengine_set_template_args( array( $key => $value ) );
                }
                wptravelengine_get_template('content-related-trip.php');
            } else {
                // Fallback display
                ?>
                <div class="category-trips-single-wrap">
                    <div class="category-trips-single">
                        <figure class="category-trip-fig">
                            <a href="<?php the_permalink(); ?>">
                                <?php
                                if (has_post_thumbnail()) {
                                    the_post_thumbnail('medium');
                                } else {
                                    echo '<div class="no-image">No Image</div>';
                                }
                                ?>
                            </a>
                        </figure>
                        <div class="category-trip-detail">
                            <h3 class="category-trip-title">
                                <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                            </h3>
                            
                            <?php
                            // إضافة سكريبت Trustindex من الـ custom field
                            $trustindex_code = get_field('trustindex_code', get_the_ID());
                            if (!empty($trustindex_code)) {
                                echo wp_kses_post($trustindex_code);
                            }
                            ?>
                            
                            <div class="category-trip-excerpt">
                                <?php the_excerpt(); ?>
                            </div>
                        </div>
                    </div>
                </div>
                <?php
            }
        endwhile;
    else :
        echo '<div class="no-trips-found">';
        echo '<h3>No trips found with the selected filters.</h3>';
        echo '<p>Please try different filter options.</p>';
        echo '</div>';
    endif;
    
    wp_reset_postdata();
    $html = ob_get_clean();
    
    wp_send_json_success(array(
        'html'     => $html,
        'has_more' => $paged < $trips->max_num_pages,
        'total'    => $trips->found_posts,
        'paged'    => $paged,
    ));
}
add_action('wp_ajax_filter_all_package_trips', 'filter_all_package_trips_ajax');
add_action('wp_ajax_nopriv_filter_all_package_trips', 'filter_all_package_trips_ajax');

// AJAX handler للفلترة في صفحة Package منفردة
function filter_single_package_trips_ajax() {
    // التحقق من الـ nonce
    if (!wp_verify_nonce($_POST['nonce'], 'filter_single_package_trips')) {
        wp_die('Security check failed');
    }
    
    $filters = $_POST['filters'];
    $package_slug = sanitize_text_field($_POST['package_slug']);
    
    // بناء الـ tax_query
    $tax_query = array('relation' => 'AND');
    
    // إضافة Package الحالي
    $tax_query[] = array(
        'taxonomy' => 'packages',
        'field' => 'slug',
        'terms' => $package_slug,
    );
    
    if (!empty($filters['destination']) && is_array($filters['destination'])) {
        $tax_query[] = array(
            'taxonomy' => 'destination',
            'field' => 'slug',
            'terms' => $filters['destination'],
        );
    }
    
    if (!empty($filters['activities']) && is_array($filters['activities'])) {
        $tax_query[] = array(
            'taxonomy' => 'activities',
            'field' => 'slug',
            'terms' => $filters['activities'],
        );
    }
    
    if (!empty($filters['trip_types']) && is_array($filters['trip_types'])) {
        $tax_query[] = array(
            'taxonomy' => 'trip_types',
            'field' => 'slug',
            'terms' => $filters['trip_types'],
        );
    }
    
    if (!empty($filters['difficulty']) && is_array($filters['difficulty'])) {
        $tax_query[] = array(
            'taxonomy' => 'difficulty',
            'field' => 'slug',
            'terms' => $filters['difficulty'],
        );
    }
    
    // إضافة meta query للـ Duration
    $meta_query = array('relation' => 'AND');
    
    // فلتر المدة
    if (!empty($filters['duration']) && is_array($filters['duration'])) {
        $duration_conditions = array('relation' => 'OR');
        foreach ($filters['duration'] as $duration_range) {
            switch ($duration_range) {
                case '1-3':
                    $duration_conditions[] = array(
                        'key' => 'wp_travel_engine_setting',
                        'value' => array(1, 2, 3),
                        'compare' => 'IN',
                        'type' => 'NUMERIC'
                    );
                    break;
                case '4-7':
                    $duration_conditions[] = array(
                        'key' => 'wp_travel_engine_setting',
                        'value' => array(4, 5, 6, 7),
                        'compare' => 'IN',
                        'type' => 'NUMERIC'
                    );
                    break;
                case '8-14':
                    $duration_conditions[] = array(
                        'relation' => 'AND',
                        array(
                            'key' => 'wp_travel_engine_setting',
                            'value' => 8,
                            'compare' => '>=',
                            'type' => 'NUMERIC'
                        ),
                        array(
                            'key' => 'wp_travel_engine_setting',
                            'value' => 14,
                            'compare' => '<=',
                            'type' => 'NUMERIC'
                        )
                    );
                    break;
                case '15+':
                    $duration_conditions[] = array(
                        'key' => 'wp_travel_engine_setting',
                        'value' => 15,
                        'compare' => '>=',
                        'type' => 'NUMERIC'
                    );
                    break;
            }
        }
        if (count($duration_conditions) > 1) {
            $meta_query[] = $duration_conditions;
        }
    }
    
    $paged = max( 1, intval( $_POST['paged'] ?? 1 ) );

    // Query للرحلات
    $args = array(
        'post_type' => 'trip',
        'post_status' => 'publish',
        'posts_per_page' => 24,
        'paged' => $paged,
        'tax_query' => $tax_query,
    );
    
    if (count($meta_query) > 1) {
        $args['meta_query'] = $meta_query;
    }
    
    $trips = new WP_Query($args);
    
    ob_start();
    if ($trips->have_posts()) :
        while ($trips->have_posts()) : $trips->the_post();
            if (function_exists('wptravelengine_get_template')) {
                // Initialize WP Travel Engine variables properly
                global $post;
                $all_args = wte_get_trip_details( $post->ID );
                $all_args['user_wishlists'] = wptravelengine_user_wishlists();
                $all_args['related_query'] = true;
                foreach ( $all_args as $key => $value ) {
                    wptravelengine_set_template_args( array( $key => $value ) );
                }
                wptravelengine_get_template('content-related-trip.php');
            } else {
                // Fallback display
                ?>
                <div class="category-trips-single-wrap">
                    <div class="category-trips-single">
                        <figure class="category-trip-fig">
                            <a href="<?php the_permalink(); ?>">
                                <?php
                                if (has_post_thumbnail()) {
                                    the_post_thumbnail('medium');
                                } else {
                                    echo '<div class="no-image">No Image</div>';
                                }
                                ?>
                            </a>
                        </figure>
                        <div class="category-trip-detail">
                            <h3 class="category-trip-title">
                                <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                            </h3>
                            
                            <?php
                            // إضافة سكريبت Trustindex من الـ custom field
                            $trustindex_code = get_field('trustindex_code', get_the_ID());
                            if (!empty($trustindex_code)) {
                                echo wp_kses_post($trustindex_code);
                            }
                            ?>
                            
                            <div class="category-trip-excerpt">
                                <?php the_excerpt(); ?>
                            </div>
                            <div class="category-trip-meta">
                                <?php
                                $trip_meta = get_post_meta(get_the_ID(), 'wp_travel_engine_setting', true);
                                if (isset($trip_meta['trip_duration']) && !empty($trip_meta['trip_duration'])) {
                                    echo '<span class="trip-duration">' . esc_html($trip_meta['trip_duration']) . ' Days</span>';
                                }
                                ?>
                            </div>
                        </div>
                    </div>
                </div>
                <?php
            }
        endwhile;
    else :
        echo '<div class="no-trips-found">';
        echo '<h3>' . __('No trips found with selected filters.', 'wp-travel-engine') . '</h3>';
        echo '<p>' . __('Please try different filter options.', 'wp-travel-engine') . '</p>';
        echo '</div>';
    endif;
    
    wp_reset_postdata();
    $html = ob_get_clean();
    
    wp_send_json_success(array(
        'html'     => $html,
        'count'    => $trips->found_posts,
        'has_more' => $paged < $trips->max_num_pages,
        'paged'    => $paged,
    ));
}

add_action('wp_ajax_filter_single_package_trips', 'filter_single_package_trips_ajax');
add_action('wp_ajax_nopriv_filter_single_package_trips', 'filter_single_package_trips_ajax');

// AJAX handler للـ Load More
function load_more_package_trips_ajax() {
    // التحقق من الـ nonce
    if (!wp_verify_nonce($_POST['nonce'], 'load_more_package_trips')) {
        wp_die('Security check failed');
    }
    
    $page = intval($_POST['page']);
    $type = sanitize_text_field($_POST['type']);
    $package_slug = isset($_POST['package_slug']) ? sanitize_text_field($_POST['package_slug']) : '';
    
    // إعداد الـ query حسب النوع
    $args = array(
        'post_type' => 'trip',
        'post_status' => 'publish',
        'posts_per_page' => get_option('posts_per_page', 10),
        'paged' => $page + 1, // الصفحة التالية
    );
    
    if ($type === 'all-packages') {
        // جميع الـ packages
        $args['tax_query'] = array(
            array(
                'taxonomy' => 'packages',
                'operator' => 'EXISTS',
            ),
        );
    } else {
        // package محدد
        $args['tax_query'] = array(
            array(
                'taxonomy' => 'packages',
                'field' => 'slug',
                'terms' => $package_slug,
            ),
        );
    }
    
    $trips = new WP_Query($args);
    
    ob_start();
    if ($trips->have_posts()) :
        while ($trips->have_posts()) : $trips->the_post();
            if (function_exists('wptravelengine_get_template')) {
                // Initialize WP Travel Engine variables properly
                global $post;
                $all_args = wte_get_trip_details( $post->ID );
                $all_args['user_wishlists'] = wptravelengine_user_wishlists();
                $all_args['related_query'] = true;
                foreach ( $all_args as $key => $value ) {
                    wptravelengine_set_template_args( array( $key => $value ) );
                }
                wptravelengine_get_template('content-related-trip.php');
            }
        endwhile;
    endif;
    
    wp_reset_postdata();
    $html = ob_get_clean();
    
    wp_send_json_success(array(
        'html' => $html,
        'has_more' => ($page + 1) < $trips->max_num_pages
    ));
}

add_action('wp_ajax_load_more_package_trips', 'load_more_package_trips_ajax');
add_action('wp_ajax_nopriv_load_more_package_trips', 'load_more_package_trips_ajax');

// 3. إضافة Packages للقائمة الرئيسية تلقائياً — معطّل، المنيو بيسحب من Primary بالظبط
// function add_packages_to_menu($items, $args) { ... }
// add_filter('wp_nav_menu_items', 'add_packages_to_menu', 10, 2);

// 4. إصلاح مشكلة 404 للصفحة /packages/
add_action('init', function() {
    // إضافة rewrite rule للـ packages archive
    add_rewrite_rule('^packages/?$', 'index.php?post_type=trip&packages_archive=1', 'top');
    
    // flush rewrite rules مرة واحدة فقط
    if (get_option('packages_rewrite_flushed') !== 'yes') {
        flush_rewrite_rules();
        update_option('packages_rewrite_flushed', 'yes');
    }
});

// إضافة query var للـ packages archive
add_filter('query_vars', function($vars) {
    $vars[] = 'packages_archive';
    return $vars;
});

// معالجة template للـ packages archive
add_action('template_redirect', function() {
    if (get_query_var('packages_archive')) {
        $template = get_stylesheet_directory() . '/archive-packages.php';
        if (file_exists($template)) {
            include($template);
            exit;
        }
    }
});

// 5. إضافة Trustindex code تحت عنوان الرحلة في جميع القوالب
function add_trustindex_code_after_title() {
    if (function_exists('get_field')) {
        $trustindex_code = get_field('trustindex_code', get_the_ID());
        if (!empty($trustindex_code)) {
            echo '<div class="trip-trustindex-wrapper">' . wp_kses_post($trustindex_code) . '</div>';
        }
    }
}
add_action('wptravelengine_after_trip_title', 'add_trustindex_code_after_title');
add_action('wptravelengine_after_archive_trip_title', 'add_trustindex_code_after_title');

// Package functionality is now complete and working with WP Travel Engine integration!

/* ================ تحسين البوب اب - كل الصور تفتح Gallery (للديسك توب فقط) ================ */
add_action('wp_footer', function() {
    if (!is_singular('trip') || wp_is_mobile()) return;
    ?>
    <script>
    jQuery(function($) {
        // إزالة data-fancybox من صور الـ Grid
        $('a[data-fancybox="gallery"]').removeAttr('data-fancybox');
        
        // فتح زر Gallery عند النقر على أي صورة
        $(document).on('click', '.wpte-multi-banner-image a, .wpte-multi-banner-image img, .splide__slide img', function(e) {
            e.preventDefault();
            e.stopImmediatePropagation();
            $('.wte-trip-image-gal-popup-trigger')[0]?.click();
            return false;
        });
        
        // تحسين المظهر
        $('.wpte-multi-banner-image, .splide__slide img').css('cursor', 'pointer');
    });
    </script>
    <style>
    @media (min-width: 769px) {
        .wpte-multi-banner-image, .wpte-multi-banner-image a, .wpte-multi-banner-image img, .splide__slide img {
            cursor: pointer !important;
        }
        .wpte-multi-banner-image:hover img, .splide__slide:hover img {
            transform: scale(1.02);
            transition: transform 0.3s ease;
        }
    }
    </style>
    <?php
}, 999);

/* ================ Featured Trips Widget - عرض رحلات من نفس الـ Destination ================ */
/*
 * تخصيص Featured Trips Widget ليعرض رحلات من نفس الـ Destination في صفحات الرحلات
 * - يستبعد الـ Parent Destinations ويستخدم الـ Child فقط
 * - إذا لم يجد رحلات featured، يعرض رحلات عادية من نفس الـ Destination
 * - يستبعد الرحلة الحالية من النتائج
 */
class Custom_WTE_Featured_Trips_Widget extends WP_Widget {
    
    public function __construct() {
        parent::__construct(
            'wte_featured_trips_widget',
            'WP Travel Engine: Featured Trips Widget',
            array('description' => __('A Featured Trips Widget for WP Travel Engine.', 'wp-travel-engine'))
        );
    }
    
    public function widget($args, $instance) {
        $before_widget = $args['before_widget'] ?? '';
        $after_widget  = $args['after_widget']  ?? '';
        $before_title  = $args['before_title']  ?? '';
        $after_title   = $args['after_title']   ?? '';
        $title = apply_filters('widget_title', isset($instance['title']) ? $instance['title'] : '');
        $num_post = !empty($instance['num_post']) ? $instance['num_post'] : 3;
        
        echo $before_widget;
        if (!empty($title)) {
            echo $before_title . $title . $after_title;
        }
        
        // فلترة حسب الـ destination في صفحات الرحلات
        $tax_query = array();
        
        if (is_singular('trip')) {
            global $post;
            $current_destinations = wp_get_post_terms($post->ID, 'destination', array('fields' => 'ids'));
            
            if (!empty($current_destinations)) {
                // فلترة الـ destinations - استبعاد الـ parent واستخدام الـ child فقط
                $filtered_destinations = array();
                
                foreach ($current_destinations as $dest_id) {
                    $term = get_term($dest_id, 'destination');
                    if ($term && $term->parent != 0) {
                        $filtered_destinations[] = $dest_id;
                    }
                }
                
                // إذا لم يتم العثور على child destinations، استخدم الكل
                if (empty($filtered_destinations)) {
                    $filtered_destinations = $current_destinations;
                }
                
                $tax_query = array(
                    array(
                        'taxonomy' => 'destination',
                        'field'    => 'term_id',
                        'terms'    => $filtered_destinations,
                    ),
                );
            }
        }
        
        // محاولة جلب رحلات featured أولاً
        $query_args = array(
            'post_type'      => 'trip',
            'posts_per_page' => $num_post,
            'meta_key'       => 'wp_travel_engine_featured_trip',
            'meta_value'     => 'yes',
            'meta_compare'   => '=',
        );
        
        // إضافة فلتر الـ destination
        if (!empty($tax_query)) {
            $query_args['tax_query'] = $tax_query;
            if (is_singular('trip')) {
                global $post;
                $query_args['post__not_in'] = array($post->ID);
            }
        }
        
        $query = new WP_Query($query_args);
        
        // إذا لم يتم العثور على رحلات featured، جرب بدون شرط featured
        if (!$query->have_posts() && !empty($tax_query)) {
            wp_reset_postdata();
            unset($query_args['meta_key']);
            unset($query_args['meta_value']);
            unset($query_args['meta_compare']);
            $query = new WP_Query($query_args);
        }
        
        // عرض الرحلات
        if ($query->have_posts()) {
            while ($query->have_posts()) {
                $query->the_post();
                $details = wte_get_trip_details(get_the_ID());
                wte_get_template('widgets/content-widget-feat-trip.php', $details);
            }
        }
        wp_reset_postdata();
        
        echo $after_widget;
    }
    
    public function form($instance) {
        $title = isset($instance['title']) ? $instance['title'] : __('Featured Trips', 'wp-travel-engine');
        $num_post = isset($instance['num_post']) ? $instance['num_post'] : 3;
        ?>
        <p>
            <label for="<?php echo esc_attr($this->get_field_name('title')); ?>"><?php esc_html_e('Title:', 'wp-travel-engine'); ?></label>
            <input class="widefat" id="<?php echo esc_attr($this->get_field_id('title')); ?>" name="<?php echo esc_attr($this->get_field_name('title')); ?>" type="text" value="<?php echo esc_attr($title); ?>" />
        </p>
        <p>
            <label for="<?php echo esc_attr($this->get_field_name('num_post')); ?>"><?php esc_html_e('Number of Posts:', 'wp-travel-engine'); ?></label>
            <input class="widefat" id="<?php echo esc_attr($this->get_field_id('num_post')); ?>" name="<?php echo esc_attr($this->get_field_name('num_post')); ?>" type="text" value="<?php echo esc_attr($num_post); ?>" />
        </p>
        <?php
    }
    
    public function update($new_instance, $old_instance) {
        $instance = array();
        $instance['title'] = !empty($new_instance['title']) ? strip_tags($new_instance['title']) : '';
        $instance['num_post'] = !empty($new_instance['num_post']) ? absint($new_instance['num_post']) : '';
        return $instance;
    }
}

// إلغاء تسجيل الـ Widget الأصلي وتسجيل الجديد
function replace_featured_trips_widget() {
    unregister_widget('WTE_Featured_Trips_Widget');
    register_widget('Custom_WTE_Featured_Trips_Widget');
}
add_action('widgets_init', 'replace_featured_trips_widget', 999); // أعلى priority

// استبدال الـ Widget الأصلي بالـ Custom Widget في صفحات الرحلات
add_filter('widget_display_callback', function($instance, $widget, $args) {
    if ($widget->id_base === 'wte_featured_trips_widget' && is_singular('trip')) {
        ob_start();
        $custom_widget = new Custom_WTE_Featured_Trips_Widget();
        $custom_widget->widget($args, $instance);
        $output = ob_get_clean();
        echo $output;
        return false;
    }
    return $instance;
}, 999, 3);

// تعديل query الـ Featured Trips Widget في صفحات الرحلات
add_action('pre_get_posts', function($query) {
    if (!is_admin() && 
        !$query->is_main_query() && 
        $query->get('post_type') === 'trip' && 
        $query->get('meta_key') === 'wp_travel_engine_featured_trip' &&
        is_singular('trip')) {
        
        global $post;
        $current_destinations = wp_get_post_terms($post->ID, 'destination', array('fields' => 'ids'));
        
        if (!empty($current_destinations)) {
            // فلترة الـ destinations - استبعاد الـ parent واستخدام الـ child فقط
            $filtered_destinations = array();
            foreach ($current_destinations as $dest_id) {
                $term = get_term($dest_id, 'destination');
                if ($term && $term->parent != 0) {
                    $filtered_destinations[] = $dest_id;
                }
            }
            
            if (empty($filtered_destinations)) {
                $filtered_destinations = $current_destinations;
            }
            
            $query->set('tax_query', array(
                array(
                    'taxonomy' => 'destination',
                    'field'    => 'term_id',
                    'terms'    => $filtered_destinations,
                ),
            ));
            
            $query->set('post__not_in', array($post->ID));
        }
    }
}, 999);

/* ================ Related Trips - عرض رحلات من نفس الـ Destination ================ */
add_filter('option_wp_travel_engine_settings', function($settings) {
    if (is_singular('trip')) {
        $settings['related_trip_show_by'] = 'destination';
    }
    return $settings;
});

/* ===============================================================================
   Related Trips - عرض رحلات من نفس الـ Destination فقط
   =============================================================================== */

// تعديل query الـ Related Trips لاستبعاد Parent Destinations
add_action('pre_get_posts', function($query) {
    // التحقق من أنه query الـ Related Trips
    if (!is_admin() && 
        !$query->is_main_query() && 
        $query->get('post_type') === 'trip' &&
        is_singular('trip') &&
        isset($query->query_vars['tax_query'])) {
        
        global $post;
        $current_destinations = wp_get_post_terms($post->ID, 'destination', array('fields' => 'ids'));
        
        if (!empty($current_destinations)) {
            // فلترة الـ destinations - استبعاد الـ parent واستخدام الـ child فقط
            $filtered_destinations = array();
            
            foreach ($current_destinations as $dest_id) {
                $term = get_term($dest_id, 'destination');
                // استخدام الـ child destinations فقط (اللي لها parent)
                if ($term && $term->parent != 0) {
                    $filtered_destinations[] = $dest_id;
                }
            }
            
            // إذا لم يتم العثور على child destinations، استخدم الكل
            if (empty($filtered_destinations)) {
                $filtered_destinations = $current_destinations;
            }
            
            // تحديث الـ tax_query
            $query->set('tax_query', array(
                array(
                    'taxonomy' => 'destination',
                    'field'    => 'term_id',
                    'terms'    => $filtered_destinations,
                ),
            ));
        }
    }
}, 20);
/* ===============================================================================
   FTS Custom Video & Single Trip Features (Robust V3)
   =============================================================================== */
require_once get_stylesheet_directory() . '/single-trip-custom.php';
require_once get_stylesheet_directory() . '/fts-enquiry-sidebar.php';
function add_font_awesome() {
    wp_enqueue_style('font-awesome-6', 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css' );
}
add_action('wp_enqueue_scripts', 'add_font_awesome');

// Disabled: old single trip layout replaced by V2
// require_once get_stylesheet_directory() . '/fts-single-trip-layout.php';
require_once get_stylesheet_directory() . '/fts-excerpt-control.php';
require_once get_stylesheet_directory() . '/destination-functions.php';

/* ===============================================================================
   FTS Smart Search (Tooltip)
   =============================================================================== */
require_once get_stylesheet_directory() . '/fts-smart-search/fts-smart-search.php';

/* ===============================================================================
   FTS Live Chat (Tawk.to) — skip on local (injects huge inline CSS before wp_head).
   =============================================================================== */
if ( ! ( defined( 'WP_ENVIRONMENT_TYPE' ) && WP_ENVIRONMENT_TYPE === 'local' && ! ( defined( 'FTS_LOCAL_ALLOW_TAWK' ) && FTS_LOCAL_ALLOW_TAWK ) ) ) {
    require_once get_stylesheet_directory() . '/fts-live-chat/fts-live-chat.php';
}

/**
 * FTS Trip Redesign V2 - Modular Layout
 */
require_once get_stylesheet_directory() . '/trip-design-v2/layout-controller.php';

/**
 * FTS Destination V2 - Premium Destination Archive
 */
require_once get_stylesheet_directory() . '/destination-design-v2/layout-controller.php';

/**
 * FTS Taxonomy Terms V2 - Premium Taxonomy Term Listings
 */
require_once get_stylesheet_directory() . '/taxonomy-terms-design-v2/layout-controller.php';

/**
 * FTS Custom Checkout — Premium checkout page redesign
 */
require_once get_stylesheet_directory() . '/fts-checkout/fts-checkout.php';

/**
 * FTS Home Page Sections — auto-loads all sections from home-page-sections/
 */
require_once get_stylesheet_directory() . '/home-page-sections/loader.php';

/**
 * FTS Single Post — minimal magazine-style single post template
 */
require_once get_stylesheet_directory() . '/fts-single-post/fts-single-post.php';

/**
 * FTS Trip Schema — single source of truth for JSON-LD on single trip pages
 * (suppresses Rank Math auto-Product and the WTE Trip Reviews emitter).
 */
require_once get_stylesheet_directory() . '/fts-schema/fts-trip-schema.php';

/**
 * Fallback: inject ExtraService line items when the wte-services post type
 * is not registered (common in AJAX context with the Extra Services add-on).
 * WTE's own add_extra_services hook silently fails in this case because
 * Trip::get_services() returns [] when post_type_exists('wte-services') is false.
 * We use the legacy trip_extras payload (always present) to build the line items.
 */
add_action( 'wptravelengine_after_items_added_to_cart', function ( $items, $cart ) {
    if ( ! class_exists( '\WPTravelEngine\Core\Cart\Items\ExtraService' ) ) {
        return;
    }
    foreach ( $items as $item ) {
        $line_items = $item->get_additional_line_items();
        if ( ! empty( $line_items['extra_service'] ) ) {
            continue;
        }
        $trip_extras = $item->trip_extras ?? array();
        if ( empty( $trip_extras ) || ! is_array( $trip_extras ) ) {
            continue;
        }
        foreach ( $trip_extras as $te ) {
            $qty   = (int) ( $te['qty'] ?? 0 );
            $price = (float) ( $te['price'] ?? 0 );
            $label = (string) ( $te['extra_service'] ?? '' );
            if ( $qty < 1 || $label === '' ) {
                continue;
            }
            $item->add_additional_line_items(
                new \WPTravelEngine\Core\Cart\Items\ExtraService(
                    $cart,
                    array(
                        'label'    => $label,
                        'quantity' => $qty,
                        'price'    => $price,
                    )
                )
            );
        }
    }
}, 20, 2 );

/* ================ Expose booking currency on Thank You page ================ */
add_action( 'wp_footer', 'fts_wte_thankyou_booking_currency' );
function fts_wte_thankyou_booking_currency() {
    if ( ! function_exists( 'wptravelengine_get_thankyou_page_id' ) ) {
        return;
    }
    if ( ! is_page( wptravelengine_get_thankyou_page_id() ) ) {
        return;
    }

    $booking_id = 0;

    if ( isset( $_GET['payment_key'] ) && class_exists( '\WPTravelEngine\Core\Models\Post\Payment' ) ) {
        try {
            $payment = \WPTravelEngine\Core\Models\Post\Payment::from_payment_key(
                sanitize_text_field( wp_unslash( $_GET['payment_key'] ) )
            );
            $booking = \WPTravelEngine\Core\Models\Post\Booking::from_payment( $payment );
            $booking_id = $booking->get_id();
        } catch ( \Exception $e ) {}
    }

    if ( ! $booking_id && class_exists( '\WTE_Booking' ) ) {
        $data = \WTE_Booking::get_callback_token_payload( 'thankyou' );
        if ( is_array( $data ) && isset( $data['bid'] ) ) {
            $booking_id = (int) $data['bid'];
        }
    }

    if ( ! $booking_id ) {
        return;
    }

    $cart_info = get_post_meta( $booking_id, 'cart_info', true );
    $currency  = is_array( $cart_info ) && ! empty( $cart_info['currency'] ) ? $cart_info['currency'] : '';

    if ( $currency ) {
        printf( '<script>window.ftsBookingCurrency=%s;</script>', wp_json_encode( $currency ) );
    }
}
/* Change Rank Math Breadcrumb Schema: Trips to Tours */
add_filter( 'rank_math/json_ld', function( $data, $jsonld ) {
    foreach ( $data as &$entity ) {
        if (
            isset( $entity['@type'] )
            && $entity['@type'] === 'BreadcrumbList'
            && isset( $entity['itemListElement'] )
            && is_array( $entity['itemListElement'] )
        ) {
            foreach ( $entity['itemListElement'] as &$item ) {
                if (
                    isset( $item['item']['name'], $item['item']['@id'] )
                    && $item['item']['name'] === 'Trips'
                    && strpos( $item['item']['@id'], '/tours/' ) !== false
                ) {
                    $item['item']['name'] = 'Tours';
                }
            }
        }
    }

    return $data;
}, 99, 2 );

add_filter( 'fts_v2_force_partial_payment', function( $enabled ) {
    return true;
}, 10, 1 );

add_filter( 'fts_v2_pay_later_text', function( $text ) {
    if ( is_string( $text ) && trim( $text ) !== '' ) {
        return trim( $text );
    }
    $opt = get_option( 'fts_pay_later_text', '' );
    if ( is_string( $opt ) && trim( $opt ) !== '' ) {
        return trim( $opt );
    }
    return __( 'Pay a deposit today and the remaining balance later. Deposit and due amounts are shown at checkout.', 'fts' );
}, 10, 1 );

function fts_is_checkout_page_v2() {
    if ( is_admin() || wp_doing_ajax() ) {
        return false;
    }
    $checkout_page_id = 0;
    if ( function_exists( 'wptravelengine_get_checkout_page_id' ) ) {
        $checkout_page_id = wptravelengine_get_checkout_page_id();
    }
    if ( ! $checkout_page_id ) {
        $settings         = get_option( 'wp_travel_engine_settings', array() );
        $checkout_page_id = $settings['pages']['wp_travel_engine_place_order'] ?? 0;
    }
    if ( $checkout_page_id && is_page( (int) $checkout_page_id ) ) {
        return true;
    }
    return is_page( 'checkout' );
}

function fts_wte_cart_set_payment_type_safe( $cart, $type ) {
    if ( ! is_object( $cart ) ) {
        return false;
    }
    foreach ( array( 'set_payment_type', 'setPaymentType' ) as $m ) {
        if ( method_exists( $cart, $m ) ) {
            try {
                $cart->{$m}( $type );
                return true;
            } catch ( \Throwable $e ) {
                return false;
            }
        }
    }
    if ( property_exists( $cart, 'payment_type' ) ) {
        $cart->payment_type = $type;
        return true;
    }
    return false;
}

add_action( 'template_redirect', function() {
    if ( ! fts_is_checkout_page_v2() ) {
        return;
    }
    global $wte_cart;
    if ( ! is_object( $wte_cart ) || ! method_exists( $wte_cart, 'getItems' ) ) {
        return;
    }
    $items = $wte_cart->getItems();
    if ( empty( $items ) ) {
        return;
    }

    $pay_later_text = (string) apply_filters( 'fts_v2_pay_later_text', '' );
    if ( trim( $pay_later_text ) === '' ) {
        if ( isset( $_COOKIE['ftsPaymentPlan'] ) ) {
            setcookie( 'ftsPaymentPlan', '', time() - 3600, '/', '', is_ssl(), true );
        }
        return;
    }

    $plan = isset( $_COOKIE['ftsPaymentPlan'] ) ? sanitize_text_field( wp_unslash( $_COOKIE['ftsPaymentPlan'] ) ) : '';
    if ( $plan !== 'deposit' && $plan !== 'full' ) {
        $plan = 'deposit';
    }

    if ( $plan === 'full' ) {
        fts_wte_cart_set_payment_type_safe( $wte_cart, 'full' );
        return;
    }

    $ok = fts_wte_cart_set_payment_type_safe( $wte_cart, 'partial' );
    if ( ! $ok ) {
        $ok = fts_wte_cart_set_payment_type_safe( $wte_cart, 'deposit' );
    }

    $totals = method_exists( $wte_cart, 'get_totals' ) ? $wte_cart->get_totals() : array();
    $p = is_array( $totals ) ? floatval( $totals['partial_total'] ?? 0 ) : 0;
    $d = is_array( $totals ) ? floatval( $totals['due_total'] ?? 0 ) : 0;
    if ( $p <= 0 || $d <= 0 ) {
        fts_wte_cart_set_payment_type_safe( $wte_cart, 'full' );
        setcookie( 'ftsPaymentPlan', 'full', time() + 86400, '/', '', is_ssl(), true );
        setcookie( 'ftsPaymentPlanError', '1', time() + 300, '/', '', is_ssl(), true );
    }
}, 1 );

function fts_trip_search_query_has_trip( $q ) {
    if ( ! $q || ! is_object( $q ) ) {
        return false;
    }
    $pt = $q->get( 'post_type' );
    if ( ! $pt ) {
        return false;
    }
    if ( is_string( $pt ) ) {
        return $pt === 'trip';
    }
    if ( is_array( $pt ) ) {
        return in_array( 'trip', $pt, true );
    }
    return false;
}

add_filter( 'posts_search', function( $search, $wp_query ) {
    if ( is_admin() ) {
        return $search;
    }
    if ( ! $wp_query || ! is_object( $wp_query ) || ! $wp_query->is_search() ) {
        return $search;
    }
    if ( ! fts_trip_search_query_has_trip( $wp_query ) ) {
        return $search;
    }
    $term = (string) $wp_query->get( 's' );
    $term = trim( $term );
    if ( $term === '' ) {
        return $search;
    }
    global $wpdb;
    $like = '%' . $wpdb->esc_like( $term ) . '%';

    $exists_index = $wpdb->prepare(
        "EXISTS (SELECT 1 FROM {$wpdb->postmeta} fts_si WHERE fts_si.post_id = {$wpdb->posts}.ID AND fts_si.meta_key = 'fts_search_index' AND fts_si.meta_value LIKE %s)",
        $like
    );
    $exists_meta = $wpdb->prepare(
        "EXISTS (SELECT 1 FROM {$wpdb->postmeta} fts_pm WHERE fts_pm.post_id = {$wpdb->posts}.ID AND fts_pm.meta_key NOT LIKE '\\\\_%' AND fts_pm.meta_value LIKE %s)",
        $like
    );

    if ( is_string( $search ) && trim( $search ) !== '' && substr_count( $search, ')' ) > 0 ) {
        $pos = strrpos( $search, ')' );
        if ( $pos !== false ) {
            $search = substr( $search, 0, $pos ) . " OR {$exists_index} OR {$exists_meta}" . substr( $search, $pos );
            return $search;
        }
    }

    return $search . " AND ({$exists_index} OR {$exists_meta})";
}, 10, 2 );

add_filter( 'posts_distinct', function( $distinct, $wp_query ) {
    if ( is_admin() ) {
        return $distinct;
    }
    if ( ! $wp_query || ! is_object( $wp_query ) || ! $wp_query->is_search() ) {
        return $distinct;
    }
    if ( ! fts_trip_search_query_has_trip( $wp_query ) ) {
        return $distinct;
    }
    return 'DISTINCT';
}, 10, 2 );

function fts_trip_search_flatten_value( $value, array &$out, $depth = 0 ) {
    if ( $depth > 6 ) {
        return;
    }
    if ( is_null( $value ) ) {
        return;
    }
    if ( is_bool( $value ) ) {
        $out[] = $value ? '1' : '0';
        return;
    }
    if ( is_int( $value ) || is_float( $value ) ) {
        $out[] = (string) $value;
        return;
    }
    if ( is_string( $value ) ) {
        $v = trim( wp_strip_all_tags( $value ) );
        if ( $v !== '' ) {
            $out[] = $v;
        }
        return;
    }
    if ( is_array( $value ) ) {
        foreach ( $value as $k => $v ) {
            if ( is_string( $k ) ) {
                $out[] = $k;
            }
            fts_trip_search_flatten_value( $v, $out, $depth + 1 );
            if ( count( $out ) > 2000 ) {
                return;
            }
        }
        return;
    }
    if ( is_object( $value ) ) {
        if ( $value instanceof \WP_Post ) {
            $out[] = (string) $value->post_title;
            $out[] = (string) $value->post_excerpt;
            $out[] = (string) $value->post_content;
            return;
        }
        if ( $value instanceof \WP_Term ) {
            $out[] = (string) $value->name;
            return;
        }
        if ( method_exists( $value, '__toString' ) ) {
            $out[] = trim( (string) $value );
        }
        return;
    }
}

function fts_trip_search_build_index_text( $post_id ) {
    $post_id = intval( $post_id );
    if ( $post_id <= 0 ) {
        return '';
    }
    $p = get_post( $post_id );
    if ( ! $p || $p->post_type !== 'trip' ) {
        return '';
    }

    $chunks = array();
    $chunks[] = (string) $p->post_title;
    $chunks[] = (string) $p->post_excerpt;
    $chunks[] = (string) $p->post_content;
    $chunks[] = (string) $p->post_name;

    $taxes = array( 'destination', 'activities', 'trip_types', 'difficulty', 'tags', 'category' );
    foreach ( $taxes as $tx ) {
        $terms = get_the_terms( $post_id, $tx );
        if ( empty( $terms ) || is_wp_error( $terms ) ) {
            continue;
        }
        foreach ( $terms as $t ) {
            if ( $t instanceof \WP_Term ) {
                $chunks[] = (string) $t->name;
                $chunks[] = (string) $t->slug;
            }
        }
    }

    $meta = get_post_meta( $post_id );
    if ( is_array( $meta ) ) {
        foreach ( $meta as $k => $vals ) {
            $k = (string) $k;
            if ( $k === '' || $k[0] === '_' ) {
                continue;
            }
            $chunks[] = $k;
            if ( ! is_array( $vals ) ) {
                continue;
            }
            foreach ( $vals as $raw ) {
                $val = maybe_unserialize( $raw );
                fts_trip_search_flatten_value( $val, $chunks, 0 );
                if ( count( $chunks ) > 4000 ) {
                    break 2;
                }
            }
        }
    }

    if ( function_exists( 'get_fields' ) ) {
        $acf = get_fields( $post_id );
        if ( is_array( $acf ) ) {
            foreach ( $acf as $k => $v ) {
                if ( is_string( $k ) && $k !== '' ) {
                    $chunks[] = $k;
                }
                fts_trip_search_flatten_value( $v, $chunks, 0 );
                if ( count( $chunks ) > 4500 ) {
                    break;
                }
            }
        }
    }

    $txt = implode( ' ', array_filter( array_map( 'trim', $chunks ) ) );
    $txt = strtolower( wp_strip_all_tags( $txt ) );
    $txt = preg_replace( '/\s+/u', ' ', $txt );
    $txt = trim( $txt );

    if (
        strpos( $txt, 'national museum of egyptian civilization' ) !== false
        || strpos( $txt, 'egyptian civilization museum' ) !== false
        || strpos( $txt, 'nmec' ) !== false
        || strpos( $txt, 'المتحف القومي للحضارة' ) !== false
        || strpos( $txt, 'المتحف القومى للحضارة' ) !== false
        || strpos( $txt, 'المتحف المصري للحضارة' ) !== false
        || strpos( $txt, 'المتحف المصرى للحضارة' ) !== false
    ) {
        if ( strpos( $txt, ' nmec ' ) === false && substr( $txt, -4 ) !== 'nmec' ) {
            $txt .= ' nmec';
        }
    }

    if ( strlen( $txt ) > 200000 ) {
        $txt = substr( $txt, 0, 200000 );
    }

    return $txt;
}

function fts_trip_search_update_index( $post_id ) {
    $post_id = intval( $post_id );
    if ( $post_id <= 0 ) {
        return;
    }
    if ( wp_is_post_revision( $post_id ) || wp_is_post_autosave( $post_id ) ) {
        return;
    }
    if ( get_post_type( $post_id ) !== 'trip' ) {
        return;
    }
    $txt = fts_trip_search_build_index_text( $post_id );
    if ( $txt === '' ) {
        delete_post_meta( $post_id, 'fts_search_index' );
        return;
    }
    update_post_meta( $post_id, 'fts_search_index', $txt );
}

add_action( 'save_post_trip', function( $post_id ) {
    fts_trip_search_update_index( $post_id );
}, 20, 1 );

add_action( 'init', function() {
    if ( ! function_exists( 'wp_schedule_single_event' ) ) {
        return;
    }
    $ver = intval( get_option( 'fts_trip_search_index_ver', 0 ) );
    if ( $ver >= 1 ) {
        return;
    }
    update_option( 'fts_trip_search_index_ver', 1, false );
    update_option( 'fts_trip_search_reindex_offset', 0, false );
    if ( ! wp_next_scheduled( 'fts_trip_search_reindex_event' ) ) {
        wp_schedule_single_event( time() + 10, 'fts_trip_search_reindex_event' );
    }
}, 20 );

add_action( 'fts_trip_search_reindex_event', function() {
    $offset = intval( get_option( 'fts_trip_search_reindex_offset', 0 ) );
    if ( $offset < 0 ) {
        $offset = 0;
    }
    $q = new WP_Query( array(
        'post_type'              => 'trip',
        'post_status'            => array( 'publish', 'draft', 'pending', 'private' ),
        'posts_per_page'         => 30,
        'offset'                 => $offset,
        'fields'                 => 'ids',
        'no_found_rows'          => true,
        'update_post_meta_cache' => false,
        'update_post_term_cache' => false,
        'orderby'                => 'ID',
        'order'                  => 'ASC',
    ) );

    if ( empty( $q->posts ) ) {
        delete_option( 'fts_trip_search_reindex_offset' );
        return;
    }

    foreach ( $q->posts as $id ) {
        fts_trip_search_update_index( intval( $id ) );
    }

    update_option( 'fts_trip_search_reindex_offset', $offset + count( $q->posts ), false );

    if ( function_exists( 'wp_schedule_single_event' ) ) {
        wp_schedule_single_event( time() + 10, 'fts_trip_search_reindex_event' );
    }
}, 10, 0 );

function fts_bootstrap_currency_converter() {
    if ( class_exists( 'Wte_Currency_Converter' ) ) {
        return;
    }
    if ( ! defined( 'WTE_CURRENCY_CONVERTER_DIR' ) ) {
        return;
    }
    if ( ! class_exists( 'WPTravelEngine\\Plugin' ) && ! class_exists( 'Wp_Travel_Engine' ) ) {
        return;
    }
    $main_class_file = WTE_CURRENCY_CONVERTER_DIR . 'includes/class-wte-currency-converter.php';
    if ( ! file_exists( $main_class_file ) ) {
        return;
    }
    require_once $main_class_file;
    if ( class_exists( 'Wte_Currency_Converter' ) ) {
        Wte_Currency_Converter::execute();
        if ( class_exists( 'Wte_Currency_Converter_Shortcode' ) ) {
            Wte_Currency_Converter_Shortcode::get_instance()->register_shortcodes();
        }
    }
}
fts_bootstrap_currency_converter();

add_action( 'init', function () {
    if ( shortcode_exists( 'fts_currency_switcher' ) ) {
        return;
    }
    add_shortcode( 'fts_currency_switcher', function () {
        if ( shortcode_exists( 'wte_currency_converter' ) ) {
            return do_shortcode( '[wte_currency_converter]' );
        }
        return '';
    } );
}, 20 );

add_action( 'wp_enqueue_scripts', function () {
    $rel  = '/fts-currency-switcher/assets/css/style.css';
    $path = get_stylesheet_directory() . $rel;
    if ( ! file_exists( $path ) ) {
        return;
    }
    wp_enqueue_style(
        'fts-currency-switcher',
        get_stylesheet_directory_uri() . $rel,
        array(),
        (string) filemtime( $path )
    );

    /*
     * Force the WTE Currency Converter dropdown items to show only the
     * short "symbol + code" label instead of the long currency name.
     * Loads after the plugin's public script so it can override its output.
     */
    $js_rel  = '/fts-currency-switcher/assets/js/short-labels.js';
    $js_path = get_stylesheet_directory() . $js_rel;
    if ( file_exists( $js_path ) ) {
        wp_enqueue_script(
            'fts-currency-switcher-short-labels',
            get_stylesheet_directory_uri() . $js_rel,
            array( 'jquery' ),
            (string) filemtime( $js_path ),
            true
        );
    }
}, 30 );

/**
 * Convert a price from the base currency to the user-selected currency.
 *
 * Tries the helper (which uses the API or admin manual rates) and falls
 * back to the manual rate list if the helper bails (e.g. API down).
 *
 * @param float  $amount       Amount in the base currency.
 * @param string $base_code    Base currency code (e.g. EUR).
 * @param string $target_code  Target currency code (e.g. AUD).
 * @return float|null Converted amount, or null when conversion is not possible.
 */
function fts_convert_amount( $amount, $base_code, $target_code ) {
    if ( ! is_numeric( $amount ) || $amount <= 0 ) {
        return null;
    }
    $base_code   = strtoupper( (string) $base_code );
    $target_code = strtoupper( (string) $target_code );
    if ( $base_code === '' || $target_code === '' || $base_code === $target_code ) {
        return null;
    }
    if ( ! class_exists( 'Wte_Currency_Converter_Helper_Functions' ) ) {
        return null;
    }
    try {
        $helper = Wte_Currency_Converter_Helper_Functions::get_instance();
        if ( ! $helper->is_currency_converter_enabled() ) {
            return null;
        }
        $converted = $helper->get_price( $base_code, $target_code, $amount );
        if ( ! is_numeric( $converted ) || (float) $converted === (float) $amount ) {
            $list = method_exists( $helper, 'get_currency_converter_list' )
                ? $helper->get_currency_converter_list()
                : array();
            if ( is_array( $list ) && ! empty( $list['code'] ) ) {
                foreach ( $list['code'] as $i => $code ) {
                    if ( strtoupper( (string) $code ) === $target_code ) {
                        $rate = isset( $list['rate'][ $i ] ) ? floatval( $list['rate'][ $i ] ) : 0;
                        if ( $rate > 0 ) {
                            return $amount * $rate;
                        }
                    }
                }
            }
            return null;
        }
        return (float) $converted;
    } catch ( \Throwable $e ) {
        return null;
    }
}

/**
 * Render an "approximate equivalent" notice on the checkout cart summary.
 *
 * The plugin freezes the displayed currency to the base currency on the
 * checkout page (so payment gateways and bookings stay consistent). We
 * append a subtle row that tells the visitor what the total would be in
 * the currency they were browsing the site with.
 */
add_action( 'wptravelengine_at_cart_summary_end', function () {
    if ( ! function_exists( 'wte_currency_code_in_db' ) ) return;

    $target = '';
    if ( ! empty( $_COOKIE['cc_code'] ) ) {
        $target = strtoupper( sanitize_text_field( wp_unslash( $_COOKIE['cc_code'] ) ) );
    } elseif ( ! empty( $_COOKIE['wte_currency_code'] ) ) {
        $target = strtoupper( sanitize_text_field( wp_unslash( $_COOKIE['wte_currency_code'] ) ) );
    }
    if ( ! preg_match( '/^[A-Z]{3}$/', $target ) ) return;

    $base = strtoupper( (string) wte_currency_code_in_db() );
    if ( $target === $base ) return;

    global $wte_cart;
    if ( ! $wte_cart ) return;

    $total = 0.0;
    if ( method_exists( $wte_cart, 'get_total_payable_amount' ) ) {
        $total = floatval( $wte_cart->get_total_payable_amount() );
    }
    if ( $total <= 0 && method_exists( $wte_cart, 'get_total' ) ) {
        $total = floatval( $wte_cart->get_total() );
    }
    if ( $total <= 0 ) return;

    $converted = fts_convert_amount( $total, $base, $target );
    if ( ! is_numeric( $converted ) || $converted <= 0 ) return;

    $symbol = function_exists( 'wp_travel_engine_get_currency_symbol' )
        ? wp_travel_engine_get_currency_symbol( $target )
        : $target . ' ';
    $wte_settings_dec = get_option( 'wp_travel_engine_settings', array() );
    $dec_digits = isset( $wte_settings_dec['decimal_digits'] ) && $wte_settings_dec['decimal_digits'] !== 'default'
        ? intval( $wte_settings_dec['decimal_digits'] )
        : 0;
    $formatted = $symbol . number_format( round( $converted, $dec_digits ), $dec_digits );

    /* translators: %s: base currency code (e.g. EUR) */
    $hint = sprintf( __( 'Charged in %s', 'fts' ), $base );
    ?>
    <tr class="fts-checkout-equiv">
        <td colspan="2" class="fts-checkout-equiv__cell">
            <div class="fts-checkout-equiv__pill" role="note" aria-live="polite" title="<?php echo esc_attr( $hint ); ?>">
                <span class="fts-checkout-equiv__approx" aria-hidden="true">≈</span>
                <span class="fts-checkout-equiv__amount"><?php echo esc_html( $formatted ); ?></span>
                <span class="fts-checkout-equiv__code"><?php echo esc_html( $target ); ?></span>
                <span class="fts-checkout-equiv__sep" aria-hidden="true">·</span>
                <span class="fts-checkout-equiv__hint"><?php echo esc_html( $hint ); ?></span>
            </div>
        </td>
    </tr>
    <?php
}, PHP_INT_MAX - 10 );

/**
 * Server-side currency cookie sync.
 *
 * The WP Travel Engine Currency Converter plugin only sets the `cc_code` cookie
 * from JavaScript (and as a session cookie). When the user-side JS fails to run,
 * is cached out, or the session cookie is cleared, the cookie is empty and every
 * server-side conversion (filters, our destination AJAX, etc.) falls back to the
 * default currency. We mirror the `?wte_cc=XXX` query param into a persistent
 * cookie on `init` priority 1 — before the plugin's filters run.
 */
add_action( 'init', function () {
    $code = '';
    if ( ! empty( $_GET['wte_cc'] ) ) {
        $code = strtoupper( sanitize_text_field( wp_unslash( $_GET['wte_cc'] ) ) );
    } elseif ( ! empty( $_COOKIE['cc_code'] ) ) {
        $code = strtoupper( sanitize_text_field( wp_unslash( $_COOKIE['cc_code'] ) ) );
    } elseif ( ! empty( $_COOKIE['wte_currency_code'] ) ) {
        $code = strtoupper( sanitize_text_field( wp_unslash( $_COOKIE['wte_currency_code'] ) ) );
    }

    if ( ! preg_match( '/^[A-Z]{3}$/', $code ) ) {
        return;
    }

    $_COOKIE['cc_code']           = $code;
    $_COOKIE['wte_currency_code'] = $code;

    if ( ! headers_sent() ) {
        $expire = time() + 30 * DAY_IN_SECONDS;
        setcookie( 'cc_code',           $code, $expire, '/' );
        setcookie( 'wte_currency_code', $code, $expire, '/' );
    }
}, 1 );

