<?php
if (!defined('ABSPATH')) exit;

class XP_TI_CPT {
    public static function init() {
        add_action('init', [__CLASS__, 'register_post_type']);
    }

    public static function register_post_type() {
        $capabilities = [
            'edit_post'              => 'edit_xp_itinerary',
            'read_post'              => 'read_xp_itinerary',
            'delete_post'            => 'delete_xp_itinerary',
            'edit_posts'             => 'edit_xp_itineraries',
            'edit_others_posts'      => 'edit_others_xp_itineraries',
            'publish_posts'          => 'publish_xp_itineraries',
            'read_private_posts'     => 'read_private_xp_itineraries',
            'delete_posts'           => 'delete_xp_itineraries',
            'delete_private_posts'   => 'delete_private_xp_itineraries',
            'delete_published_posts' => 'delete_published_xp_itineraries',
            'delete_others_posts'    => 'delete_others_xp_itineraries',
        ];

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
            'show_in_menu'        => true,
            'show_in_admin_bar'   => true,
            'show_in_rest'        => false,
            'menu_icon'           => 'dashicons-location-alt',
            'supports'            => ['title', 'editor', 'thumbnail', 'excerpt'],
            'has_archive'         => true,
            'rewrite'             => ['slug' => 'travel-itineraries'],
            'capability_type'     => ['xp_itinerary', 'xp_itineraries'],
            'map_meta_cap'        => true,
            'capabilities'        => $capabilities,
        ]);
    }

    public static function grant_admin_capabilities() {
        $role = get_role('administrator');
        if (!$role) {
            return;
        }

        foreach ([
            'edit_xp_itinerary',
            'read_xp_itinerary',
            'delete_xp_itinerary',
            'edit_xp_itineraries',
            'edit_others_xp_itineraries',
            'publish_xp_itineraries',
            'read_private_xp_itineraries',
            'delete_xp_itineraries',
            'delete_private_xp_itineraries',
            'delete_published_xp_itineraries',
            'delete_others_xp_itineraries',
        ] as $cap) {
            $role->add_cap($cap);
        }
    }
}
