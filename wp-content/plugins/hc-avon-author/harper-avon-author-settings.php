<?php
if (!function_exists('is_admin')) {
    header('Status: 403 Forbidden');
    header('HTTP/1.1 403 Forbidden');
    exit();
}
$current_dir = dirname(__FILE__);
if (!class_exists("AuthorDetail")) :
require_once(__DIR__.'/AuthorDetail.php');
endif;

if (!class_exists("AuthName2GID")) :
require_once('AuthName2GID.php');
endif;


if (!class_exists("Harper_AVON_AUTHOR_API_Settings")) :

/*
	Create example settings page for our plugin.

	- We show how to render our own controls using HTML.
	- We show how to get WordPress to render controls for us using do_settings_sections'

	WordPress Settings API tutorials
	http://codex.wordpress.org/Settings_API
	http://ottopress.com/2009/wordpress-settings-api-tutorial/
*/
class Harper_AVON_AUTHOR_API_Settings {

	public static $default_settings =
		array(
			  	'example_text' => 'Test text',
			  	'example_checkbox1' => 'apples',
				'example_checkbox2' => 'oranges',
			  	'mbox_example_text' => 'Shiba example plugin by ShibaShake',
			  	'mbox_example_checkbox1' => 'grapes',
				'mbox_example_checkbox2' => 'lemons'
				);
	var $pagehook, $page_id, $settings_field, $options;


	function __construct() {
		$this->page_id = 'avon_authors';
		// This is the get_options slug used in the database to store our plugin option values.
		$this->settings_field = 'harper_avon_authors_options';
		$this->options = get_option( $this->settings_field );

		add_action('admin_init', array($this,'admin_init'), 20 );
		add_action( 'admin_menu', array($this, 'admin_menu'), 20);
	}

	function admin_init() {
		register_setting( $this->settings_field, $this->settings_field, array($this, 'sanitize_theme_options') );
		add_option( $this->settings_field, Harper_AVON_AUTHOR_API_Settings::$default_settings );


		/*
			This is needed if we want WordPress to render our settings interface
			for us using -
			do_settings_sections

			It sets up different sections and the fields within each section.
		*/
		add_settings_section('shiba_main', '',
			array($this, 'main_section_text'), 'example_settings_page');

		add_settings_field('example_text', 'Example Text',
			array($this, 'render_example_text'), 'example_settings_page', 'shiba_main');

		add_settings_field('example_checkbox1', 'Check to publish',
			array($this, 'render_example_checkbox'), 'example_settings_page', 'shiba_main',
			array('id' => 'example_checkbox1', 'value' => 'publish', 'text' => 'Publish') );
// 		add_settings_field('example_checkbox2', '',
// 			array($this, 'render_example_checkbox'), 'example_settings_page', 'shiba_main',
// 			array('id' => 'example_checkbox2', 'value' => 'publish', 'text' => 'Oranges') );
	}

	function admin_menu() {
		if ( ! current_user_can('update_plugins') )
			return;

		// Add a new submenu to the standard Settings panel
		$this->pagehook = $page =  add_options_page(
			__('Avon Authors', 'avon_authors'), __('Avon Authors', 'avon_authors'),
			'administrator', $this->page_id, array($this,'render') );

		// Executed on-load. Add all metaboxes.
		add_action( 'load-' . $this->pagehook, array( $this, 'metaboxes' ) );

		// Include js, css, or header *only* for our settings page
		add_action("admin_print_scripts-$page", array($this, 'js_includes'));
//		add_action("admin_print_styles-$page", array($this, 'css_includes'));
		add_action("admin_head-$page", array($this, 'admin_head') );
	}

	function admin_head() { ?>
		<style>
		.settings_page_shiba_example label { display:inline-block; width: 150px; }
		</style>

	<?php }


	function js_includes() {
		// Needed to allow metabox layout and close functionality.
		wp_enqueue_script( 'postbox' );
	}


	/*
		Sanitize our plugin settings array as needed.
	*/
	function sanitize_theme_options($options) {
		$options['example_text'] = stripcslashes($options['example_text']);
		return $options;
	}


	/*
		Settings access functions.

	*/
	protected function get_field_name( $name ) {

		return sprintf( '%s[%s]', $this->settings_field, $name );

	}

	protected function get_field_id( $id ) {

		return sprintf( '%s[%s]', $this->settings_field, $id );

	}

	protected function get_field_value( $key ) {

		return $this->options[$key];

	}


	/*
		Render settings page.

	*/

	function render() {
		global $wp_meta_boxes;

		$title = __('Avon Authors', 'avon_authors');
		?>
		<div class="wrap">
			<h2><?php echo esc_html( $title ); ?></h2>

			<form method="post" action="options.php">
				<p>
				<input type="submit" class="button button-primary" name="save_options" value="<?php esc_attr_e('Save Options'); ?>" />
				</p>

                <div class="metabox-holder">
                    <div class="postbox-container" style="width: 99%;">
                    <?php
						// Render metaboxes
                        settings_fields($this->settings_field);
                        do_meta_boxes( $this->pagehook, 'main', null );
                      	if ( isset( $wp_meta_boxes[$this->pagehook]['column2'] ) )
 							do_meta_boxes( $this->pagehook, 'column2', null );
                    ?>
                    </div>
                </div>

				<p>
				<input type="submit" class="button button-primary" name="save_options" value="<?php esc_attr_e('Save Options'); ?>" />
				</p>
			</form>
		</div>

        <!-- Needed to allow metabox layout and close functionality. -->
		<script type="text/javascript">
			//<![CDATA[
			jQuery(document).ready( function ($) {
				// close postboxes that should be closed
				$('.if-js-closed').removeClass('if-js-closed').addClass('closed');
				// postboxes setup
				postboxes.add_postbox_toggles('<?php echo $this->pagehook; ?>');
			});
			//]]>
		</script>
	<?php }


	function metaboxes() {

		// Example metabox showing plugin version and release date.
		// Also includes and example input text box, rendered in HTML in the info_box function
		add_meta_box( 'shiba-example-version', __( 'Information', 'avon_authors' ), array( $this, 'info_box' ), $this->pagehook, 'main', 'high' );

		// Example metabox containing two example checkbox controls.
		// Also includes and example input text box, rendered in HTML in the condition_box function
		add_meta_box( 'shiba-example-conditions', __( 'Publish Conditions', 'avon_authors' ), array( $this, 'condition_box' ), $this->pagehook, 'main' );

		// Example metabox containing an example text box & two example checkbox controls.
		// Example settings rendered by WordPress using the do_settings_sections function.
// 		add_meta_box( 	'shiba-example-all',
// 						__( 'Rendered by WordPress using do_settings_sections', 'avon_authors' ),
// 						array( $this, 'do_settings_box' ), $this->pagehook, 'main' );

	}

	function info_box() {

		?>
		<p><strong><?php _e( 'Version:', 'avon_authors' ); ?></strong> <?php echo AVON_AUTHOR_API_VERSION; ?> <?php echo '&middot;'; ?> <strong><?php _e( 'Released:', 'avon_authors' ); ?></strong> <?php echo AVON_AUTHOR_API_RELEASE_DATE; ?></p>

		<p>
 			<label for="<?php echo $this->get_field_id( 'mbox_example_text' ); ?>"><?php _e( 'Author GID', 'avon_authors' ); ?></label>
			<input type="text" name="<?php echo $this->get_field_name( 'mbox_example_text' ); ?>" id="<?php echo $this->get_field_id( 'mbox_example_text' ); ?>" value="<?php echo esc_attr( $this->get_field_value( 'mbox_example_text' ) ); ?>" style="width:25%;" />
		</p>

		<?php

	}

	function condition_box() {
	?>
		<p>
			<input type="checkbox" name="<?php echo $this->get_field_name( 'mbox_example_checkbox1' ); ?>" id="<?php echo $this->get_field_id( 'mbox_example_checkbox1' ); ?>" value="grapes" <?php echo isset($this->options['mbox_example_checkbox1']) ? 'checked' : '';?> />
			<label for="<?php echo $this->get_field_id( 'mbox_example_checkbox1' ); ?>"><?php _e( 'Check to publish', 'avon_authors' ); ?></label>
            <br/>
		</p>
	<?php }


	function do_settings_box() {
		do_settings_sections('example_settings_page');
	}

	/*
		WordPress settings rendering functions

		ONLY NEEDED if we are using wordpress to render our controls (do_settings_sections)
	*/


	function main_section_text() {
		echo '<p>Some example inputs.</p>';
	}

	function render_example_text() {
		?>
        <input id="example_text" style="width:50%;"  type="text" name="<?php echo $this->get_field_name( 'example_text' ); ?>" value="<?php echo esc_attr( $this->get_field_value( 'example_text' ) ); ?>" />
		<?php
	}

	function render_example_checkbox($args) {
		$id = 'shiba_example_options['.$args['id'].']';
		?>
  		<input name="<?php echo $id;?>" type="checkbox" value="<?php echo $args['value'];?>" <?php echo isset($this->options[$args['id']]) ? 'checked' : '';?> /> <?php echo " {$args['text']}"; ?> <br/>
		<?php
	}

	function author_update_missing_GID()
	{
	    global $wpdb;
	    global $post;
	    $counter = 0;

	    $pageposts = $wpdb->get_results( "SELECT $wpdb->posts.*
	    FROM $wpdb->posts
	    WHERE wp_posts.post_status = 'publish'
	    AND wp_posts.post_type = 'book-author'");

// 	    AND wp_posts.post_date < NOW()
// 	    AND NOT EXISTS (SELECT post_id FROM $wpdb->postmeta WHERE wp_posts.ID = wp_postmeta.post_id
// 	    AND wp_postmeta.meta_key = 'AUTHOR_GID')");

//	    $pageposts = get_posts( array('post_type' => 'book-author', 'numberposts' => -1 ) );
			foreach ( $pageposts as $my_post ):
			// New API call to get author GID fron author name is here.
    			$encauth = urlencode($my_post->post_title);
    			$objGid= new AuthName2GID($encauth);
//    			$newid = 'a:1:{i:0;s:' . strlen($my_post->ID) . ':"' . $my_post->ID . '";}';

				 update_post_meta($my_post->ID, "Author_GID", $objGid->author_gid);
                 $newid = $this->getbookauthor($objDetail->author);
                 update_post_meta($post_id, "book_author", $newid);
				 update_post_meta($my_post->ID, "profile_image", $objGid->author_image2);
				 $counter++;
				 unset($objGid);
 		endforeach;
		echo "<span style=color:red>Metadata Updated for $counter books.</span><BR>";
	}
	function hc_avon_author_update()
	{
		$options = get_option('harper_avon_authors_options');
	    $authid = $this->get_field_value( 'mbox_example_text' );
		$locale = "0";
		$objDetail= new AuthorDetail($authid);
		$counter = $this->sendData($objDetail);
		echo "<BR><span style=color:blue>" . $counter . " records have been added.</span><BR>";
		if($counter == 0)
		{
			/*******************************************************************************
			something is wrong, probably in settings values so lets display to help resolve.
			*******************************************************************************/
			echo "<span style=color:red>It looks like a configuration issue has occurred.</span><BR>";
			echo "The Author search value used from settings is as follows;<BR>";
			echo "Author ID " . $options['search_author'] . " <BR>";
			$seoonly = isset($options['seo_checkbox']) ? TRUE : FALSE;
// 			if($seoonly == TRUE) {
// 				echo "SEO copy is required for addition <i>(maybe none available, only reviews ?)</i>.<BR>";
// 			}
// 			else
// 			{
// 				echo "SEO copy is NOT required for addition.<BR>";
// 			}
		}
		unset($objDetail);
		return;
	}

	function sendData($objDetail)
	{
	    $options = get_option('harper_avon_authors_options');
//	    $publishtype = isset($options['publish_checkbox']) ? "publish" : "draft";
	    $publishtype = isset($this->options['mbox_example_checkbox1']) ? "publish" : "draft";
	    $posttype = isset($options['post_checkbox']) ? "post" : "page";
	    $counter = 0;
	    $good2go = FALSE;
//	    $seoonly = isset($options['seo_checkbox']) ? TRUE : FALSE;
	    if (isset($objDetail->title) ) {
	        if (strlen($objDetail->author_id) > 0) {
	            /*******************************************************************************
	             Select the image size desired for your use here via stored options.
	             *******************************************************************************/
	            $postdata = "";
	            $posttitle = $objDetail->title;
	            $good2go = TRUE;
	            } // strlen($objDetail->seo) > 0

	            } // if (isset($objDetail->isbn)
	                if($good2go == TRUE) {
	                /*******************************************************************************
			This is the Post page or post section here, if enough data is available.
				*******************************************************************************/
				//			    echo "About to send";
				$slug = sanitize_title($posttitle);
				$new_post = array(
				    'post_title' => $posttitle,
				    'post_name' => $slug,
				    'post_status' => $publishtype,
				    'post_type' => 'book-author',
				    'post_author' => 1
				);

				$post_id = wp_insert_post($new_post);
				update_post_meta($post_id, 'profile_image', $objDetail->author_image);
				update_post_meta($post_id, profile_text, $objDetail->seo);
                update_post_meta($post_id, "Author_GID", $objDetail->author_id);
//                $book_author = $this->getbookauthor($post_id);
                $auth_id = $objDetail->author_id;
                $newid = $this->getbookauthor($objDetail->author);
                update_post_meta($post_id, "book_author", $newid);
//                update_post_meta($post_id, "book_author", $book_author);
                if (is_wp_error($post_id)) {
				$errors = $post_id->get_error_messages();
				foreach ($errors as $error) {
				echo $error; //this is just an example and generally not a good idea, you should implement means of processing the errors further down the track and using WP's error/message hooks to display them
	            }
	            }
	            if($post_id != 0) {
	            $counter++;
	            }
	            else {
	            echo "it failed";
	               }
				}  // if(strlen($postdata) > 128)
	 			unset ($postdata);
				return $counter;
		} // function sendData($objDetail)

		function getbookauthor($name)
		{
		    global $wpdb;
		    $newpost = $wpdb->get_var( "SELECT ID FROM wp_posts WHERE post_title='$name'");
            $newid = 'a:1:{i:0;s:' . strlen((string)$newpost) . ':"' . $newpost . '";}';
		    return $newid;

		}


} // end class
endif;
?>