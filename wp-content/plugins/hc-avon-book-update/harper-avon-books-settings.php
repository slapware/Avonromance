<?php
error_reporting(E_ALL & ~E_NOTICE);
if (!function_exists('is_admin')) {
    header('Status: 403 Forbidden');
    header('HTTP/1.1 403 Forbidden');
    exit();
}

if (!class_exists("ProductDetail")) :
require_once('ProductDetail.php');
endif;

require_once 'import_csv.php';

if (!class_exists("Harper_Avon_API_Settings")) :

/*
	Create example settings page for our plugin.

	- We show how to render our own controls using HTML.
	- We show how to get WordPress to render controls for us using do_settings_sections'

	WordPress Settings API tutorials
	http://codex.wordpress.org/Settings_API
	http://ottopress.com/2009/wordpress-settings-api-tutorial/
*/
class Harper_Avon_API_Settings {

	public static $default_settings =
		array(
			  	'example_text' => 'Test text',
			  	'example_checkbox1' => 'publish',
				'example_checkbox2' => 'oranges',
			  	'mbox_example_text' => '97800',
			  	'mbox_epub_text' => '97800',
			  	'mbox_example_checkbox1' => 'grapes',
				'mbox_example_checkbox2' => 'lemons'
				);
	var $pagehook, $page_id, $settings_field, $options;


	function __construct() {
		$this->page_id = 'avon_books';
		// This is the get_options slug used in the database to store our plugin option values.
		$this->settings_field = 'avon_books_options';
		$this->options = get_option( $this->settings_field );

		add_action('admin_init', array($this,'admin_init'), 20 );
		add_action( 'admin_menu', array($this, 'admin_menu'), 20);
	}

	function admin_init() {
		register_setting( $this->settings_field, $this->settings_field, array($this, 'sanitize_theme_options') );
		add_option( $this->settings_field, Harper_Avon_API_Settings::$default_settings );


		/*
			This is needed if we want WordPress to render our settings interface
			for us using -
			do_settings_sections

			It sets up different sections and the fields within each section.
		*/
		add_settings_section('avon_main', '',
			array($this, 'main_section_text'), 'avon_books_settings_page');

		add_settings_field('example_text', 'Format',
			array($this, 'render_example_text'), 'avon_books_settings_page', 'avon_main');

		add_settings_field('example_checkbox1', 'Check to publish',
			array($this, 'render_example_checkbox'), 'avon_books_settings_page', 'avon_main',
			array('id' => 'example_checkbox1', 'value' => 'publish', 'text' => 'Publish') );
/*		add_settings_field('example_checkbox2', '',
			array($this, 'render_example_checkbox'), 'avon_books_settings_page', 'avon_main',
			array('id' => 'example_checkbox2', 'value' => 'oranges', 'text' => 'Oranges') ); */
	}

	function admin_menu() {
		if ( ! current_user_can('update_plugins') )
			return;

		// Add a new submenu to the standard Settings panel
		$this->pagehook = $page =  add_options_page(
			__('Avon Books', 'avon_books'), __('Avon Books', 'avon_books'),
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
		.settings_page_avon_books label { display:inline-block; width: 150px; }
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

		$title = __('Avon Books', 'avon_books');
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
		add_meta_box( 'avon-books-version', __( 'Information', 'avon_books' ), array( $this, 'info_box' ), $this->pagehook, 'main', 'high' );

		// Example metabox containing two example checkbox controls.
		// Also includes and example input text box, rendered in HTML in the condition_box function
		add_meta_box( 'avon-books-conditions', __( 'Publish Conditions', 'avon_books' ), array( $this, 'condition_box' ), $this->pagehook, 'main' );

		// Example metabox containing an example text box & two example checkbox controls.
		// Example settings rendered by WordPress using the do_settings_sections function.
		add_meta_box( 	'avon-books-all',
						__( 'Format and post options', 'avon_books' ),
						array( $this, 'do_settings_box' ), $this->pagehook, 'main' );

	}

	function info_box() {

		?>
		<p><strong><?php _e( 'Version:', 'avon_books' ); ?></strong> <?php echo AVON_BOOKS_VERSION; ?> <?php echo '&middot;'; ?> <strong><?php _e( 'Released:', 'avon_books' ); ?></strong> <?php echo AVON_BOOKS_RELEASE_DATE; ?></p>

		<p>
 			<label for="<?php echo $this->get_field_id( 'mbox_example_text' ); ?>"><?php _e( 'ISBN', 'avon_books' ); ?></label>
			<input type="text" name="<?php echo $this->get_field_name( 'mbox_example_text' ); ?>" id="<?php echo $this->get_field_id( 'mbox_example_text' ); ?>" value="<?php echo esc_attr( $this->get_field_value( 'mbox_example_text' ) ); ?>" style="width:15%;" />
		</p>
		<p>
 			<label for="<?php echo $this->get_field_id( 'mbox_epub_text' ); ?>"><?php _e( 'eISBN', 'avon_books' ); ?></label>
			<input type="text" name="<?php echo $this->get_field_name( 'mbox_epub_text' ); ?>" id="<?php echo $this->get_field_id( 'mbox_epub_text' ); ?>" value="<?php echo esc_attr( $this->get_field_value( 'mbox_epub_text' ) ); ?>" style="width:15%;" />
		</p>

		<?php

	}

	function condition_box() {
	?>
		<p>
			<input type="checkbox" name="<?php echo $this->get_field_name( 'mbox_example_checkbox1' ); ?>" id="<?php echo $this->get_field_id( 'mbox_example_checkbox1' ); ?>" value="grapes" <?php echo isset($this->options['mbox_example_checkbox1']) ? 'checked' : '';?> />
			<label for="<?php echo $this->get_field_id( 'mbox_example_checkbox1' ); ?>"><?php _e( 'SEO must be present', 'avon_books' ); ?></label>
            <br/>


			<input type="checkbox" name="<?php echo $this->get_field_name( 'mbox_example_checkbox2' ); ?>" id="<?php echo $this->get_field_id( 'mbox_example_checkbox2' ); ?>" value="lemons" <?php echo isset($this->options['mbox_example_checkbox2']) ? 'checked' : '';?> />
			<label for="<?php echo $this->get_field_id( 'mbox_example_checkbox2' ); ?>"><?php _e( 'Check for book, not post', 'avon_books' ); ?></label>

		</p>
	<?php }


	function do_settings_box() {
		do_settings_sections('avon_books_settings_page');
	}

	/*
		WordPress settings rendering functions

		ONLY NEEDED if we are using wordpress to render our controls (do_settings_sections)
	*/


	function main_section_text() {
		echo '<p>Additional inputs.</p>';
	}

	function render_example_text() {
		?>
        <input id="example_text" style="width:50%;"  type="text" name="<?php echo $this->get_field_name( 'example_text' ); ?>" value="<?php echo esc_attr( $this->get_field_value( 'example_text' ) ); ?>" />
		<?php
	}

	function render_example_checkbox($args) {
		$id = 'avon_books_options['.$args['id'].']';
		?>
  		<input name="<?php echo $id;?>" type="checkbox" value="<?php echo $args['value'];?>" <?php echo isset($this->options[$args['id']]) ? 'checked' : '';?> /> <?php echo " {$args['text']}"; ?> <br/>
		<?php
	}
/*
 * load and run the CSV loader function.
 */
	function hc_avon_csv_loader() {
	    $loader = new AvonImport();
	    $loader->loaddata();
	}
/*  This is where the Genre and book Title are added as meta data for the post, triggered
 *  by the dashboard widget. Will update all posts that have an ISBN as a tag we use as the identifier.
 */
	function hc_avon_genre_update() {
	    //
	    global $wpdb;
	    global $post;

	    $querystr = "
	    SELECT $wpdb->posts.*
	    FROM $wpdb->posts
	    WHERE $wpdb->posts.post_status = 'publish'
	    AND $wpdb->posts.post_type = 'post'
	    AND $wpdb->posts.post_date < NOW()
	    ORDER BY $wpdb->posts.post_date DESC
	    ";
	    $pageposts = get_posts( array('post_type' => 'post', 'numberposts' => -1 ) );

        $counter = 0;
		$args = array(
		    'post_type' => 'post',
		    'post_status' => 'any',
		    'orderby'   => 'title',
		    'order'     => 'ASC',
		);
		foreach ( $pageposts as $my_post ):
		$posttags = get_the_tags($my_post->ID);
 			if ($posttags) {
 			  foreach($posttags as $tag) {
 			  $isin = strpos($tag->name, "isbn:");
 			  if($isin === 0) {
 			  	$isbn = substr($tag->name, 5);
			     $objDetail= new ProductDetail($isbn, "");
				 update_post_meta($my_post->ID, "Genre", $objDetail->genre);
				 update_post_meta($my_post->ID, "BookTitle", $objDetail->title);
				 update_post_meta($my_post->ID, "BookAuthor", $objDetail->author);
				 update_post_meta($my_post->ID, "BookImprint", $objDetail->imprimt);
				 update_post_meta($my_post->ID, "BookIsbn", $objDetail->isbn);
				 $counter++;
				 unset($objDetail);
			     break;
				}
 			  }
 			}
 		endforeach;
		echo "<span style=color:red>Meta Updated for $counter books.</span><BR>";
	}
	/**
	* This function reads the setting and calls the API via the objects ProductDelta and ProductDetail
	* and loops over the data to push the data into wordpress as draft or publish as set on options page.
	* If specific formatting is desired this would be the spot to perform that task, not the API objects.
	*/
	function hc_avon_book_update() {
//		$options = get_option('hc_avon_options');
		/**
		* Is this a single ISBN search or multi record search. This is determined if the ISBN field
		* has been set to a specific ISBN in the settings panel.
		*/
//		$GLOBALS['DebugMyPlugin']->panels['main']->addMessage('Checking the ISBN:',$this->get_field_value( 'mbox_example_text' ));
//		var_dump($this->get_field_value( 'mbox_example_text' ));
		$pisbn = $this->get_field_value( 'mbox_example_text' );
		$eisbn = $this->get_field_value( 'mbox_epub_text' );
		$counter = $this->sendData($pisbn, $eisbn);
		if ( strlen($this->get_field_value( 'mbox_example_text' )) > 12)
		{
			$locale = "0";
			$objDetail= new ProductDetail($pisbn, $locale);
// 			$myisbn = $this->get_field_value( 'mbox_example_text' );
// 			if(strlen($pisbn) > 12)
			$counter = $this->sendData($pisbn, $eisbn);
			echo "<BR><span style=color:blue>" . $counter . " records have been added.</span><BR>";
			if($counter == 0)
			{
				/*******************************************************************************
				something is wrong, probably in settings values so lets display to help resolve.
				*******************************************************************************/
				echo "<span style=color:red>It looks like a configuration issue has occurred.</span><BR>";
				echo "The ISBN search value used from settings is as follows;<BR>";
				echo "ISBN " . $this->get_field_value( 'mbox_example_text' ) . " <BR>";
				echo "eISBN " . $this->get_field_value( 'mbox_epub_text' ) . " <BR>";
				$seoonly = isset($this->options['mbox_example_checkbox1']) ? TRUE : FALSE;
				if($seoonly == TRUE) {
					echo "SEO copy is required for addition <i>(maybe none available, only reviews ?)</i>.<BR>";
				}
				else
				{
					echo "SEO copy is NOT required for addition.<BR>";
				}
			}
			echo "Genre:" . $objDetail->genre;
//			var_dump( $wp_error);
			unset($objDetail);
			return;
		}
	}	// hc_avon_book_update

	function sendData($pisbn, $eisbn)
	{
			$locale = "0";
			$counter = 0;
			if(strlen($pisbn) > 12)
				$objDetail= new ProductDetail($pisbn, $locale);
			else
				$objDetail= new ProductDetail($eisbn, $locale);

        $publishtype = "publish";
//        $publishtype = "draft";
        $posttype = "book";
	    $good2go = isset($options['mbox_example_checkbox1']) ? FALSE : TRUE;
        $posttitle = $objDetail->title;
        if (strlen($objDetail->seo) > 0)  {
            $post_content = $objDetail->seo;
            $good2go = TRUE;
        }
        $cover_photo_url = $objDetail->cover_large;
        // The SEO CheckBox logic is here.
        $onsaledate = $this->Fixdate($objDetail->onsale_date);
        if(strcasecmp($objDetail->format, 'E-BOOK') == 0)
        {
            $ebook_price = $objDetail->price;
            $isbn_code_for_ebook = $eisbn;
            $isbn_code_for_paperback = "";
        }
        else
        {
            $isbn_code_for_paperback = $pisbn;
            if(strlen($eisbn) > 12)
                $isbn_code_for_ebook = $eisbn;
            else
                $isbn_code_for_ebook = "";
        }
        $genre = $this->firstGenre($objDetail->genre);
		if((strlen($post_content) > 32) || ($good2go == TRUE)) {
		/*******************************************************************************
		This is the Post page or post section here, if enough data is available.
		*******************************************************************************/
			$new_post = array(
			'post_title' => $posttitle,
			'post_content' => $post_content,
			'post_status' => $publishtype,
			'post_type' => $posttype,
			'post_author' => 1,
			'post_date' => date("Y-m-d H:i:s", time())
			);
			$counter = 1;
			if (!get_page_by_title($posttitle, 'OBJECT', 'book') ){
			$post_id = wp_insert_post($new_post);
			if ($post_id == 0) {
				$counter = 0;
				return 0;
			}
			$tag_author = 'author:' . $objDetail->author;
			$tag_isbn = 'isbn:' . $objDetail->isbn;
			$cat_array = array($tag_author, $tag_isbn);
			wp_set_post_terms($post_id, $cat_array,'post_tag',true);
			update_post_meta($post_id, "Genre", $objDetail->genre);
			update_post_meta($post_id, "isbn_code_for_ebook", $isbn_code_for_ebook);
			update_post_meta($post_id, "isbn_code_for_paperback", $isbn_code_for_paperback);
            if(strcasecmp($objDetail->format, 'E-BOOK') == 0)
            {
			   update_post_meta($post_id, "ebook_price", $objDetail->price);
            }
            update_post_meta($post_id, "sale_date", $onsaledate);
            update_post_meta($post_id, "BookIsbn", $objDetail->isbn);
            update_post_meta($post_id, "cover_photo_url", $cover_photo_url);
            update_post_meta($post_id, "Author_GID", $objDetail->author_gid);
            $book_author = $this->getbookauthor($objDetail->author);
            update_post_meta($post_id, "book_author", array($book_author));
//            update_post_meta($post_id, "book_author", $book_author);
            $others = $this->getOthers($objDetail->isbn, $posttitle);
            update_post_meta($post_id, "other_retailers", $others);
            } // if (!get_page_by_title($title, 'OBJECT', 'post') )
          }
//		}

 			unset ($postdata);
			return $counter;
	} // sendData

	function getbookauthor($name)
    {
        global $wpdb;
        $newpost = $wpdb->get_var( "SELECT ID FROM wp_posts WHERE post_title='$name' AND post_type='book-author'");
//         $newid = 'a:1:{i:0;s:' . strlen((string)$newpost) . ':"' . $newpost . '";}';
// 	    return $newid;
		return $newpost;

    }

    function Fixdate($olddate)
    {
        $pieces = explode("/", $olddate);
        $newdate = $pieces[2] . $pieces[0] . $pieces[1];
        return $newdate;
    }

    function firstGenre($mgenre)
    {
        $pieces = explode("/", $mgenre);
        return $pieces[0];
    }

    function getOthers($pisbn, $ptitle)
    {
        //
        $newtitle = str_replace(" ", "_", $ptitle);
        $here = <<<HERE
<ul>
<li><a href="http://ads.harpercollins.com/avonsite?isbn=$pisbn&amp;retailer=amazon" target="_blank" onclick="_gaq.push(['_trackEvent', 'OFFSITE_BUY_TEXTLINK_BUYPAGE', 'Amazon', '$pisbn-_-$newtitle']);">Amazon</a></li>
<li><a href="http://ads.harpercollins.com/avonsite?isbn=$pisbn&amp;retailer=barnesandnoble" target="_blank" onclick="_gaq.push(['_trackEvent', 'OFFSITE_BUY_TEXTLINK_BUYPAGE', 'Barnes &amp; Noble', '$pisbn-_-$newtitle']);">Barnes &amp; Noble</a></li>
<li><a href="http://ads.harpercollins.com/avonsite?isbn=$pisbn&amp;retailer=booksamillion" target="_blank" onclick="_gaq.push(['_trackEvent', 'OFFSITE_BUY_TEXTLINK_BUYPAGE', 'Books-A-Million', '$pisbn-_-$newtitle']);">Books-A-Million</a></li>
<li><a href="http://ads.harpercollins.com/avonsite?isbn=$pisbn&amp;retailer=googleplay" target="_blank" onclick="_gaq.push(['_trackEvent', 'OFFSITE_BUY_TEXTLINK_BUYPAGE', 'Google', '$pisbn-_-$newtitle']);">Google</a></li>
<li><a href="http://ads.harpercollins.com/avonsite?isbn=$pisbn&amp;retailer=apple" target="_blank" onclick="_gaq.push(['_trackEvent', 'OFFSITE_BUY_TEXTLINK_BUYPAGE', 'iBookstore', '$pisbn-_-$newtitle']);">iBooks</a></li>
<li><a href="http://ads.harpercollins.com/avonsite?isbn=$pisbn&amp;retailer=indiebound" target="_blank" onclick="_gaq.push(['_trackEvent', 'OFFSITE_BUY_TEXTLINK_BUYPAGE', 'Indiebound', '$pisbn-_-$newtitle']);">Indiebound</a></li>
<li><a href="http://ads.harpercollins.com/avonsite?isbn=$pisbn&amp;retailer=kobo" target="_blank" onclick="_gaq.push(['_trackEvent', 'OFFSITE_BUY_TEXTLINK_BUYPAGE', 'Kobo', '$pisbn-_-$newtitle']);">Kobo</a></li>
<li><a href="http://ads.harpercollins.com/avonsite?isbn=$pisbn&amp;retailer=walmart" target="_blank" onclick="_gaq.push(['_trackEvent', 'OFFSITE_BUY_TEXTLINK_BUYPAGE', 'Walmart', '$pisbn-_-$newtitle']);">Walmart</a></li>
<a class="close" href="#">close</a>
</ul>
HERE;
        return $here;
    }

} // end class
endif;
?>