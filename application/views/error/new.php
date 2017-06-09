<?php defined('SYSPATH') OR die('No direct access allowed.'); ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>


<link rel="shortcut icon" href="<?php echo URL_BASE;?>public/common/images/favicon.png" />

<?php /* */ ?>

	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />


<title>Error page</title>
<link rel="stylesheet" type="text/css" href="<?php echo SCRIPTPATH.'jquery.tweet.css';?>" media="screen" />	
<link rel="stylesheet" type="text/css" href="<?php echo THICKBOX_CSSPATH;?>" media="screen" />
        <link rel="stylesheet" type="text/css" href="<?php echo URL_BASE;?>public/common/css/style.css" />
        <link rel="stylesheet" type="text/css" href="<?php echo URL_BASE;?>public/common/css/media_style.css" /
</head>
<body>
<div id="wrapper">

  <?php  echo new View(USERVIEW."header"); ?>

<div class="caregivers_list_content_outer1 clearfix">
           <div class="banner_inner">
           		<div class="banner clearfix">
           					<div class="flexslider">
           						 <?php //echo $content;?>
           						 <img src="<?php echo URL_BASE;?>public/common/images/404.jpg"  style="margin-left:207px;">
           				 
           					</div>
           		</div>
            </div>
        </div>
	

<?php  echo new View(USERVIEW."footer"); ?>  
</div>
<script type="text/javascript" >
function equalHeight(group) {
   tallest = 0;
   group.each(function() {
      thisHeight = $(this).height();
      if(thisHeight > tallest) {
         tallest = thisHeight;
      }
   });
   group.height(tallest);
}
$(document).ready(function() {



	$("#login").click(function(){

		$("#login_form").slideToggle("slow");
	});
   var a=equalHeight($(".equal_height"));

});
  

</script>

</body>
</html>

