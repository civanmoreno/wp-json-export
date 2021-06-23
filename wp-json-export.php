<?php
/**
 * Plugin Name: Generate json service for migrate
 * Plugin URI:  "
 * Description: Plugin for wordpress
 * Version: 1.0
 * Author: Iván Moreno
 * Author URI:  #
 */

// wp-json/migrate/v1/post/{{quantity}}/{{page}}
// Create rest url to migrate.
function get_post_by_page( $data ) {
    
    $args = array(
        'posts_per_page' => $data['per_page'],
        'post_type' => 'post',
        'paged' => $data['paged'],
        'orderby' => 'date',
        'order'   => 'DESC',
        'post_status' => 'publish',
        //'date_query' => array(
        //  array(
        //      'year' => $data['year'],
        //      'month' => $data['month'],
        //  )
        //)
    );
    $query = new WP_Query( $args ); 
    if( empty($query->posts) ){
        return "no found";
    }else{
        $posts = $query->posts;
    }
    
    foreach ($posts as $key => $value) {
        
        $categories = wp_get_post_categories($value->ID);
        unset($get_cat_name);
        foreach ($categories as $category) {
            $get_cat_name[] = get_cat_name($category);
        }
        
        $tags = wp_get_post_tags($value->ID);
        unset($tags_names);
        foreach ($tags as $tag) {
            $tags_names[] = $tag->name;
        }
        
        $vars[$key] = [
            'id' => $value->ID,
            'title' => $value->post_title,
            'content' => $value->post_content,
            'post_date' => $value->post_date,
            'post_modified' => $value->post_modified,
            'post_status' => $value->post_status,
            'image' => get_the_post_thumbnail_url($value->ID, 'full'),
            'link' => get_permalink($value->ID),
            'categories' => isset($get_cat_name) ? $get_cat_name: NULL,
            'tags' => isset($tags_names) ? $tags_names: NULL,
        ];
    }
    return $vars;
}

add_action( 'rest_api_init', function () {
    //register_rest_route( 'migrate/v1', '/post/(?P<year>\d+)/(?P<month>\d+)/(?P<per_page>\d+)/(?P<paged>\d+)/', array(
    register_rest_route( 'migrate/v1', '/post/(?P<per_page>\d+)/(?P<paged>\d+)/', array(
        'methods' => 'GET',
        'callback' => 'get_post_by_page',
    ) );
} );
