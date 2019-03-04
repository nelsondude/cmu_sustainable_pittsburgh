<!DOCTYPE HTML>

<html>

<head>

<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">

<title>

Processing

</title>

</head>



<body>

	<center>

	<img src="http://www.gwcpgh.org/images/logo.jpg">

	<p>Processing...</p>

	</center>



<form action="https://globalgatewaye4.firstdata.com/pay" method="POST" name="myForm" id="myForm">



<?php

$x_amount = $_POST["x_amount"];

$x_user1 = $_POST["x_user1"];

$x_user2 = $_POST["x_user2"];

$x_user3 = $_POST["x_user3"];



// Take x_login from Payment Page ID in Payment Pages interface

// Take transaction_key from Payment Pages configuration interface

switch ($x_amount) {

	case 25:

		$x_login="WSP-SUSTA-ordy5QAxqw";

		$transaction_key = "u7f8uX0mnJvWSVRf~YDa";

		break;

	case 35:

		$x_login="WSP-SUSTA-6Ep7kwAxrA";

		$transaction_key = "~oS~uDWd8uH75uWXEFef";

		break;

	case 45:

		$x_login="WSP-SUSTA-raM6tAAxrg";

		$transaction_key = "BffNUCFePVjvv0mmLD1p";

		break;

}



$x_currency_code = "USD"; // Needs to agree with the currency of the payment page

srand(time()); // initialize random generator for x_fp_sequence

$x_fp_sequence = rand(1000, 100000) + 123456;

$x_fp_timestamp = time(); // needs to be in UTC. Make sure webserver produces UTC



// The values that contribute to x_fp_hash

$hmac_data = $x_login . "^" . $x_fp_sequence . "^" . $x_fp_timestamp . "^" . $x_amount . "^" . $x_currency_code;

$x_fp_hash = hash_hmac('MD5', $hmac_data, $transaction_key);



echo ('<input name="x_login" value="' . $x_login . '" type="hidden">' );

echo ('<input name="x_amount" value="' . $x_amount . '" type="hidden">' );

echo ('<input name="x_fp_sequence" value="' . $x_fp_sequence . '" type="hidden">' );

echo ('<input name="x_fp_timestamp" value="' . $x_fp_timestamp . '" type="hidden">' );

echo ('<input name="x_fp_hash" value="' . $x_fp_hash . '" size="50" type="hidden">' );

echo ('<input name="x_currency_code" value="' . $x_currency_code . '" type="hidden">');

echo ('<input name="x_user1" value="' . $x_user1 . '" type="hidden">');

echo ('<input name="x_user2" value="' . $x_user2 . '" type="hidden">');

echo ('<input name="x_user3" value="' . $x_user3 . '" type="hidden">');



?>



<input type="hidden" name="x_show_form" value="PAYMENT_FORM"/>

</form>



<script type='text/javascript'>document.myForm.submit();</script>



</body>

</html>

