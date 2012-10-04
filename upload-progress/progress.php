<?
//Include the Viddler API library
include '/path/to/viddler/library/';

//Start Viddler object
$v = new Viddler_V2('YOUR API KEY');

//Authenticate
$auth = $v->viddler_users_auth(array(
  'user'      => 'USERNAME',
  'password'  => 'YOUR PASSWORD'
));

/**
If authentication errors, stop the process.
You will want to handle the error better than below,
this was just for example purposes
**/
if (! isset($auth['auth']['sessionid'])) {
  print 'Could not authenticate the account.';
  exit;
}

/**
- Set session ID
- Update format to json to pass back to javascript method for parsing
- Call uploadProgress
- print json result
**/

$sessionid = $auth['auth']['sessionid'];
$v->format = 'json';
$res = $v->viddler_videos_uploadProgress(array(
  'sessionid' =>  $sessionid,
  'token'     =>  urldecode($_GET['token'])
));

print $res;