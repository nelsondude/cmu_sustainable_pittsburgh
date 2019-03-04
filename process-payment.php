<!DOCTYPE HTML>
<html>
<head>
<metahttp-equiv="Content-Type" content="text/html; charset=UTF-8">
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
$x_user4 = $_POST["x_user4"];

// Take x_login from Payment Page ID in Payment Pages interface
// Take transaction_key from Payment Pages configuration interface
switch ($x_amount) {
	case 'gwc1':
		$x_login         = "WSP-SUSTA-DdiRJQBHug";
		$transaction_key = "R2pd1KbkequWrjGYhHMv";
		$x_amount        = 10;
		break;
	case 'gwc2':
		$x_login         = "WSP-SUSTA-9XjJTABHuw";
		$transaction_key = "PruLv3SFbNeNtYHJh_K6";
		$x_amount        = 65;
		break;
	case 'gwc3':
		$x_login         = "WSP-SUSTA-8mw7OQBHvA";
		$transaction_key = "WvDWQ6ht1frTdrslG0FX";
		$x_amount        = 45;
		break;
	case 'gwc4':
		$x_login         = "WSP-SUSTA-jtb9GwBHvQ";
		$transaction_key = "zdnNfV5~MPESAb5VH7XF";
		$x_amount        = 10;
		break;
	case 'gwc5':
		$x_login         = "WSP-SUSTA-r1leYwBHvg";
		$transaction_key = "XMAtG7oIH94iqVMlLswE";
		$x_amount        = 45;
		break;
	case 'gwc6':
		$x_login         = "WSP-SUSTA-NrkaEQBHvw";
		$transaction_key = "aFV7r4ePpnQ~SAvq65rq";
		$x_amount        = 85;
		break;
	case 'gwc7':
		$x_login         = "WSP-SUSTA-Ru1cCgBHwQ";
		$transaction_key = "20Wp4Dga7oypXrGL8r8s";
		$x_amount        = 65;
		break;
	case 'gwc8':
		$x_login         = "WSP-SUSTA-7nex6QBHwg";
		$transaction_key = "Yd_Zm7h2OaOaHmSJOaUy";
		$x_amount        = 45;
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
echo ('<input name="x_user4" value="' . $x_user4 . '" type="hidden">');

?>

<input type="hidden" name="x_show_form" value="PAYMENT_FORM"/>
</form>

<script type='text/javascript'>document.myForm.submit();</script>

</body>
</html>

