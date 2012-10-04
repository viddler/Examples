<?php

include '/path/to/viddler/';

$callback_url = 'CALLBACK';

$v = new Viddler_V2('API KEY');

// Get a sessionid
$auth = $v->viddler_users_auth(array(
  'user'     => 'USERNAME',
  'password' => 'PASSWORD'
));

/**
If session id cannot be found there was an error authenticating your account.
You will want to handle this error better, but for example purposes we will
simply kill the script.

This will simply print the return error #, description and details.
**/
if (! isset($auth['auth']['sessionid'])) {
  print '<pre>';
  print_r($auth);
  print '</pre>';
  exit;
}

//Set session id to a variable
$sessionid = $auth['auth']['sessionid'];

// Call prepareUpload to retrieve the token and endpoint we need to use
$prepare_resp = $v->viddler_videos_prepareUpload(array('sessionid' => $sessionid));

/**
If not endpoint is found there was an error. Again you are going
to want to handle this error better than below.

This will simply print the return error #, description and details.
**/
if (! isset($prepare_resp['upload']['endpoint'])) {
  print '<pre>';
  print_r($prepare_resp);
  print '</pre>';
  exit;
}

$upload_server = $prepare_resp['upload']['endpoint'];
$upload_token  = $prepare_resp['upload']['token'];

/**
HTML field below is to support replacing of a current video.
If you want to upload a video to replace an already existing video,
simply add a hidden input with the name of 'video_id' and a value
of the video_id to replace.

IE: <input type="hidden" name="video_id" value="VIDEO_ID_TO_REPLACE (OPTIONAL)" />
**/
?>

<form method="post" action="<?= $upload_server ?>" enctype="multipart/form-data">
  <input type="hidden" name="uploadtoken" value="<?= $upload_token ?>" />
  <input type="hidden" name="callback" value="http://www.YOURSITE.com/callback.php" />
  <label>Title:</label> <input type="text" name="title" /><br />
  <label>Description:</label> <input type="text" name="description" /><br />
  <label>Tags:</label> <input type="text" name="tags" /><br />
  <label>File:</label> <input type="file" name="file" /><br />
  <input type="submit" value="Upload" />
</form>