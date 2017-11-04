<?php

class AuthName2GID
{
    /**
     * Author name
     * @var string $author
     * This is returned by the API.
     */
    public $author;

    /**
     * Author GID
     * @var string author GID
     * This is returned by the API.
     */
    public $author_gid;

    /**
     * Author image
     * @var string author image1
     * This is returned by the API.
     */
    public $author_image1;

    /**
     * Author image
     * @var string author image2
     * This is returned by the API.
     */
    public $author_image2;

    /**
	 * API query string
	 * @var string $api_query_string
	 */
    protected $api_query_string;
	// The base call before options are added if present.
	protected $detail_base = "http://api.harpercollins.com/api/v3/hcapim?apiname=AuthorName&format=XML&contributor-name=";

    /**
     * @return the $author
     */
    public function getAuthor()
    {
        return $this->author;
    }

	/**
     * @return the $author_gid
     */
    public function getAuthor_gid()
    {
        return $this->author_gid;
    }

	/**
     * @param string $author
     */
    public function setAuthor($author)
    {
        $this->author = $author;
    }

	/**
     * @param string $author_gid
     */
    public function setAuthor_gid($author_gid)
    {
        $this->author_gid = $author_gid;
    }

	function __construct($pauthname)
    {
        $this->author = $pauthname;
        $this->load();

    }

    function __destruct()
    {
        //
    }
    function load()
    {
        /*******************************************************************************
         Ensure not a blank string from dropdown box, it is select option 1 after all.
         *******************************************************************************/
        $this->api_query_string = $this->detail_base . $this->author;
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
        	$xml = new DOMDocument();
        	$xml->loadXML($data);

        	// We don't want to bother with white spaces
        	$xml->preserveWhiteSpace = false;

        	$xpath = new DOMXPath($xml);
        	$tmpid = $xml->getElementsByTagName("Contributor_Detail_URI")->item(0)->nodeValue;
        	$posstart = strrpos ( $tmpid , "id=", 0 );
        	$posend = strrpos ( $tmpid ,  "&amp", $posstart );
//        	$id = +3
        	$len =  $posend - ($posstart + 3);
        	$this->author_gid = substr($tmpid, $posstart +3, $len);

        	$this->author_image1 = $xml->getElementsByTagName("Image1_URL")->item(0)->nodeValue;
        	$this->author_image2 = $xml->getElementsByTagName("Image2_URL")->item(0)->nodeValue;
    }

}

?>