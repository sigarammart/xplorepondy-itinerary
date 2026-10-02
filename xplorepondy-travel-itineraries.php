<?php
/**
 * Plugin Name: XplorePondy Travel Itineraries
 * Description: Admin-only curated travel itineraries built from existing XplorePondy listings, with WooCommerce booking support and REST API.
 * Version: 0.1.3
 * Author: XplorePondy
 * Requires at least: 6.0
 * Requires PHP: 7.4
 */
if (!defined('ABSPATH')) exit;

define('XP_TI_VERSION', '0.1.3');
define('XP_TI_FILE', __FILE__);
define('XP_TI_DIR', plugin_dir_path(__FILE__));
define('XP_TI_URL', plugin_dir_url(__FILE__));

require_once XP_TI_DIR . 'includes/class-xp-ti-cpt.php';
require_once XP_TI_DIR . 'includes/class-xp-ti-admin.php';
require_once XP_TI_DIR . 'includes/class-xp-ti-woocommerce.php';
require_once XP_TI_DIR . 'includes/class-xp-ti-rest.php';

function xp_ti_init() {
    XP_TI_CPT::init();
    XP_TI_Admin::init();
    XP_TI_WooCommerce::init();
    XP_TI_REST::init();
}
add_action('plugins_loaded', 'xp_ti_init');

function xp_ti_activate() {
    XP_TI_CPT::register_post_type();
    flush_rewrite_rules();
}
register_activation_hook(__FILE__, 'xp_ti_activate');

function xp_ti_deactivate() {
    flush_rewrite_rules();
}
register_deactivation_hook(__FILE__, 'xp_ti_deactivate');
