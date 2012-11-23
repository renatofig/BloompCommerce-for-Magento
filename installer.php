<?php

$TARGET = $argv[1];
//exit(var_dump($TARGET));

define('DS', DIRECTORY_SEPARATOR); // I always use this short form in my code.
function recurse_copy( $path, $dest )
{
    if( is_dir($path) )
    {
    		if(!file_exists($dest)){
        	if(mkdir( $dest ))
        		exit("Fail to create dir $dest \n");
        }
        	
        $objects = scandir($path);
        if( sizeof($objects) > 0 )
        {
            foreach( $objects as $file )
            {
                if( $file == "." || $file == ".." )
                    continue;
                // go on
                if( is_dir( $path.DS.$file ) )
                {

                    if(recurse_copy($path.DS.$file, $dest.DS.$file )){
                    	echo $dest.DS.$file." ............. Ok! \n";
                    }else{
                    	echo $dest.DS.$file." ............. FAIL! \n";
                    }
                }
                else
                {
                    if(copy( $path.DS.$file, $dest.DS.$file )){
                    	echo $dest.DS.$file." ............. Ok! \n";
                    }else{
                    	echo $dest.DS.$file." ............. FAIL! \n";	
                    }
                }
            }
        }
        return true;
    }
    elseif( is_file($path) )
    {
        if(copy($path, $dest)){
        	echo $dest." ............. Ok! \n";
        	return true;
        }else{
        	echo $dest." ............. FAIL! \n";
        }
    }
    else
    {
        return false;        
    }
}


if( $TARGET != ''){

	echo 'Copying files...'."\n";
	echo '-----------'."\n";

	# Copy files
	recurse_copy('app/code/community/Bloompa', $TARGET.'/app/community/Bloompa');
	recurse_copy('app/etc/modules/Bloompa_BloompCommerce.xml', $TARGET.'/app/etc/modules/Bloompa_BloompCommerce.xml');	
	recurse_copy('app/design/frontend/base/default/layout/bloompa_bloompcommerce.xml', $TARGET.'/app/design/frontend/base/default/layout/bloompa_bloompcommerce.xml');
	recurse_copy('app/design/frontend/base/default/template/bloompa', $TARGET.'/app/design/frontend/base/default/template/bloompa');
	
	
	echo '-----------'."\n";
	echo 'Done!'."\n";

}else{

	echo "\n"."No Magento directory was informed."."\n";

}