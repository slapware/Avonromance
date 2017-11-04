<?php
/* To add Avon Books to wordpress by ISBN and API 
 * 
 */
if (!class_exists("ProductDetail")) :
require_once('ProductDetail.php');
endif;

date_default_timezone_set('America/New_York');

/**
 *
 * @author slap
 *
 */
class AvonImport
{
    // The CSV filename
    public $csvfile;

    public $csvarray = array();
    /**
     */
    function __construct()
    {
        $this->csvfile = '/tmp/avon.csv';
                // TODO - Insert your code here
    }

    /**
     */
    function __destruct()
    {

        // TODO - Insert your code here
    }
    
    function loaddata()
    {
        $this->csvarray = $this->csv_to_array($this->csvfile, ",");
        foreach ($this->csvarray as $i => $row)
        {
            $primaryISBN = $row["PISBN"];
            $eisbn = $row["EISBN"];
            if(strlen($primaryISBN) > 12)
                $this->sendData($primaryISBN, $eisbn);
        }
    }
    
    function sendData($pisbn, $eisbn)
    {
		$locale = "0";
        $objDetail= new ProductDetail($pisbn, $locale);
        $publishtype = "publish";
//        $publishtype = "draft";
        $posttype = "book";
        //$good2go = FALSE;
        $posttitle = $objDetail->title;
//        if (strlen($objDetail->seo) > 0)  {
//            $post_content = $objDetail->seo;
//            $good2go = TRUE;
//        }

	    $post_content = $objDetail->seo;

        $cover_photo_url = $objDetail->cover_large;
        $onsaledate = $this->Fixdate($objDetail->onsale_date);
        if(strcasecmp($objDetail->format, 'E-BOOK') == 0)
        {
            $ebook_price = $objDetail->price;
            $isbn_code_for_ebook = $pisbn;
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
//		if((strlen($post_content) > 128) && ($good2go == TRUE)) {
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

	    //check for existing book
	    $post_id = $this->updateBookPostByIsbn($new_post, $objDetail->isbn);

	    if($post_id === 0 && isset($isbn_code_for_ebook))
	    {
		    $post_id = $this->updateBookPostByIsbn($new_post, $isbn_code_for_ebook);

		    if($post_id === 0)
		    {
			    $post_id = wp_insert_post($new_post);
		    }
	    }

	    //if there was an issue inserting the post log the error and do not insert metadata
	    if(!isset($post_id))
	    {
		    $this->logBookAddError("There was an issue when inserting the post", $posttitle);
	    }
	    else
	    {
		    $tag_author = 'author:' . $objDetail->author;
		    $tag_isbn   = 'isbn:' . $objDetail->isbn;
		    $cat_array  = array( $tag_author, $tag_isbn );
		    wp_set_post_terms( $post_id, $cat_array, 'post_tag', true );
		    update_post_meta( $post_id, "Genre", $objDetail->genre );
		    update_post_meta( $post_id, "isbn_code_for_ebook", $isbn_code_for_ebook );
		    update_post_meta( $post_id, "isbn_code_for_paperback", $isbn_code_for_paperback );
		    if ( strcasecmp( $objDetail->format, 'E-BOOK' ) == 0 ) {
			    update_post_meta( $post_id, "ebook_price", $objDetail->price );
		    }
		    update_post_meta( $post_id, "sale_date", $onsaledate );
		    update_post_meta( $post_id, "BookIsbn", $objDetail->isbn );
		    update_post_meta( $post_id, "cover_photo_url", $cover_photo_url );
		    update_post_meta( $post_id, "Author_GID", $objDetail->author_gid );
		    $book_author = $this->getbookauthor( $objDetail->author );
		    update_post_meta( $post_id, "book_author", array( $book_author ) );
		    $others = $this->getOthers( $objDetail->isbn, $posttitle );
		    update_post_meta( $post_id, "other_retailers", $others );
	    }
            
    }

	//updates book post by isbn
	//@return post_id on successful update
	//@return 0 on no match
	function updateBookPostByIsbn($new_post, $isbn)
	{
		global $wpdb;

		$post_results = $wpdb->get_results(
			"SELECT p.id, p.post_type
			FROM wp_posts p, wp_postmeta m
			WHERE m.meta_key = 'BookIsbn'
			AND m.meta_value = $isbn
			AND m.post_id = p.id");

		$book_results = array_filter($post_results, function ($postObject) {
			if($postObject->post_type === 'book')
				return $postObject;
		});

		if(count($book_results) > 0)
		{
			foreach ( $book_results as $book )
			{
				$new_post['ID'] = $book->id;

				//update post data if changed
				$post_id = wp_update_post( $new_post );

				//take the first post if there are more than one and just return
				return $post_id;
			}
		}
		else
		{
			return 0;
		}
	}

	function logBookAddError($message, $title)
	{
		$error_post = array(
			'post_title' => "There was an issue adding the book: $title",
			'post_content' => $message,
			'post_status' => 'private',
			'post_type' => 'book_error_log',
			'post_author' => 1,
			'post_date' => date("Y-m-d H:i:s", time())
		);

		wp_insert_post($error_post);
	}
    
    function getbookauthor($name)
    {
        global $wpdb;
        $auth_id = $wpdb->get_var( "SELECT ID FROM wp_posts WHERE post_title='$name'");
        return $auth_id;
    
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
    
    function csv_to_array($filename, $delimiter="\t")
    {
        if(!file_exists($filename) || !is_readable($filename))
            return FALSE;
    
        $header = NULL;
        $data = array();
        if (($handle = fopen($filename, 'r')) !== FALSE)
        {
            while (($row = fgetcsv($handle, 110, $delimiter)) !== FALSE)
            {
                if(!$header)
                    $header = $row;
                else
                    $data[] = array_combine($header, $row);
            }
            fclose($handle);
        }
        return $data;
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
    
}




?>