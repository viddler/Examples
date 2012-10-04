<?
if (isset($_GET['videoid'])) {
  print 'Hey your video was uploaded with the ID of: ' . $_GET['videoid'];
}
else {
  print 'No video ID found :(';
}