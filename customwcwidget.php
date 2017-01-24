<?php
/*
Plugin Name: 	customwcwidget.php
Plugin URI: 	http://joshfisher.io/plugins/customwcwidget.php
Description: 	This is a custom plugin build by Josh Fisher called customwcwidget.php.
Author: 		Josh Fisher
Version 		1.0
Author URI: 	http://joshfisher.io/
License:		GPL2

Copyright 2015 Josh Fisher

customwcwidget.php is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 2 of the License, or
any later version.
 
customwcwidget.php is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.
 
You should have received a copy of the GNU General Public License
along with customwcwidget.php. If not, see http://joshfisher.io.
*/



class Custom_WC_Widget extends WP_Widget {

	public function __construct() {
		$widget_ops = array( 
			'classname' => 'custom_wc_widget',
			'description' => 'Custom WooCommerce Category Widget',
		);
		parent::__construct( 'custom_wc_widget',__( 'Custom WC Widget', 'customwcwidgettextdomain' ), $widget_ops );
	}

	public function widget( $args, $instance ) {
		
		
		$title 	 = apply_filters( 'widget_title', $instance['title'] );
		echo $args['before_widget'];
		if ( ! empty( $title ) )
			echo $args['before_title'] . $title . $args['after_title'];
		
		$cate = get_queried_object();
		$cateID = $cate->term_id;
		$cateNM = $cate->name;
		
		$wcatTerms = get_terms('product_cat', array('hide_empty' => 1, 'orderby' => 'ASC', 'parent' => $cateID, )); 
        
        echo '<ul style="padding: 0px 10px;" class="product-categories"><li class="cat-item cat-parent">';
        echo '<a style="color: #1e1d1d ;font-size: 13px;font-weight: bold;line-height: 1.7;padding: 2px 0 2px 3px;margin: 0;font-family: "Roboto Condensed",sans-serif;" href="#">' . $cateNM . '</a>';
        echo '<ul style="padding-left: 15px;" class="children">';
        foreach($wcatTerms as $wcatTerm) : 
        $wthumbnail_id = get_woocommerce_term_meta( $wcatTerm->term_id, 'thumbnail_id', true );
        $wimage = wp_get_attachment_url( $wthumbnail_id );
	    ?>
	    <li class="cat-item"><a rel="nofollow" href="<?php echo get_term_link( $wcatTerm->slug, $wcatTerm->taxonomy ); ?>"><?php echo $wcatTerm->name; ?></a></li>
	    
	    <?php endforeach; 
		   echo '</ul></li></ul>';
		echo $args['after_widget'];
		
		
	}

	public function form( $instance ) {
		
		
		if ( isset( $instance[ 'title' ] ) ) {
			$title = $instance[ 'title' ];
		} else {
			$title = __( 'New title', 'customwcwidgettextdomain' );
		}
		
		
		?>
		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label> 
			<input 
				class="widefat" 
				id="<?php echo $this->get_field_id( 'title' ); ?>" 
				name="<?php echo $this->get_field_name( 'title' ); ?>" 
				type="text" 
				value="<?php echo esc_attr( $title ); ?>" />
		</p>
		<p>Nothing to see here, check the front end.</p>
		<?php 
			
			
	}

	public function update( $new_instance, $old_instance ) {
	
		$instance = array();
		
		$instance['title'] = (!empty($new_instance['title'])) ? strip_tags($new_instance['title']) : '';
		
		return $instance;
		
	}
}

add_action( 'widgets_init', function(){
	register_widget( 'Custom_WC_Widget' );
});

