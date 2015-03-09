jQuery(document).ready(function($) {

	$("#save").hide();
	$("#change_order").hide();
	$("#slide1").hide();
	
	$("#uploadImage").click(function(event){
		$("#slide1").click();
	});
	
	$("#slide1").change(function(e){
		$inimage=$(this);
		var fileName = $inimage.val();
		fileName = fileName.split('\\');
		$("#imageSrc").html(fileName[fileName.length - 1]);
		
	});


	
	$("#change_order").click(function(event){
		$("#table").toggleClass("connectedSortable");
		 $( ".connectedSortable" ).sortable({
			connectWith: ".connectedSortable"
		}).disableSelection();
		$("#change_order").hide();
		$("#save").show();
	});
	
	 
	$("#table").toggleClass("connectedSortable");
	
	$( "#table" ).sortable({
		opacity: 0.5
	});
	
	$( "#table" ).sortable({
		change: function( event, ui ) {
			$("#save").show();
		}
	});


	$("#save").click(function(event) {
		// List images
		var order = $(".table").sortable("serialize");
		order = order + "&action=vaibhavslider_ajax_update_order";
		//alert(order);
		$(".vp_loader").show();
		$.ajax({
			url : ajaxurl, 
			type : "POST", 
			data : order,			
			success : function(data) 
			{
				//$("#table").append(data);
				loadImageList();
				$(".vp_loader").hide();
				$(".vp_msgBox").html("Image Order Save Successfully...!!!");
				$(".vp_msgBox").fadeIn(1500).delay(3000).fadeOut();
				$("#save").hide();
			}
		});
		
	});  
	

	// Add Images 
	$("#f1").submit(function(event) {
		if($("#slide1").val() != ""){
			$(".vp_loader").show();
			$.ajax({
				url : ajaxurl, 
				type : "POST", 
				data : new FormData(this),
				contentType : false,
				cache : false,
				processData : false, 
				success : function(data) 
				{
					$("#f1")[0].reset();
					$(".vp_loader").hide();
					loadImageList();
					if(data == "0"){
						$(".vp_msgBox").html("Congratulation Slides Added Successfully...!!!");
					}else if(data == "1"){
						$(".vp_msgBox").html("There is some problem in uploading. Please Try again......");
					}else if(data == "2"){
						$(".vp_msgBox").html("This image Cannot Upload. (min width=100 and min height=100 )");
					}
					$(".vp_msgBox").fadeIn(1500).delay(3000).fadeOut();				
					$("#imageSrc").html("No Image Is Selected.. Please Select Image.....");
					$("#f1").reset();
				}
			});
		}else{
			$(".vp_msgBox").html("Slide cannot be added.Please Upload Image First.");
			$(".vp_msgBox").fadeIn(1500).delay(3000).fadeOut();				
			
		}
		return false;
	});
	loadImageList();
	
	
});

function loadImageList(){
		$(".vp_loader").show();
		$.ajax({
			url : ajaxurl, 
			type : "POST", 
			data : { action: 'vaibhavslider_ajax_images_list'},			
			success : function(data) 
			{
				$("#table").html(data);
				$(".vp_loader").hide();
			}
		});
}

function deleteImage(post_ID){
		
		$("#ID_"+post_ID).fadeOut();
		
		$.ajax({
			url : ajaxurl, 
			type : "POST", 
			data : { action: 'vaibhavslider_delete_image',id: post_ID},			
			success : function(data) 
			{
				//loadImageList();
				$( ".selector" ).sortable( "refresh" );
			}
		});
}
