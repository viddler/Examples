<?
include '/path/to/viddler/api/';

$v = new Viddler_V2('API_KEY');
$auth = $v->viddler_users_auth(array(
  'user'      =>  'YOUR USERNAME',
  'password'  =>  'YOUR PASSWORD'
));

/**
- Check for the session id
- If not found, there was an error
- You will want to handle this error better than the example below.
- You should never print out errors to your production system
**/
if (! isset($auth['auth']['sessionid'])) {
  print '<pre>';
  print_r($auth);
  print '</pre>';
  exit;
}

//Get Thumbnails
$video = $v->viddler_videos_getDetails(array(
  'sessionid' =>  $auth['auth']['sessionid'],
  'video_id'  =>  'VIDEO_ID'
));

/**
- Check for thumbnails count
- If not found, there was an error
- You will want to handle this error better than the example below.
- You should never print out errors to your production system
**/
if (! isset($video['video']['thumbnails_count'])) {
  //Handle no thumbnails error
}

//Original Thumb and Total Thumbs
$org_thumb = $video['video']['thumbnail_url'];
$total_thumbs = $video['video']['thumbnails_count'];

//Looping and show medium thumb version of each
for ($i = 0; $i < $total_thumbs; $i++) {

  //Replace _2_ with _1_ to get medium thumb rather than large
  $tmp_thumb = str_replace('_2_', '_1_', $org_thumb);
  
  //Regex to replace _v# with current thumb
  $tmp_thumb = preg_replace('/(.*?)(_v[0-9])(\..*?)/', "$1_$i$3", $tmp_thumb);
  
  //Display it
  print '<img src="' . $tmp_thumb . '" width="114" height="86" border="0" />';
}
?>