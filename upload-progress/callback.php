<?
//Get the video ID
//You can extract any other info you have passed with the callback url as well
if (isset($_GET['videoid'])) {
  print 'The video was saved under the ID of: ' . $_GET['videoid'];
}
else {
  print 'There was an error uploading your file.';
}