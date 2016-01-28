#= require active_admin/base
$(document).ready(function (){
	$('#site_title_image').height("35px");
	// Init for Lock
	
	$('a[data-action="lock"]').click(function(){
		$(".dropdown_menu_list_wrapper").hide();
		if($(".ui-dialog").is(":visible")){ return true};
		setTimeout(function(){
		   $(".ui-dialog-content.ui-widget-content").css('display','none');
		   $(".ui-dialog-buttonset").css('margin-left','65px');
		},50)
	});
	
	//Init for Wipe
	
	$('a[data-action="wipe"]').click(function(){
		$(".dropdown_menu_list_wrapper").hide();
		if($(".ui-dialog").is(":visible")){ return true};
		setTimeout(function(){
		   $(".ui-dialog-content.ui-widget-content").css('display','none');
		   $(".ui-dialog-buttonset").css('margin-left','65px');
		},50)
	});
	
	//Init For Push

	$('a[data-action="push"]').click(function(){
		setTimeout(function(){
		   $(".ui-dialog-buttonset").css('margin-left','65px');
		},50)
	});

	//App delete

	$('a[data-action="destroy"]').click(function() {
		setTimeout(function(){
		   $(".ui-dialog-buttonset").css('margin-left','65px');
		},50)
	})
});