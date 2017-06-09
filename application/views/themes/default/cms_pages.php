<?php  defined('SYSPATH') OR die("No direct access allowed."); 
$content = count($cmscontent);
$page_url = str_replace('/','',$_SERVER['REQUEST_URI']);
if($content>0){
	$pagecontent = $cmscontent[0]['content'];
?>

<div class="about_outer">
	<div class="about_baner">
		<img src="<?php echo URL_BASE;?>public/common/images/about_bg.png" alt="images"/>
	</div> 
	<div class="inner">
		<div class="about_common">
			<div class="about_left">
				<?php if(isset($cmscontent) && $cmscontent[0]['menu'] == "Tutorial") {?>
				<div class="vid_tit">Videos</div>
				<?php } else { ?>
					<?php ucfirst($cmscontent[0]['menu']); ?>
				<?php } ?>
			</div>                                
		</div>
	</div>
</div> 
 <div class="about_bottom_outer">
	   <div class="inner">
		   <?php if($page_url != 'package.html'){  ?> <div class="about_bottom_common"> <?php } ?>
				<?php if($page_url == 'termsconditions.html'){
						if(($pagecontent == '') || ($pagecontent == 'Terms and Conditions')){echo __('default_terms_condition'); }else{echo $pagecontent;}
					}elseif($page_url == 'privacypolicy.html'){
						if($pagecontent == '' || $pagecontent == 'Privacy policy'){echo __('default_privacy_policy'); }else{echo $pagecontent;}
					} else {
						echo $pagecontent;
					} ?>
		   </div>
	   </div>  
 </div>
<?php }else{ ?>
	<div class="about_outer">
		<div class="about_baner">
		<img src="<?php echo URL_BASE;?>public/common/images/about_bg.png" alt="images"/>
	</div>
	</div> <div class="about_bottom_outer">
            <div class="inner">
                <div class="about_bottom_common">
					<div class="key_block_outer1">
						<?php if($page_url == 'termsconditions.html'){?>
                        <h2><?php echo __('default_terms_condition'); ?></h2>
                        <?php } elseif($page_url == 'privacypolicy.html') { ?>
                        <h2><?php echo __('default_privacy_policy'); ?></h2>
                        <?php } else { ?>
                        <h2><?php echo __('no_data'); ?></h2>
                        <?php } ?>
                    </div>                           
                </div>
            </div>
        </div> 
<?php }
  ?>

