<?php defined('SYSPATH') OR die("No direct access allowed."); ?>

<button type="button" class="export_me_menu greyishB">Export</button>

<?php if($export_table_count > 0){ $row_create= ceil($export_table_count / 100); } else { $row_create = 1; } ?>
<?php if($export_table_count > 20000){ $row_create_2= ceil($export_table_count / 20000); } else { $row_create_2 = 1; }?>
<div class="export_me_menu_div"> <a class="export_me_menu_div_close"></a>
    <ul>
        <li>
            <label>Type</label>
            <b>:</b>
            <div class="input_box">
                <input type="radio" class='records_type2'  checked value="1"><span>XLS</span>&nbsp;<input class='records_type1' type="radio" value="2"><span>PDF</span><br>
            </div>
        </li>
        <li style="display:none"  class="records_type1_select">
            <label>No of rows</label>
            <b>:</b>
            <div class="input_box">
                <select class='records_from_select1'>
                <?php $km=1;for($il=1;$il<=$row_create;$il++){ ?>
                <option value="<?php  echo $km.'-'.($il*100);?>">from <?php echo number_format($km);?> - <?php echo number_format($il*100);?></option>
                <?php $km=($il*100)+1;}?>
                </select>
            </div>
        </li>
        </li>
        <li class="records_type2_select">
            <label>No of rows</label>
            <b>:</b>
            <div class="input_box">
                <div  class="records_type2_select">
                <select class='records_from_select2'>
                <?php $km=1;for($il=1;$il<=$row_create_2;$il++){ ?>
                <option value="<?php  echo $km.'-'.($il*20000);?>">from <?php echo number_format($km);?> - <?php echo number_format($il*20000);?></option>
                <?php $km=($il*20000)+1;}?>
                </select>
                </div>
            </div>
        </li>
        <li>
            <label>&nbsp;</label>
            <b>&nbsp;</b>
            <div class="new_button">
                <input type="button" class="export_me_menu_download" value="Download" />
            </div>
        </li>
    </ul>


</div>
<div id="fade"></div>
<script type="text/javascript">
	$(document).ready(function(){
		$( ".export_me_menu" ).live( "click", function() {
			$('.export_me_menu').hide();
			$('.export_me_menu_div').show();
			$('#fade').show();
		});
		$( ".export_me_menu_div_close" ).live( "click", function() {
			$('.export_me_menu').show();
			$('.export_me_menu_div').hide();
			$('#fade').hide();
		});
		$( ".records_type2,.records_type1" ).live( "click", function() {
			$('.records_type2,.records_type1').removeAttr('checked');
			$(this).attr('checked','checked');
			$('.records_type2_select,.records_type1_select').hide();
			classname=$(this).attr('class');
			$("."+classname+'_select').show();
		});
		$( ".export_me_menu_download" ).live( "click", function() {
			if($('.records_type1').is(':checked')) { 
				var send=$('.records_from_select1').val()+'-2';
			}else{
				var send=$('.records_from_select2').val()+'-1';
			}
			$.ajax({
			url:"<?php echo URL_BASE;?>manage/set_ajax_session?set="+send,
			success:function(data){
			$('.export_me_menu').show(); $('.export_me_menu_div').hide();
				<?php if(isset($_GET['search_user'])) {?>
				location.reload();
				<?php }else{?>
				$('form').submit();
				<?php }?>
				$('#fade').hide();
			},
			error:function(data)
			{
			   alert('Retry again');
			}
		});	
			
		});
     });
</script>
