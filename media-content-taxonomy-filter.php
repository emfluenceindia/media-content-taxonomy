<?php
/*
 * Plugin Name: Media Content Taxonomy
 * Description: Creates a custom taxonomy and a User Interface in admin for adding terms to it. Creates an additional filter dropdown in Media Library screen.
 * Author: Subrata Sarkar
 * Author URI: http://subratasarkar.com
 * Version: 0.0.1
 */

/*
 * File name: media-content-taxonomy-register.php
 * Purpose: Creates and registers custom taxonomy "media_content_category"
 */
require_once('media-content-taxonomy-register.php');

class Media_Content_Taxonomy {

    function __construct()
    {
        add_action( 'admin_init', array( $this, 'init' ) );
    }

    function Media_Content_Taxonomy() {
        self::__construct();
    }

    /* Call function(s) when plugin initializes */
    function init() {
        $this->enqueue();
        $this->stylize();
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
}

$GLOBALS['media_content_taxonomy'] = new Media_Content_Taxonomy();