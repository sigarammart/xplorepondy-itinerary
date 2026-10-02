<?php
if (!defined('ABSPATH')) exit;

class XP_TI_CPT {
    public static function init() {
        add_action('init', [__CLASS__, 'register_post_type']);
        add_action('admin_init', [__CLASS__, 'grant_admin_capabilities']);
        add_action('admin_menu', [__CLASS__, 'admin_menu'], 99);
    }

    public static function register_post_type() {
        register_post_type('xp_itinerary', [
            'labels' => [
                'name'               => 'Travel Itineraries',
                'singular_name'      => 'Travel Itinerary',
                'menu_name'          => 'Travel Itineraries',
                'add_new'            => 'Add New',
                'add_new_item'       => 'Add New Travel Itinerary',
                'edit_item'          => 'Edit Travel Itinerary',
                'new_item'           => 'New Travel Itinerary',
                'view_item'          => 'View Travel Itinerary',
                'search_items'       => 'Search Travel Itineraries',
                'not_found'          => 'No travel itineraries found.',
                'not_found_in_trash' => 'No travel itineraries found in Trash.',
                'all_items'          => 'All Travel Itineraries',
            ],
            'public'              => true,
            'show_ui'             => true,
            'show_in_menu'        => false,
            'show_in_admin_bar'   => true,
            'show_in_rest'        => false,
            'menu_icon'           => 'dashicons-location-alt',
            'supports'            => ['title', 'editor', 'thumbnail', 'excerpt'],
            'has_archive'         => true,
            'rewrite'             => ['slug' => 'travel-itineraries'],
            'capability_type'     => 'post',
            'map_meta_cap'        => true,
            'capabilities'        => [
                'edit_post'             => 'manage_options',
                'read_post'             => 'manage_options',
                'delete_post'           => 'manage_options',
                'edit_posts'            => 'manage_options',
                'edit_others_posts'     => 'manage_options',
                'publish_posts'         => 'manage_options',
                'read_private_posts'    => 'manage_options',
                'delete_posts'          => 'manage_options',
                'delete_private_posts'  => 'manage_options',
                'delete_published_posts'=> 'manage_options',
                'delete_others_posts'   => 'manage_options',
                'create_posts'          => 'manage_options',
            ],
        ]);
    }

    public static function grant_admin_capabilities() {
        // The CPT intentionally uses manage_options, so only administrators
        // (and any role explicitly granted manage_options) can manage it.
    }

    public static function admin_menu() {
        if (!current_user_can('manage_options')) {
            return;
        }

        add_menu_page(
            'Travel Itineraries',
            'Travel Itineraries',
            'manage_options',
            'edit.php?post_type=xp_itinerary',
            '',
            'dashicons-location-alt',
            61
        );

        add_submenu_page(
            'edit.php?post_type=xp_itinerary',
            'All Travel Itineraries',
            'All Itineraries',
            'manage_options',
            'edit.php?post_type=xp_itinerary'
        );

        add_submenu_page(
            'edit.php?post_type=xp_itinerary',
            'Add New Travel Itinerary',
            'Add New',
            'manage_options',
            'post-new.php?post_type=xp_itinerary'
        );
    }
}
