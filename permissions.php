<?php
error_reporting(E_ALL);

function test($path, $filemode) {
	echo $path."<br>";
 	if (!is_dir($path)) {
       return chmod($path, $filemode);
 	}
 	
   $dh = opendir($path);
   while ($file = readdir($dh)) {
       if($file != '.' && $file != '..') {
           $fullpath = $path.'/'.$file;
           if(!is_dir($fullpath)) {
             if (!@chmod($fullpath, $filemode))
                 return FALSE;
           } else {
             if (!test($fullpath, $filemode))
                 return FALSE;
           }
       }
   }
 
   closedir($dh);
  
   if(chmod($path, $filemode)) {
     return TRUE;
   } else {
     return FALSE;
   }
}

echo test('/home/vhosts/gkw-test.unibas.ch/httpdocs/typo3conf/ext/x4equalification',0777);

?>