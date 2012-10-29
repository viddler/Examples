var callbacks = {
  'getCurrentTime':       function(ret){log(ret);},
  'getDuration':          function(ret){log(ret);},
  'getPlayerState':       function(ret){log(ret);},
  'getVideoBytesLoaded':  function(ret){log(ret);},
  'getVideoBytesTotal':   function(ret){log(ret);},
  'getVideoEmbedCode':    function(ret){log(ret);},
  'getVideoKey':          function(ret){log(ret);},
  'getVideoStartBytes':   function(ret){log(ret);},
  'getVideoUrl':          function(ret){log(ret);},
  'getVolume':            function(ret){log(ret);},
  'isMuted':              function(ret){log(ret);}
};

var current = {
  'embed': '',
  'method': '',
  'ready': 0,
  'video': 0
};

var embeds = {
  'iframe':   'viddler-4c57d97a',
  'legacy':   'viddler_viddler_160',
  'fallback': 'viddlerOuter-4c57d97a'
};

var videos = ['4c57d97a','e1530e86'];

function find_embed(pid)
{
  for (var i in embeds) {
    if (embeds[i] == pid) {
      current.embed = i;
    }
  }
}

function log(msg)
{
  var l = $('#' + current.embed + '-log');
  var cm = (current.method != '') ? current.method + ': ' : '';
  l.val(l.val() + cm + msg + "\n");
  current.method = '';
  l = l.get(0);
  l.scrollTop = l.scrollHeight - l.clientHeight;
}

$('document').ready(function()
{
	for (var i in embeds) {
		var player = Viddler(embeds[i]);
		console.log(i);
		console.log(player);
		
		player.onReady(function()
		{
		  var pid = this.playerId;
		  find_embed(pid);
		  log('player ready');

			current.ready += 1;
			if (current.ready == 3) {
			 $('#controls').fadeIn('slow');
			}
		});
		
		player.onStateChange(function(data)
		{
		  find_embed(this.playerId);
			log('stateChange: ' + data);
		});
		
		player.onError(function(data)
		{
			find_embed(this.playerId);
			log('error: ' + data);
		});
		
		player.onPlaybackQualityChange(function(data)
		{
		  find_embed(this.playerId);
			log('playbackQualityChange: ' + data);
		});
		
		player.onLoginResult(function(data)
		{
		  find_embed(this.playerId);
			log('loginResult: ' + data);
		});
		
		player.onSetThumbnailResult(function(data)
		{
		  find_embed(this.playerId);
			log('setThumbnailResult: ' + data);
		});
		
		player.onVolumeChange(function(data)
		{
		  find_embed(this.playerId);
			log('volumeChange: ' + data);
		});
		
		player.onMuteChange(function(data)
		{
		  find_embed(this.playerId);
			log('muteChange: ' + data);
		});
		
		player.onSaveTagResult(function(data)
		{
		  find_embed(this.playerId);
			log('saveTagResult: ' + data);
		});
		
		player.onSaveCommentResult(function(data)
		{
		  find_embed(this.playerId);
			log('saveCommentResult: ' + data);
		});
		
		player.onRateCommentResult(function(data)
		{
		  find_embed(this.playerId);
			log('rateCommentResult: ' + data);
		});
		
		player.onDeleteCommentResult(function(data)
		{
		  find_embed(this.playerId);
			log('deleteCommentResult: ' + data);
		});
	}
	
	//Control Buttons
  $('input[id^="button-"]').click(function()
  {
    current.embed = $('input[id^="source-"]:checked').val();
    var player = Viddler(embeds[current.embed]);
    current.method = $(this).attr('id').split('-')[1];
    if (current.method == 'loadVideoByKey' || current.method == 'cueVideoByKey') {
      current.video = (current.video == 0) ? 1 : 0;
      player[current.method](videos[current.video]);
      log(videos[current.video]);
    }
    else if (current.method == 'setSize') {
      player.setSize(500,500);
      log('500x500');
    }
    else if (current.method == 'seekTo') {
      player.seekTo(30);
      log('30 seconds');
    }
    else if (current.method == 'setVolume') {
      player.setVolume(30);
      log('30%');
    }
    else {
      var cm = current.method;
      try {
        if (callbacks[cm] !== undefined) {
          var cb = (current.embed == 'iframe') ? callbacks[cm] : callbacks[cm].toString();
          player[cm](cb)
        }
        else {
          log('1');
          player[cm]();
        }
      }
      catch(e) { log(current.embed, 'ERROR >>>' + e); }
    }
    
  });
});