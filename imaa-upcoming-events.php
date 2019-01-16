<?php

/**
 * Plugin Name: Upcomming Events
 * Description: Upcomming Events for The Event Calendar
 * Author: Nick Bosswell
 * Version: 1.0
 * Text Domain: imaa-upcoming-events
 */

/**
 * Add image sizes for sliders
 */
add_image_size( 'upcoming-events', 400, 265, true );

/**
 * Load CSS and JavaScript files
 */
function imma_upcoming_events_css_js() {
    wp_enqueue_style( 'imma_upcoming_events_style_css', plugins_url( 'css/', __FILE__ ).'style.css' ); 
}
add_action( 'wp_enqueue_scripts', 'imma_upcoming_events_css_js' );

/**
 * Custom Loop shortcode [imaa_upcoming_events]
 */
function imaa_upcoming_events_shortcode($atts){

    // define shortcode variable
    extract(shortcode_atts(array(
        'post_type' => array(TribeEvents::POSTTYPE),
        'orderby' => 'date',
        'posts_per_page' => 4,
        'tax_terms' => 'certificate-programs'
    ), $atts));

    // define parameters
    $args = array(
        'post_type' => $post_type,
        'orderby' => $orderby,
        'posts_per_page' => $posts_per_page,
        'tax_query' => array(
            array(
                'taxonomy' => 'tribe_events_cat',
                'field' => 'slug',
                'terms' => $tax_terms,
            ),
        ),
    );

    // query the posts
    $posts = new WP_Query($args);

    // begin output variable
    if($posts->have_posts()){
        $output .= '<div>';
            $output .= '<div class="upcoming-events-container">';
            global $post;
            while($posts->have_posts()): $posts->the_post();
                $output .= '<div>';
                    $output .= '<div class="image-container">';
                        $output .= get_the_post_thumbnail( $post->ID, 'upcoming-events' );
                        $terms = get_the_terms( get_the_ID(), 'post_tag' );
                        $posttags = get_the_tags();
                        if( ! empty( $terms ) ){
                            foreach( $terms as $term ){
                                $termID = $term->term_id;
                                $termMeta = get_term_meta( $termID );
                                $tagColor = $termMeta['et_color'][0];
                                $output .= '<span class="event-tag" style="background-color: ' . $tagColor . '">' . $term->name . '</span>';
                            }
                        }
                        $output .= '<div class="after"></div>';
                        $output .= '<div class="event-title-date"><h3 class="event-title">' . get_the_title() . '</h3><span>' . tribe_get_start_date( $post, false, 'F j, Y') .'</span></div>';
                        $output .= '<div class="event-cost">' . tribe_get_cost(null, true) .'</div>';
                        $output .= '<a href="' . get_permalink() . '" class="read-more"><i class="fa fa-chevron-right"></i></a>';
                    $output .= '</div>';
                $output .= '</div>';
            endwhile;
            $output .= '</div>';
        $output .= '</div>';
    }
    else{
        $output .= '<div class="alert alert-danger">' . esc_html__('Sorry, no posts matched your criteria.', 'imaa-past-trainings') . '</div>';
    }

    // reset post data
    wp_reset_postdata();

    // return output
    return $output;

}

// register shortcode function
add_shortcode('imaa_upcoming_events', 'imaa_upcoming_events_shortcode');