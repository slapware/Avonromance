<?php

/**
 * @author Stephen La Pierre
 * Get data from the ProductInfo API call for a given ISBN and optional locale.
 * This API call only comes in XML so no choice to make here.
 */
if (!class_exists("ProductDetail")) :

class ProductDetail {
	// TODO - Insert your code here
	/**
	 * The isbn of the book object we want detail for.
	 * @var string $isbn
	 */
	public $isbn;

	/**
	 * Price
	 * @var string $price
	 * This is returned by the API.
	 */
	public $price;
	
	/**
	 * Imprint
	 * @var string $imprint
	 * This is returned by the API.
	 */
	public $imprint;

	/**
	 * Format of book.
	 * @var string $format
	 * This is returned by the API.
	 */
	public $format;

	/**
	 * Genre of book.
	 * @var string $genre
	 * This is returned by the API.
	 */
	public $genre;

	/**
	 * SubFormat of book.
	 * @var string $subformat
	 * This is returned by the API.
	 */
	public $subformat;

	/**
	 * Locale, if exists
	 * @var string $mylocale
	 */
	public $mylocale;

	/**
	 * SEO copy
	 * @var string $seo
	 * This is returned by the API.
	 */
	public $seo;

	/**
	 * Book Title
	 * @var string $title
	 * This is returned by the API.
	 */
	public $title;

	/**
	 * Cover Large url
	 * @var string $cover_large
	 * This is returned by the API.
	 */
	public $cover_large;

	/**
	 * Cover Medium url
	 * @var string $cover_medium
	 * This is returned by the API.
	 */
	public $cover_medium;

	/**
	 * Cover Medium Large url
	 * @var string $cover_medium_large
	 * This is returned by the API.
	 */
	public $cover_medium_large;

	/**
	 * Cover Small url
	 * @var string $cover_small
	 * This is returned by the API.
	 */
	public $cover_small;

	/**
	 * Book Release Date
	 * @var string $release_date
	 * This is returned by the API.
	 */
	public $release_date;

	/**
	 * Book On Sale Date
	 * @var string $onsale_date
	 * This is returned by the API.
	 */
	public $onsale_date;

	/**
	 * Book Description collection 605
	 * @var array $description
	 * This is returned by the API.
	 */
	public $description = array();

	/**
	 * Book Catalog copy collection 607
	 * @var array $catalog
	 * This is returned by the API.
	 */
	public $catalog = array();

	/**
	 * Book Excerpt collection 609
	 * @var array $excerpt
	 * This is returned by the API.
	 */
	public $excerpt = array();

	/**
	 * Book Quote collection 618
	 * @var array $quote
	 * This is returned by the API.
	 */
	public $quote = array();

	/**
	 * Error message
	 * @var string $error
	 * This is bad news.
	 */
	public $error;

	/**
	 * Author name
	 * @var string $author
	 * This is returned by the API.
	 */
	public $author;

	/**
	 * Author g;obal ID
	 * @var string $author_gid
	 * This is returned by the API.
	 */
	public $author_gid;

	/**
	 * Best Seller Flag
	 * @var bool $best_seller
	 * This is returned by the API.
	 */
	public $best_seller;

	/**
	 * New Release Flag
	 * @var bool $new_release
	 * This is returned by the API.
	 */
	public $new_release;

	/**
	 * API query string
	 * @var string $api_query_string
	 */
	protected $api_query_string;
	// The base call before options are added if present.
	protected $detail_base = "http://api.harpercollins.com/api/v3/hcapim?apiname=ProductInfo&format=XML&isbn=";


	function __construct($pisbn, $plocale) {
		// TODO - Get passed values to get get detail on and make safe for API call.
		$this->isbn = $pisbn;
		$this->mylocale = urlencode($plocale);
		$this->best_seller = FALSE;
		$this->new_release = FALSE;
		$this->genre = "";
		$this->load();
	}

   function __destruct() {
       unset($this->description);
       unset($this->catalog);
       unset($this->excerpt);
       unset($this->quote);
   }


	/**
	 * Loads the post data from detail API into the class and
	 * store in member vars for use by calling object
	 */
	function load()
	{
		/*******************************************************************************
		Ensure not a blank string from dropdown box, it is select option 1 after all.
		*******************************************************************************/
        if (strlen($this->mylocale) > 3) {
        	$this->api_query_string = $this->detail_base . $this->isbn . "&locale=" . $this->mylocale;
       }
        else
        {
         	$this->api_query_string = $this->detail_base . $this->isbn;
        }
        // add the mashery key for this plugin
		$this->api_query_string = $this->api_query_string . "&apikey=btpr75hbaqn7b2hgha7ntxmv";
		/*******************************************************************************
		Here we make the call to the API to get detail information on this book.
		*******************************************************************************/
        $ch = curl_init();
        $timeout = 30;
        curl_setopt($ch, CURLOPT_URL, $this->api_query_string);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
        $data = curl_exec($ch);
        if (strlen($data) < 128) {
        	echo "<p style='color:red'>Error on GET: $this->isbn</p>";
	        $this->logBookAddError("Book not found in OpenBook API.", $this->isbn);
        }
        /*******************************************************************************
        clean the nasty stuff we find from some text data.
        *******************************************************************************/
        $cleandata = filter_var($data, FILTER_UNSAFE_RAW, FILTER_FLAG_STRIP_LOW);
        curl_close($ch);
        if (strlen($data) > 32) {
        	$xml = new DOMDocument();

        	// We don't want to bother with white spaces
        	$xml->preserveWhiteSpace = false;

        	try {
        		$xml->loadXML($cleandata);

		        $productContentNodes = $xml->getElementsByTagName("Product_Content");

		        foreach($productContentNodes as $productContent)
		        {
			        $contentType = $productContent->firstChild;

			        if($contentType->nodeValue === "605")
			        {
				        $contentArea1 = $contentType->nextSibling;
				        foreach($contentArea1->childNodes as $child)
				        {
					        if ( $child->nodeType == XML_CDATA_SECTION_NODE )
					        {
						        $this->seo = filter_var( $child->textContent, FILTER_SANITIZE_STRING );
					        }
				        }
			        }
		        }

//		        $destinations = $xml->getElementsByTagName("Product_Group_SEO_Copy");
//
//
//
//
//		        if($destinations->item(0)->firstChild !== null)
//		        {
//			        foreach ( $destinations as $destination ) {
//				        foreach ( $destination->childNodes as $child ) {
//
//					        if ( $child->nodeType == XML_CDATA_SECTION_NODE ) {
//
//						        $this->seo = filter_var( $child->textContent, FILTER_SANITIZE_STRING );
//					        }
//				        } // foreach
//			        } // foreach
//		        }
//		        else //there was no SEO copy try to get the product content
//		        {
//			        //echo "no seo copy" . PHP_EOL;
//
//
//		        }

        	} // try
        	catch (Exception $e) {
        		$this->error = $e->getMessage();
        	}

        	$xpath = new DOMXPath($xml);
            try {
        	$this->onsale_date = $xml->getElementsByTagName("On_Sale_Date")->item(0)->nodeValue;
            }
            catch(Exception $e) {
                $this->onsale_date = "";
	            echo "exception caught!" . PHP_EOL;
            }

            try {
            $this->release_date = $xml->getElementsByTagName("Release_Date")->item(0)->nodeValue;
            }
            catch(Exception $e) {
                $this->release_date = "";
            }
            try {
            $this->imprint = $xml->getElementsByTagName("Imprint")->item(0)->nodeValue;
            }
            catch(Exception $e) {
                $this->imprint = "";
            }
            try {
                $this->price = $xml->getElementsByTagName("Price")->item(0)->nodeValue;
            }
            catch(Exception $e) {
                $this->price = "";
            }
            
            try {
            $this->format = $xml->getElementsByTagName("Format")->item(0)->nodeValue;
            }
            catch(Exception $e) {
            $this->format = "";
            }
            
            try {
            $this->subformat = $xml->getElementsByTagName("Sub_Format")->item(0)->nodeValue;
            }
            catch(Exception $e) {
            $this->subformat = "";
            }
            try {
            $this->title = $xml->getElementsByTagName("Title")->item(0)->nodeValue;
            }
            catch(Exception $e) {
            $this->title = "";
            }
            
            try {
            $this->author = $xml->getElementsByTagName("Display_Name")->item(0)->nodeValue;
            }
            catch(Exception $e) {
            $this->author = "";
            }
            
//        	$authtitle = $author . ', ' . $this->title;
			/*******************************************************************************
			cover image urls data is here.
			*******************************************************************************/
        	$this->cover_large = $xml->getElementsByTagName("CoverImageURL_Large")->item(0)->nodeValue;
         	$this->cover_medium = $xml->getElementsByTagName("CoverImageURL_Medium")->item(0)->nodeValue;
         	$this->cover_medium_large = $xml->getElementsByTagName("CoverImageURL_MediumLarge")->item(0)->nodeValue;
         	$this->cover_small = $xml->getElementsByTagName("CoverImageURL_Small")->item(0)->nodeValue;
         	// Flags for best seller and new release
         	$best = $xml->getElementsByTagName("Best_Seller_Flag")->item(0)->nodeValue;
         	$new = $xml->getElementsByTagName("New_Release_Flag")->item(0)->nodeValue;
         	$this->author_gid = $xml->getElementsByTagName("Contributor_Persona_ID")->item(0)->nodeValue;
         	/*******************************************************************************
         	compare API flag strings and set boolean flags
         	*******************************************************************************/
         	if (strcasecmp($best, "Y") == 0) {
         		$this->best_seller = TRUE;
         	}
         	else {
         		$this->best_seller = FALSE;
         	}

         	if (strcasecmp($new, "1") == 0) {
         		$this->new_release = TRUE;
         	}
         	else {
         		$this->new_release = FALSE;
         	}
         	/*******************************************************************************
         	Parse the BISAC description entries of detail api and store in string.
         	*******************************************************************************/
         	$this->genre = "";
         	$genrelist = $xpath->query('//OpenBook_API/Product_Detail/Product_BISAC_Codes/Product_BISAC');
			foreach ($genrelist as $aGenre)
			{
				$gn = $aGenre->getElementsByTagName("BISAC_Desc")->item(0)->nodeValue;
				$pos = strpos(strtolower($gn), "historical");
				if($pos !== FALSE)
				    $this->genre .= "Historical/";
				$pos = strpos(strtolower($gn), "contemporary");
				if($pos !== FALSE)
				    $this->genre .= "Contemporary/";
				$pos = strpos(strtolower($gn), "paranormal");
				if($pos !== FALSE)
				    $this->genre .= "Paranormal/";
				$pos = strpos(strtolower($gn), "suspence");
				if($pos !== FALSE)
				    $this->genre .= "Suspence/";
				$pos = strpos(strtolower($gn), "anthologies");
				if($pos !== FALSE)
				    $this->genre .= "Anthologies/";
				$pos = strpos(strtolower($gn), "erotica");
				if($pos !== FALSE)
				    $this->genre .= "Erotica/";
				$pos = strpos(strtolower($gn), "romance");
				if($pos !== FALSE)
				    $this->genre .= "Romance/";
			}
         	/*******************************************************************************
         	Parse the Product_Content entries of detail api and store in arrays.
         	*******************************************************************************/
        	$nodelist = $xpath->query('//OpenBook_API/Product_Detail/Product_Contents/Product_Content');
			foreach ($nodelist as $aNode)
			{
				$id = $aNode->getElementsByTagName("Content_Type_ID")->item(0)->nodeValue;
				$read_list =  array('605', '607', '609', '618');
				if (in_array($id, $read_list))
				{
					switch ($id)
					{
				    case "605":	// Book description information.
						array_push($this->description, $aNode->getElementsByTagName("Content_Area1")->item(0)->nodeValue);
				    	break;
				    case "607":	// Catalog copy information
						array_push($this->catalog, $aNode->getElementsByTagName("Content_Area1")->item(0)->nodeValue);
				    	break;
				    case "609":	// Chapter excerpt if available.
						array_push($this->excerpt, $aNode->getElementsByTagName("Content_Area1")->item(0)->nodeValue);
				    	break;
				    case "618":	// Review quote(s) if available. Sometimes quite a few hence the array.
						array_push($this->quote, $aNode->getElementsByTagName("Content_Area1")->item(0)->nodeValue);
				    	break;
				    default:
				    	// no action
				    	break;
					 }
				}  // if (in_array
			}

        } // if (strlen($data) > 64
        else {
        	$this->error = "short data returned " . strlen($data) . "<BR>\n" . $data;
        }
		/*******************************************************************************
		We have a lot of luggage, so lets give it up.
		*******************************************************************************/
		unset($data);
		unset($cleandata);
		unset($xml);
	} // load()

	public function logBookAddError($message, $title)
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
} // class ProductDetail
endif;
?>