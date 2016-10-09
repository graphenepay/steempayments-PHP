
# Donation vs payment

When choosing to receive **donations**, the user is free to send any amount of any currency. To set the donation option, simple use `amount=0` in your parameters. Set currency to either `SBD` or `STEEM` since it's irrelevant for the end user. The user will receive a message to send any amount to your address.

For fixed amounts, called **payments**, set the `amount` and `currency` to the values required for you. 

**WARNING: Make sure to use 3 decimals!** (AMOUNT.000) when generating a payment. This has multiple reasons, including future expantion of Steempay.io.

# Requesting a new payment

A new payment is created by passing URL or DATA parameters to steempay.io. This means you could send raw HTTP requests, or stay on your own website and send the data via AJAX calls or JSON request.

Following parameters are **required**:

 - receiver 
 - amount 
 - currency

Following parameters are **optional** ***but recommended***

 - callback

## ! IMPORTANT !
Amount : **Make sure to use 3 decimals!** (AMOUNT.000) This has multiple reasons, including future expantion of Steempay.io

Callback: Get's called on success. You can use this to post a success message or handle data on your server/website if needed.

# Handle callbacks

Set the `callback` parameter with your desired url. Always use `http://` to start your url.

If a callback URL has been provided, steempay will `post`  the unique paymentID to your URL.  A simple php script can catch the the callback and verify the payment a last time (recommended). 

example.php
```
<?php
if (isset($_GET['payid'])) {
	// Payment is success, confirm once again
	
	$url = "http://steempay.io/payment/verify?payid=" . $_GET['success'] . "& receiver=YOUR_USERNAME&amount=1.000&currency=SBD";
	
	$json = file_get_contents($url);
	if (json_decode($json->status) && json_decode($json->success) == "success") 
	{
		// Payment is double verified, do whatever needed now.
	}
}else{
	// Some error occured
    echo $_GET['message'];
}
```

Verification is possible via `http://steempay.io/payment/verify?payid=XXX&receiver=XXX&amount=XXX&currency=XXX`.

The returned main parameters are

 - **success** *boolean*
 - **message** *Message about the success status*
 - **payid** *unique serverside generated ID*

The full response is given below (if success)

    {"status":"success","success":true,"message":"Payment completed with fixed amount (0.001 SBD).","block":4306279,"trx_id":"58c415fe70fe7d953e30997b78551415c7e4d190","payid":"Re3Hbl1ekAeSwVtzKS","amount":"0.001 SBD","timestamp":"2016-08-22T14:55:15"}



> Written with [StackEdit](https://stackedit.io/).
