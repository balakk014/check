<?php defined('SYSPATH') OR die("No direct access allowed.");

$status_val = isset($srch["status"]) ? $srch["status"] :''; 
$keyword = isset($srch["keyword"]) ? $srch["keyword"] :''; 
//print_r($all_rating_drivers);
$total_drivers = count($all_rating_drivers);
foreach($driver_profile as $result){
	$driver_name = $result['name'];
} 
$table_css=$export_excel_button="";
if($total_drivers >0)
{ 
	$table_css='class="table_border"'; 

	$export_excel_button='<input type="button"  title="'.__('').'" class="button" value="'.__('button_export').'" onclick="location.href=\''.URL_BASE.'manage/export?keyword='.$keyword.'\'" />';
}?>
<div class="container_content fl clr">
	<div class="cont_container mt15 mt10">
		<div class="content_middle"> 
			<form method="get" class="form" name="managerating_form" id="managerating_form" action="/manage/managerating_driversview_search">
				<div class="widget">
					<div class="title">
						<h6><?php echo $page_title." - ".ucfirst($driver_name); ?></h6>
					</div>
			<?php
			
			if(count($all_rating_drivers)>0){
			 foreach($all_rating_drivers as $res){
				//if($res->rating != 0) {
				 $comments = $res->comments;
				 $passengers_log_id = $res->passengers_log_id;
				//echo "<pre>";print_r($res);echo "</pre>";
				if($res->profile_image)
				{
					$img1 = 'thumb_'.$res->profile_image;
					$img = URL_BASE.'public/'.UPLOADS.'/passenger/'.$img1;
					
				}else{
					$img1 = URL_BASE."/public/common/images/noimages.jpg"; // thumb
					$img = URL_BASE."/public/common/images/noimages.jpg";//Big
				}
				switch($res->rating){
					case 1: $star = "one";
							break;
					case 2: $star = "two";
							break;
					case 3: $star = "three";
							break;
					case 4: $star = "four";
							break;
					case 5: $star = "five";
							break;
					default: $star = "";
							break;
				}
				 ?>
			<div class="review" />
				<div class="review-head">
					<div style="float:left;" class="review-title"><?php echo ucfirst($res->name);?></div>
					<div style="float:right;"><?php echo Commonfunction::getDateTimeFormat($res->createdate,2);?></div>
				</div>
				<div class="review-text">
					<div class="reviewerprofile">
						<div id="revimg">
							<?php if (file_exists(DOCROOT.'public/'.UPLOADS.'/passenger/'.$img1)){ ?>
							<img src="<?php echo $img;?>" width="50" height="50" />
							<?php } 
							else{
							$no_img = URL_BASE.'public/common/images/noimages.jpg';
							 ?>
							<img src="<?php echo $no_img; ?>" width="50" height="50"/>
							<?php } ?>
						</div>
						<div id="reviewer">
							<span class="review-owner" style="text-align:center;"><?php //echo $res->name;?></span>
						</div>
						<div id="revdate"></div>
					</div>					
					<div class="revieweright"> 
					<?php if($comments){ ?> 
					<div style="float:right;"><a title="<?php echo __('del_comment'); ?>" href="<?php echo URL_BASE.'manage/update_comments/'.$passengers_log_id; ?>" class="deleteicon"></a></div>
					<?php
					}
					?>
					<p class="rating <?php echo $star;?>"></p>
						<?php if($res->comments){echo $res->comments;}else{echo __('no_comments');}?>
					</div>
					
				</div>
			</div>
			<?php //}
			}
		}else{
			echo "<div class='nodata'>".__('no_data')."</div>";
		}
	 ?>
		</div>
</form>
</div>

<div class="bottom_contenttot">
<div class="pagination">
		<?php if(($action != 'packagesearch') && $total_drivers > 0): ?>
		 <?php echo $pag_data->render(); ?>
		<?php endif; ?> 
  </div>
</div>
</div>


<script type="text/javascript">
var confirm_msg =  "<?php echo __('Do you want to delete this ratings');?>";
function deleterating_drivers(id){
	var ans = confirm(confirm_msg);
	if(ans){
		window.location='<?php echo URL_BASE ;?>manage/delete_ratingdrivers/'+id;
	}
}

function delete_comments(passengers_log_id)
{
	alert(passengers_log_id);
	 var url= '/manage/update_comments/?passengers_log_id='+passengers_log_id;
                            $.post(url,function(check){	
								alert(check);exit;	
                               // location.reload(true);
                            });
}

</script>
