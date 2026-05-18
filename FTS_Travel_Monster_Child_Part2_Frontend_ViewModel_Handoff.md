# FTS Travel Monster Child — Part 2 Frontend UI Layer Handoff

## Purpose

This handoff continues the work after Part 1, where the project adds a `frontend_view_model` / `AI_Frontend_View_Model` data layer.

Part 2 is responsible for consuming that ViewModel inside the WordPress child theme and upgrading the current trip page UI without breaking the existing V2 design, booking modal, WP Travel Engine logic, reviews, currency conversion, or schema.

---

## Repository

Repository:

```text
eladawy5222-cmd/Travel-Monster-Child
```

Important inspected ref:

```text
8610a3c37531aa2fa38630aa055c02a0e73ff90d
```

Main current frontend engine:

```text
trip-design-v2/layout-controller.php
```

Current rendering entrypoint:

```php
FTS_Trip_Redesign_V2::render_nuclear_custom_layout();
```

This is called by:

```text
wp-travel-engine/single-trip.php
```

The child theme forces trip pages to use that template through `functions.php` with a `template_include` filter.

---

## Current Architecture Summary

The class `FTS_Trip_Redesign_V2` currently:

1. Registers hooks in `init()`.
2. Cleans up default WP Travel Engine hooks in `cleanup_and_setup()`.
3. Fetches all trip page data once in `get_trip_data()`.
4. Stores the data in `self::$trip_data`.
5. Renders the page in `render_nuclear_custom_layout()`.
6. Includes component files from:

```text
trip-design-v2/parts/
```

7. Enqueues CSS/JS from:

```text
trip-design-v2/assets/
```

8. Passes booking data to JS via:

```php
wp_localize_script( 'fts-trip-v2-script', 'ftsV2Data', array(...) );
```

9. Prints Product/Review schema inside `render_nuclear_custom_layout()`.

Current key parts:

```text
trip-design-v2/parts/header-v2.php
trip-design-v2/parts/quick-info-v2.php
trip-design-v2/parts/tabs-accordion-v2.php
trip-design-v2/parts/sidebar-v2.php
trip-design-v2/parts/footer-v2.php
trip-design-v2/parts/booking-modal-v2.php
```

Current key assets:

```text
trip-design-v2/assets/js/script-v2.js
trip-design-v2/assets/css/responsive.css
trip-design-v2/assets/css/header.css
trip-design-v2/assets/css/quick-info.css
trip-design-v2/assets/css/sidebar.css
trip-design-v2/assets/css/packages.css
```

---

## Core Decision

Do **not** rebuild the trip page from scratch.

Do **not** create a new plugin for the first implementation.

Instead:

```text
Upgrade the existing FTS_Trip_Redesign_V2 system to become frontend_view_model-aware.
```

Principle:

```text
Current V2 data = fallback
Frontend ViewModel = optional override
```

If `frontend_view_model` is missing, invalid, or incomplete, the page must continue to work exactly as it does today.

---

## Data Sources to Read

The frontend renderer should read ViewModel data from these possible locations, in order:

```text
wp_travel_engine_setting['frontend_view_model']
wp_travel_engine_setting['ai_frontend_view_model']
post meta: fts_frontend_view_model
post meta: ai_frontend_view_model
post meta: AI_Frontend_View_Model
```

Expected ViewModel shape:

```json
{
  "version": "1.0",
  "generated_at": "...",
  "source": {},
  "trip": {},
  "hero": {
    "title": "",
    "subtitle": "",
    "image": null,
    "badges": [],
    "primary_cta": "Check availability",
    "secondary_cta": "Chat on WhatsApp"
  },
  "trust": {
    "rating": null,
    "reviews_count": null,
    "label": "",
    "badges": []
  },
  "quick_info": [],
  "highlights": [],
  "itinerary": [],
  "packages": [],
  "included": [],
  "excluded": [],
  "faq": [],
  "images": {
    "hero": null,
    "gallery": []
  },
  "cta": {
    "price_text": "",
    "primary": "Check availability",
    "secondary": "Chat on WhatsApp",
    "supporting_text": ""
  },
  "layout_hints": {}
}
```

---

## Safety Rules

These are mandatory:

1. Do not break the existing V2 design.
2. Do not remove existing WP Travel Engine booking logic.
3. Do not change package IDs, category IDs, traveler counters, checkout payload, or datepicker logic in the first pass.
4. Do not add new JSON-LD schema in this frontend phase.
5. Do not rely on ViewModel as the only source of truth.
6. Do not remove `FTS_Trip_Redesign_V2` fallbacks.
7. If ViewModel is invalid JSON, ignore it and log only if useful.
8. Use ViewModel only as display/copy/layout enhancement first.
9. Keep existing schema, booking modal, currency converter, reviews, Trustindex, and WP Rocket exclusions intact.
10. Add a feature flag/filter so ViewModel rendering can be disabled quickly.

Recommended feature flag:

```php
$use_frontend_view_model = (bool) apply_filters(
    'fts_v2_use_frontend_view_model',
    ! empty( $frontend_view_model ),
    $trip_id,
    $frontend_view_model
);
```

Emergency rollback snippet:

```php
add_filter( 'fts_v2_use_frontend_view_model', '__return_false' );
```

---

# Implementation Plan

## Sprint 1 — Add ViewModel Reader to layout-controller.php

### Files

```text
trip-design-v2/layout-controller.php
```

### Goal

Make `FTS_Trip_Redesign_V2::get_trip_data()` read and expose the ViewModel to all component parts.

### Required Changes

Add private helper methods inside the class:

```php
private static function read_frontend_view_model( $trip_id, $settings )
private static function vm_get( $vm, $path, $fallback = null )
private static function vm_text( $vm, $path, $fallback = '' )
private static function vm_list( $vm, $path, $fallback = array() )
```

In `get_trip_data()`, after reading `$bold_promise` and `$at_a_glance`, read:

```text
frontend_view_model
ai_frontend_view_model
fts_frontend_view_model
AI_Frontend_View_Model
```

Create:

```php
$frontend_view_model = self::read_frontend_view_model( $trip_id, $settings );
$has_frontend_view_model = ! empty( $frontend_view_model );
$use_frontend_view_model = (bool) apply_filters(
    'fts_v2_use_frontend_view_model',
    $has_frontend_view_model,
    $trip_id,
    $frontend_view_model
);
```

Create convenience variables:

```php
$vm_hero       = self::vm_get( $frontend_view_model, 'hero', array() );
$vm_trust      = self::vm_get( $frontend_view_model, 'trust', array() );
$vm_quick_info = self::vm_list( $frontend_view_model, 'quick_info', array() );
$vm_highlights = self::vm_list( $frontend_view_model, 'highlights', array() );
$vm_itinerary  = self::vm_list( $frontend_view_model, 'itinerary', array() );
$vm_packages   = self::vm_list( $frontend_view_model, 'packages', array() );
$vm_included   = self::vm_list( $frontend_view_model, 'included', array() );
$vm_excluded   = self::vm_list( $frontend_view_model, 'excluded', array() );
$vm_faq        = self::vm_list( $frontend_view_model, 'faq', array() );
$vm_images     = self::vm_get( $frontend_view_model, 'images', array() );
$vm_cta        = self::vm_get( $frontend_view_model, 'cta', array() );
```

Add these to the `compact()` call:

```php
'frontend_view_model',
'has_frontend_view_model',
'use_frontend_view_model',
'vm_hero',
'vm_trust',
'vm_quick_info',
'vm_highlights',
'vm_itinerary',
'vm_packages',
'vm_included',
'vm_excluded',
'vm_faq',
'vm_images',
'vm_cta'
```

Inside `wp_localize_script()`, add:

```php
'frontendViewModel'    => $data['use_frontend_view_model'] ? $data['frontend_view_model'] : array(),
'hasFrontendViewModel' => ! empty( $data['use_frontend_view_model'] ),
```

### Validation

- Trip without ViewModel renders exactly as before.
- Trip with ViewModel exposes `ftsV2Data.frontendViewModel` in browser console.
- No PHP warnings if ViewModel is missing or invalid.
- No booking modal behavior changes.

### TREA Prompt

```text
You are working in the Travel-Monster-Child repository.

Task: Make FTS_Trip_Redesign_V2 ViewModel-aware without changing visual output yet.

Modify only:
- trip-design-v2/layout-controller.php

Requirements:
1. Add private helper methods inside class FTS_Trip_Redesign_V2:
   - read_frontend_view_model($trip_id, $settings)
   - vm_get($vm, $path, $fallback = null)
   - vm_text($vm, $path, $fallback = '')
   - vm_list($vm, $path, $fallback = array())

2. read_frontend_view_model must read from:
   - $settings['frontend_view_model']
   - $settings['ai_frontend_view_model']
   - get_post_meta($trip_id, 'fts_frontend_view_model', true)
   - get_post_meta($trip_id, 'ai_frontend_view_model', true)
   - get_post_meta($trip_id, 'AI_Frontend_View_Model', true)

3. It must accept both array and JSON string. If invalid, return array().

4. In get_trip_data(), after $bold_promise and $at_a_glance are prepared, set:
   - $frontend_view_model
   - $has_frontend_view_model
   - $use_frontend_view_model using apply_filters('fts_v2_use_frontend_view_model', ...)
   - convenience variables: $vm_hero, $vm_trust, $vm_quick_info, $vm_highlights, $vm_itinerary, $vm_packages, $vm_included, $vm_excluded, $vm_faq, $vm_images, $vm_cta

5. Add all these variables to the compact() call for self::$trip_data.

6. In wp_localize_script('fts-trip-v2-script', 'ftsV2Data', ...), add:
   - frontendViewModel
   - hasFrontendViewModel

7. Do not change rendering logic yet.
8. Do not remove any existing variables.
9. Do not modify booking, schema, datepicker, packages, CSS, JS, or parts in this sprint.

After editing, run php -l trip-design-v2/layout-controller.php and summarize changes.
```

---

## Sprint 2 — Hero/Header + Quick Info from ViewModel

### Files

```text
trip-design-v2/parts/header-v2.php
trip-design-v2/parts/quick-info-v2.php
```

### Goal

Use the ViewModel to improve first-screen decision data while preserving current fallback.

### Header Data Priority

Title priority:

```text
vm.hero.title
existing title logic
```

Subtitle/promise priority:

```text
vm.hero.subtitle
$bold_promise
$overview_excerpt
```

Badges priority:

```text
vm.hero.badges
existing benefit/trust logic
```

Rating priority:

```text
vm.trust.rating + vm.trust.reviews_count
$avg_rating + $review_count
```

### Quick Info Priority

If `use_frontend_view_model` and `vm_quick_info` has items, render quick info from VM.

Fallback:

```text
$trip_facts_items
existing quick-info-v2.php logic
```

VM item shape:

```json
{
  "label": "Duration",
  "value": "11–14 hours",
  "icon": "clock",
  "priority": 1
}
```

Map icons to current FontAwesome 4 classes when needed:

```text
clock -> fa-clock-o
pickup -> fa-map-marker
language -> fa-language
meal -> fa-cutlery
vehicle -> fa-car
users/group -> fa-users
map-pin -> fa-map-marker
calendar -> fa-calendar
shield -> fa-shield
check -> fa-check-circle
landmark -> fa-university
```

### Validation

- H1 remains single and valid.
- Hero works on mobile and desktop.
- Quick info renders from VM when present.
- Old quick info works when VM missing.
- No broken icons.

### TREA Prompt

```text
Task: Use frontend_view_model data in the V2 header and quick info components while keeping full fallback.

Modify only:
- trip-design-v2/parts/header-v2.php
- trip-design-v2/parts/quick-info-v2.php

Requirements:
1. Do not remove existing rendering. Add ViewModel overrides only when $use_frontend_view_model is true.
2. In header-v2.php:
   - Use $vm_hero['title'] as title if present.
   - Use $vm_hero['subtitle'] as subtitle/promise if present.
   - Use $vm_hero['badges'] if present, otherwise existing badges/benefits.
   - Use $vm_trust['rating'] and $vm_trust['reviews_count'] if present, otherwise $avg_rating and $review_count.
3. In quick-info-v2.php:
   - If $use_frontend_view_model and $vm_quick_info is a non-empty array, render quick info from it.
   - Otherwise keep existing $trip_facts_items rendering.
4. Map VM icon keys to FontAwesome 4 classes.
5. Escape all output with esc_html, esc_attr, esc_url as appropriate.
6. Do not change booking behavior, JS data, schema, or CSS in this sprint.
7. Ensure a trip without VM is visually unchanged.

After editing, run php -l on both files and summarize changes.
```

---

## Sprint 3 — Sidebar Booking Card + Mobile CTA

### Files

```text
trip-design-v2/parts/sidebar-v2.php
trip-design-v2/parts/footer-v2.php
trip-design-v2/assets/css/responsive.css
```

### Goal

Use ViewModel CTA/trust data in the booking sidebar and mobile sticky bar.

### ViewModel fields

```text
vm.cta.price_text
vm.cta.primary
vm.cta.secondary
vm.cta.supporting_text
vm.trust.rating
vm.trust.reviews_count
vm.trust.badges
vm.hero.badges
```

### Rules

- Do not change datepicker logic.
- Do not change traveler counters.
- Do not change checkout payload.
- Do not change package IDs or pricing calculations.
- CTA button can use VM text, but click behavior remains the same.

### Desired Mobile Sticky Bar

```text
From €81 / person        Check availability
Free cancellation up to 24 hours
```

Use fallback if VM missing.

### Validation

- Sidebar works on desktop.
- Sticky CTA works on mobile.
- Booking modal opens normally.
- Datepicker and travelers still work.
- WhatsApp link remains valid.

### TREA Prompt

```text
Task: Add ViewModel-aware CTA and trust copy to sidebar and mobile footer without changing booking behavior.

Modify only:
- trip-design-v2/parts/sidebar-v2.php
- trip-design-v2/parts/footer-v2.php
- trip-design-v2/assets/css/responsive.css only if absolutely needed for small display fixes

Requirements:
1. If $use_frontend_view_model is true, use:
   - $vm_cta['price_text'] for display-only price text where appropriate.
   - $vm_cta['primary'] for primary CTA button label.
   - $vm_cta['secondary'] for secondary/WhatsApp label if applicable.
   - $vm_cta['supporting_text'] for cancellation/supporting message.
   - $vm_trust['badges'] and $vm_hero['badges'] for trust points if present.
2. Keep all current pricing variables and booking calculations intact.
3. Do not change hidden inputs, package IDs, datepicker logic, travelers logic, or checkout JS hooks.
4. If any VM field is missing, fallback to existing variables: $display_price, $old_price, $avg_rating, $review_count, $cancel_hours, $sidebar_trust_items.
5. Use proper escaping.
6. Do not touch booking-modal-v2.php in this sprint.

After editing, test desktop sidebar and mobile sticky CTA manually.
```

---

## Sprint 4 — Tabs Content from ViewModel

### File

```text
trip-design-v2/parts/tabs-accordion-v2.php
```

### Goal

Allow page content sections to use structured VM data where available.

### Sections

Use VM first, old data second:

| Section | VM source | Fallback |
|---|---|---|
| Highlights | `vm.highlights` | `$highlights` |
| Itinerary | `vm.itinerary` | `$itin_titles`, `$itin_content`, `$itin_days_label` |
| Included | `vm.included` | `$cost_includes` |
| Excluded | `vm.excluded` | `$cost_excludes` |
| FAQ | `vm.faq` | `$faq_titles`, `$faq_content` |

### Rules

- Keep existing tab IDs and anchors.
- Keep existing accordion behavior and classes where possible.
- Preserve SEO-visible FAQ content.
- Do not add schema.
- Do not alter package booking logic.

### Validation

- Tabs nav still works.
- Accordions open/close.
- FAQ content visible in HTML.
- Missing VM falls back correctly.

### TREA Prompt

```text
Task: Make tabs-accordion-v2.php use frontend ViewModel section data when present, with full fallback.

Modify only:
- trip-design-v2/parts/tabs-accordion-v2.php

Requirements:
1. If $use_frontend_view_model is true:
   - Render highlights from $vm_highlights when non-empty.
   - Render itinerary from $vm_itinerary when non-empty.
   - Render included items from $vm_included when non-empty.
   - Render excluded items from $vm_excluded when non-empty.
   - Render FAQ from $vm_faq when non-empty.
2. If a VM section is empty, use the existing old data/rendering for that section.
3. Keep existing wrapper classes, tab IDs, accordion classes, and navigation compatibility.
4. Escape text output properly. Allow safe HTML only where the old template already allowed content HTML.
5. Do not add JSON-LD schema.
6. Do not modify package booking calculations.
7. Keep a trip without ViewModel visually/functionally unchanged.

After editing, run php -l and test all tabs/accordions.
```

---

## Sprint 5 — Package Copy Enhancement Only

### Files

```text
trip-design-v2/parts/tabs-accordion-v2.php
trip-design-v2/parts/booking-modal-v2.php
```

### Goal

Use ViewModel packages only for display copy enhancements.

### Important Rule

Do not use VM package data as booking truth in Sprint 5.

Booking truth remains:

```text
$packages_list
booking_modal_data
ftsV2Data.packages
```

### Use VM only for

```text
badge
short_description
best_for
features/includes summary
```

### Matching rule

Match VM package to existing package by:

1. package id if available
2. normalized package name
3. index fallback

### Validation

- Package selection works.
- Traveler counters work.
- Checkout receives correct package/category data.
- Display copy improves when VM exists.
- Old package display works without VM.

### TREA Prompt

```text
Task: Enhance package display copy from frontend ViewModel without changing booking truth.

Modify only:
- trip-design-v2/parts/tabs-accordion-v2.php
- trip-design-v2/parts/booking-modal-v2.php only if package copy is rendered there

Requirements:
1. Existing $packages_list remains the booking source of truth.
2. Use $vm_packages only to override/enhance display copy:
   - badge
   - short_description
   - best_for
   - display features/includes summary
3. Match VM packages to existing packages by id, normalized name, then index fallback.
4. Do not change package IDs, category IDs, prices, traveler counters, hidden fields, checkout URL, or JS booking payload.
5. If no VM package match, use existing package data.
6. Escape all output.

After editing, test package selection and checkout flow.
```

---

## Sprint 6 — ViewModel CSS Overrides

### New File

```text
trip-design-v2/assets/css/viewmodel-overrides.css
```

### Modify

```text
trip-design-v2/layout-controller.php
```

### Goal

Add isolated styling for ViewModel-enhanced UI.

### Add CSS module to `$trip_css`

```php
private static $trip_css = array(
    ...,
    'viewmodel-overrides',
);
```

### CSS scope

All new classes should be scoped under:

```css
.fts-v2-root
```

or VM-specific classes like:

```css
.fts-v2-vm-badge
.fts-v2-vm-trust
.fts-v2-vm-quick-info
```

### Focus

- Improve badges.
- Improve quick info cards.
- Improve mobile sticky CTA copy layout.
- Improve trust strip density.

### Validation

- No global theme breakage.
- Desktop unaffected except intended improvements.
- Mobile 360px and 390px work.

### TREA Prompt

```text
Task: Add isolated CSS for ViewModel-enhanced UI.

Create:
- trip-design-v2/assets/css/viewmodel-overrides.css

Modify:
- trip-design-v2/layout-controller.php

Requirements:
1. Add 'viewmodel-overrides' to the $trip_css list so it enqueues after existing modules.
2. New CSS must be scoped to .fts-v2-root or VM-specific classes.
3. Add small, focused styles only for:
   - ViewModel badges
   - trust badges
   - quick info items
   - mobile sticky CTA supporting text
4. Do not rewrite existing layout CSS.
5. Do not use external libraries.
6. Keep CSS lightweight.

After editing, verify the CSS file is enqueued on single trip pages.
```

---

## Sprint 7 — QA and Rollback

### Test Matrix

Test at least 3 trip states:

```text
1. Trip with no ViewModel
2. Trip with complete ViewModel
3. Trip with partial/malformed ViewModel
```

### Device widths

```text
360px
390px
768px
1024px
1366px
1920px
```

### Functional checks

```text
Header renders
Quick info renders
Sidebar renders
Mobile sticky CTA renders
Booking modal opens
Datepicker works
Traveler counters work
Package selection works
Checkout payload is correct
WhatsApp link works
Tabs navigation works
Accordions work
FAQ visible in HTML
Reviews still render
Trustindex still loads
Currency converter still works
```

### SEO checks

```text
Only one H1
No duplicate JSON-LD added
Canonical unchanged
Meta title/description unchanged
FAQ visible to users
Images have alt text where available
```

### Performance checks

```text
No large new JS bundle
No new external libraries
No obvious CLS from badges/CTA
Hero image still loads correctly
Mobile sticky bar does not cover important controls
```

### Rollback

Disable ViewModel usage:

```php
add_filter( 'fts_v2_use_frontend_view_model', '__return_false' );
```

Or remove/disable `frontend_view_model` meta for a trip.

---

# Final File Change List

## Must modify

```text
trip-design-v2/layout-controller.php
trip-design-v2/parts/header-v2.php
trip-design-v2/parts/quick-info-v2.php
trip-design-v2/parts/sidebar-v2.php
trip-design-v2/parts/footer-v2.php
trip-design-v2/parts/tabs-accordion-v2.php
```

## Likely modify later

```text
trip-design-v2/parts/booking-modal-v2.php
trip-design-v2/assets/js/script-v2.js
trip-design-v2/assets/css/responsive.css
```

## Add

```text
trip-design-v2/assets/css/viewmodel-overrides.css
```

## Do not modify initially

```text
functions.php
wp-travel-engine/single-trip.php
style.css
```

Reason: `functions.php` already routes trip pages to `wp-travel-engine/single-trip.php`, and that template already calls `FTS_Trip_Redesign_V2::render_nuclear_custom_layout()`.

---

# Completion Definition

Part 2 is considered complete when:

1. `FTS_Trip_Redesign_V2` reads `frontend_view_model` safely.
2. `frontendViewModel` is available in `ftsV2Data`.
3. Header, quick info, trust, sidebar CTA, mobile CTA, highlights, itinerary, includes/excludes, FAQ can use VM data.
4. Missing VM falls back to current V2 behavior.
5. Booking modal, datepicker, package selection, and checkout remain intact.
6. No duplicate schema is added.
7. Feature flag can disable ViewModel instantly.
8. CSS additions are isolated and lightweight.

---

# Recommended Next Action

Start with Sprint 1 only.

Do not proceed to visual components until Sprint 1 is tested on:

```text
- one trip without ViewModel
- one trip with ViewModel
```

Then proceed Sprint by Sprint.
