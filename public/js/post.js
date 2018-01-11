$(document).ready(function () {
	load_unseen_notification();

	$('#frmPost').on('submit',function(event){
		event.preventDefault();
		title = $('#txtTitle').val();
		body = $('#txtAreaPost').val();
		if ( title !='' &&  body !='') {
			$.ajax({
				url:config.base_url+'post/insertPost',
				method:'post',
				dataType:'json',
				data:{
					title:title,
					body:body
				},
				success:function(data){
					$('#frmPost')[0].reset();
					load_unseen_notification();
				}
			});
		}else{
			alert('Both Fields are required.');
		}
	});
});

function load_unseen_notification(view='') {
	$.ajax({
		url:config.base_url + 'post/unseenNotificationPost',
		method:'post',
		data:{view:view},
		dataType:'json',
		success:function(data){
			$('.dropdown-menu').html(data.notification);
			if (data.unseen_notification) {
				$('.count').html(data.unseen_notification);
			}
		}
	});
}