<html>
<head>
<title>test Upload</title>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<link href="<?=$SWFRoot?>default.css" rel="stylesheet" type="text/css" />
</head>
<body>
<?php

$keys = split(",",$this->uri->segment(3)); // cafeful, need to sanatize -- this has not been done!

// show files
if ( is_array($keys) ) {
  if ($keys[0] != '') {
    foreach ($keys as $value) {
      echo 'File transferred: ' . $BUCKET . '/' . $value . '<br />';
      $expires = time() + 1*24*60*60/*$expires*/;
      $resource = $BUCKET."/".urlencode($value);
      $stringToSign = "GET\n\n\n$expires\n/$resource";
      $signature = urlencode(base64_encode(hash_hmac("sha1", $stringToSign, $AWS_SECRET_ACCESS_KEY, TRUE/*raw_output*/)));

      $privateURL = "<a href=\"http://s3.amazonaws.com/$resource?AWSAccessKeyId=".$AWS_ACCESS_KEY_ID."&Expires=$expires&Signature=$signature\">$BUCKET/$value</a>";
      $publicURL = "<a href=\"http://".$BUCKET.".s3.amazonaws.com/$value\">".$value."</a>";
 
      echo "URL (private read): $privateURL <br />";
      echo "URL (public read) : $publicURL <br />";
      echo "<br />";
    } // foreach
  } else {
    echo "Nothing was uploaded, try again.";
  } 
} 
?>
<?php if (preg_match("/.*(png|jpg|gif)/",$value)) { // very simple check for a picture (only on last file) -- for fun ?>
<img src="http://<?=$BUCKET?>.s3.amazonaws.com/<?=$value?>" />
<?php } ?>

<p><a href="<?=site_url('upload');?>">&laquo; Go back and upload more files...</a></p>
</body>
</html>