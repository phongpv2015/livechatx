
	var windowFocus 		= true;
	var username;
	var chatHeartbeatCount 	= 0;
	var minChatHeartbeat 	= 1000;
	var maxChatHeartbeat 	= 33000;
	var chatHeartbeatTime 	= minChatHeartbeat;
	var originalTitle;
	var blinkOrder 			= 0;

	var chatboxFocus 		= new Array();
	var newMessages 		= new Array();
	var newMessagesWin 		= new Array();
	var chatBoxes 			= new Array();

	// Sử dụng alias jQuery thay cho $
	jQuery.noConflict();

	jQuery(document).ready(function(){
		originalTitle = document.title;
		startChatSession();

		jQuery([window, document]).blur(function(){
			windowFocus = false;
		}).focus(function(){
			windowFocus = true;
			document.title = originalTitle;
		});
	});


	function restructureChatBoxes() {
		align = 0;
		for (x in chatBoxes) {
			chatboxtitle = chatBoxes[x];

			if (jQuery("#chatbox_"+chatboxtitle).css('display') != 'none') {
				if (align == 0) {
					jQuery("#chatbox_"+chatboxtitle).css('right', '20px');
				} else {
					width = (align)*(225+7)+20;
					jQuery("#chatbox_"+chatboxtitle).css('right', width+'px');
				}
				align++;
			}
		}
	}


	function chatWith(chatuser,id) {
		createChatBox(chatuser,id);
		jQuery("#chatbox_"+chatuser+" .chatboxtextarea").focus();
	}


	function createChatBox(chatboxtitle,id,minimizeChatBox) {
		if (jQuery("#chatbox_"+chatboxtitle).length > 0) {
			if (jQuery("#chatbox_"+chatboxtitle).css('display') == 'none') {
				jQuery("#chatbox_"+chatboxtitle).css('display','block');
				restructureChatBoxes();
			}
			jQuery("#chatbox_"+chatboxtitle+" .chatboxtextarea").focus();
			return;
		}

		jQuery(" <div />" ).attr("id","chatbox_"+chatboxtitle)
				.addClass("chatbox")
				.html('<div class="chatboxhead"><div class="chatboxtitle">'
					+chatboxtitle
					+'</div><div class="chatboxoptions"><a href="javascript:void(0)" onclick="javascript:toggleChatBoxGrowth(\''
					+chatboxtitle+'\','+id+')">-</a> <a href="javascript:void(0)" onclick="javascript:closeChatBox(\''
					+chatboxtitle+'\','+id+')">X</a></div><br clear="all"/></div><div class="chatboxcontent"></div><div class="chatboxinput"><textarea class="chatboxtextarea" onkeydown="javascript:return checkChatBoxInputKey(event,this,\''
					+chatboxtitle+'\','+id+');"></textarea></div>')
				.appendTo(jQuery( "body" ));
				   
		jQuery("#chatbox_"+chatboxtitle).css('bottom', '0px');
		
		chatBoxeslength = 0;

		for (x in chatBoxes) {
			if (jQuery("#chatbox_"+chatBoxes[x]).css('display') != 'none') {
				chatBoxeslength++;
			}
		}

		if (chatBoxeslength == 0) {
			jQuery("#chatbox_"+chatboxtitle).css('right', '20px');
		} else {
			width = (chatBoxeslength)*(225+7)+20;
			jQuery("#chatbox_"+chatboxtitle).css('right', width+'px');
		}
		
		chatBoxes.push(chatboxtitle);

		if (minimizeChatBox == 1) {
			minimizedChatBoxes = new Array();

			if (jQuery.cookie('chatbox_minimized')) {
				minimizedChatBoxes = jQuery.cookie('chatbox_minimized').split(/\|/);
			}
			minimize = 0;
			for (j=0;j<minimizedChatBoxes.length;j++) {
				if (minimizedChatBoxes[j] == chatboxtitle) {
					minimize = 1;
				}
			}

			if (minimize == 1) {
				jQuery('#chatbox_'+chatboxtitle+' .chatboxcontent').css('display','none');
				jQuery('#chatbox_'+chatboxtitle+' .chatboxinput').css('display','none');
			}
		}

		chatboxFocus[chatboxtitle] = false;

		jQuery("#chatbox_"+chatboxtitle+" .chatboxtextarea").blur(function(){
			chatboxFocus[chatboxtitle] = false;
			jQuery("#chatbox_"+chatboxtitle+" .chatboxtextarea").removeClass('chatboxtextareaselected');
		}).focus(function(){
			chatboxFocus[chatboxtitle] = true;
			newMessages[chatboxtitle] = false;
			jQuery('#chatbox_'+chatboxtitle+' .chatboxhead').removeClass('chatboxblink');
			jQuery("#chatbox_"+chatboxtitle+" .chatboxtextarea").addClass('chatboxtextareaselected');
		});

		jQuery("#chatbox_"+chatboxtitle).click(function() {
			if (jQuery('#chatbox_'+chatboxtitle+' .chatboxcontent').css('display') != 'none') {
				jQuery("#chatbox_"+chatboxtitle+" .chatboxtextarea").focus();
			}
		});

		jQuery("#chatbox_"+chatboxtitle).show();
		
	}

	// Server trả về response ứng với request bị treo vừa được gửi đi (trong thời hạn time-out) và đóng nó lại
	// Sau đó client sử dụng response đó để gửi 1 request long-lived mới
	function chatHeartbeat(){

		var itemsfound = 0;
		
		if (windowFocus == false) {
	 
			var blinkNumber = 0;
			var titleChanged = 0;
			for (x in newMessagesWin) {
				if (newMessagesWin[x] == true) {
					++blinkNumber;
					if (blinkNumber >= blinkOrder) {
						document.title = x+' nói...';
						titleChanged = 1;
						break;	
					}
				}
			}
			
			if (titleChanged == 0) {
				document.title = originalTitle;
				blinkOrder = 0;
			} else {
				++blinkOrder;
			}

		} else {
			for (x in newMessagesWin) {
				newMessagesWin[x] = false;
			}
		}

		for (x in newMessages) {
			if (newMessages[x] == true) {
				if (chatboxFocus[x] == false) {
					jQuery('#chatbox_'+x+' .chatboxhead').toggleClass('chatboxblink');
				}
			}
		}
		
		jQuery.ajax({
			url: config.base_url + "chat/successChat",
			type:'get',
			cache: false,
			dataType: "json",
			data:{
				action:'chatheartbeat'
			},
			success: function(data) {
				jQuery.each(data.items, function(i,item){
					if (item)	{ // fix strange ie bug

						chatboxtitle = item.f;

						if (jQuery("#chatbox_"+chatboxtitle).length <= 0) {
							createChatBox(chatboxtitle);
						}
						if (jQuery("#chatbox_"+chatboxtitle).css('display') == 'none') {
							jQuery("#chatbox_"+chatboxtitle).css('display','block');
							restructureChatBoxes();
						}
						
						if (item.s == 1) {
							item.f = username;
						}

						if (item.s == 2) {
							jQuery("#chatbox_"+chatboxtitle+" .chatboxcontent").append('<div class="chatboxmessage"><span class="chatboxinfo">'+item.m+'</span></div>');
						} else {
							newMessages[chatboxtitle] = true;
							newMessagesWin[chatboxtitle] = true;
							jQuery("#chatbox_"+chatboxtitle+" .chatboxcontent").append('<div class="chatboxmessage"><span class="chatboxmessagefrom">'+item.f+':&nbsp;&nbsp;</span><span class="chatboxmessagecontent">'+item.m+'</span></div>');
						}

						jQuery("#chatbox_"+chatboxtitle+" .chatboxcontent").scrollTop(jQuery("#chatbox_"+chatboxtitle+" .chatboxcontent")[0].scrollHeight);
						itemsfound += 1;
					}
				});

				chatHeartbeatCount++;

				// Với mỗi lần request thành công , itemsfound và chatHeartbeatCount tăng lên 1
				// 9 request đầu, luân phiên gửi mỗi 1s
				// 9 request tiếp theo, thời gian luân phiên gửi request tăng gấp đôi
				// Cứ như thế cho đến khi khoảng thời gian giữa mỗi lần gửi request là 33s
				if (itemsfound > 0) {
					chatHeartbeatTime = minChatHeartbeat;
					chatHeartbeatCount = 1;
				} else if (chatHeartbeatCount >= 10) {
					chatHeartbeatTime *= 2;
					chatHeartbeatCount = 1;
					if (chatHeartbeatTime > maxChatHeartbeat) {
						chatHeartbeatTime = maxChatHeartbeat;
					}
				}

				// Thời hạn của request
				// Thời hạn này chỉ có tác dụng 1 lần đối với request hiện tại
				// Ở request tiếp theo chatHeartbeatTime có thể đã bị thay đổi
				setTimeout('chatHeartbeat();',chatHeartbeatTime);
			}
		});
	}


	function closeChatBox(chatboxtitle,id) {
		jQuery('#chatbox_'+chatboxtitle).css('display','none');
		restructureChatBoxes();

		jQuery.post(config.base_url + "chat/successChat?action=closechat", {chatbox: id} , function(data){	
		});

	}


	function toggleChatBoxGrowth(chatboxtitle,id) {
		if (jQuery('#chatbox_'+chatboxtitle+' .chatboxcontent').css('display') == 'none') {  
			
			var minimizedChatBoxes = new Array();
			
			if (jQuery.cookie('chatbox_minimized')) {
				minimizedChatBoxes = jQuery.cookie('chatbox_minimized').split(/\|/);
			}

			var newCookie = '';

			for (i=0;i<minimizedChatBoxes.length;i++) {
				if (minimizedChatBoxes[i] != chatboxtitle) {
					newCookie += chatboxtitle+'|';
				}
			}

			newCookie = newCookie.slice(0, -1)


			jQuery.cookie('chatbox_minimized', newCookie);
			jQuery('#chatbox_'+chatboxtitle+' .chatboxcontent').css('display','block');
			jQuery('#chatbox_'+chatboxtitle+' .chatboxinput').css('display','block');
			jQuery("#chatbox_"+chatboxtitle+" .chatboxcontent").scrollTop(jQuery("#chatbox_"+chatboxtitle+" .chatboxcontent")[0].scrollHeight);
		} else {
			
			var newCookie = chatboxtitle;

			if (jQuery.cookie('chatbox_minimized')) {
				newCookie += '|'+jQuery.cookie('chatbox_minimized');
			}


			jQuery.cookie('chatbox_minimized',newCookie);
			jQuery('#chatbox_'+chatboxtitle+' .chatboxcontent').css('display','none');
			jQuery('#chatbox_'+chatboxtitle+' .chatboxinput').css('display','none');
		}
		
	}


	function checkChatBoxInputKey(event,chatboxtextarea,chatboxtitle,id) {
		 
		if(event.keyCode == 13 && event.shiftKey == 0)  {
			message = jQuery(chatboxtextarea).val();
			message = message.replace(/^\s+|\s+$/g,"");

			jQuery(chatboxtextarea).val('');
			jQuery(chatboxtextarea).focus();
			jQuery(chatboxtextarea).css('height','44px');
			if (message != '') {
				jQuery.post(config.base_url + "chat/successChat?action=sendchat"
					, {to: id, message: message} 
					, function(data){
						message = message.replace(/</g,"&lt;").replace(/>/g,"&gt;").replace(/\"/g,"&quot;");
						jQuery("#chatbox_"+chatboxtitle+" .chatboxcontent")
							.append('<div class="chatboxmessage"><span class="chatboxmessagefrom">'
								+username+':&nbsp;&nbsp;</span><span class="chatboxmessagecontent">'
								+message+'</span></div>');
						jQuery("#chatbox_"+chatboxtitle+" .chatboxcontent")
							.scrollTop(jQuery("#chatbox_"+chatboxtitle+" .chatboxcontent")[0].scrollHeight);
				});
			}
			chatHeartbeatTime = minChatHeartbeat;
			chatHeartbeatCount = 1;

			return false;
		}

		var adjustedHeight = chatboxtextarea.clientHeight;
		var maxHeight = 94;

		if (maxHeight > adjustedHeight) {
			adjustedHeight = Math.max(chatboxtextarea.scrollHeight, adjustedHeight);
			if (maxHeight)
				adjustedHeight = Math.min(maxHeight, adjustedHeight);
			if (adjustedHeight > chatboxtextarea.clientHeight)
				jQuery(chatboxtextarea).css('height',adjustedHeight+8 +'px');
		} else {
			jQuery(chatboxtextarea).css('overflow','auto');
		}
		 
	}


	function startChatSession(){  
		jQuery.ajax({
			url: config.base_url + "chat/successChat",
			cache: false,
			type:'get',
			dataType: "json",
			data:{
				action:'startchatsession'
			},
			success: function(data) {
	 			// console.log(data);
				username = data.username;

				jQuery.each(data.items, function(i,item){
					if (item)	{ // fix strange ie bug

						chatboxtitle = item.f;

						if (jQuery("#chatbox_"+chatboxtitle).length <= 0) {
							createChatBox(chatboxtitle,1);
						}
						
						if (item.s == 1) {
							item.f = username;
						}

						if (item.s == 2) {
							jQuery("#chatbox_"+chatboxtitle+" .chatboxcontent").append('<div class="chatboxmessage"><span class="chatboxinfo">'+item.m+'</span></div>');
						} else {
							jQuery("#chatbox_"+chatboxtitle+" .chatboxcontent").append('<div class="chatboxmessage"><span class="chatboxmessagefrom">'+item.f+':&nbsp;&nbsp;</span><span class="chatboxmessagecontent">'+item.m+'</span></div>');
						}
					}
				});
			
				for (i=0;i<chatBoxes.length;i++) {
					chatboxtitle = chatBoxes[i];
					jQuery("#chatbox_"+chatboxtitle+" .chatboxcontent").scrollTop(jQuery("#chatbox_"+chatboxtitle+" .chatboxcontent")[0].scrollHeight);
					setTimeout('jQuery("#chatbox_"+chatboxtitle+" .chatboxcontent").scrollTop(jQuery("#chatbox_"+chatboxtitle+" .chatboxcontent")[0].scrollHeight);', 100); // yet another strange ie bug
				}
		
				setTimeout('chatHeartbeat();',chatHeartbeatTime);
			}
		});
	}


	/**
	 * Cookie plugin
	 */
	jQuery.cookie = function(name, value, options) {
	    if (typeof value != 'undefined') { // name and value given, set cookie
	        options = options || {};
	        if (value === null) {
	            value = '';
	            options.expires = -1;
	        }
	        var expires = '';
	        if (options.expires && (typeof options.expires == 'number' || options.expires.toUTCString)) {
	            var date;
	            if (typeof options.expires == 'number') {
	                date = new Date();
	                date.setTime(date.getTime() + (options.expires * 24 * 60 * 60 * 1000));
	            } else {
	                date = options.expires;
	            }
	            expires = '; expires=' + date.toUTCString(); // use expires attribute, max-age is not supported by IE
	        }
	        // CAUTION: Needed to parenthesize options.path and options.domain
	        // in the following expressions, otherwise they evaluate to undefined
	        // in the packed version for some reason...
	        var path = options.path ? '; path=' + (options.path) : '';
	        var domain = options.domain ? '; domain=' + (options.domain) : '';
	        var secure = options.secure ? '; secure' : '';
	        document.cookie = [name, '=', encodeURIComponent(value), expires, path, domain, secure].join('');
	    } else { // only name given, get cookie
	        var cookieValue = null;
	        if (document.cookie && document.cookie != '') {
	            var cookies = document.cookie.split(';');
	            for (var i = 0; i < cookies.length; i++) {
	                var cookie = jQuery.trim(cookies[i]);
	                // Does this cookie string begin with the name we want?
	                if (cookie.substring(0, name.length + 1) == (name + '=')) {
	                    cookieValue = decodeURIComponent(cookie.substring(name.length + 1));
	                    break;
	                }
	            }
	        }
	        return cookieValue;
	    }
	};