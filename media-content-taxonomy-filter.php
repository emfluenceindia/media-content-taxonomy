<?php
/*
 * Plugin Name: Media Content Taxonomy
 * Description: Creates a custom taxonomy and a User Interface in admin for adding terms to it. Creates an additional filter dropdown in Media Library screen.
 * Author: Subrata Sarkar
 * Author URI: http://subratasarkar.com
 * Version: 0.0.2
 */

class Media_Content_Taxonomy {

    function __construct()
    {
        add_action( 'init', array( $this, 'mctf_register_media_content_taxonomy'), 0);
        add_action( 'admin_init', array( $this, 'init' ) );

        add_action('restrict_manage_posts', array( $this,  'mctf_media_list_view_filter_add' ), 0);
        add_action('pre_get_posts', array( $this, 'mctf_media_list_view_filter_action' ), 0);
    }

    function Media_Content_Taxonomy() {
        self::__construct();
    }

    /* Call function(s) when plugin initializes */
    function init() {
        $this->enqueue();
        $this->stylize();
    }

    function mctf_register_media_content_taxonomy() {
        $this->mctf_media_content_taxonomy();
    }

    /* Adds Media Content Taxonomy filter dropdown in listview */
    function mctf_media_list_view_filter_add() {
        $this->mctf_add_content_category_filter_dropdown();
    }

    /* Filter Action */
    function mctf_media_list_view_filter_action() {
        global $wp_query;
        $this->mctf_media_content_filter($wp_query);
    }

    function enqueue() {
        // Load 'terms' from our custom taxonomy into a JavaScript variable that media-taxonomy-filter.js has access to
        wp_enqueue_script('media-library-taxonomy-filter', plugins_url( "js/media-taxonomy-filter.js", __FILE__ ), array( 'media-editor', 'media-views' ), '20170906', true );

        /*
         * Creates a third dropdown for Media Content Category filter options.
         * media_content_category is the ID of the taxonomy used at the time of registration
        */

        wp_localize_script( 'media-library-taxonomy-filter', 'MediaLibraryTaxonomyFilterOptions', array(
            'terms'     => get_terms(
                'media_content_category', array( 'hide_empty' => false ) ),
        ) );
    }

    function stylize() {
        add_action( 'admin_footer', function(){
            ?>
            <style>
                .media-modal-content .media-frame select.attachment-filters {
                    max-width: -webkit-calc(33% - 12px);
                    max-width: calc(33% - 12px);
                }
            </style>
            <?php
        });
    }

    /* Create and Register taxonomy media_content_category */
    function mctf_media_content_taxonomy() {
        $labels = array (
            'name'              => __('Media Content Categories'),
            'singular_name'     => __('Media Content Category'),
            'search_items'      => __('Search Media Content Categories'),
            'all_items'         => __('All Media Content Categories'),
            'parent_item'       => __('Parent Media Content Category'),
            'parent_item_colon' => __('Parent Media Content Category:'),
            'edit_item'         => __('Edit Media Content Category'),
            'update_item'       => __('Update Media Content Category'),
            'add_new_item'      => __('Add New Media Content Category'),
            'new_item_name'     => __('New Media Content Category Name'),
            'menu_name'         => __('Content Category')
        );

        /* Registering taxonomy for "attachment" */
        register_taxonomy('media_content_category', array('attachment'), array (
            'labels'            => $labels,
            'hierarchical'      => true,
            'show_ui'           => true,
            'show_admin_column' => true,
            'query_var'         => true,
            'rewrite'           => array('slug', 'content_category')
        ));
    }

    function mctf_add_content_category_filter_dropdown()
    {
        $scr = get_current_screen();
        if ( $scr->base !== 'upload' ) {
            return;
        }

        $terms = get_terms('media_content_category', array('hide_empty' => false));

        if( !empty( $terms ) && ! is_wp_error( $terms ) ) {
            printf( '<select name="%1$s" class="postform">', esc_attr('mcfdd') );
            print( '<option value="">All Categories</option>' );
            foreach( $terms as $term ) {
                printf( '<option value="%1$s" %2$s>%3$s</option>', esc_attr( $term->term_id ), selected( intval( $_GET['mcfdd'] ), $term->term_id ), esc_html( $term->name ) );
            }
            print( '</select>' );
        }
    }

    function mctf_media_content_filter($query) {
        if( !is_admin() ) { // Only admin can access this
            return;
        }

        if( $query->is_main_query() ) { // Only main queries
            //Attachment query
            if('attachment' == $query->get('post_type') ) {
                //Attachment Tax Query
                if( isset( $_GET, $_GET['mcfdd'] ) && !empty( $_GET['mcfdd'] ) ) {
                    $query->set( 'tax_query', array( array (
                        'taxonomy' => 'media_content_category',
                        'filed' => 'term_id',
                        'terms' => array( intval( $_GET['mcfdd'] ) )
                    )  ) );
                }
            }
        }
    }
}

$GLOBALS['media_content_taxonomy'] = new Media_Content_Taxonomy();