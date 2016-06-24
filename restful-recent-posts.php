<?php
/*
 Plugin Name: RESTful Recent Posts
 Description: Display recent posts using the REST API
 Author: thingone
 Version: 0.1
 */


class RESTful_Recent_Posts extends WP_Widget {

	/**
	 * Sets up the widgets name etc
	 */
	public function __construct() {
		$widget_ops = array(
			'classname' => 'Restful_Recent_Posts',
			'description' => 'Display recent posts using the REST API',
		);
		parent::__construct( 'RESTful_Recent_Posts', 'RESTful Recent Posts', $widget_ops );
	}

	/**
	 * Outputs the content of the widget
	 *
	 * @param array $args
	 * @param array $instance
	 */
	public function widget( $args, $instance ) {
		// outputs the content of the widget

		$json = wp_remote_get( 'http://wpaustin.com/wp-json/wp/v2/posts/?filter[posts_per_page]=5' );

		if ( ! $json ) {
			return;
		}

		$body = $json['body'];

		$results = json_decode( $body, true );

		echo '<h3>WP Austin\'s Recent Posts';

		echo '<ul>';

		foreach ( $results as $result ) {
			echo '<li>';

			if ( $result['featured_media'] ) {
				$imgurl = "http://wpaustin.com/wp-json/wp/v2/media/{$result['featured_media']}";
				$response = wp_remote_get( $imgurl );

				if ( $response ) {
					$image_object = json_decode( $response['body'] );

					echo '<img src="' . $image_object->guid->rendered .  '">';
				}
			}

			//wrap url around title create href
			echo '<a href="' . $result['link'] . '">' . $result['title']['rendered'] . '</a>';

			echo $result['excerpt']['rendered'];

			echo "</li>";
		}

		echo "</ul>";
	}
}

add_action( 'widgets_init', function(){
	register_widget( 'RESTful_Recent_Posts' );
});
