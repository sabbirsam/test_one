<?php
/**
 * Plugin Name: woocommerce-plugin-s3
 * Plugin URI: http://techsambd.com
 * Author: Md Sabbir Ahmed
 * Author URI: http://techsambd.com
 * Description: This Plugin can remove add to cart on shop and single page also the price so the shop become a catalog
 * License: GPL2 or later
 * Version: 1.00
 * Text-domain: sam_woocommerce_s3
 *
 */

define("ASSETS_DIR",plugin_dir_url(__FILE__)."/assets");
define("ASSETS_ADMIN_DIR",plugin_dir_url(__FILE__)."/assets/admin");
define("ASSETS_PUBLIC_DIR",plugin_dir_url(__FILE__)."/assets/public");




//1-Declare Class and initate
function sam_woocommerce_bootstrap() {
    load_plugin_textdomain( "sam_woocommerce_s3", false, dirname( __FILE__ ) . "/languages" );
}

add_action( 'plugins_loaded', 'sam_woocommerce_bootstrap' );

function sam_woocommerce_s3_post_columns( $columns ) {
    print_r( $columns );
    unset( $columns['tags'] );
    unset( $columns['comments'] );
    /*unset($columns['author']);
    unset($columns['date']);
    $columns['author']="Author";
    $columns['date']="Date";*/
    $columns['id']        = __( 'Post ID', 'sam_woocommerce_s3' );
    $columns['thumbnail'] = __( 'Thumbnail', 'sam_woocommerce_s3' );
    $columns['wordcount'] = __( 'Word Count', 'sam_woocommerce_s3' );

    return $columns;
}


add_filter( 'manage_pages_columns', 'sam_woocommerce_s3_post_columns' );

function sam_woocommerce_s3_post_column_data( $column, $post_id ) {
    if ( 'id' == $column ) {
        echo $post_id;
    } elseif ( 'thumbnail' == $column ) {
        $thumbnail = get_the_post_thumbnail( $post_id, array( 100, 100 ) );
        echo $thumbnail;
    } elseif ( 'wordcount' == $column ) {
        /*$_post = get_post($post_id);
        $content = $_post->post_content;
        $wordn = str_word_count(strip_tags($content));*/
        $wordn = get_post_meta( $post_id, 'wordn', true );
        echo $wordn;
    }
}

add_action( 'manage_posts_custom_column', 'sam_woocommerce_s3_post_column_data', 10, 2 );


function sam_woocommerce_s3_sortable_column( $columns ) {
    $columns['wordcount'] = 'wordn';

    return $columns;
}

add_filter( 'manage_edit-post_sortable_columns', 'sam_woocommerce_s3_sortable_column' );

/*function sam_woocommerce_s3_set_word_count() {
	$_posts = get_posts( array(
		'posts_per_page' => - 1,
		'post_type'      => 'post',
		'post_status'    => 'any'
	) );

	foreach ( $_posts as $p ) {
		$content = $p->post_content;
		$wordn   = str_word_count( strip_tags( $content ) );
		update_post_meta( $p->ID, 'wordn', $wordn );
	}
}

add_action( 'init', 'sam_woocommerce_s3_set_word_count' );*/

function sam_woocommerce_s3_sort_column_data( $wpquery ) {
    if ( ! is_admin() ) {
        return;
    }

    $orderby = $wpquery->get( 'orderby' );
    if ( 'wordn' == $orderby ) {
        $wpquery->set( 'meta_key', 'wordn' );
        $wpquery->set( 'orderby', 'meta_value_num' );
    }
}

add_action( 'pre_get_posts', 'sam_woocommerce_s3_sort_column_data' );

function sam_woocommerce_s3_update_wordcount_on_post_save($post_id){
    $p = get_post($post_id);
    $content = $p->post_content;
    $wordn   = str_word_count( strip_tags( $content ) );
    update_post_meta( $p->ID, 'wordn', $wordn );
}
add_action('save_post','sam_woocommerce_s3_update_wordcount_on_post_save');