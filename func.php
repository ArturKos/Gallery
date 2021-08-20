<?php
 function log_user($user)
 {
  $date = new DateTime();
  $date = $date->format("d:m:y H:i:s");
	$file = 'log.txt';
  // Open the file to get existing content
  $current = file_get_contents($file);
  // Append a new person to the file
  $current .= $date." ".$user." ".$_SERVER['REMOTE_ADDR']."\n";
  // Write the contents back to the file
  file_put_contents($file, $current);
 }

 function Szukaj_plikow($ka)
 {
  $url_dec = urldecode($ka);
	//$url_dec = $url_dec."//";
 	foreach(new DirectoryIterator($url_dec) as $file)
   if(!$file->isDot() && $file->isFile())
	  {
		 if (strtolower($file->getExtension ( ))=="jpg" || strtolower($file->getExtension ( ))=="jpeg" ||
				   strtolower($file->getExtension ( ))=="tif" || strtolower($file->getExtension ( ))=="tiff" ||
				   strtolower($file->getExtension ( ))=="bmp" || strtolower($file->getExtension ( ))=="gif"	 ||
				   strtolower($file->getExtension ( ))=="wmf" || strtolower($file->getExtension ( ))=="wmf")
    {
     $thumb = $file->getPath()."//miniatury//".$file->getFilename();
     if(file_exists($thumb))
       echo "<a class=linki href='".$file->getPathname()."' \target=\_blank\><img src='".$thumb."' width=100></a>"; else 
			    echo "<a class=linki href='".$file->getPathname()."' \target=\_blank\><img src='".$file->getPathname()."' width=100></a>";
    } else
		 if (strtolower($file->getExtension( ))=="mp4")
		  echo "<video controls width=200><source src='".$file->getPathname()."' type='video/mp4' ></video>"; else
		 if (strtolower($file->getExtension ( ))=="mkv")
		  echo "<video controls width=200 src='".$file->getPathname()."'></video>"; else
    {
     $size_in_bytes = "MiB";
     $size = $file->getSize()/(1024*1024);
     $size_string = round( $size, 1)." ".$size_in_bytes;
			  echo "<a class=linki title='Kliknij aby pobrać lub odtworzyć' href='".$file->getPathname()."'\target=\_blank\>".$file->getFilename()." [".$size_string."]</a><br>";
    }
 }
}
function Szukaj_katalogow($sciezka)
 {
	global $aktualny;
	foreach(new DirectoryIterator($sciezka) as $file)
   if(!$file->isDot() && $file->isDir() && ($file->getFilename()!="miniatury"))
	  echo "<dd><a href=index.php?directory=".urlencode("$aktualny"."/".$file->getFilename()).">".$file->getFilename()."</a></dd>";
}
?>
