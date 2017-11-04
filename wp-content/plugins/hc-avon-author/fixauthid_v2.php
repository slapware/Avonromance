<?php
// fix postmeta to place serialized book-author post id in meta-key book-@author slapware
error_reporting(0); // Turn off all error reporting

if (!class_exists("AuthorDetail")) :
require_once('AuthorDetail.php');
endif;

function init_db() {
    global $mysql_database;

 //   $dsn = "mysql:host=10.41.74.72;dbname=wordpress";
    	$dsn = "mysql:host=137.116.114.77;dbname=wordpress";
    $opt = array(
        // any occurring errors wil be thrown as PDOException
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        // an SQL command to execute when connecting
        PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'UTF8'"
    );
    $db = new PDO($dsn, "wordpressuser", "password");

    return $db;
}	// init_db

$db = init_db();
//$post = 7894;
//$sql = 'select post_id, meta_value from wp_postmeta where meta_value = "a:1:{i:0;N;}" and post_id = 7894';
$query = $db->prepare('select post_id, meta_value from wp_postmeta where meta_value = "a:1:{i:0;N;}"');
//$query->bindParam(':post', $post);
try
{
    $result = $query->execute();
}
catch (PDOException $Exception)
{
    //display custom message
    echo $Exception->getMessage();
}
if(!$result)
{
    echo "get_credentials DB query failed !\n";
    $err = $query->errorInfo();
    echo print_r($err, true);
}
while($row = $query->fetch(PDO::FETCH_ASSOC)) {
    $postid = $row['post_id'];
    $metaval = $row['meta_value'];
    // loop starts here
    $sql2 = $db->prepare('select meta_value from wp_postmeta where meta_key = "Author_GID" and post_id =:post2');
    $sql2->bindParam(':post2', $postid);
    try
    {
        $result2 = $sql2->execute();
    }
    catch (PDOException $Exception)
    {
        //display custom message
        echo $Exception->getMessage();
    }
    if(!$result2)
    {
        echo "get_credentials DB query failed !\n";
        $err = $sql2->errorInfo();
        echo print_r($err, true);
    }
    while($row2 = $sql2->fetch(PDO::FETCH_ASSOC)) {
        $metaval2 = $row2['meta_value'];
    }
    // new added sectio here
    $objDetail= new AuthorDetail($metaval2);
    if (isset($objDetail->title) ) {
        $authquery = $db->prepare('select post_id from wp_posts where post_title = :authname');
        $auth_found = $objDetail->title;
        $authquery->bindParam(':authname', $auth_found);
        $authresult = $authquery->execute();
        try
        {
            $authresult = $authquery->execute();
        }
        catch (PDOException $Exception)
        {
            //display custom message
            echo $Exception->getMessage();
        }
        if(!$authresult)
        {
            echo "Add author GID : " . $metaval2 . "<br>";
            unset ($objDetail);
            continue;
        }
        while($authrow = $authquery->fetch(PDO::FETCH_ASSOC)) {
            $newpost = $authrow['ID'];
        }
        if($newpost == 0 || !isset($newpost)) {
            echo "Add author GID : " . $metaval2 . PHP_EOL;
            unset ($objDetail);
            continue;
        }
        $newid = 'a:1:{i:0;s:' . strlen($newpost) . ':"' . $newpost . '";}';

//        $newid = 'a:1:{i:0;s:' . strlen($metaval2) . ':"' . $metaval2 . '";}';

    $sql3 = $db->prepare('UPDATE wp_postmeta SET meta_value = :newvalue WHERE post_id =:post3 AND meta_key = "book_author"');
    $sql3->bindParam(':post3', $postid);
    $sql3->bindParam(':newvalue', $newid);
    try
    {
        $result3 = $sql3->execute();
    }
    catch (PDOException $Exception)
    {
        //display custom message
        echo $Exception->getMessage();
    }
    if(!$result3)
    {
        echo "get_credentials DB query failed !\n";
        $err = $sql3->errorInfo();
        echo print_r($err, true);
    }
        unset ($objDetail);
    } // if (isset($objDetail->title) )

    // loop ends here
}

echo "The fat lady sings";