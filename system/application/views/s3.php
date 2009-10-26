<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
      "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">

<html xmlns="http://www.w3.org/1999/xhtml">

<head>

<title>S3</title>

</head>

<body>
<div id="content">
	<table>
		<thead>
			<tr>
				<td>Filename</td>
				<td>Size</td>
				<td>Modified</td>
			</tr>
		</thead>
		<tbody>
			<?php
				foreach($objects->body->Contents as $obj)
				{
					echo '<tr>';
					echo '<td><a href="http://'.$objects->body->Name.'.s3.amazonaws.com/'.rawurlencode($obj->Key).'">'.$obj->Key.'</a></td>';
					echo '<td>'.$utils->size_readable($obj->Size).'</td>';
					echo '<td>'.date('j M Y, g:i a', strtotime($obj->LastModified)).'</td>';
					echo '</td>';
				}
			?>
		</tbody>
	<table>
		<p><a href="<?=site_url('upload');?>">Upload files to bucket...</a></p>
</div>
			
</body>
</html>