<?php
/*
 * Creates a Custom Content Type
 * Content Type Name: Media Content Category
 * Content Type ID: media_content_category
 * Creates a UI in the admin to add taxonomies just like Categories, Tags or other Custom taxonomies
 * Gets tied with attachments
 */

add_action('init', 'mctf_media_content_taxonomy', 0);

/* Creating Custom Taxonomy */
function mctf_media_content_taxonomy() {
    $labels = array (
        'name' => _x('Media Content Categories', 'Media Content Categories'),
        'singular_name' => _x('Media Content Category', 'Media Content Category'),
        'search_items' => __('Search Media Content Categories'),
        'all_items' => __('All Media Content Categories'),
        'parent_item' => __('Parent Media Content Category'),
        'parent_item_colon' => __('Parent Media Content Category:'),
        'edit_item' => __('Edit Media Content Category'),
        'update_item' => __('Update Media Content Category'),
        'add_new_item' => __('Add New Media Content Category'),
        'new_item_name' => __('New Media Content Category Name'),
        'menu_name' => __('Content Category')
    );

    /* Registering taxonomy for "attachment" */
    register_taxonomy('media_content_category', array('attachment'), array (
        'hierarchical' => true,
        'labels' => $labels,
        'show_ui' => true,
        'show_admin_column' => true,
        'query_var' => true,
        'rewrite' => array('slug', 'content_category')
    ));
}