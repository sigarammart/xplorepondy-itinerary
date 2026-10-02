<?php
if (!defined('ABSPATH')) exit;
class XP_TI_Admin {
    public static function init() {
        add_action('add_meta_boxes', [__CLASS__, 'meta_boxes']);
        add_action('save_post_xp_itinerary', [__CLASS__, 'save'], 10, 2);
        add_action('admin_enqueue_scripts', [__CLASS__, 'assets']);
        add_action('wp_ajax_xp_ti_search_listings', [__CLASS__, 'search_listings']);
    }
    public static function meta_boxes() {
        add_meta_box('xp_ti_details','Itinerary Details',[__CLASS__,'details'],'xp_itinerary','normal','high');
        add_meta_box('xp_ti_builder','Itinerary Builder',[__CLASS__,'builder'],'xp_itinerary','normal','high');
        add_meta_box('xp_ti_booking','Booking & WooCommerce',[__CLASS__,'booking'],'xp_itinerary','side','default');
    }
    public static function assets($hook) {
        $screen = get_current_screen(); if (!$screen || $screen->post_type !== 'xp_itinerary') return;
        wp_enqueue_script('jquery-ui-sortable'); wp_enqueue_script('jquery-ui-autocomplete');
        wp_enqueue_script('xp-ti-admin', XP_TI_URL.'assets/js/admin.js',['jquery','jquery-ui-sortable','jquery-ui-autocomplete'],XP_TI_VERSION,true);
        wp_enqueue_style('xp-ti-admin', XP_TI_URL.'assets/css/admin.css',[],XP_TI_VERSION);
        wp_localize_script('xp-ti-admin','XP_TI_ADMIN',[ 'ajaxurl'=>admin_url('admin-ajax.php'),'nonce'=>wp_create_nonce('xp_ti_admin'),'currency'=>get_woocommerce_currency() ]);
    }
    public static function details($post) {
        $days=(int)get_post_meta($post->ID,'_xp_ti_days',true); $destination=get_post_meta($post->ID,'_xp_ti_destination',true);
        $style=get_post_meta($post->ID,'_xp_ti_style',true); $budget=get_post_meta($post->ID,'_xp_ti_budget',true); $best=get_post_meta($post->ID,'_xp_ti_best_time',true);
        wp_nonce_field('xp_ti_save','xp_ti_nonce');
        echo '<div class="xp-ti-grid">'; self::field('Destination','_xp_ti_destination',$destination); self::field('Number of days','_xp_ti_days',$days,'number',1,30); self::field('Travel style','_xp_ti_style',$style); self::field('Budget','_xp_ti_budget',$budget); self::field('Best time','_xp_ti_best_time',$best); echo '</div>';
    }
    private static function field($label,$name,$value,$type='text',$min='',$max='') { echo '<label class="xp-ti-field"><span>'.esc_html($label).'</span><input type="'.esc_attr($type).'" name="'.esc_attr($name).'" value="'.esc_attr($value).'"'.($min!==''?' min="'.esc_attr($min).'"':'').($max!==''?' max="'.esc_attr($max).'"':'').'></label>'; }
    public static function builder($post) {
        $days=(int)get_post_meta($post->ID,'_xp_ti_days',true); $days=max(1,$days); $data=get_post_meta($post->ID,'_xp_ti_days_data',true); $data=is_array($data)?$data:[];
        echo '<div id="xp-ti-builder">';
        for($d=1;$d<=$days;$d++){ $day=$data[$d]??['title'=>'Day '.$d,'description'=>'','items'=>[]];
            echo '<section class="xp-ti-day" data-day="'.esc_attr($d).'">'; echo '<div class="xp-ti-day-head"><h3>Day '.esc_html($d).'</h3><input class="xp-ti-day-title" name="xp_ti_days['.$d.'][title]" value="'.esc_attr($day['title']).'" placeholder="Day title"></div>'; echo '<textarea name="xp_ti_days['.$d.'][description]" placeholder="Day description">'.esc_textarea($day['description']).'</textarea>';
            echo '<div class="xp-ti-items">'; foreach((array)$day['items'] as $i=>$item) self::item($d,$i,$item); echo '</div><button type="button" class="button xp-ti-add-item">+ Add itinerary item</button></section>';
        }
        echo '</div><p class="description">Items reference existing XplorePondy <code>listing</code> posts. They do not create or modify Listeo bookings.</p>';
    }
    private static function item($d,$i,$item){ $listing_id=(int)($item['listing_id']??0); $listing=$listing_id?get_post($listing_id):null; $title=$listing?$listing->post_title:''; $time=$item['time']??''; $activity=$item['activity']??''; $duration=$item['duration']??''; $notes=$item['notes']??'';
        echo '<div class="xp-ti-item" draggable="true"><span class="xp-ti-handle">☷</span><input type="time" name="xp_ti_days['.$d.'][items]['.$i.'][time]" value="'.esc_attr($time).'" title="Time"><input type="text" name="xp_ti_days['.$d.'][items]['.$i.'][activity]" value="'.esc_attr($activity).'" placeholder="Activity (e.g. Breakfast)"><input class="xp-ti-listing-id" type="hidden" name="xp_ti_days['.$d.'][items]['.$i.'][listing_id]" value="'.esc_attr($listing_id).'">';
        echo '<input class="xp-ti-listing-search" type="text" value="'.esc_attr($title).'" placeholder="Search existing listing…" autocomplete="off"><input type="number" min="0" name="xp_ti_days['.$d.'][items]['.$i.'][duration]" value="'.esc_attr($duration).'" placeholder="Minutes"><input type="text" name="xp_ti_days['.$d.'][items]['.$i.'][notes]" value="'.esc_attr($notes).'" placeholder="Notes"><button type="button" class="button-link-delete xp-ti-remove">Remove</button></div>'; }
    public static function booking($post) {
        $enabled=get_post_meta($post->ID,'_xp_ti_booking_enabled',true); $adult=get_post_meta($post->ID,'_xp_ti_adult_price',true); $child=get_post_meta($post->ID,'_xp_ti_child_price',true); $min=get_post_meta($post->ID,'_xp_ti_min_guests',true); $max=get_post_meta($post->ID,'_xp_ti_max_guests',true); $wc=get_post_meta($post->ID,'_xp_ti_wc_product_id',true);
        echo '<p><label><input type="checkbox" name="_xp_ti_booking_enabled" value="1" '.checked($enabled,'1',false).'> Enable itinerary booking</label></p>'; self::field('Adult price','_xp_ti_adult_price',$adult,'number',0); self::field('Child price','_xp_ti_child_price',$child,'number',0); self::field('Minimum guests','_xp_ti_min_guests',$min,'number',1); self::field('Maximum guests','_xp_ti_max_guests',$max,'number',1); echo '<p><strong>WooCommerce product:</strong> '.($wc?'#'.esc_html($wc):'Will be created/synced on save if WooCommerce is active.').'</p>'; }
    public static function save($post_id,$post) {
        if(!isset($_POST['xp_ti_nonce'])||!wp_verify_nonce($_POST['xp_ti_nonce'],'xp_ti_save')||defined('DOING_AUTOSAVE')||!current_user_can('edit_post',$post_id)) return;
        foreach(['_xp_ti_destination','_xp_ti_days','_xp_ti_style','_xp_ti_budget','_xp_ti_best_time','_xp_ti_adult_price','_xp_ti_child_price','_xp_ti_min_guests','_xp_ti_max_guests'] as $k){ if(isset($_POST[$k])) update_post_meta($post_id,$k,sanitize_text_field(wp_unslash($_POST[$k]))); }
        update_post_meta($post_id,'_xp_ti_booking_enabled',isset($_POST['_xp_ti_booking_enabled'])?'1':'0');
        $raw=$_POST['xp_ti_days']??[]; $clean=[];
        foreach((array)$raw as $d=>$day){ $clean[(int)$d]=['title'=>sanitize_text_field($day['title']??''),'description'=>sanitize_textarea_field($day['description']??''),'items'=>[]]; foreach((array)($day['items']??[]) as $item){ if(empty($item['listing_id'])) continue; $clean[(int)$d]['items'][]=['time'=>sanitize_text_field($item['time']??''),'activity'=>sanitize_text_field($item['activity']??''),'listing_id'=>absint($item['listing_id']),'duration'=>absint($item['duration']??0),'notes'=>sanitize_textarea_field($item['notes']??'')]; }}
        update_post_meta($post_id,'_xp_ti_days_data',$clean);
    }
    public static function search_listings(){ check_ajax_referer('xp_ti_admin','nonce'); if(!current_user_can('edit_posts')) wp_send_json_error(['message'=>'Forbidden'],403); $q=sanitize_text_field(wp_unslash($_GET['q']??'')); $args=['post_type'=>'listing','post_status'=>'publish','posts_per_page'=>20,'s'=>$q]; $posts=get_posts($args); $out=[]; foreach($posts as $p){$out[]=['id'=>$p->ID,'title'=>$p->post_title,'url'=>get_permalink($p),'image'=>get_the_post_thumbnail_url($p->ID,'thumbnail')?:''];} wp_send_json($out); }
}
