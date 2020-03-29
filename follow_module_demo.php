<?php


include 'freemedya_api.php';


$username = 't13r';
$password = '123456';



$insta = new FreeMedya();
$response = $insta->Login($username, $password);



if(strpos($response[1], "Üzgünüm Mk")) {
		echo "İp Blocklanmış Olabilir vpn kullan.";
			print_r($response);
				exit();
}


if(empty($response[1])) {

		echo "Sunucudan boş veri geldi.";
	print_r($response);
	exit();
}

// takip et tek kod

$res=$insta->TakipEt("3712100395");


exit();

?>
