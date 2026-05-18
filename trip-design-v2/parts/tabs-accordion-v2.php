<?php
/**
 * Content Sections V2 - Collapsible Accordion (Single Open)
 * Variables provided by layout-controller.php via extract()
 */
if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! function_exists( 'fts_v2_vm_text_value' ) ) {
    function fts_v2_vm_text_value( $item, $keys = array() ) {
        if ( is_scalar( $item ) ) {
            $text = trim( wp_strip_all_tags( (string) $item ) );
            return $text !== '' ? $text : '';
        }

        if ( ! is_array( $item ) ) {
            return '';
        }

        foreach ( (array) $keys as $key ) {
            if ( isset( $item[ $key ] ) && is_scalar( $item[ $key ] ) ) {
                $text = trim( wp_strip_all_tags( (string) $item[ $key ] ) );
                if ( $text !== '' ) {
                    return $text;
                }
            }
        }

        return '';
    }
}

if ( ! function_exists( 'fts_v2_vm_list_texts' ) ) {
    function fts_v2_vm_list_texts( $items ) {
        $texts = array();

        if ( ! is_array( $items ) ) {
            return $texts;
        }

        foreach ( $items as $item ) {
            $text = fts_v2_vm_text_value( $item, array( 'text', 'label', 'title', 'value' ) );
            if ( $text !== '' ) {
                $texts[] = $text;
            }
        }

        return $texts;
    }
}

if ( ! function_exists( 'fts_v2_vm_faq_items' ) ) {
    function fts_v2_vm_faq_items( $items ) {
        $faq_items = array();

        if ( ! is_array( $items ) ) {
            return $faq_items;
        }

        foreach ( $items as $item ) {
            if ( ! is_array( $item ) ) {
                continue;
            }

            $question = fts_v2_vm_text_value( $item, array( 'question', 'title', 'q' ) );
            if ( $question === '' ) {
                continue;
            }

            $answer = '';
            foreach ( array( 'answer', 'content', 'a' ) as $key ) {
                if ( isset( $item[ $key ] ) && is_scalar( $item[ $key ] ) ) {
                    $answer = trim( (string) $item[ $key ] );
                    if ( $answer !== '' ) {
                        break;
                    }
                }
            }

            $faq_items[] = array(
                'question' => $question,
                'answer'   => $answer,
            );
        }

        return $faq_items;
    }
}

if ( ! function_exists( 'fts_v2_vm_itinerary_items' ) ) {
    function fts_v2_vm_itinerary_items( $items ) {
        $itinerary_items = array();
        $fallback_index = 0;

        if ( ! is_array( $items ) ) {
            return $itinerary_items;
        }

        foreach ( $items as $item ) {
            if ( is_scalar( $item ) ) {
                $title = trim( wp_strip_all_tags( (string) $item ) );
                if ( $title !== '' ) {
                    $itinerary_items[] = array(
                        'title'   => $title,
                        'label'   => '',
                        'content' => '',
                    );
                }
                continue;
            }

            if ( ! is_array( $item ) ) {
                continue;
            }

            $content = '';
            foreach ( array( 'content', 'description', 'text' ) as $key ) {
                if ( isset( $item[ $key ] ) && is_scalar( $item[ $key ] ) ) {
                    $content = trim( (string) $item[ $key ] );
                    if ( $content !== '' ) {
                        break;
                    }
                }
            }

            $title = fts_v2_vm_text_value( $item, array( 'title', 'heading', 'label', 'day' ) );
            if ( $title === '' && $content === '' ) {
                continue;
            }

            $fallback_index++;
            if ( $title === '' ) {
                $title = sprintf( __( 'Stop %d', 'fts' ), $fallback_index );
            }

            $label = fts_v2_vm_text_value( $item, array( 'day', 'label' ) );

            $itinerary_items[] = array(
                'title'   => $title,
                'label'   => $label,
                'content' => $content,
            );
        }

        return $itinerary_items;
    }
}

if ( ! function_exists( 'fts_v2_legacy_cost_items' ) ) {
    function fts_v2_legacy_cost_items( $raw ) {
        $items = array();
        $raw_string = '';

        if ( is_array( $raw ) ) {
            $parts = array();
            foreach ( $raw as $raw_item ) {
                if ( is_scalar( $raw_item ) ) {
                    $parts[] = (string) $raw_item;
                }
            }
            $raw_string = implode( "\n", $parts );
        } elseif ( is_scalar( $raw ) ) {
            $raw_string = (string) $raw;
        }

        $raw_string = trim( $raw_string );
        if ( $raw_string === '' ) {
            return $items;
        }

        if ( preg_match_all( '/<li\b[^>]*>(.*?)<\/li>/is', $raw_string, $matches ) && ! empty( $matches[1] ) ) {
            foreach ( $matches[1] as $match ) {
                $text = trim( wp_strip_all_tags( (string) $match ) );
                if ( $text !== '' ) {
                    $items[] = $text;
                }
            }
            if ( ! empty( $items ) ) {
                return $items;
            }
        }

        foreach ( preg_split( '/\r\n|[\r\n]/', $raw_string ) as $line ) {
            $text = trim( wp_strip_all_tags( (string) $line ) );
            if ( $text !== '' ) {
                $items[] = $text;
            }
        }

        return $items;
    }
}

if ( ! function_exists( 'fts_v2_normalize_package_key' ) ) {
    function fts_v2_normalize_package_key( $value ) {
        if ( ! is_scalar( $value ) ) {
            return '';
        }

        $text = trim( wp_strip_all_tags( (string) $value ) );
        if ( $text === '' ) {
            return '';
        }

        $text = strtolower( $text );
        $text = preg_replace( '/[^a-z0-9]+/i', ' ', $text );
        $text = trim( preg_replace( '/\s+/', ' ', (string) $text ) );

        return $text;
    }
}

if ( ! function_exists( 'fts_v2_vm_package_text' ) ) {
    function fts_v2_vm_package_text( $package, $keys ) {
        if ( ! is_array( $package ) ) {
            return '';
        }

        foreach ( (array) $keys as $key ) {
            if ( isset( $package[ $key ] ) && is_scalar( $package[ $key ] ) ) {
                $text = trim( wp_strip_all_tags( (string) $package[ $key ] ) );
                if ( $text !== '' ) {
                    return $text;
                }
            }
        }

        return '';
    }
}

if ( ! function_exists( 'fts_v2_vm_package_list' ) ) {
    function fts_v2_vm_package_list( $package, $keys ) {
        if ( ! is_array( $package ) ) {
            return array();
        }

        foreach ( (array) $keys as $key ) {
            if ( ! array_key_exists( $key, $package ) ) {
                continue;
            }

            if ( is_array( $package[ $key ] ) ) {
                $items = fts_v2_vm_list_texts( $package[ $key ] );
                if ( ! empty( $items ) ) {
                    return $items;
                }
            } elseif ( is_scalar( $package[ $key ] ) ) {
                $text = trim( wp_strip_all_tags( (string) $package[ $key ] ) );
                if ( $text !== '' ) {
                    return array( $text );
                }
            }
        }

        return array();
    }
}

if ( ! function_exists( 'fts_v2_match_vm_package' ) ) {
    function fts_v2_match_vm_package( $pkg, $vm_packages, $index ) {
        if ( ! is_array( $pkg ) || ! is_array( $vm_packages ) || empty( $vm_packages ) ) {
            return array();
        }

        $pkg_id = isset( $pkg['id'] ) ? intval( $pkg['id'] ) : 0;
        if ( $pkg_id > 0 ) {
            foreach ( $vm_packages as $vm_package ) {
                if ( ! is_array( $vm_package ) ) {
                    continue;
                }
                if ( isset( $vm_package['id'] ) && intval( $vm_package['id'] ) === $pkg_id ) {
                    return $vm_package;
                }
            }
        }

        $pkg_key = '';
        if ( isset( $pkg['name'] ) && is_scalar( $pkg['name'] ) ) {
            $pkg_key = fts_v2_normalize_package_key( $pkg['name'] );
        }
        if ( $pkg_key !== '' ) {
            foreach ( $vm_packages as $vm_package ) {
                if ( ! is_array( $vm_package ) ) {
                    continue;
                }
                $vm_key = fts_v2_vm_package_text( $vm_package, array( 'name', 'title' ) );
                $vm_key = fts_v2_normalize_package_key( $vm_key );
                if ( $vm_key !== '' && $vm_key === $pkg_key ) {
                    return $vm_package;
                }
            }
        }

        if ( isset( $vm_packages[ $index ] ) && is_array( $vm_packages[ $index ] ) ) {
            return $vm_packages[ $index ];
        }

        return array();
    }
}

$fts_v2_vm_packages = ( ! empty( $use_frontend_view_model ) && ! empty( $vm_packages ) && is_array( $vm_packages ) ) ? $vm_packages : array();

$fts_v2_use_vm = ! empty( $use_frontend_view_model );

$fts_v2_highlight_items = array();
if ( $fts_v2_use_vm && ! empty( $vm_highlights ) && is_array( $vm_highlights ) ) {
    $fts_v2_highlight_items = fts_v2_vm_list_texts( $vm_highlights );
}
if ( empty( $fts_v2_highlight_items ) && ! empty( $highlights ) && is_array( $highlights ) ) {
    foreach ( $highlights as $highlight_item ) {
        $highlight_text = is_scalar( $highlight_item ) ? trim( (string) $highlight_item ) : '';
        if ( $highlight_text !== '' ) {
            $fts_v2_highlight_items[] = $highlight_text;
        }
    }
}
$fts_v2_has_highlights = ! empty( $fts_v2_highlight_items );

$fts_v2_itinerary_items = array();
if ( $fts_v2_use_vm && ! empty( $vm_itinerary ) && is_array( $vm_itinerary ) ) {
    $fts_v2_itinerary_items = fts_v2_vm_itinerary_items( $vm_itinerary );
}
if ( empty( $fts_v2_itinerary_items ) && ! empty( $itin_titles ) && is_array( $itin_titles ) ) {
    foreach ( $itin_titles as $key => $title ) {
        $title = is_scalar( $title ) ? trim( (string) $title ) : '';
        if ( $title === '' ) {
            continue;
        }

        $fts_v2_itinerary_items[] = array(
            'title'   => $title,
            'label'   => isset( $itin_days_label[ $key ] ) && is_scalar( $itin_days_label[ $key ] ) ? trim( (string) $itin_days_label[ $key ] ) : '',
            'content' => isset( $itin_content[ $key ] ) && is_scalar( $itin_content[ $key ] ) ? (string) $itin_content[ $key ] : '',
        );
    }
}
$fts_v2_has_itinerary = ! empty( $fts_v2_itinerary_items );

$fts_v2_included_items = array();
if ( $fts_v2_use_vm && ! empty( $vm_included ) && is_array( $vm_included ) ) {
    $fts_v2_included_items = fts_v2_vm_list_texts( $vm_included );
}
if ( empty( $fts_v2_included_items ) ) {
    $fts_v2_included_items = fts_v2_legacy_cost_items( $cost_includes ?? '' );
}

$fts_v2_excluded_items = array();
if ( $fts_v2_use_vm && ! empty( $vm_excluded ) && is_array( $vm_excluded ) ) {
    $fts_v2_excluded_items = fts_v2_vm_list_texts( $vm_excluded );
}
if ( empty( $fts_v2_excluded_items ) ) {
    $fts_v2_excluded_items = fts_v2_legacy_cost_items( $cost_excludes ?? '' );
}
$fts_v2_has_cost_content = ! empty( $fts_v2_included_items ) || ! empty( $fts_v2_excluded_items );

$fts_v2_faq_items = array();
if ( $fts_v2_use_vm && ! empty( $vm_faq ) && is_array( $vm_faq ) ) {
    $fts_v2_faq_items = fts_v2_vm_faq_items( $vm_faq );
}
if ( empty( $fts_v2_faq_items ) && ! empty( $faq_titles ) && is_array( $faq_titles ) ) {
    foreach ( $faq_titles as $key => $faq_title ) {
        $faq_title = is_scalar( $faq_title ) ? trim( (string) $faq_title ) : '';
        if ( $faq_title === '' ) {
            continue;
        }

        $fts_v2_faq_items[] = array(
            'question' => $faq_title,
            'answer'   => isset( $faq_content[ $key ] ) && is_scalar( $faq_content[ $key ] ) ? (string) $faq_content[ $key ] : '',
        );
    }
}
$fts_v2_has_faq_items = ! empty( $fts_v2_faq_items );
?>

<div class="fts-v2-content-sections fts-v2-accordion" data-single-open="true">

    <!-- ==================== ITINERARY ==================== -->
    <?php if ( $fts_v2_has_itinerary ) : ?>
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

        $total_items = count( $fts_v2_itinerary_items );
    ?>
    <section id="fts-v2-sec-itinerary" class="fts-v2-section fts-v2-accordion-item is-open">
        <div class="fts-v2-accordion-header">
            <h2 class="fts-v2-section-title"><?php echo esc_html__( 'Itinerary', 'fts' ); ?></h2>
            <span class="fts-v2-accordion-icon"><i class="fa fa-chevron-down"></i></span>
        </div>
        <div class="fts-v2-accordion-body">
            <div class="fts-v2-itinerary-timeline fts-v2-gyg-timeline">
                <?php $stop_num = 0; foreach ( $fts_v2_itinerary_items as $item ) : $title = $item['title'] ?? ''; if ( empty( $title ) ) continue; $stop_num++;
                    $is_first  = ( $stop_num === 1 );
                    $is_last   = ( $stop_num === $total_items );
                    $desc_raw  = $item['content'] ?? '';
                    $has_desc  = ! empty( trim( wp_strip_all_tags( $desc_raw ) ) );
                    $stop_type = fts_v2_detect_stop_type( $title, $desc_raw, $is_first, $is_last, $total_items );
                    $icon_svg  = $gyg_icons[ $stop_type ] ?? $gyg_icons['visit'];

                    $item_classes = array( 'fts-v2-tl-item', 'fts-v2-tl-type-' . $stop_type );
                    if ( $is_first ) $item_classes[] = 'fts-v2-tl-first';
                    if ( $is_last ) $item_classes[] = 'fts-v2-tl-last';
                    if ( $is_first && $has_desc ) $item_classes[] = 'active';

                    $label = $item['label'] ?? '';
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
    <?php if ( $fts_v2_has_highlights ) :
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
                    <?php foreach ( $fts_v2_highlight_items as $h ) : if ( empty( $h ) ) continue;
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
    <?php if ( $fts_v2_has_cost_content ) : ?>
    <section id="fts-v2-sec-includes" class="fts-v2-section fts-v2-accordion-item">
        <div class="fts-v2-accordion-header">
            <h2 class="fts-v2-section-title"><?php echo esc_html__( "What's Included", 'fts' ); ?></h2>
            <span class="fts-v2-accordion-icon"><i class="fa fa-chevron-down"></i></span>
        </div>
        <div class="fts-v2-accordion-body">
            <div class="fts-v2-includes-grid">
                <?php if ( ! empty( $fts_v2_included_items ) ) : ?>
                <div class="fts-v2-includes-col fts-v2-col-included">
                    <h3><i class="fa fa-check-circle"></i> <?php echo esc_html__( 'Included', 'fts' ); ?></h3>
                    <ul>
                        <?php foreach ( $fts_v2_included_items as $item ) : if ( empty( trim( $item ) ) ) continue; ?>
                        <li><i class="fa fa-check"></i> <?php echo esc_html( $item ); ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
                <?php endif; ?>

                <?php if ( ! empty( $fts_v2_excluded_items ) ) : ?>
                <div class="fts-v2-includes-col fts-v2-col-excluded">
                    <h3><i class="fa fa-times-circle"></i> <?php echo esc_html__( 'Not Included', 'fts' ); ?></h3>
                    <ul>
                        <?php foreach ( $fts_v2_excluded_items as $item ) : if ( empty( trim( $item ) ) ) continue; ?>
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
                <?php foreach ( $packages_list as $pkg_index => $pkg ) :
                    $card_cls = 'fts-v2-package-card';
                    if ( $pkg['badge'] === 'most_popular' ) $card_cls .= ' fts-v2-package-popular';
                    if ( $pkg['badge'] === 'best_value' )   $card_cls .= ' fts-v2-package-best-value';
                    $pkg_dp = floatval( $pkg['display_price'] ?? 0 );
                    $is_lowest = ( $pkg_min_price !== null && $pkg_dp > 0 && ( $pkg_dp - floatval( $pkg_min_price ) ) <= 0.01 );
                    if ( $is_lowest ) $card_cls .= ' fts-v2-package-lowest';

                    $pkg_vm_match = ! empty( $fts_v2_vm_packages ) ? fts_v2_match_vm_package( $pkg, $fts_v2_vm_packages, $pkg_index ) : array();

                    $pkg_display_badge = fts_v2_vm_package_text( $pkg_vm_match, array( 'badge' ) );
                    if ( $pkg_display_badge === '' ) {
                        if ( $pkg['badge'] === 'most_popular' ) {
                            $pkg_display_badge = esc_html__( 'Most Popular', 'fts' );
                        } elseif ( $pkg['badge'] === 'best_value' ) {
                            $pkg_display_badge = esc_html__( 'Best Value', 'fts' );
                        }
                    }

                    $pkg_vm_short_description = fts_v2_vm_package_text( $pkg_vm_match, array( 'short_description' ) );
                    $pkg_vm_long_description  = fts_v2_vm_package_text( $pkg_vm_match, array( 'description' ) );
                    $pkg_display_description  = '';
                    if ( $pkg_vm_short_description !== '' ) {
                        $pkg_display_description = $pkg_vm_short_description;
                    } elseif ( $pkg_vm_long_description !== '' ) {
                        $pkg_display_description = $pkg_vm_long_description;
                    } elseif ( ! empty( $pkg['description_full'] ) ) {
                        $pkg_display_description = (string) $pkg['description_full'];
                    } elseif ( ! empty( $pkg['description'] ) ) {
                        $pkg_display_description = (string) $pkg['description'];
                    }

                    $pkg_display_description_short = $pkg_display_description;
                    $pkg_display_description_full  = $pkg_display_description;
                    if ( $pkg_vm_short_description !== '' && $pkg_vm_long_description !== '' ) {
                        $pkg_display_description_short = $pkg_vm_short_description;
                        $pkg_display_description_full  = $pkg_vm_long_description;
                    } elseif ( $pkg_vm_short_description === '' && $pkg_vm_long_description === '' ) {
                        $pkg_display_description_short = ! empty( $pkg['description'] ) ? (string) $pkg['description'] : $pkg_display_description;
                        $pkg_display_description_full  = ! empty( $pkg['description_full'] ) ? (string) $pkg['description_full'] : $pkg_display_description_short;
                    }

                    $pkg_display_best_for = fts_v2_vm_package_text( $pkg_vm_match, array( 'best_for' ) );

                    $pkg_display_features = fts_v2_vm_package_list( $pkg_vm_match, array( 'features', 'includes' ) );
                    if ( empty( $pkg_display_features ) ) {
                        $pkg_display_features = ! empty( $pkg['features_all'] ) ? (array) $pkg['features_all'] : ( ! empty( $pkg['features'] ) ? (array) $pkg['features'] : array() );
                    }
                ?>
                <div class="<?php echo esc_attr( $card_cls ); ?>">
                    <?php if ( $pkg['badge'] === 'most_popular' ) : ?>
                    <div class="fts-v2-package-badge fts-v2-badge-popular"><span>&#9733;</span> <?php echo esc_html( $pkg_display_badge ); ?></div>
                    <?php elseif ( $pkg['badge'] === 'best_value' ) : ?>
                    <div class="fts-v2-package-badge fts-v2-badge-value"><span>&#9889;</span> <?php echo esc_html( $pkg_display_badge ); ?></div>
                    <?php elseif ( $pkg_display_badge !== '' ) : ?>
                    <div class="fts-v2-package-badge"><?php echo esc_html( $pkg_display_badge ); ?></div>
                    <?php endif; ?>
                    <?php if ( $is_lowest ) :
                        $lowest_alt = ( $pkg['badge'] === 'most_popular' || $pkg['badge'] === 'best_value' ) ? ' fts-v2-badge-alt' : '';
                    ?>
                    <div class="fts-v2-package-badge fts-v2-badge-lowest<?php echo $lowest_alt; ?>"><span>&#9660;</span> <?php echo esc_html__( 'Lowest Price', 'fts' ); ?></div>
                    <?php endif; ?>

                    <h3 class="fts-v2-package-name"><?php echo esc_html( $pkg['name'] ); ?></h3>
                    <?php if ( $pkg_display_best_for !== '' ) : ?>
                    <p class="fts-v2-package-desc"><?php echo esc_html( sprintf( __( 'Best for: %s', 'fts' ), $pkg_display_best_for ) ); ?></p>
                    <?php endif; ?>
                    <?php
                        $short_desc = is_string( $pkg_display_description_short ) ? $pkg_display_description_short : '';
                        $full_desc  = is_string( $pkg_display_description_full ) ? $pkg_display_description_full : '';
                        $has_more_desc = ( $full_desc !== '' && $full_desc !== $short_desc && $short_desc !== '' );
                    ?>
                    <?php if ( $full_desc !== '' ) : ?>
                    <div class="fts-v2-package-desc-wrap<?php echo $has_more_desc ? '' : ' no-toggle'; ?>">
                        <?php if ( $has_more_desc ) : ?>
                        <p class="fts-v2-package-desc fts-v2-desc-short"><?php echo esc_html( $short_desc ); ?></p>
                        <p class="fts-v2-package-desc fts-v2-desc-full"><?php echo esc_html( $full_desc ); ?></p>
                        <button type="button" class="fts-v2-desc-toggle" aria-expanded="false"><?php echo esc_html__( 'Show more', 'fts' ); ?></button>
                        <?php else : ?>
                        <p class="fts-v2-package-desc"><?php echo esc_html( $full_desc ); ?></p>
                        <?php endif; ?>
                    </div>
                    <?php endif; ?>

                    <div class="fts-v2-package-price">
                        <?php if ( $pkg['old_price'] > 0 ) : ?>
                        <span class="fts-v2-pkg-old"><?php echo wte_get_formated_price( $pkg['old_price'] ); ?></span>
                        <?php endif; ?>
                        <span class="fts-v2-pkg-current"><?php echo wte_get_formated_price( $pkg['display_price'] ); ?></span>
                        <span class="fts-v2-pkg-per"><?php echo esc_html__( '/ person', 'fts' ); ?></span>
                    </div>

                    <?php if ( ! empty( $pkg['discount_pct'] ) && intval( $pkg['discount_pct'] ) > 0 ) : ?>
                        <div class="fts-v2-pkg-save"><?php echo esc_html( sprintf( __( 'Save %s%%', 'fts' ), intval( $pkg['discount_pct'] ) ) ); ?></div>
                    <?php endif; ?>

                    <?php
                        $dp = floatval( $pkg['display_price'] ?? 0 );
                        $diff = ( $pkg_min_price !== null && $dp > 0 ) ? ( $dp - floatval( $pkg_min_price ) ) : 0;
                    ?>
                    <?php if ( $pkg_min_price !== null && $dp > 0 ) : ?>
                        <?php if ( $diff <= 0.01 ) : ?>
                            <div class="fts-v2-pkg-delta is-lowest"><?php echo esc_html__( 'Lowest price', 'fts' ); ?></div>
                        <?php else : ?>
                            <div class="fts-v2-pkg-delta"><?php echo esc_html( sprintf( __( '+%s vs lowest', 'fts' ), wte_get_formated_price( $diff ) ) ); ?></div>
                        <?php endif; ?>
                    <?php endif; ?>

                    <?php if ( ! empty( $pkg_display_features ) ) : ?>
                    <ul class="fts-v2-package-features">
                        <?php foreach ( $pkg_display_features as $feat ) : ?>
                        <?php $feat = is_scalar( $feat ) ? trim( (string) $feat ) : ''; if ( $feat === '' ) continue; ?>
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
    <?php if ( $fts_v2_has_faq_items ) : ?>
    <section id="fts-v2-sec-faq" class="fts-v2-section fts-v2-accordion-item">
        <div class="fts-v2-accordion-header">
            <h2 class="fts-v2-section-title"><?php echo esc_html__( 'Frequently Asked Questions', 'fts' ); ?></h2>
            <span class="fts-v2-accordion-icon"><i class="fa fa-chevron-down"></i></span>
        </div>
        <div class="fts-v2-accordion-body">
            <div class="fts-v2-faq-list">
                <?php foreach ( $fts_v2_faq_items as $faq_item ) : $faq_title = $faq_item['question'] ?? ''; if ( empty( $faq_title ) ) continue; ?>
                <div class="fts-v2-faq-item">
                    <div class="fts-v2-faq-question">
                        <span><?php echo esc_html( $faq_title ); ?></span>
                        <i class="fa fa-chevron-down"></i>
                    </div>
                    <?php if ( ! empty( $faq_item['answer'] ) ) : ?>
                    <div class="fts-v2-faq-answer">
                        <?php echo wp_kses_post( $faq_item['answer'] ); ?>
                    </div>
                    <?php endif; ?>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
    <?php endif; ?>

</div>
