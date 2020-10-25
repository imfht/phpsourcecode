<?php
require_once('./config.php');
$Channel = new SaeChannel();
$WebSocketURL = $Channel -> createChannel(CHANNEL_NAME);
$KVDB = new SaeKV();
if(!$KVDB->init()){exit('KVDB Error! Please check whether the configuration of KVDB is correct.');}
$Counter = new SaeCounter();
$MessageAmount=$Counter->get(COUNTER_NAME);
$list = array();
for ($ListMinus=0;$ListMinus<=INDEX_MESSAGES_AMOUNTS;$ListMinus++) {
  $ListID=$MessageAmount-$ListMinus;
  $content=$KVDB->get(MESSAGE_PREFIX.$ListID);
  if($content!=false){$list[] = $content;}
}
$json = json_encode($list);
?>
<!DOCTYPE html>
<html>
  
  <head>
    <meta charset="utf-8">
    <title><?php echo SITE_NAME;?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <link href="http://cdn.staticfile.org/twitter-bootstrap/3.2.0/css/bootstrap.min.css" rel="stylesheet" type="text/css">
    <script src="http://cdn.staticfile.org/jquery/2.1.1-rc2/jquery.min.js"></script>
    <script src="http://cdn.staticfile.org/twitter-bootstrap/3.2.0/js/bootstrap.min.js"></script>
    <script>
      function ww_add(m) {
        if (typeof(m) != 'string') return;
        var d = m.split('<?php echo MESSAGE_DELIMITER;?>');
        if (!d[0] || !d[1] || !d[2]) return;
        $('#list').prepend('<div class="col-md-4"><blockquote><p class="lead">' + d[2] + '</p><footer><small><cite>' + d[0] + '发表于' + d[1] + '</cite></small></footer></blockquote></div>');
      }
      $(function() {
        var list = <?php echo $json;?>;
        for (var i = list.length - 1; i >= 0; i--) {
          ww_add(list[i]);
        }
      });
      var socket = new WebSocket('<?php echo $WebSocketURL;?>');
      socket.onmessage = function(message) {
        ww_add(message.data);
      };
    </script>
    <style>
      body { padding-top: 50px; padding-bottom: 20px; }
    </style>
  </head>
  
  <body>
    <div class="navbar navbar-inverse navbar-fixed-top" role="navigation">
      <div class="container">
        <div class="navbar-header">
          <a class="navbar-brand" href="#"><?php echo PAGE_TITLE;?></a>
        </div>
      </div>
    </div>
    <div class="jumbotron">
      <div class="container">
        <h1><?php echo PAGE_TITLE;?></h1>
        <?php echo PAGE_DESCRIBTION;?>
      </div>
    </div>
    <div class="container">
      <div class="row" id="list">
      </div>
      <hr>
      <footer>
        <p>
          &copy;<?php echo date('Y',time());?>&nbsp;<?php echo COPYRIGHT_HOLDER;?>
        </p>
      </footer>
    </div>
  </body>

</html>