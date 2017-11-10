jQuery(document).ready(function($){
		/*
		* Popup function 
		*/
		$(document.body).on('click', '.popupUnlock', function(e){
			e.preventDefault();
			$target = $(this).next('div').attr('id');
			
			$(this).popup({
				content		: $('#' + $target)
			});
		});
		
		/*
		* Popup Close button
		*/
		$(document.body).on('click', '.popup_close', function(){
			$('.popup_cont, .popup_back').remove();
		});

		/*
		* Popup advertisement Click
		*/
		$(document.body).on('click', '.advertisement a', function(){
			$href = $(this).attr('href');
			window.open( $href,  '_blank' );
		});


		//$getexLike = getCookie('ex_like');
		//console.log('Cookie Value: ' + $getexLike);
		//console.log('share amount: ' + parseInt(shareamount + 1));
});

jQuery(window).load(function($) {
			/*
			* Youtube script
			*/		
			/*if(jQuery('.youtubeButton').length){
				jQuery(document.body).on('click', '.youtubeButton > div > a ', function(){
					alert('test youtube');
				});
			}*/



			/*
			* Twitter script
			*/
			if(jQuery('.unlock_social_icons').length){
			twttr.events.bind("follow", function(event) {
					$thisId = event.target.getAttribute('id');
					$id = jQuery('#'+$thisId).prev().data('id');
			        if(event){
				        $getexLike = getCookie('ex_like');
						$exvalue = ($getexLike)?$getexLike:0;
						jQuery.ajax({
							url: ajaxurl, 
							type: 'POST', 
							data: 
							{
								'action'	: 'twitterFollowCallback',
								'post_id' 	: $id,
								'cookie' 	: $exvalue
							},success:function(data){
								//console.log(jQuery.trim(data));
								$newLike = (parseInt($exvalue) < shareamount)?parseInt($exvalue) + 1:parseInt($exvalue) + 0;
								document.cookie = 'ex_like=' + $newLike + ';path=/';
								//console.log('Ex_like: ' + $newLike);
								if(parseInt(jQuery.trim(data)) >= parseInt(shareamount) ){
									 window.location.reload();
								}
							}
						});
			        }
			 });
			} // check if active


			/*
			* Instagram follow button 
			*/			
			jQuery(document.body).on('click', '.instagramFollow', function(){
					$post_id = jQuery(this).data('id');
					$getexLike = getCookie('ex_like');
					$exvalue = ($getexLike)?$getexLike:0;

					jQuery.ajax({
						url: ajaxurl, 
						type: 'POST', 
						data: 
						{
							'action'	: 'instagramFollowCallback',
							'post_id' 	: $post_id,
							'cookie' 	: $exvalue
						},success:function(data){
							//console.log(jQuery.trim(data));
							$newLike = (parseInt($exvalue) < shareamount)?parseInt($exvalue) + 1: parseInt($exvalue) + 0;
							document.cookie = 'ex_like=' + $newLike + ';path=/';
							//console.log('Ex_like: ' + $newLike);
							if(parseInt(jQuery.trim(data)) >= parseInt(shareamount) ){
									 window.location.reload();
							}
						}
					});
			});




			/*
			* Like call back function 
			*/
			var page_like_or_like_callback = function(url, html_element) {
			  if(url){
			  	$post_id = html_element.getAttribute('data-id');
				jQuery.ajax({
						url: ajaxurl, 
						type: 'POST', 
						data: 
						{
							'action'	: 'pageLikeorLikeCallback',
							'post_id' 	: $post_id
						},success:function(data){
							//console.log(jQuery.trim(data));
							$getexLike = getCookie('ex_like');
							$exvalue = ($getexLike)?$getexLike:0;
							$newLike = (parseInt($exvalue) < shareamount)?parseInt($exvalue) + 1:parseInt($exvalue) + 0;
							document.cookie = 'ex_like=' + $newLike + ';path=/';
							//console.log('Ex_like: ' + $newLike);
							if(parseInt(jQuery.trim(data)) >= parseInt(shareamount + 1) ){
									 window.location.reload();
							}
						}
					});
			  }
			  
			}




			var page_like_or_unlike_callback = function(url, html_element) {
			  if(url){
			  	$post_id = html_element.getAttribute('data-id');
				jQuery.ajax({
						url: ajaxurl, 
						type: 'POST', 
						data: 
						{
							'action'	: 'pageUnlikeCallback',
							'post_id' 	: $post_id
						},success:function(data){
							//console.log(jQuery.trim(data));
							$getexLike = getCookie('ex_like');
							$exvalue = ($getexLike)?$getexLike:0;
							$newLike = parseInt($exvalue) - 1;
							document.cookie = 'ex_like=' + $newLike + ';path=/';
							//console.log('Ex_like: ' + $newLike);
							if(parseInt(jQuery.trim(data)) >= parseInt(shareamount + 1) ){
									 window.location.reload();
							}
						}
					});
			  }
			}

			// In your onload handler
			FB.Event.subscribe("edge.create", page_like_or_like_callback);
			FB.Event.subscribe("edge.remove", page_like_or_unlike_callback);


			}); //End window load function 




			function share($this) {
				 FB.ui(
					{
						method: 'share',
					    display: 'popup',
					    mobile_iframe: true,
					    href: $this.getAttribute("data-href"),
					  /*method: "feed",
					  name: "Facebook Dialogs",
					  link: $this.getAttribute("data-href"),
					  picture: "http://fbrell.com/f8.jpg",
					  caption: "Reference Documentation",
					  description: "Dialogs provide a simple, consistent interface for applications to interface with users."*/
					},
					function(response) {
					  if (response) {
					  		$getexLike = getCookie('ex_like');
							$exvalue = ($getexLike)?$getexLike:0;
					  	 	$post_id = $this.getAttribute('data-id');
						   	jQuery.ajax({
							url: ajaxurl, 
							type: 'POST', 
							data: 
							{
								'action'	: 'pageShareCallback',
								'post_id' 	: $post_id,
								'cookie' 	: $exvalue
							},success:function(data){
								//console.log(jQuery.trim(data));
								if(parseInt(jQuery.trim(data)) >= parseInt(shareamount + 1) ){
									 window.location.reload();
								}
							}
						}); //End jQuery
					  }
					}
					); //End FB.ui
			}


			/*
			* Youtube Subscribe
			*/
			function youtubeSubscribe($this){
							$getexLike = getCookie('ex_like');
							$exvalue = ($getexLike)?$getexLike:0;
					  	 	$post_id = $this.getAttribute('data-id');
						   	jQuery.ajax({
							url: ajaxurl, 
							type: 'POST', 
							data: 
							{
								'action'	: 'youtubeSubscribeCallback',
								'post_id' 	: $post_id,
								'cookie' 	: $exvalue
							},success:function(data){
								//console.log(jQuery.trim(data));
								if(parseInt(jQuery.trim(data)) >= parseInt(shareamount + 1) ){
									 window.location.reload();
								}
							}
						}); //End jQuery

			}

			/*
			* Get cookie 
			*/
			function getCookie(cname) {
			    var name = cname + "=";
			    var decodedCookie = decodeURIComponent(document.cookie);
			    var ca = decodedCookie.split(';');
			    for(var i = 0; i <ca.length; i++) {
			        var c = ca[i];
			        while (c.charAt(0) == ' ') {
			            c = c.substring(1);
			        }
			        if (c.indexOf(name) == 0) {
			            return c.substring(name.length, c.length);
			        }
			    }
			    return "";
			}
