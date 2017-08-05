<?php

/**
* Plugin Name: Update Category
* Plugin URI: http://www.mainwp.com
* Description: This plugin upgrade Category in WordPress
* Version: 1.0.0
* Author: Kitson Lai
* Author URI: http://www.mainwp.com
* License: GPL2
*/

/* Notes to Examiner

* I was given the test and on my first attempt was a crap. Wasn't in the right mind as I was down with flu and taken medicine and drowsy.
* Thank you for your time and here I attempt again after doing further reference to WP Codex and some research.
* There's still limitation for this plugin in terms of security and options to insert URL for JSON
* Somehow I am unable to work out on updating category ID or specifying category ID if insert.
* Looking forward to work with you and learn more codex usages if have the opportunity.

*/

//I want to make options page for the plugin or at least a page to response when 'Update Category Now' clicked.
add_action('admin_menu', 'page_create');
function page_create() {
    $page_title = 'Update Category Admin';
    $menu_title = 'Update Categories Now';
    $capability = 'edit_posts';
    $menu_slug = 'category_update';
    $function = 'updateCategory';
    $icon_url = '';
    $position = 24;

    add_options_page( $page_title, $menu_title, $capability, $menu_slug, $function, $icon_url, $position );
}

//This will help setup the cron interval and not the hourly as shown in Codex schedule event example
function cronInterval( $schedules ) {
 
    $schedules['every_thirty_minutes'] = array(
            'interval'  => 1800,
            'display'   => __( 'Every 30 Minutes', 'textdomain' )
    );
     
    return $schedules;
}
add_filter( 'cron_schedules', 'cronInterval' );

//Here I hook it for any cron manager to pickup and clean deactivation when not required
if ( ! wp_next_scheduled( 'my_task_hook' ) ) {
  wp_schedule_event( time(), 'every_thirty_minutes', 'my_task_hook' );
}

add_action( 'my_task_hook', 'updateCategory' );

register_deactivation_hook(__FILE__, 'my_deactivation');

function my_deactivation() {
	wp_clear_scheduled_hook('my_task_hook');
}

//Created this function based on the api server created
function updateCategory() {
    
    $request = wp_remote_get( 'http://localhost:3000/categories' );

    if( is_wp_error( $request ) ) {
        return false; // Will want to bail here if it's error
    }

    $body = wp_remote_retrieve_body( $request );
    $data = json_decode( $body );
    if( ! empty( $data ) ) {
        
            foreach( $data as $category ) {    
                $category_id = $category->id;        
                $my_cat = array('name' => $category->name,'slug' => $category->name, 'parent' => $category->parent_id);
                wp_update_term($category_id, 'category', $my_cat);

                /*
                *This test require update on category and I think it's okay to create categories if it's not available WordPress
                *I would like to use the followings

                $my_cat = array('cat_ID' => $category->id,'cat_name' => $category->name, 'category_description' => '', 'category_nicename' => $category->name, 'category_parent' => $category->parent_id, 'taxonomy' => 'category');
                $my_cat = array('cat_ID' => 0, 'cat_name' => $category->name, 'category_description' => '', 'category_nicename' => $category->name, 'category_parent' => $category->parent_id, 'taxonomy' => 'category');
                $my_cat_id = wp_insert_category($my_cat);
                */
                
            }
    

    }
    //I want to show some text and response when user click on 'Update Category Now' button on Settings
    include 'form.php';
    
}

?>