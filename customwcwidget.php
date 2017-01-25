<?php
/*
Plugin Name: 	Custom Woocommerce Category Widget
Plugin URI: 	http://joshfisher.io/plugins/customwcwidget
Description: 	This is a custom plugin build by Josh Fisher called customwcwidget.php.
Author: 		Josh Fisher
Version 		1.04
Author URI: 	http://joshfisher.io/
License:		GPL2

Copyright 2017 Josh Fisher

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
		
		$wcatTerms = get_terms('product_cat', array(
						'hide_empty' => 1, 
						'orderby' => 'ASC', 
						'orderby' => 'name',
						'parent' => $cateID, 
		)); 
		
        
        $catsToShow = [$wcatTerms];
        $catsOpened = ['']; 
        $cId = $cateID;
        $parentId = $cate->parent;
        while($cId != '') {
            $siblings = get_terms('product_cat', array(
                            'hide_empty' => 1, 
                            'orderby' => 'ASC', 
                            'orderby' => 'name',
                            'parent' => $parentId, 
            )); 
            $catsToShow[]=$siblings;
            $catsOpened[]=$cId;
            if ($parentId == 0) {
                //hide top categories except the case if choosen top category does not have children
                if (count($catsToShow) != 2 || count($wcatTerms) != 0) {
                    $catsToShow = $this->removeTopCategories($catsToShow, $catsOpened);
                }
                break;
            }
            $parentItem = get_term($parentId);
            if (count($parentItem) == 0) {
                $cId = '';
                break;
            } else {
                $parentId = $parentItem->parent;
                $cId = $parentItem->term_id;
            }
        }
          $this->showCategories($catsToShow, $catsOpened, $cateID); 
		
		echo $args['after_widget'];
	}
    /**
     * removes top categories except direct parent
     *
     * @param array $catsToShow
     * @param array $catsOpened
     *
     * @return array 
     */
    private function removeTopCategories($catsToShow, $catsOpened) {
        $top = array_pop($catsToShow);
        $opened = array_pop($catsOpened);
        $res = null;
        foreach($top as $e) {
            if ($e->term_id == $opened) {
                $res = $e;
                break;
            }
        }
        $catsToShow[]=[$res];
        return $catsToShow;
    }
    private function showCategories($catsToShow, $catsOpened, $realCateID) {
        $wcatTerms = array_pop($catsToShow);
        $openCateID = array_pop($catsOpened);

        echo '<ul style="padding: 0px 10px;" class="product-categories"><li class="cat-item cat-parent">';
        
        foreach($wcatTerms as $wcatTerm) : 
        	
        	
        
       		?>
		    
		    <li style="color: #1e1d1d;" class="cat-item">
                <a  style="color: #1e1d1d;<?php if (!is_null($realCateID) && $realCateID == $wcatTerm->term_id) {echo 'font-weight:bold;';}?>" href="<?php echo get_term_link( $wcatTerm->slug, $wcatTerm->taxonomy ); ?>"><?php echo $wcatTerm->name; ?></a>

            <?php
            if ($wcatTerm->term_id == $openCateID && count($catsToShow) > 0) {
                $this->showCategories($catsToShow, $catsOpened, $realCateID);
            }	
            ?>

		    </li>
		    
	    
			<?php 
				
				    
		endforeach; 
		   
		   echo '</ul>';
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
