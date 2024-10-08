<html><head>
<title>my guestbook</title>
<?php
$data = 'data.txt';
$contents = file_exists($data) ? file_get_contents($data) : '';
if (!empty($contents)) {
 $lines = explode("\n", $contents);
 $posts = array();
 foreach ($lines as $line) {
  $parts = explode(',', $line);
  if (count($parts) > 1) {
   $posts[] = array('user' => $parts[0],
   'time' => date('Y-m-d', $parts[1]),
   'message' =>  $parts[2]);
  }
 }
}

$error = false;

if (isset($_POST['userpost']) && empty($_POST['email'])) {
 $name = trim($_POST['userpost']);
 $name = str_replace(',', '&#44;', $name);
 $name = filter_var($_POST['userpost'], FILTER_SANITIZE_STRING);
 
 $url = filter_var($_POST['urlpost'], FILTER_SANITIZE_STRING);
 $url = trim($url);
 $url = str_replace('http://','',$url);
 $url = str_replace('https://','',$url);
 $url = rtrim($url,"/");

 $time = time();

 $message = filter_var($_POST['messagepost'], FILTER_SANITIZE_STRING);
 $message = str_replace("\r\n", "<br>", $message);
 $message = str_replace(',', '&#44;', $message);

 if (($name != '') AND ($message != '')
 ) {
  if (!empty($url)) {
   $user = '<a href="http://' . $url . '" target="_blank">' . $name . '</a>';
  } else {
   $user = $name;
  }

  $line = $user . ',' . $time . ',' . $message . "\n";

//  uncomment the two lines below and add your email address if you want to receive an email when you get a new message
//  $mailline = "a message by " . $name . " @ " . date('Y-m-d H:i', $time) . "\n" . $url .  "\n\n" . $_POST['messagepost'];
//  mail('your@email.com', 'guestbook entry', $mailline);

  header('Location:./');
  $file = fopen($data, 'a');
  if (!fwrite($file, $line)) {
   $error = '<div class="mssg">I could not post your message, probably a problem with serverside file permissions.</div>';
  }
  fclose($file);
  unset($_POST);
 } else {
  $error = '<div class="mssg">It looks to me like you did not fill in a name or a message.</div>';
 }
}
?>
<style>
.small{font-size:9px;font-weight:normal;text-align:justify;margin:0px}
.mssg{border:1px dashed #000;margin:5px;padding:30px}
</style>
</head><body>

<h1>guestbook</h1>

Welcome to the guestbook. Please use only plain text when writing your message; your &lt;html&gt; has no power in this place.<p>

<form action="./#message" method="post" id="t">
 <table style="width:100%; text-align:center">
  <tr>
   <td>
    <label id="message">message<br>
    <textarea name="messagepost" style="width:220px; height:200px" maxlength="3000"><? if (isset($_POST['userpost'])) echo $_POST['messagepost']; ?></textarea></label>
   </td>
   <td>
    <label>name<br>
    <input type="text" name="userpost" maxlength="100" size="16"<? if (isset($_POST['userpost'])) echo 'value="' . $_POST['userpost'] . '"'; ?>></label><p>
    <label>website <span class="small">(not required)</span><br>
    <input type="text" maxlength="100" name="urlpost" size="16"<? if (isset($_POST['userpost'])) echo 'value="' . $_POST['urlpost'] . '"'; ?>><br>
    <span class="small">(<i>not</i> your email address)</span></label><p>
    <input type="submit" name="submit" value="post" style="margin-top:45px">
    <label style="position:absolute; left:-5000px">don't put anything in this field!<br><input type="text" name="email" style="position:absolute; left:-5000px" size="16"<? if (isset($_POST['email'])) echo 'value="' . $_POST['email'] . '"'; ?>></label>
   </td>
  </tr>
 </table>
</form>

<? if ($error !== false): echo $error;
endif;
if (!empty($contents)) {foreach (array_reverse($posts) as $post): echo
'<div class="mssg"><div class="small"><b>',$post['user'],
'</b> (',$post['time'],')</div>',
stripslashes($post['message']),'</div>', "\n";
endforeach;} else {echo '<div class="mssg">I could not post your message, probably a problem with serverside file permissions.</div>';} ?>

</body></html>
