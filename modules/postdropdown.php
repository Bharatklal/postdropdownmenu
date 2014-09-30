<?php
add_action( 'wpcf7_init', 'wpcf7_add_shortcode_postdropdown' );

function wpcf7_add_shortcode_postdropdown() {
	wpcf7_add_shortcode( array( 'postdropdown', 'postdropdown*' ),
		'wpcf7_postdropdown_shortcode_handler', true );
}

function wpcf7_postdropdown_shortcode_handler( $tag ) {
	$tag = new WPCF7_Shortcode( $tag );
	
	$catagoryId = $tag->name;
	$cid=explode('-',$catagoryId);

		global $post;
		$args = array('numberposts' => 0, 'category' => 5 );
		$myposts = get_posts('cat='.$cid[1]);

		$output = "<select name=".$tag->name." onchange='document.getElementById(\'".$tag->name."'\).value=this.value;'><option></option>";
		foreach ( $myposts as $post ) : setup_postdata($post);
			$title = get_the_title();
			$output .= "<option value='$title'> $title </option>";
			endforeach;
		$output .= "</select>";
		
		return $output;
	
	}
	
/* Validation filter */

add_filter( 'wpcf7_validate_postdropdown', 'wpcf7_postdropdown_validation_filter', 10, 2 );
add_filter( 'wpcf7_validate_postdropdown*', 'wpcf7_postdropdown_validation_filter', 10, 2 );

function wpcf7_postdropdown_validation_filter( $result, $tag ) {
	$tag = new WPCF7_Shortcode( $tag );
		
		
	$name = $tag->name;

	if ( isset( $_POST[$name] ) && is_array( $_POST[$name] ) ) {
		foreach ( $_POST[$name] as $key => $value ) {
			if ( '' === $value )
				unset( $_POST[$name][$key] );
				
		}
	}

	if ( $tag->is_required() ) {
		if ( ! isset( $_POST[$name])|| empty( $_POST[$name] ) && '0' !== $_POST[$name] ) {
			$result['valid'] = false;
			$result['reason'][$name] = wpcf7_get_message( 'invalid_required' );
			
		}
		
	}

	if ( isset( $result['reason'][$name] ) && $id = $tag->get_id_option() ) {
		$result['idref'][$name] = $id;
	}

	return $result;
}


/* Tag generator */


add_action( 'admin_init', 'wpcf7_add_tag_generator_postdropdown', 25 );

function wpcf7_add_tag_generator_postdropdown() {
	if ( ! function_exists( 'wpcf7_add_tag_generator' ) )
		return;

	wpcf7_add_tag_generator( 'postdropdown', __( 'Post Drop Down', 'contact-form-7' ),
		'wpcf7-tg-pane-postdropdown', 'wpcf7_tg_pane_postdropdown' );
}

function wpcf7_tg_pane_postdropdown( $contact_form ) {
?>
<div id="wpcf7-tg-pane-postdropdown" class="hidden">
<form action="">
<table>
<tr><td><input type="checkbox" name="required" />&nbsp;<?php echo esc_html( __( 'Required field?', 'contact-form-7' ) ); ?></td></tr>
<tr><td><?php echo esc_html( __( 'Name', 'contact-form-7' ) ); ?><br /><input type="text" name="name" class="tg-name oneline" /></td><td></td></tr>
</table>

<div class="tg-tag"><?php echo esc_html( __( "Copy this code and paste it into the form left.", 'contact-form-7' ) ); ?><br /><input type="text" name="postdropdown" class="tag wp-ui-text-highlight code" readonly="readonly" onfocus="this.postpropdown()" /></div>

<div class="tg-mail-tag"><?php echo esc_html( __( "And, put this code into the Mail fields below.", 'contact-form-7' ) ); ?><br /><input type="text" class="mail-tag wp-ui-text-highlight code" readonly="readonly" onfocus="this.postdropdown()" /></div>
</form>
</div>
<?php
}
?>