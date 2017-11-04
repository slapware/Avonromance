<?php

/**
 * @author Stephen La Pierre
 * Get data from the ProductInfo API call for a given ISBN and optional locale.
 * This API call only comes in XML so no choice to make here.
 */
if (!class_exists("AuthorDetail")) :

class AuthorDetail {
	// TODO - Insert your code here
	/**
	 * The isbn of the book object we want detail for.
	 * @var string $isbn
	 */
	public $author_id;

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
	public $author_image;

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
	protected $detail_base = "http://api.harpercollins.com/api/v3/hcapim?apiname=AuthorInfo&format=XML&global_contributor_id=";


	function __construct($pauthID) {
		// TODO - Get passed values to get get detail on and make safe for API call.
		$this->author_id = $pauthID;
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
        $this->api_query_string = $this->detail_base . $this->author_id;
        // add the mashery key for this plugin
		$this->api_query_string = $this->api_query_string . "&apikey=epddsj8ee33e387vbagk8n96";
		/*******************************************************************************
		Here we make the call to the API to get detail information on this book.
		*******************************************************************************/
        $ch = curl_init();
        $timeout = 30;
        curl_setopt($ch, CURLOPT_URL, $this->api_query_string);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
        $data = curl_exec($ch);
        if ($data == FALSE) {
        	echo "Error on GET";
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
        		$authbio = $xml->getElementsByTagName("Content_Area1");
        		foreach ($authbio as $bio) {
        		foreach($bio->childNodes as $child) {
        		    if ($child->nodeType == XML_CDATA_SECTION_NODE) {
        		        $this->seo = filter_var($child->textContent, FILTER_SANITIZE_STRING);
        		    }
        		} // foreach
        		if(strlen($this->seo) > 32)
        		    break;
        	} // foreach

        	} // try
        	catch (Exception $e) {
        		$this->error = $e->getMessage();
        	}

        	$xpath = new DOMXPath($xml);
        	$this->title = $xml->getElementsByTagName("Display_Name")->item(0)->nodeValue;
        	$this->author_image = $xml->getElementsByTagName("Image2_URL")->item(0)->nodeValue;

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
} // class ProductDetail
endif;
?>