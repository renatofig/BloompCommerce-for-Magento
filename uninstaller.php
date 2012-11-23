<?php
$TARGET = $argv[1];

if( $TARGET != ''){

	echo 'Removing files...'."\n";
	echo '-----------'."\n";

	# Copy files
	
	if(unlink($TARGET.'/app/etc/modules/Bloompa_BloompCommerce.xml'))
		echo $TARGET.'/app/etc/modules/Bloompa_BloompCommerce.xml'." ............. Ok! \n";
	else
		echo $TARGET.'/app/etc/modules/Bloompa_BloompCommerce.xml'." ............. Ok! \n";

	if(unlink($TARGET.'/app/design/frontend/base/default/layout/bloompa_bloompcommerce.xml'))
		echo $TARGET.'/app/design/frontend/base/default/layout/bloompa_bloompcommerce.xml'." ............. Ok! \n";
	else
		echo $TARGET.'/app/design/frontend/base/default/layout/bloompa_bloompcommerce.xml'." ............. FAIL! \n";


	//recurse_delete($TARGET.'/app/community/Bloompa');
	//recurse_delete($TARGET.'/app/design/frontend/base/default/template/bloompa');
	
	
	echo '-----------'."\n";
	echo 'Done!'."\n";

}else{

	echo "\n"."No Magento directory was informed."."\n";

}