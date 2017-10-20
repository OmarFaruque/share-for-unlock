jQuery(document).ready(function($){
		  // checkbox function 
	  jQuery('a.checkbox-active').live('click', function(){
	  	var checkbox = jQuery(this).prev();

	  	if(checkbox.is(':checked')){
	  		checkbox.prop("checked", false);
            checkbox.attr('value', 'no');
	  		jQuery(this).removeClass('active');
	  		$('.unlock_amount').addClass('hide');
	  	}else{
	  		checkbox.prop("checked", true);
            checkbox.attr('value', 'yes');
	  		jQuery(this).addClass('active');
	  		$('.unlock_amount').removeClass('hide');
	  	}
	  	return false;
	  });


  		var mediaUploader;
		  $('#unlock_popup_imgid').click(function(e) {
			e.preventDefault();
			// If the uploader object has already been created, reopen the dialog
			  if (mediaUploader) {
			  mediaUploader.open();
			  return;
			}
			// Extend the wp.media object
			mediaUploader = wp.media.frames.file_frame = wp.media({
			  title: 'Choose Popup Image',
			  button: {
			  text: 'Choose Popup Image'
			}, multiple: false });
		 
			// When a file is selected, grab the URL and set it as the text field's value
			mediaUploader.on('select', function() {
			  attachment = mediaUploader.state().get('selection').first().toJSON();
			  $('.delete, .suggesion').remove();
			  $('input[name="unlock_popup_imgid"]').val(attachment.id);
			  $('#img_upload-preview').html('<img src="'+attachment.url+'"/>').addClass('nopadding');
			  //$('<div class="delete"><div alt="f158" class="dashicons dashicons-no" style="display: inline-block;"></div></div>').insertBefore('#img_upload-preview');
			  $('#img_upload-preview').prepend('<div class="delete"><div alt="f158" class="dashicons dashicons-no" style="display: inline-block;"></div></div>');
			});
				// Open the uploader dialog
				mediaUploader.open();
			  });

			  //Delete Image 
			  $(document.body).on('click', 'div#img_upload-preview .delete', function(){
			  	$('#img_upload-preview').html('').removeClass('nopadding');
			  	$('input[name="unlock_popup_imgid"]').val('');
			  }); // End Delete function

}); // End Document Ready