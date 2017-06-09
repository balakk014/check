<?php defined('SYSPATH') OR die('No direct access allowed.');?>

<!DOCTYPE html>
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<link rel="shortcut icon" href="<?php echo URL_BASE; ?>public/<?php echo UPLOADS; ?>/favicon/<?php echo SITE_FAVICON;?>" --type="image/x-icon" />
		<title><?php echo $page_title.' | '.SITENAME;?></title>

		<!-- jQuery library -->
			<?php 
			if($action=="dashboard" || $action=="booking"){ ?>
				<script src="https://maps.google.com/maps/api/js?key=<?php echo GOOGLE_MAP_API_KEY; ?>&libraries=places,geometry" type="text/javascript"></script>
				<script type="text/javascript" src="<?php echo URL_BASE;?>public/common/js/gmaps.js"></script>
			<?php } else if($action=="manage_booking") { ?>
				<script src="https://maps.google.com/maps/api/js?key=<?php echo GOOGLE_MAP_API_KEY; ?>&libraries=places,geometry" type="text/javascript"></script>
			<?php } ?>
			<script type="text/javascript" src="<?php echo URL_BASE;?>public/common/js/jquery1.11.0.js"></script>
			<script type="text/javascript" src="<?php echo URL_BASE;?>public/dispatch/vendor/bootstrap/js/jquery.min.js"></script>			
		<!-- jQuery library -->
		<?php /*
		<script>
			var language = <?php echo $js_language; ?>
		</script>
		*/ ?>
		<?php 
			$alert_message = array('email_exists' => 'Email already exists','phone_exists'=>'Phone number already exists');
			$encoded_message = json_encode($alert_message);
		?>
		
		<script>
			var MAP_COUNTRY ="<?php echo MAP_COUNTRY;?>";
			var URL_BASE = "<?php echo URL_BASE;?>";
			var IMGPATH = "<?php echo IMGPATH;?>";
			var SHOW_MAP = "<?php echo SHOW_MAP;?>";
			var ACTION = "<?php echo $action; ?>";
			var LOCATION_LATI = "<?php echo LOCATION_LATI;?>";
			var LOCATION_LONG = "<?php echo LOCATION_LONG;?>";
			var ALERT_MESSAGES = '<?php echo $encoded_message;?>';
			var CUR_DATE_TIME = '<?php echo $company_all_currenttimestamp;?>';
			var COMPANY_ID = '<?php echo $_SESSION['company_id'];?>';
			var DEFAULT_DATE_FORMAT_SCRIPT = '<?php echo DEFAULT_DATE_FORMAT_SCRIPT; ?>';
			var DEFAULT_TIME_SHOW = '<?php echo DEFAULT_TIME_SHOW; ?>';
			var DEFAULT_TIME_FORMAT_SCRIPT = '<?php echo DEFAULT_TIME_FORMAT_SCRIPT; ?>';
		</script>
	</head>
	<body>
		<input type="hidden" name="baseurl" id="baseurl" value="<?php echo URL_BASE; ?>">
		
		<?php  echo new View("admin/taxi_dispatch/header"); ?>
			<div id="content">
				<div class="container_content">
					<?php  //For Notice Messages
						$sucessful_message=Message::display();
						if($sucessful_message) { ?>
							<div id="messagedisplay">
								<div style="width:570px; margin:0 auto;">
									<?php echo $sucessful_message; ?>
								</div>
							</div>
						<?php } ?>
						<?php echo $content;  
					?>
				</div>
			</div>
		<?php echo new View("admin/taxi_dispatch/footer"); ?> 
	</body>
</html>

