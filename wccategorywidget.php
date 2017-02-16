<?php
/*
Plugin Name: 	Woocommerce Category Widget
Plugin URI: 	http://joshfisher.io/plugins/wccategorywidget
Description: 	This is a custom plugin build by Josh Fisher called wccategorywidget.php.
Author: 		Josh Fisher
Version 		1.06
Author URI: 	http://joshfisher.io/
License:		GPL2

Copyright 2017 Josh Fisher

wccategorywidget.php is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 2 of the License, or
any later version.
 
wccategorywidget.php is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.
 
You should have received a copy of the GNU General Public License
along with wccategorywidget.php. If not, see http://joshfisher.io.
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
		
?>
<style>
#customcwidget{text-align:left;padding:0 5px}
#customcwidget .ccw-plus{width:15px;height:15px;float:right;background-position:left center;background-repeat:no-repeat;background-image:url(data:image/gif;base64,R0lGODlhMgAQAJECAGZmZv///////wAAACH5BAEAAAIALAAAAAAyABAAAAJOjI8Zwu3flJzUwPuq1riLrQGA5GFgJZIldFKpsrKt9CZxNhvizo/WHdH1eIoh8Qf8CI2+SQ2RZOSKTWh0mngeokqsUJX0brhizrVMIbcKADs=);cursor:pointer}
#customcwidget .children{margin-left:5px;padding-left:10px;display:none}
#customcwidget li.current-cat-ancestor >a,#customcwidget li.current-cat-parent >a,#customcwidget li.current-cat >a{font-weight:700}
#customcwidget li.current-cat-ancestor > .ccw-plus,#customcwidget .current-cat-parent > .ccw-plus,#customcwidget .current-cat > .ccw-plus{background-position:right center}
#customcwidget li.current-cat-ancestor > ul.children,#customcwidget .current-cat-parent > ul.children,#customcwidget .current-cat > ul.children{display:block}
#customcwidget.ccw-ccp > li.current-cat-ancestor,#customcwidget.ccw-ccp > li.current-cat{display:block!important}
#customcwidget.ccw-ccp > li{display:none}
</style>
<script>
        //<![CDATA[
        function ccwExpand(e) {
            ge = e;
            var eparent = e.parentElement;
            if (eparent.getElementsByTagName("ul")[0].style.display != "block") {
                    eparent.getElementsByTagName("ul")[0].style.display="block";
                    e.style.backgroundPosition="right center";
            } else {
                    eparent.getElementsByTagName("ul")[0].style.display="none";
                    e.style.backgroundPosition="left center";
            }
        }
        //]]
</script>
<?php
        //get categories tree
        $cats = wp_list_categories([
            'taxonomy'=>'product_cat',
            'current_category'=>$cateID,
            'title_li'=>'',
            'echo'=>false,
            'use_desc_for_title'=>false,
        ]);
        //embed open/close control
        $cats = preg_replace('/<ul[^>]+children[^>]*>/i', '<span onclick="ccwExpand(this);" class="ccw-plus"></span>$0', $cats);
        //add rel="nofollow"
        $cats = preg_replace('/(<a [^>]+)>/i', '$1 rel="">', $cats);
        if ($cate == null ) {
            //no current category
            $topHtml = '<ul id="customcwidget">';
        } else {
            $topHtml = '<ul id="customcwidget" class="ccw-ccp">';
        }
        echo $topHtml;
        echo $cats;
        echo '</ul>';
		
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