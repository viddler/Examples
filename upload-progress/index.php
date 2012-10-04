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

//Set session, call prepareUpload
$sessionid = $auth['auth']['sessionid'];
$prepare_resp = $v->viddler_videos_prepareUpload(array('sessionid' => $sessionid));

/**
If the endpoint is not found, exit out.
Again you are going to want to handle this
error better.
**/
if (! isset($prepare_resp['upload']['endpoint'])) {
  print 'Could not extract the correct upload endpoint & token';
  exit;
}

//Set the endpoint, token variables
$endpoint = $prepare_resp['upload']['endpoint'];
$token = $prepare_resp['upload']['token'];
?>

<html>
  <head>
    <title>Viddler Upload Progress</title>
    <link type="text/css" rel="stylesheet" href="progress.css"/>
    <script type="text/javascript" src="//ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js"></script>
    <script type="text/javascript">
      var endpoint  = '<?=$endpoint?>';
      var token     = '<?=$token?>';
      var timer = -1;
      var progress = 0;
      
      //For Testing
      function log(msg)
      {
        if (window.console) {
          console.log(msg);
        }
      }
      
      function clear(msg)
      {
        log('DONE');
        $('#progress-container').fadeOut('fast', function()
        {
          $('#progress-bar').css('width', '5%').html('0%');
          $('#sb').attr('disabled', false);
          progress = 0;
          
          /**
            - Just using an alert, you could make this response more inline
            - If you do not send the user to a different page, you must refresh the uploadtoken since it can only be used once
          **/
          alert(msg);
        });
      }
      
      //Fire off AJAX request to get upload progress
      function checkProgress()
      {
        $.ajax({
          url: 'progress.php?token=' + token,
          cache: false,
          dataType: 'json',
          success: function(res)
          {
            /**
            - Check if progress returned is greater than previous progress
            - This is a precaution in case you start to send more than one request per second, the API *may* not return the first query first ;)
            **/
            var np = parseInt(res.upload_progress.percent, 10);
            progress = (np > progress) ? np : progress;
            var status = parseInt(res.upload_progress.status, 10);
            var ps = progress.toString();
            
            log('Status: ' + status.toString());
            log('Progress: ' + ps);
            
            //If upload is NOT in progress, clear the interval
            if (res.upload_progress.status != 1) {
              clearInterval(timer);
            }
            
            //If there was an error or user cancelled, show the user an alert
            //Can be customized to just show a message inline
            if (res.upload_progress.status == 4 || res.upload_progress.status == 3) {
              clear('Issue uploading your file: ' + res.error.description + ' : ' + res.error.details);
            }
            //Upload is finished
            else if (res.upload_progress.status == 2) {
              clear('Your upload is complete. Thank you.');
            }
            
            //Passes the above, just update the progress bar
            else {
              $('#progress-container').fadeIn('fast', function()
              {
                $('#sb').attr('disabled', true);
                if (progress > 5) {
                  $('#progress-bar').css('width', ps + '%').html(ps + '%');
                }
              });
            }
          },
          error: function(jqXHR, textStatus, errorThrown)
          {
            //Any AJAX error, log the results
            //These typically happen because the page you are requesting is not found or you have an programming error
            log('Error Uploading');
            log(jqXHR);
            log(textStatus);
            log(errorThrown);
            clearInterval(timer);
            clear('There was an error uploading your file. Please try again.');
          }
        });
      }
      
      /**
      Fire off an event to call the checkProgress() method every second when
      the user submits the form for upload.
      **/
      $('document').ready(function()
      {
      	$("#sb").click(function()
      	{
          
          //Set interval, 1000ms = 1 sec
          checkProgress();
        	timer = setInterval(function()
        	{
        	 //Progress less than 100, call again
        	 //Otherwise clear the interval
        	 if (progress < 100) {
        	   checkProgress();
        	 }
        	 else {
        	   //Should never reach this, but if it does, catch it
        	   clearInterval(timer);
        	   clear('Your upload is complete. Thank you.');
        	 }
        	}, 1000);
      	});
      	
      });

    </script>
  </head>
  <body>
    <div id="container">
      <p>
        The form below will submit to a hidden iframe labeled 'upload-iframe'. The file will upload to that target so that you can fire off an AJAX event at the time of upload. The callback used in the upload form will also be trapped in the iframe. You can still execute any logic you have after upload is complete from this page.
      </p>
      <form name="upload" id="upload" action="<?=$endpoint?>" method="post" enctype="multipart/form-data" target="upload-iframe">
        <input type="hidden" name="uploadtoken" id="uploadtoken" value="<?=$token?>" />
        <input type="hidden" name="callback" value="http://<?=$_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']?>callback.php" />
        <p>
          <label for="title">Video Title: </label>
          <input type="text" name="title" id="title" value="" size="40" />
        </p>
        <p>
          <label for="description">Video Description: </label>
          <textarea nam="description" id="description"></textarea>
        </p>
        <p>
          <label for="tags">Video Tags: </label>
          <input type="text" name="tags" id="tags" value="" size="40" />
        </p>
        <p>
          <label for="file">File: </label>
          <input type="file" name="file" id="file" />
        </p>
        <p>
          <label>&nbsp;</label>
          <input type="submit" name="sb" id="sb" value="Upload" />
        </p>
        <p id="progress">
          <div id="progress-container">
            <div id="progress-bar">0%</div>
          </div>
        </p>
      </form>
    </div>
    <iframe name="upload-iframe" id="upload-iframe" src="http://<?=$_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']?>blank.php" width="0" height="0" scrolling="no" frameborder="0" marginheight="0" marginwidth="0"></iframe>
  </body>
</html>