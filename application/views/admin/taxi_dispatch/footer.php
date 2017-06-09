<!--All in one JS File [Below mentioned files are combined]-->
<script type="text/javascript" src="<?php echo URL_BASE;?>public/dispatch/vendor/bootstrap/js/mainbase.js"></script>
<script src="<?php echo URL_BASE; ?>public/dispatch/vendor/bootstrap/js/enscroll-0.6.2.min.js"></script>
<script type="text/javascript">	
    function add_book_int(){
    	//to reset the form fields
    		$("#firstname").val("");
    		$("#email").val("");
    		$("#country_code").val("");
    		$("#phone").val("");
    		$("#current_location").val("");
    		$("#pickup_lat").val("");
    		$("#pickup_lng").val("");
    		$("#drop_location").val("");
    		$("#drop_lat").val("");
    		$("#drop_lng").val("");
    		$("#notes").val("");
    		var filterModel = $("#select_taxi_model").val();
    		$("#taxi_model").val(filterModel);
    		 $("#email").removeAttr("readonly");
    		 $("#firstname").removeAttr("readonly");
    		 $("#phone").removeAttr("readonly");
    		 $("#country_code").removeAttr("readonly");
    		//to reset the distance and fare texts
    		$("#find_duration").html("<?php echo __('zero_mins'); ?>");
    		$("#find_km").html("<?php echo __('zero_distance'); ?>");
    		$("#min_fare").html("0");
    		//to hide the error messages
    		$("label.error").html("");
    		<?php if($_SESSION['user_type']=="A"){ ?>
    			var company = $("#select_company").val();
    			/*if(company ==0){
    				alert('Please select company for dispatch');
    				return false;
    			}*/
    		<?php } ?>
    		$("#add_booking_tab").html('<?php echo __("add_booking"); ?>');
    		var addbook = $("#add_book_tab").attr("class");
    		$("#edit_book_tab").hide();
    		$("#edit_book_tab").removeClass('edit_book_active');
    		$("#add_book_tab").addClass('add_book_active');
    		$("#add_book_tab").show();
    		$("#find_km").html("<?php echo __('zero_distance'); ?>");
    		//to display default pickup time as current date
    		var today = new Date();
    		var Y = today.getFullYear(),
    		    month = today.getMonth()+1,
    		    dateVal = today.getDate(),
    			h = today.getHours(),
    			m = today.getMinutes(),
    			s = today.getSeconds();
    			month = (month < 10) ? "0" + month : month;
    			dateVal = (dateVal < 10) ? "0" + dateVal : dateVal;
    			h = (h < 10) ? "0" + h : h;
    			m = (m < 10) ? "0" + m : m;
    			s = (s < 10) ? "0" + s : s;
    		var pickupTime = Y + "-" + month + "-" + dateVal + " " + h + ":" + m + ":" + s;
    		$("#pickup_date").val(pickupTime);
    		//To get tax value to display
    		var selectedCompany = $("#select_company").val();
    		var dataS = "company="+selectedCompany;
    		var url_path = "<?php echo URL_BASE;?>taxidispatch/gettaxval";
    		$.ajax({
    			type: "POST",
    			url:url_path,
    			data: dataS, 
    			async: true,
    			success:function(data){
    				$("#company_tax").val(data);
    				$("#vat_tax").html(data);
    			},
    			error:function() {
    				//alert('failed'); 
    			}
    		});
    		var selectedCompany = $("#select_company").val();
    		
    		//function to load the map in add booking
    		initialize();
    	
    }
    
    $(window).load(function() {
    	$(".loader").fadeOut("fast");		
    });
    
    /* Menu Toggle Script*/
    $("#menu-toggle").click(function(e) {
    	e.preventDefault();
    	$("#wrapper").toggleClass("toggled");
    });
    
    $('#myTab a').click(function(e) {
    	e.preventDefault()
    	$(this).tab('show')
    });
    
    $("#firstname,#edit_firstname" ).keyup(function(event) {
    	//to allow left and right arrow key move
    	if(event.which>=37 && event.which<=40)
    	{
    		return false;
    
    	}
    	this.value = this.value.replace(/[`~!@#$%^&*()_|+\-=?;:'",.<>\{\}\[\]\\\/0-9]/gi, '');
    });
    
    $("#notes,#edit_notes" ).keyup(function(event) {
    	//to allow left and right arrow key move
    	if(event.which>=37 && event.which<=40)
    	{
    		return false;
    
    	}
    	this.value = this.value.replace(/[`~!@#$%^*_|+\=?;:'".<>\{\}\[\]\\\/]/gi, '');
    });
    
    $("#phone,#edit_phone" ).keyup(function(event) {
    	//to allow left and right arrow key move
    	if(event.which>=37 && event.which<=40)
    	{
    		return false;
    
    	}
    	//this.value = this.value.replace(/[`~!@#$%^&*()_|+\-=?;:'",.<>\{\}\[\]\\\/A-Z]/gi, '');
    	this.value = this.value.replace(/[`~!@#$%^&*()\s_|+\-=?;:'",.<>\{\}\[\]\\\/A-Z]/gi, '');
    });
    $("#country_code,#edit_country_code" ).keyup(function(event) {
    	//to allow left and right arrow key move
    	if(event.which>=37 && event.which<=40)
    	{
    		return false;
    
    	}
    	this.value = this.value.replace(/[`~!@#$%^&*\s_|\-=?;:'",.<>\{\}\[\]\\\/A-Z]/gi, '');
    });
    
    
    $(document).ready(function() {
    
    	$(':checkbox').each(function() {
    		if($(this).val() != 1) { this.checked = true; }
    	});
    
    	var today = new Date();
    	$("#pickup_date").datetimepicker({
    		format: "yyyy-mm-dd hh:ii:ss",
    		autoclose: true,
    		startDate: today,
    		todayBtn: true,
    		//endDate: '+4d',
    		pickerPosition: "top-right"
    	})
    
    	$("#edit_pickup_date").datetimepicker({
    		format: "yyyy-mm-dd hh:ii:ss",
    		autoclose: true,
    		startDate: today,
    		todayBtn: true,
    		//endDate: '+4d',
    		pickerPosition: "top-right"
    	});
    
    	$(".today").html('Now');
    	
    	//For Tab Menu Start
    	$("#add_book_tab").hide();
    	$("#edit_book_tab").hide();
    	$("#eb_tab").hide();
    	//$('#eb_tab').attr('disabled', true);
    	//$('#eb_tab *').prop('disabled',true);
    	
    	
    	parent.top.$("#client-ui-answer").click(function(){
    	
    	 add_book_int();
    	 phnuber=parent.top.$("#phone_call").val();
    		var purl="<?php echo URL_BASE;?>users/phone_check?phone="+ encodeURIComponent(phnuber);
    		    //purl= encodeURI(purl);
    		   
    			$.post(purl, function(data, status){
    			 arr = data.split('**');
    			  if(arr.length==2){
    				 $("#country_code").val(arr[0].trim());
    				 $("#phone").val(arr[1].trim());
    			 }else{
    				 $("#country_code").val(arr[0].trim());
    				 $("#phone").val(arr[1].trim());
    				 $("#firstname").val(arr[2].trim());
    				 $("#email").val(arr[3].trim());
    			 }
    			 
    			
    			});
    	
    	
    	
        });
    	
    	parent.top.$("#dial-input-button").click(function(){
    		add_book_int();
    		phnuber=parent.top.$("#dial-phone-number").val();
    		var purl="<?php echo URL_BASE;?>users/phone_check?phone="+ encodeURIComponent(phnuber);
    		    //purl= encodeURI(purl);
    		   
    			$.post(purl, function(data, status){
    			 arr = data.split('**');
    			 if(arr.length==2){
    				 $("#country_code").val(arr[0].trim());
    				 $("#phone").val(arr[1].trim());
    			 }else{
    				 $("#country_code").val(arr[0].trim());
    				 $("#phone").val(arr[1].trim());
    				 $("#firstname").val(arr[2].trim());
    				 $("#email").val(arr[3].trim());
    			 }
    			 
    			
    			});
    			
    			/*window.parent.$(".twilio-call").click(function(){
    		    add_book_int();
    	        }); */
    	
    	});
    		
    
    	$("#add_booking_tab").click(function() {
    	 add_book_int();
    	});
    	
    	$("#edit_booking_tab").click(function() {
    		var editbook = $("#edit_book_tab").attr("class");
    		$("#add_book_tab").hide();
    		$("#edit_book_tab").addClass('edit_book_active');
    		$("#edit_book_tab").show();
    		/*if (editbook == "edit_book_active") {
    			$("#edit_book_tab").removeClass('edit_book_active');
    			$("#edit_book_tab").hide();
    
    		} else {
    			$("#add_book_tab").hide();
    			$("#edit_book_tab").addClass('edit_book_active');
    			$("#edit_book_tab").show();
    		}*/
    	});
                   
                   	$(".popup_close_button").click(function() {			 				
    			$("#add_book_tab").hide(); 
                                   $("#edit_book_tab").hide();
                                   $("#add_booking_tab").html('<?php echo __("add_booking"); ?>');
                           });
                                   
    	//For Tab Menu End
    
    	$("#sidebar-wrapper").hide();
    	$("#page-content-wrapper").click(function() {
    		$("#sidebar-wrapper").show();
    		$("#menu-toggle").hide();
    	});
    
    	$(".close_side_bar").click(function() {
    		<?php if($_SESSION['user_type'] == 'A') { ?>
    		//to deselect the selected company
    		$("#select_company").val("0");
    		driver_list_with_status();
    		all_booking_manage_list();
    		<?php } ?>
    		//to get the default data - start
    		/*map_recur();
    		driver_status_dets(); */
    		
    		$("#edit_book_tab").removeClass('edit_book_active');
    		$("#eb_tab").removeClass('active');
    		//to get the default data - end
    		$("#menu-toggle").show();
    		$("#sidebar-wrapper").hide();
    		$("#wrapper").addClass("toggled");
    	});
    	
    	/*Window Height Script Start*/
    	var blog_height = $(window).height();
    	$(".lft_outer").css({
    		'height': blog_height,
    		'overflow-y': 'hidden'
    	})	 				
    	var blog_height = $(window).height() - 379;
    	$(".driver_status_height").css({
    		'height': blog_height,
    		'overflow-y': 'hidden'
    	})
    //		var blog_width = $(window).width();
    //		$(".manage_booking_bottom_outer").css({
    //			'width': blog_width,
    //			'overflow-y': 'hidden'
    //		})		
    //		var manage_booking_bottom = ($(window).width() * 80) / 100;		
    //		$(".manage_booking_bottom_scroll").css({
    //			'width': manage_booking_bottom,
    //			'overflow-y': 'hidden'
    //		})		
    	var rgt_outer = ($(window).width() * 100) / 500;                                
    	$(".rgt_outer").css({
    		'width': rgt_outer,
    		'overflow-y': 'hidden'
    	})		
    	var map_height = ($(window).height() * 60) / 100;
    	var dr_status_height = ($(window).height() * 36) / 100;                                
    	$(".driver_status_height_outer_bottom").css({
    		'height': dr_status_height,
    		'overflow-y': 'hidden'
    	})
    	$(".driver_status_height_outer_top").css({
    		'height': map_height,
    		'overflow-y': 'hidden'
    	})
    	var blog_height = $(window).height() - 379;
    	$(".driver_status_height_re_act").css({
    			'height': blog_height,
    			'overflow-y': 'scroll'
    	})
                   
                   var blog_height = $(window).height();
    	$(".all_booking_manage_scroll_one").css({
    		'height': blog_height,
    		'overflow-y': 'hidden'
    	})
    //		var blog_height = $(window).height() - 430;
    //		$(".all_booking_manage_scroll").css({
    //				'height': blog_height,
    //				'overflow-y': 'scroll'
    //		})
    $('.table_body').enscroll({
       showOnHover: false,
       verticalTrackClass: 'track3',
       verticalHandleClass: 'handle3'
    });
                   var blog_height = $(window).height() - 228;
                  $(".table_body").css({
                   'height': blog_height
                  // 'overflow-y': 'auto'
                  })
    	var blog_height = $(window).height() - 420;
    	$("#taxi_scroll_one").css({
    			'height': blog_height,
    			'overflow-y': 'auto'
    	}) 
    	
    	
    	/*Window Resize Script Start*/
    	
    	$(window).on('resize', function() {
    		var blog_height = $(window).height();
    		$(".lft_outer").css({
    			'height': blog_height,
    			'overflow-y': 'hidden'
    		})	
    		var blog_height = $(window).height();
    		$(".friends-blog.driver_status_bottom").css({
    			'height': blog_height				
    		})				 
    //			var blog_width = $(window).width();
    //			$(".manage_booking_bottom_outer").css({
    //				'width': blog_width,
    //				'overflow-y': 'hidden'
    //			})		
    //			var manage_booking_bottom = ($(window).width() * 80) / 100;		
    //			$(".manage_booking_bottom_scroll").css({
    //				'width': manage_booking_bottom,
    //				'overflow-y': 'hidden'
    //			})		
    		var rgt_outer = ($(window).width() * 100) / 500;                                
    		$(".rgt_outer").css({
    			'width': rgt_outer,
    			'overflow-y': 'hidden'
    		})		
    		var map_height = ($(window).height() * 60) / 100;
    		var dr_status_height = ($(window).height() * 36) / 100;                                
    		$(".driver_status_height_outer_bottom").css({
    			'height': dr_status_height,
    			'overflow-y': 'hidden'
    		})
    		$(".driver_status_height_outer_top").css({
    			'height': map_height,
    			'overflow-y': 'hidden'
    		})
    		var blog_height = $(window).height() - 290;			
    		$(".driver_status_height_re_act").css({
    				'height': blog_height,
    				'overflow-y': 'scroll'
    		})			
    		/* var blog_height = $(window).height() - 400;
    		$("#taxi_scroll_one").css({
    				'height': blog_height,
    				//'overflow-y': 'scroll'
    		})
    		$('#tab-content_scroll,#taxi_scroll,#taxi_scroll_one,#taxi_scroll_two,#taxi_scroll_three,.friends-blog,.friends-blog ul').enscroll({
    			showOnHover: false,
    			verticalTrackClass: 'track3',
    			verticalHandleClass: 'handle3'
    		}); */
    	});
    	
    	/*Window Height Script End*/
    	
    	$("#model_close_one").click(function() {
    		window.location = "<?php echo URL_BASE; ?>taxidispatch/dashboard";
    	});
    
    	$("#model_close_two").click(function() {
    		window.location = "<?php echo URL_BASE; ?>taxidispatch/dashboard";
    	});
    		
    });
    
    $('#driver_details p').click(function() {
    						var detailsid = this.id;
    						var findimg = detailsid.split('_');
    
    						var pass_logid = $('#passenger_log_id').val();	
    						
    						var dataS = "pass_logid="+pass_logid+"&driver_id="+findimg[0]+"&taxi_id="+findimg[1]+"&driver_away_in_km="+findimg[2];	
    						
    						$("#show_process").html('<img src="<?php echo IMGPATH; ?>loader.gif">');
    						$.ajax
    						({ 			
    							type: "GET",
    							url: "<?php echo URL_BASE;?>taxidispatch/updatebooking", 
    							data: dataS, 
    							cache: false, 
    							dataType: 'html',
    							success: function(response) 
    							{ 		
    								$("#show_process").html('');
    								//console.log(response);
    								//document.location.href="<?php echo URL_BASE;?>tdispatch/managebooking/#stuff";
    								 window.location="<?php echo URL_BASE;?>taxidispatch/dashboard";
    							} 
    							 
    						});	
    					});
    
</script>
<script>
    function scrolify(tblAsJQueryObject, height){
        var oTbl = tblAsJQueryObject;
    
        // for very large tables you can remove the four lines below
        // and wrap the table with <div> in the mark-up and assign
        // height and overflow property  
        var oTblDiv = $("<div/>");
        oTblDiv.css('height', height);
        // oTblDiv.css('overflow-y','auto'); 
        oTblDiv.addClass('newtable');
        oTbl.wrap(oTblDiv);
    
        // save original width
        oTbl.attr("data-item-original-width", oTbl.width());
        oTbl.find('thead tr td').each(function(){
            $(this).attr("data-item-original-width",$(this).width());
        }); 
        oTbl.find('tbody tr:eq(0) td').each(function(){
            $(this).attr("data-item-original-width",$(this).width());
        });                 
    
    
        // clone the original table
        var newTbl = oTbl.clone();
    
        // remove table header from original table
        oTbl.find('thead tr').remove();                 
        // remove table body from new table
        newTbl.find('tbody tr').remove();   
    
        oTbl.parent().parent().prepend(newTbl);
        newTbl.wrap("<div/>");
    
        // replace ORIGINAL COLUMN width                
        newTbl.width(newTbl.attr('data-item-original-width'));
        newTbl.find('thead tr td').each(function(){
            $(this).width($(this).attr("data-item-original-width"));
        });     
        oTbl.width(oTbl.attr('data-item-original-width'));      
        oTbl.find('tbody tr:eq(0) td').each(function(){
            $(this).width($(this).attr("data-item-original-width"));
        });                 
    }    
</script>
