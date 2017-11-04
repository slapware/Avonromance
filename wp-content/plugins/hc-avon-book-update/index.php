<?php
if (!class_exists("ProductDetail")) :
require_once('ProductDetail.php');
endif;

$objDetail= new ProductDetail($myisbn, $locale);
