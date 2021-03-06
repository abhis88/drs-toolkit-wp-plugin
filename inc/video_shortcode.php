<?php
/* adds shortcode */
add_shortcode( 'drstk_collection_playlist', 'drstk_collection_playlist' );
add_shortcode( 'drstk_media', 'drstk_collection_playlist' );
function drstk_collection_playlist($atts){
  global $errors;
  $cache = get_transient(md5('DRSTK'.serialize($atts)));
  if($cache) {
    return $cache;
  }
    $collection = array_map('trim', explode(',', $atts['id']));
    $playlists = '';
    if (isset($atts['height']) && $atts['height'] != 0){
      $height = $atts['height'];
    } else {
      $height = '270';
    }
    if (isset($atts['width']) && $atts['width'] != 0){
      $width = $atts['width'];
    } else {
      $width = '100%';
    }
    if (isset($atts['aspectratio'])){
      $aspectratio = $atts['aspectratio'];
    } else {
      $aspectratio = '16:9';
    }
    if (isset($atts['skin'])){
      $skin = $atts['skin'];
    }
    if (isset($atts['listbarwidth']) && $atts['listbarwidth'] != 0){
      $listbarwidth = $atts['listbarwidth'];
    } else {
      $listbarwidth = '250';
    }
    foreach($collection as $video){
      $repo = drstk_get_repo_from_pid($video);
      if ($repo != "drs"){$pid = explode(":",$video); $pid = $pid[1];} else {$pid = $video;}
      $poster;
      if ($repo == "drs"){
        $url = "https://repository.library.northeastern.edu/api/v1/files/" . $video;
        $data = get_response($url);
        $data = json_decode($data);
        if (!isset($data->error)){

          $poster[] = $data->thumbnails[4];
          $this_poster = $data->thumbnails[4];
          $title = $data->mods->Title[0];
          foreach($data->canonical_object as $key=>$val){
            $pid = $key;
            $pid = explode("/", $pid);
            $pid = end($pid);
            $encoded = str_replace(':','%3A', $pid);
            $dir = substr(md5("info:fedora/".$pid."/content/content.0"), 0, 2);
            $user_agent = $_SERVER['HTTP_USER_AGENT'];
            if (stripos( $user_agent, 'Chrome') !== false){
              $full_pid = "info%3Afedora%2F".$encoded."%2Fcontent%2Fcontent.0";
            } elseif (stripos( $user_agent, 'Safari') !== false) {
              $full_pid = urlencode("info%3Afedora%2F".$encoded."%2Fcontent%2Fcontent.0");
            } else {
              $full_pid = "info%3Afedora%2F".$encoded."%2Fcontent%2Fcontent.0";
            }
            if ($val == 'Audio File'){
              $rtmp = 'rtmp://libwowza.neu.edu:1935/vod/_definst_/MP3:datastreamStore/cerberusData/newfedoradata/datastreamStore/'.$dir.'/info%3Afedora%2F'.$encoded.'%2Fcontent%2Fcontent.0';
              $playlist = 'http://libwowza.neu.edu:1935/vod/_definst_/datastreamStore/cerberusData/newfedoradata/datastreamStore/'.$dir.'/MP3:'. $full_pid .'/playlist.m3u8';
              $no_flash = 'http://libwowza.neu.edu/datastreamStore/cerberusData/newfedoradata/datastreamStore/' . $dir . '/' . urlencode($full_pid);
              $type = 'MP3';
              $provider = 'sound';
            }
            if ($val == 'Video File'){
              $rtmp = 'rtmp://libwowza.neu.edu:1935/vod/_definst_/MP4:datastreamStore/cerberusData/newfedoradata/datastreamStore/'.$dir.'/info%3Afedora%2F'.$encoded.'%2Fcontent%2Fcontent.0';
              $playlist = 'http://libwowza.neu.edu:1935/vod/_definst_/datastreamStore/cerberusData/newfedoradata/datastreamStore/'.$dir.'/MP4:'. $full_pid .'/playlist.m3u8';
              $no_flash = 'http://libwowza.neu.edu/datastreamStore/cerberusData/newfedoradata/datastreamStore/'.$dir.'/'.urlencode($full_pid);
              $type = 'MP4';
              $provider = 'video';
            }
            $playlists .= '{ sources: [ ';
            $playlists .= '{ file: "' .  $rtmp . '"}, { file: "' . $playlist . '"},';
            $playlists .= ' { file: "' . $no_flash . '", type: "'.strtolower($type).'" } ], image: "' . $this_poster . '", title: "' . $title . '" },';
          }
        } else {
          return $errors['shortcodes']['fail'];
        }

      }
      if ($repo == "wp"){
        $post = get_post($pid);
        $this_poster = "";
        $poster[0] = "";
        $title = $post->post_title;
        if (strpos($post->post_mime_type, "video") !== false){
          $provider = 'sound';
        }
        if (strpos($post->post_mime_type, "audio") !== false){
          $provider = 'audio';
        }
        $playlists .= '{sources:[{file:"'.$post->guid.'",title:"'.$title.'"}],title:"'.$title.'"}';
      }

      $download = 'download';

    }
    $pid_selector = "drs-item-video-".str_replace(':', "-", $pid);
    $cache_output = '<div id="'.$pid_selector.'">
        <img style="width: 100%;" src="' . $poster[0] .'" />
      </div>
      <script type="text/javascript">
        jwplayer.key="gi5wgpwDtAXG4xdj1uuW/NyMsECyiATOBxEO7A==";
        var primary = "flash";
        if (typeof swfobject == "undefined" || swfobject.getFlashPlayerVersion().major == 0) {
          primary = "html5";
        }
        jQuery(document).ready(function($){
        jwplayer("'.$pid_selector.'").setup({
          width: "'.$width.'",
          height: "'.$height.'",
          rtmp: { bufferlength: 5 } ,
          image: "'.$this_poster.'",
          provider: "'.$provider.'",
          fallback: "false",
          androidhls: "true",
          aspectratio:"'.$aspectratio.'",';
          if (isset($skin)){
            $cache_output .= 'skin: "'.$skin.'",';
          }
          $cache_output .='primary: primary,';
    if(count($collection) > 1){
        $cache_output .= 'listbar: {
          position: "right",
          size: 250,
          layout: "basic"
        },';
    }
      $cache_output .= 'playlist: [ '. $playlists . ']
        });
        var errorMessage = function(e) {
          $("#drs-item-video").before("<div class=\'alert alert-warning\'>'.$errors['item']['jwplayer_fail'].'<br /><strong>Error Message:</strong> "+e.message+"</div>");
        };
       jwplayer().onError(errorMessage);
       jwplayer().onSetupError(errorMessage);
       jwplayer().onBuffer(function() {
         theTimeout = setTimeout(function(e) {
           errorMessage(e);
         }, 5000);
       });
      });
      </script>';
    $cache_time = 1000;
    set_transient(md5('DRSTK'.serialize($atts)) , $cache_output, $cache_time * 60);
    return $cache_output;

}

function drstk_video_shortcode_scripts() {
    global $post, $VERSION, $wp_query, $DRS_PLUGIN_URL;
    if( is_a( $post, 'WP_Post' ) && (has_shortcode( $post->post_content, 'drstk_collection_playlist') || has_shortcode( $post->post_content, 'drstk_media')) && !isset($wp_query->query_vars['drstk_template_type']) ) {
      wp_register_script('drstk_jwplayer7', $DRS_PLUGIN_URL . '/assets/js/jwplayer/jwplayer.js', array(), $VERSION, false );
      wp_enqueue_script('drstk_jwplayer7');
      wp_register_script('swfobject', '');
      wp_enqueue_script('swfobject');
    }
}
add_action( 'wp_enqueue_scripts', 'drstk_video_shortcode_scripts');
