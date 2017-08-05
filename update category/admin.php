<?php
add_action('admin_menu', 'page_create');
function page_create() {
    $page_title = 'Update Category Admin';
    $menu_title = 'Update categories now';
    $capability = 'edit_posts';
    $menu_slug = 'category_update';
    $function = 'updateCategory';
    $icon_url = '';
    $position = 24;

    add_options_page( $page_title, $menu_title, $capability, $menu_slug, $function, $icon_url, $position );
}
?>