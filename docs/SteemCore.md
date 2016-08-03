# Helper classes understanding

The helper class `'SteemHelper'` takes care of some basic things for you, like generating a new paymentID.

`static function generatePaymentID` will return a random paymentID that you can use for your transactions.

You can either use **generatePaymentID** at every call you make or use it as a static value inside your PHP files (recommended option).

A JS function is also included to produce a paymentID on clientside (AJAX/HTML).

# Creating a payment

You can initiate a new payment by displaying a new **generatePaymentID** along with the instructions to follow. 
This includes optionally the amount requested (set to 0 if the buyer can select the amount himself; donations, ...) and a callback URL if needed.*(callback not implemented yet)*

Use the `SteemCore/detectDeposit` function to  loop trough the receiver's transaction to find the paymentID and confirm the transaction.

**Warning: A PaymentID can only be used once!** 


----------


### Pure PHP solution


	$payID = SteemHelper::generatePaymentID();
	
    $data = array(
    			'paymentID' => $payID, 
    			'receiver' => $_ENV['STEEMPAY_ACCOUNT'],
    			'amount' => "0.002 SBD", // Or 0 for donation
    			'callback' => 0,
    	);
    
    $success = SteemCore::makePayment($data);

### AJAX solution


    var paymentID = generatePaymentID();
    
    var interval = 1000;  // 1000 = 1 second
        function doAjax() {
            $.ajax({
                type: 'GET',
                url: '/SERVER_OR_URL/YOUR_FILE.PHP',
                data: {paymentID: 'xxx', receiver: 'xxx', amount: '0.001 STEEM'},
                dataType: 'json',
                success: function (data) {
	                // Redirect to payment completed page
                    window.location.href = "/payed/ + paymentID;
                    // Or do whatever you like to handle a success :-)
                },
                complete: function (data) {
                    // Unpaid, Schedule the next
                    setTimeout(doAjax, interval);
                }
            });
        }
        
    setTimeout(doAjax, interval);

### HTML forms solution

    <form>
	    <input type="hidden" name="paymentID" value="GENERATED_ID">
	    <input type="text" placeholder="amount" name="amount">
        <input type="text" placeholder="receiver" name="receiver">
    </form>
    
    // Inside your AJAX change
    
    data: $(this).serialize(),

Loop trough this code until the response object **status** = 'success' ***AND*** **success** is true. If success is returned, the payment can be considered as completed. Proceed to the next step of verifying the payment.

The response exists of an array with following objects:

 - **Status** *String* Returns either 'success' or 'error'
 - **success** *boolean*
 -  **message** A short description of the status

The reason for both having a status and a success response is because the deposit could succeed, but the verification could fail. For example, if a specific amount does not match the deposited amount.


----------


### Following responses can arise

**Validation error**

    $data['status'] = "error";
    $data['success'] = false;
    $data['message'] = "[HERE WILL BE A SHORT DESCRIPTION].";
   
**Transaction not detected**

    $data['status'] = false;
    $data['success'] = false;
    $data['message'] = "No transaction has been found so far.";

.**Amount verification failure**

    $data['status'] = "success";
    $data['success'] = false;
    $data['message'] = "The deposited amount (" . $operation[1]->amount . ")  does not match the requested (" . $amount . ").";
                                
**Completed payment**

    $data['status'] = "success";
    $data['success'] = true;
    $data['message'] = "Payment completed [WITH_DETAILS]";
                            


----------

### Verifying the payment; **Optional** *but recommended*

After a successfull return, it's recommended to run `SteemHelper::detectDepositDetails()` in the background to get the actual blockchain data returned and compare it against the input.

    if($success['status'] === 'success' && $success['success'] === true) {
	    $verifyPayment = SteemHelper::detectDepositDetails($id,[RECEIVER]);
    };

This will return some more details about the completed transaction including **time**, **block number**, and **trx_id**. You can link the trx_id to a `<a href>` towards the block explorer Steemd.com
