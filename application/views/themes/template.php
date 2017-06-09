<!DOCTYPE html>
<?php defined('SYSPATH') OR die('No direct access allowed.'); ?>
<html>
	<head>
		<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
		<meta name="title" content="<?php echo $meta_title; ?>" />
		<meta name="keywords" content="<?php echo $meta_keywords; ?>" />
		<meta name="description" content="<?php echo $meta_description; ?>" />
		<meta name="google-site-verification" content="21fvh7_QDOghOf9mK9ZSOe1dVVjM-CDFfa48z7mzH7o" />
		<script type="text/javascript" src="<?php echo URL_BASE;?>public/common/js/jquery-1.4.3.min.js"></script> 
		<title><?php  echo $page_title;?></title>
	</head>
	<body>
		<?php echo $content;?>  
	</body>
</html>
<style>
#messagedisplay {
	position:relative;
	width:100%;  
	text-align:center;
}
</style>
