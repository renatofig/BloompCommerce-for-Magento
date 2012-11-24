<?php

require('bloompcommerce_resources/db_functions.php');

$TARGET = $argv[1];


echo "\n-----------";
echo "\nBloompCommerce Uninstaller (for MAGENTO 1.7):";
echo "\n-----------\n";

define('DS', DIRECTORY_SEPARATOR); // I always use this short form in my code.

 function recurse_delete($dir) { 
   if (is_dir($dir)) { 
     $objects = scandir($dir); 
     foreach ($objects as $object) { 
       if ($object != "." && $object != "..") { 
         if (filetype($dir."/".$object) == "dir") 
         	recurse_delete($dir."/".$object); 
         else{ 
         
         	if(unlink($dir."/".$object)){
         		echo $dir."/".$object." ...... Ok! \n";
         	}else{
						echo $dir."/".$object." ...... FAIL! \n";
         	}	
       	 
       	 }

       } 
     } 
     
     if(rmdir($dir)){
			echo $dir." ........ Ok! \n";
     }else{
     	echo $dir." ........ FAIL! \n";
     }
		
		reset($objects); 
   }else{
			if(file_exists($dir)){
				if(unlink($dir))
					echo $dir." ............. Ok! \n";
				else
					echo $dir." ............. Ok! \n";
			}	   	
   } 
 }




if( $TARGET != ''){

	echo 'Removing files...'."\n";
	echo '-----------'."\n";

	# delete files	
	recurse_delete($TARGET.'/app/etc/modules/Bloompa_BloompCommerce.xml');
	recurse_delete($TARGET.'/app/design/frontend/base/default/layout/bloompa_bloompcommerce.xml');
	recurse_delete($TARGET.'/app/code/community/Bloompa');
	recurse_delete($TARGET.'/app/design/frontend/base/default/template/bloompa');
	

	
	echo '-----------'."\n";
	echo 'Done!'."\n";
	
	db_delete_tables();
	
}else{

	echo "\n"."No Magento directory was informed."."\n";

}
echo "\n";