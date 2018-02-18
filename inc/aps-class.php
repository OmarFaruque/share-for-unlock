<?php 
/*
* Application Status Class
*/


if (!class_exists('socialShareAndUnlock')) {
    class socialShareAndUnlock{
    	
	   	public $plugin_name;
	    public $plugin_slug;
	    public $version;
	    public $plugin_url;
	    public $plugin_path;
	    public $table;
	    public $wpdb;
	    public $admin_handle;
	    private $dc_url;

    
    
    	/**
		 * here we go
		*/
    	public function __construct() {
    	global $wpdb;
	    $this->plugin_name = 'share for unlock';
	    $this->wpdb = $wpdb;
	    $this->plugin_slug = 'share-for-unlock';
	    $this->version = '1.0.0';
	    $this->plugin_url = SRURL;
	    $this->plugin_path = SRDIR;
	    $this->dc_url = 'http://larasoftbd.com/docs/' . $this->plugin_slug;
	    $this->table = $this->wpdb->prefix . 'shareforunlock';  
	    $this->init();
	    $this->get_settings();
    	} 
    	

    	private function init(){
    		add_action( 'admin_menu', array($this, 'application_status_menu') );
    		add_action( 'admin_init', array( $this, 'share_admin_page_settings' ) );
    		add_action( 'add_meta_boxes', array($this, 'social_share_unlock_metabox') );
    		add_action( 'admin_enqueue_scripts', array($this, 'admin_add_script'));
    		add_action( 'save_post', array($this, 'unlock_save_meta_box_data') );
    		add_filter( 'the_content', array( $this, 'process_content' ), 9999 ); // fulter content
    		add_filter( 'wp_head', array( $this, 'scriptToWpHead' ) ); // javascript to wp_head
    		add_action( 'wp_enqueue_scripts', array($this, 'frontend_add_script'));
    		add_action( 'init', array( $this, 'create_share_table' ) );

    		// Ajax Callback function 
    		add_action('wp_ajax_nopriv_pageLikeorLikeCallback', array($this, 'pageLikeorLikeCallback'));
			add_action( 'wp_ajax_pageLikeorLikeCallback', array($this, 'pageLikeorLikeCallback') );

			add_action('wp_ajax_nopriv_pageUnlikeCallback', array($this, 'pageUnlikeCallback'));
			add_action( 'wp_ajax_pageUnlikeCallback', array($this, 'pageUnlikeCallback') );

			add_action('wp_ajax_nopriv_pageShareCallback', array($this, 'pageShareCallback'));
			add_action( 'wp_ajax_pageShareCallback', array($this, 'pageShareCallback') );

			add_action('wp_ajax_nopriv_instagramFollowCallback', array($this, 'instagramFollowCallback'));
			add_action( 'wp_ajax_instagramFollowCallback', array($this, 'instagramFollowCallback') );

			add_action('wp_ajax_nopriv_twitterFollowCallback', array($this, 'twitterFollowCallback'));
			add_action( 'wp_ajax_twitterFollowCallback', array($this, 'twitterFollowCallback') );

			add_action('wp_ajax_nopriv_youtubeSubscribeCallback', array($this, 'youtubeSubscribeCallback'));
			add_action( 'wp_ajax_youtubeSubscribeCallback', array($this, 'youtubeSubscribeCallback') );
    	}

    	

		/**
		 * get all settings
		 */
	    private function get_settings() {
	      $defaultM = 'Perform on social section for unlock content';
	      $this->settings = array();
	      $this->settings['unlock_post_types'] = get_option( 'unlock_post_types');
	      $this->settings['unlock_icon_position'] = (get_option( 'unlock_icon_position'))?get_option( 'unlock_icon_position'):'left';
	      $this->settings['unlock_visitor_message'] =(get_option( 'unlock_visitor_message'))?get_option( 'unlock_visitor_message'):$defaultM; 
	      $this->settings['unlock_fb_page_url'] = (get_option('unlock_fb_page_url'))?get_option('unlock_fb_page_url'):'';
	      $this->settings['unlock_instagram_username'] = (get_option('unlock_instagram_username'))?get_option('unlock_instagram_username'):'';
	      $this->settings['unlock_twitter_username'] = (get_option('unlock_twitter_username'))?get_option('unlock_twitter_username'):'';
	      $this->settings['youtube_chanel_id'] = (get_option('youtube_chanel_id'))?get_option('youtube_chanel_id'):'';
	      $this->settings['unlock_popup_url'] = (get_option('unlock_popup_url'))?get_option('unlock_popup_url'):'';
	      $this->settings['unlock_popup_imgid'] = (get_option('unlock_popup_imgid'))?get_option('unlock_popup_imgid'):'';
		  $this->settings['unlock_popup_msg'] = (get_option('unlock_popup_msg'))?get_option('unlock_popup_msg'):'';
	      
	    }



		function application_status_menu() {
			 $this->admin_handle = add_options_page( ucfirst($this->plugin_name) . __( 'Settings' ), 'Share for Unlock', 'manage_options', 'share-for-unlock-settings', array( $this, 'share_admin_page' ) );
		}

		/**
	    * handle the settings field : no link creation
	    */
	    function admin_post_types() {
	      $allPosts = get_post_types( array('public'=>true) );
	      unset($allPosts['attachment']);

	      $posts = '';
		      foreach($allPosts as $k => $post){
		      	$exarray = (is_array($this->settings['unlock_post_types']) )?$this->settings['unlock_post_types']:array();
		      	$posts .= '<input type="checkbox" name="unlock_post_types[]" id="social_unlock_post_types_'.$k.'" value="'.$post.'"' . ( ( in_array($post, $exarray) ) ?  'checked="checked"' : '' ) . ' /><label for="social_unlock_post_types_'.$k.'" class="check">' . __( ucfirst($post), 'social_unlock' ) . '</label><br/>';	
		      }
	  	
	      echo $posts;
	    }

	    /*
	    * Design Seciton body
	    */
	    function admin_unlock_design(){
	    	$left = ($this->settings['unlock_icon_position'] == 'left')?'checked':'';
	    	$right = ($this->settings['unlock_icon_position'] == 'right')?'checked':''; 
	    	$position =  '
	    	<label><input value="left" '.$left.' name="unlock_icon_position" type="radio"/>Left</label>&nbsp;&nbsp;
	    	<label><input value="right" '.$right.' name="unlock_icon_position" type="radio"/>Right</label>
	    	';
	    	echo $position;
	    }

	    /*
	    * message admin  field 
	    */
	    function admin_unlock_message(){
	    	echo '<textarea cols="110" name="unlock_visitor_message">'.$this->settings['unlock_visitor_message'].'</textarea>';
	    }

	    /*
	    * Facebook page url 
	    */
	    function admin_unlock_fb_page_url(){
	    	echo '<input style="min-width:70%;" type="url" name="unlock_fb_page_url" value="'.$this->settings['unlock_fb_page_url'].'"/><br/><span><small><i>Copy your facebook page URL & past here. </i></small></span>';
	    }

	    /*
	    * Instagram Username
	    */
	    function admin_instagram_username(){
	    	echo '<input style="min-width:70%;" type="text" name="unlock_instagram_username" value="'.$this->settings['unlock_instagram_username'].'"/><br/><span><small><i>Instagram Username </i></small></span>';

	    }

	    /*
	    * Twitter Username
	    */
	    function admin_twitter_username(){
	    	echo '<input style="min-width:70%;" type="text" name="unlock_twitter_username" value="'.$this->settings['unlock_twitter_username'].'"/><br/><span><small><i>Twitter Username </i></small></span>';
	    }

	    /*
	    * Youtube Channel ID
	    */
	    function admin_youtube_channel(){
	    	echo '<input style="min-width:70%;" type="text" name="youtube_chanel_id" value="'.$this->settings['youtube_chanel_id'].'"/><br/><span><small><i>Youtube Chanel ID </i></small></span>';
	    }

	    /*
	    * Custom URL for POPUP
	    */
	    function admin_popup_url(){
	    	echo '<input style="min-width:70%;" type="url" name="unlock_popup_url" value="'.$this->settings['unlock_popup_url'].'"/><br/><span><small><i>Custom URL (if blank popup set home url automatically.) </i></small></span>';
	    }

	     /*
	    * Custom Message for POPUP
	    */
	    function admin_popup_msg(){
	    	echo '<input style="min-width:70%;" type="text" name="unlock_popup_msg" value="'.$this->settings['unlock_popup_msg'].'"/><br/><span><small><i>Message (Message apper in popup top.) </i></small></span>';
	    }

	    /*
	    * Popup Image ID
	    */
	    function admin_popup_imgid(){

	    	$preview = '';
	    	if($this->settings['unlock_popup_imgid'] != ''){
	    		$preview .= '<div class="delete"><div alt="f158" class="dashicons dashicons-no" style="display: inline-block;"></div></div><img src="'.wp_get_attachment_url( (int)$this->settings['unlock_popup_imgid'] ).'"/>';	
	    	}
	    	echo '<input style="min-width:70%;" type="hidden" name="unlock_popup_imgid" value="'.$this->settings['unlock_popup_imgid'].'"/>
	    	<div id="img_upload-preview">'.$preview.'</div>
	    	<button class="button button-primary" id="unlock_popup_imgid">Set Image</button><br/>
	    	<span><small><i>Custom URL (if blank popup set home url automatically.) </i></small></span>';


	    }



	    /**
	    * echo title for tags settings section
	    */
	    function admin_section_tags_title() {
	      echo '<p><strong>' . __( 'Post types', 'social_unlock' ) . ':</strong></p><hr />';
	    }

	    /*
	    * Detisn tab title
	    */
	    function admin_section_design_title(){
	    	echo '<p><strong>' . __( 'Design', 'social_unlock' ) . ':</strong></p><hr />';	
	    }

	    /*
	    * Social Secion settings
	    */
	    function admin_section_social_title(){
	    	echo '<p><strong>' . __( 'Social Profile Settings', 'social_unlock' ) . ':</strong></p><hr />';	
	    }

	    /*
	    * Popup title
	    */
	    function admin_section_popup_title(){
	    	echo '<p><strong>' . __( 'Popup Settings', 'social_unlock' ) . ':</strong></p><hr />';	
	    }

	    

	    

		function share_admin_page_settings(){
			add_settings_section( 'social-unlock-settings-tags', '', array( $this, 'admin_section_tags_title' ), 'socialunlock_settings_section_general' );
	      	register_setting( 'socialunlock_settings_general', 'unlock_post_types' );
	      	add_settings_field( 'swcc_unlock_settings_tags_nolinks', __( 'Set Post type', 'social_unlock') . '' , array( $this, 'admin_post_types' ), 'socialunlock_settings_section_general', 'social-unlock-settings-tags', array( 'label_for' => 'unlock_post_types' ) );

	      	add_settings_section( 'social-unlock-settings-design', '', array( $this, 'admin_section_design_title' ), 'socialunlock_settings_section_design' );
	      	register_setting( 'socialunlock_settings_design', 'unlock_icon_position' );
	      	add_settings_field( 'swcc_unlock_settings_design', __( 'Icon position', 'social_unlock') . '' , array( $this, 'admin_unlock_design' ), 'socialunlock_settings_section_design', 'social-unlock-settings-design', array( 'label_for' => 'unlock_icon_position' ) );

	      	register_setting( 'socialunlock_settings_design', 'unlock_visitor_message' );
	      	add_settings_field( 'swcc_unlock_settings_design_message', __( 'Message for Visitor', 'social_unlock') . '' , array( $this, 'admin_unlock_message' ), 'socialunlock_settings_section_design', 'social-unlock-settings-design', array( 'label_for' => 'unlock_visitor_message' ) );


	      	add_settings_section( 'social-unlock-settings-social', '', array( $this, 'admin_section_social_title' ), 'socialunlock_settings_section_social' );
	      	register_setting( 'socialunlock_settings_social', 'unlock_fb_page_url' );
	      	add_settings_field( 'swcc_unlock_settings_social', __( 'Facebook Page URL', 'social_unlock') . '' , array( $this, 'admin_unlock_fb_page_url' ), 'socialunlock_settings_section_social', 'social-unlock-settings-social', array( 'label_for' => 'unlock_fb_page_url' ) );

	      	register_setting( 'socialunlock_settings_social', 'unlock_instagram_username' );
	      	add_settings_field( 'swcc_unlock_settings_social_instragram', __( 'Instagram Username', 'social_unlock') . '' , array( $this, 'admin_instagram_username' ), 'socialunlock_settings_section_social', 'social-unlock-settings-social', array( 'label_for' => 'unlock_instagram_username' ) );

	      	register_setting( 'socialunlock_settings_social', 'unlock_twitter_username' );
	      	add_settings_field( 'swcc_unlock_settings_social_twitter', __( 'Twitter Username', 'social_unlock') . '' , array( $this, 'admin_twitter_username' ), 'socialunlock_settings_section_social', 'social-unlock-settings-social', array( 'label_for' => 'unlock_twitter_username' ) );

	      	register_setting( 'socialunlock_settings_social', 'youtube_chanel_id' ); //YouTube Channel ID
	      	add_settings_field( 'swcc_unlock_settings_social_youtube', __( 'YouTube Channel ID', 'social_unlock') . '' , array( $this, 'admin_youtube_channel' ), 'socialunlock_settings_section_social', 'social-unlock-settings-social', array( 'label_for' => 'youtube_chanel_id' ) );


	      	add_settings_section( 'social-unlock-settings-popup', '', array( $this, 'admin_section_popup_title' ), 'socialunlock_settings_section_popup' );
	      	
			register_setting( 'socialunlock_settings_popup', 'unlock_popup_msg' ); //Popup Message
	      	add_settings_field( 'swcc_unlock_settings_social_msg', __( 'Message', 'social_unlock') . '' , array( $this, 'admin_popup_msg' ), 'socialunlock_settings_section_popup', 'social-unlock-settings-popup', array( 'label_for' => 'unlock_popup_msg' ) );

	      	register_setting( 'socialunlock_settings_popup', 'unlock_popup_url' ); //Custom URL
	      	add_settings_field( 'swcc_unlock_settings_social_url', __( 'URL Link', 'social_unlock') . '' , array( $this, 'admin_popup_url' ), 'socialunlock_settings_section_popup', 'social-unlock-settings-popup', array( 'label_for' => 'unlock_popup_url' ) );

	      	register_setting( 'socialunlock_settings_popup', 'unlock_popup_imgid' ); //Custom URL
	      	add_settings_field( 'swcc_unlock_settings_social_imgid', __( 'Popup Bottom Image', 'social_unlock') . '' , array( $this, 'admin_popup_imgid' ), 'socialunlock_settings_section_popup', 'social-unlock-settings-popup', array( 'label_for' => 'unlock_popup_imgid' ) );


		}


    /**
     * show admin page
     */
    function share_admin_page() {
      
      $url = admin_url( 'options-general.php?page=' . $_GET['page'] . '&tab=' );
      $current_tab = 'general';
      if ( isset( $_GET['tab'] ) ) {
        $current_tab = $_GET['tab'];
      }
      if ( ! in_array( $current_tab, array('general', 'design', 'social', 'popup') ) ) {
        $current_tab = 'general';
      }
      ?>
      <div class="wrap">
        <h1 id="pp-plugin-info-social-share-unlock"><?php echo $this->plugin_name; ?><span></span></h1>
        <h2 class="nav-tab-wrapper" id="wp-social_share">
          <a href="<?php echo $url . 'general'; ?>" class="nav-tab<?php if ( 'general' == $current_tab ) { echo ' nav-tab-active'; } ?>"><?php _e( 'Post Types' ); ?></a>
          <a href="<?php echo $url . 'design'; ?>" class="nav-tab<?php if ( 'design' == $current_tab ) { echo ' nav-tab-active'; } ?>"><?php _e( 'Display' ); ?></a>
          <a href="<?php echo $url . 'social'; ?>" class="nav-tab<?php if ( 'social' == $current_tab ) { echo ' nav-tab-active'; } ?>"><?php _e( 'Social Profile' ); ?></a>
          <a href="<?php echo $url . 'popup'; ?>" class="nav-tab<?php if ( 'popup' == $current_tab ) { echo ' nav-tab-active'; } ?>"><?php _e( 'Popup' ); ?></a>
        </h2>
          <form method="post" action="options.php" id="pp-plugin-settings-hashtagger">
            <div class="postbox">
              <div class="inside">
                  <?php
                  settings_fields( 'socialunlock_settings_' . $current_tab );   
                  do_settings_sections( 'socialunlock_settings_section_' . $current_tab );
                  submit_button(); 
                 ?>
              </div>
            </div>
          </form>

      </div>
      <?php
    }
    
    function social_share_unlock_metabox(){
    	$allPostTypes = $this->settings['unlock_post_types'];
    	foreach($allPostTypes as $type):
		add_meta_box('social_share_meta', __('Unlock / Social Share', 'social_unlock'), array($this, 'social_share_unlock_metabox_callback'), $type, 'side', 'low'	);
		endforeach;
    }

    function social_share_unlock_metabox_callback( $post ){
		wp_nonce_field( 'product_meta_box', 'product_meta_box_nonce' );
		wp_nonce_field( basename( __FILE__ ), 'prfx_nonce' );
	    $prfx_stored_meta = get_post_meta( $post->ID );

	    $active 	= get_post_meta( $post->ID, 'active_unlock', true );
	    $amount 	= get_post_meta( $post->ID, 'unlock_amount', true );
	    $checked	= ($active == 'yes')?'checked="checked"':''; 
	    $class 		= ($active == 'yes')?'active':'';
	    $hclass 	= ($active == 'yes')?'':'hide';
	    $amountv 	= ($amount)?$amount:4;

	    $output = '<div style="margin-bottom: 15px;display: block;float: left; width: 100%; margin-top: 10px;">
	    			<label style="display: block;float: left;margin-right: 10px;" for="active_unlock">Active Unlock</label>
	    			<input type="checkbox" style="display: none;" value="yes" '.$checked.'  name="active_unlock" id="active_unlock"><a href="javascript:void(0)" class="checkbox-active '.$class.'">Checkbox</a></div>';

	    $output .= '<div class="unlock_amount '.$hclass.'">
	    	<div class="unlock_amount_inner">
	    		<label for="unlock_amount">Unloc after Share</label>
	    		<input type="number" style="max-width:60px; margin-left:6px;" id="unlock_amount" name="unlock_amount" value="'.$amountv.'" class="form-control" />
	    	</div>
	    </div>';

	    echo $output;

	}


	/*
	* Save meta data
	*/
	function unlock_save_meta_box_data($post_id){
		/*
	 * We need to verify this came from our screen and with proper authorization,
	 * because the save_post action can be triggered at other times.
	 */

	// Check if our nonce is set.
	if ( ! isset( $_POST['product_meta_box_nonce'] ) ) {
		return;
	}

	// Verify that the nonce is valid.
	if ( ! wp_verify_nonce( $_POST['product_meta_box_nonce'], 'product_meta_box' ) ) {
		return;
	}

	// If this is an autosave, our form has not been submitted, so we don't want to do anything.
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}

	// Check the user's permissions.
	if ( isset( $_POST['post_type'] ) && 'page' == $_POST['post_type'] ) {

		if ( ! current_user_can( 'edit_page', $post_id ) ) {
			return;
		}

	} else {

		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}
	}

	$my_data_active_un 	= sanitize_text_field( $_POST['active_unlock'] ); // Active Unlock
	$my_data_amount 	= sanitize_text_field( $_POST['unlock_amount'] ); // Share Amount

	update_post_meta( $post_id, 'active_unlock', $my_data_active_un );
	update_post_meta( $post_id, 'unlock_amount', $my_data_amount );


	} //End meta data save function 


	/*
	* admin css and js file
	*/
	function admin_add_script(){
	  wp_enqueue_style( 'wp-unlock-css', $this->plugin_url . 'asset/admin.css', array(), '10102017', 'screen' );
	  wp_enqueue_script( 'wp-unlock-js', $this->plugin_url . '/asset/admin.js', array(), false, true );
	}

	/*
	* process content
	*/
	function process_content($content){
		global $post;
		$position = ($this->settings['unlock_icon_position'] == 'left')?'pull-left':'pull-right';

		$ip = $this->getUserIP();
		$row = $this->wpdb->get_row("SELECT `amount` FROM `$this->table` WHERE `ip` = '".$ip."' AND `post_id` = ".$post->ID."", OBJECT);
		
		$unAmount = ($row)?$row->amount:0;

		$active 	= (get_post_meta( $post->ID, 'active_unlock', true ) == 'yes')?'yes':'no';
	    $amount 	= get_post_meta( $post->ID, 'unlock_amount', true );
	    
	    $output = '';

	    if($active == 'yes' && $unAmount >= $amount ){
			$output .= '<div class="unlok_with_share">'.$content.'</div>';
		}
		elseif($active == 'no'){
			$output .= $content;
		}
	    if($active == 'yes'){
		$output .= '<div class="unlock_social_icons '.$position.'">
			<a class="popupUnlock" href="#shareForUnlock_'.$post->ID.'"><div class="message_unlock">'.$this->settings['unlock_visitor_message'].'<span class="dashicons dashicons-share-alt2"></span></div></a>';
		//$output .= '<div style="display:none;" id="testP">Content test popup.</div>';

		$output .= '<div style="display:none;" id="shareForUnlock_'.$post->ID.'" class="unlock_popup">';	
			$output .= '<div class="opupMessage text-center mb10"><p>'.$this->settings['unlock_popup_msg'].'</p></div>';
			if($this->settings['unlock_fb_page_url'] != ''){
			$output .= '<div data-id="'.$post->ID.'" class="fb-like" data-href="'.$this->settings['unlock_fb_page_url'].'" data-layout="button_count" data-action="like" data-size="small" data-show-faces="false" data-share="false"></div>';
			}

			$output .= '<button data-id="'.$post->ID.'" onclick="share(this);" class="fb-custom-share-button" 
			    data-href="'.get_the_permalink( $post->ID ).'" 
			    data-layout="button_count">Share
		  	</button>';

		  	if($this->settings['unlock_instagram_username'] !=''){
			$output .= '<a data-id="'.$post->ID.'" target="_blank" href="https://www.instagram.com/'.$this->settings['unlock_instagram_username'].'/?ref=badge" class="insta-custom-follow-button instagramFollow" id="instagramFollow">Instagram</a>';
			}

			if($this->settings['youtube_chanel_id'] != ''){
			$output .= '<div class="youtubeButton"><div class="subscribe"><a onclick="youtubeSubscribe(this)" data-id="'.$post->ID.'" target="_blank" href="https://www.youtube.com/channel/'.$this->settings['youtube_chanel_id'].'?sub_confirmation=1">Youtube</a></div></div>';
			}

			if($this->settings['unlock_twitter_username'] != ''){
			$output .='<a data-id="'.$post->ID.'" href="https://twitter.com/'.$this->settings['unlock_twitter_username'].'" class="twitter-follow-button">Follow @'.$this->settings['unlock_twitter_username'].'</a>';
			}
			if($this->settings['unlock_popup_imgid'] != ''){
				$image_alt = get_post_meta( $this->settings['unlock_popup_imgid'], '_wp_attachment_image_alt', true);
				$url = ($this->settings['unlock_popup_url'] != '')?$this->settings['unlock_popup_url']:get_home_url( '/' );
				$output .= '<div class="advertisement"><a target="_blank" href="'.$url.'"><img class="img-responsive img-fluid" alt="'.$image_alt.'" src="'.wp_get_attachment_url( $this->settings['unlock_popup_imgid'] ).'"/></a></div>';	
			}
			
			$output .='</div></div>
		';
		}
		
		return $output;	
		
		
	} //End process content

	function scriptToWpHead(){
		global $post;
		$active 	= get_post_meta( $post->ID, 'active_unlock', true );
	    $amount 	= get_post_meta( $post->ID, 'unlock_amount', true );

		?>
		
			<script>
			var shareamount = <?= ($active=='yes')?(int)$amount - 1:0;  ?>;
			(function(d, s, id) {
			  var js, fjs = d.getElementsByTagName(s)[0];
			  if (d.getElementById(id)) return;
			  js = d.createElement(s); js.id = id;
			  js.src = "//connect.facebook.net/en_US/all.js#xfbml=1&version=v2.10&appId=1545424379115354";
			  fjs.parentNode.insertBefore(js, fjs);
			}(document, "script", "facebook-jssdk"));
			</script>
		<?php
	}


	/*
	* Front End Script
	*/
	function frontend_add_script(){
		wp_enqueue_style( 'front-unlock-css', $this->plugin_url . 'asset/shareforunlock.css', array(), '10122017', 'all' );
		wp_enqueue_style( 'dashicons');
		wp_enqueue_script( 'popup-js', $this->plugin_url . '/asset/Popup/assets/js/jquery.popup.min.js', array('jquery'), '10102017', false );
		wp_enqueue_script( 'unlock-js', $this->plugin_url . 'asset/unlock.js', array('jquery'), '10122017', false );
		wp_localize_script( 'unlock-js', 'ajaxurl',  admin_url( 'admin-ajax.php' ) );

		wp_enqueue_script( 'twitter-widget', $this->plugin_url . '/asset/twitter-widgets.js', array('jquery'), '10152017', true );
	} // End front end script

	function create_share_table(){
		$tblename = $this->wpdb->prefix . 'shareforunlock';  
		if($this->wpdb->get_var("SHOW TABLES LIKE '$tblename'") != $tblename) {
		     //table not in database. Create new table
		     $charset_collate = $this->wpdb->get_charset_collate();
		     $sql = "CREATE TABLE $tblename (
		          id mediumint(10) NOT NULL AUTO_INCREMENT,
		          post_id varchar(200) NOT NULL,
		          ip varchar(500) NOT NULL,
		          amount varchar(500) NOT NULL,
		          date timestamp NOT NULL,
		          UNIQUE KEY id (id)
		     ) $charset_collate;";
		     require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
		     dbDelta( $sql );
		}
	} // End TableCreate 


	/*
	* Facebook like ajax callback
	*/
	function pageLikeorLikeCallback(){
		if(isset($_POST['post_id'])){
		  $ip = $this->getUserIP();
		  $row = $this->wpdb->get_row("SELECT `amount` FROM `$this->table` WHERE `ip` = '".$ip."' AND `post_id` = ".$_POST['post_id']."", OBJECT);
		  if($row){
		  	$newamount = (int)$row->amount + 1; 
		  	$sql = "UPDATE $this->table SET `amount`=".$newamount." WHERE `ip` = '".$ip."' AND `post_id`=".$_POST['post_id']."";
		  }else{
		  	$sql = "INSERT INTO $this->table (post_id, ip, amount) VALUES('".$_POST['post_id']."', '".$ip."', 1)";
		  }
			if($this->wpdb->query($sql)){
				$nrow = $this->wpdb->get_row("SELECT `amount` FROM `$this->table` WHERE `ip` = '".$ip."' AND `post_id` = ".$_POST['post_id']."", OBJECT);
				echo $nrow->amount;
			}
		}
		die();
	}

	/*
	* Facebook Unlike callback function 
	*/
	function pageUnlikeCallback(){
		if(isset($_POST['post_id'])){
			$ip = $this->getUserIP();
			$row = $this->wpdb->get_row("SELECT `amount` FROM `$this->table` WHERE `ip` = '".$ip."' AND `post_id` = ".$_POST['post_id']."", OBJECT);
			if($row){
				$newamount = (int)$row->amount - 1; 
				$sql = "UPDATE $this->table SET `amount`=".$newamount." WHERE `ip` = '".$ip."' AND `post_id`=".$_POST['post_id']."";

			if($this->wpdb->query($sql)){
				$nrow = $this->wpdb->get_row("SELECT `amount` FROM `$this->table` WHERE `ip` = '".$ip."' AND `post_id` = ".$_POST['post_id']."", OBJECT);
				echo $nrow->amount;
			}
			}
		}
		die();
	}

	/*
	* Share call back
	*/
	function pageShareCallback(){
		if(isset($_POST['post_id'])){
		  $ip = $this->getUserIP();
		  $row = $this->wpdb->get_row("SELECT `amount` FROM `$this->table` WHERE `ip` = '".$ip."' AND `post_id` = ".$_POST['post_id']."", OBJECT);
		  if($row){
		  	$cookeAmount = ((int)$_POST['cookie'] > $row->amount)?(int)$_POST['cookie']:$row->amount;
		  	$newamount = (int)$cookeAmount + 1; 
		  	$sql = "UPDATE $this->table SET `amount`=".$newamount." WHERE `ip` = '".$ip."' AND `post_id`=".$_POST['post_id']."";
		  }else{
		  	$amount = (int)$_POST['cookie'] + 1;
		  	$sql = "INSERT INTO $this->table (post_id, ip, amount) VALUES('".$_POST['post_id']."', '".$ip."', ".$amount.")";
		  }
			if($this->wpdb->query($sql)){
				$nrow = $this->wpdb->get_row("SELECT `amount` FROM `$this->table` WHERE `ip` = '".$ip."' AND `post_id` = ".$_POST['post_id']."", OBJECT);
				echo $nrow->amount;
			}
		}
		die();
	}


	/*
	* Instagramm Follow callback
	*/
	function instagramFollowCallback(){
		$this->pageShareCallback();
	}

	/*
	* Twitter follow back
	*/
	function twitterFollowCallback(){
		$this->pageShareCallback();
	}

	/*
	* Youtube Subscribe Callback
	*/
	function youtubeSubscribeCallback(){
		$this->pageShareCallback();
	}

	/*
	* Client IP Address
	*/
	function getUserIP()
	{
	    $client  = @$_SERVER['HTTP_CLIENT_IP'];
	    $forward = @$_SERVER['HTTP_X_FORWARDED_FOR'];
	    $remote  = $_SERVER['REMOTE_ADDR'];

	    if(filter_var($client, FILTER_VALIDATE_IP))
	    {
	        $ip = $client;
	    }
	    elseif(filter_var($forward, FILTER_VALIDATE_IP))
	    {
	        $ip = $forward;
	    }
	    else
	    {
	        $ip = $remote;
	    }

	    return $ip;
	}

	} //End Class
} // Check class existi