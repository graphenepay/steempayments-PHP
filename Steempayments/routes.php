<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the Closure to execute when that URI is requested.
|
*/

require_once(app_path() . '/classes/Steem.php');

Route::get('/', function()
{
	return View::make('welcome');
});

// Initiate new payment
Route::get('/pay/', function() {

	$data = array(
			'paymentID' => Steemit_helper::generateMemo(),
			'user' => $_ENV['STEEMPAY_ACCOUNT'],
			'msg' => "message",
	);

		$paymentID = Steemit_helper::generateMemo();
		return View::make('pay')->with($data);
});

// Detect if payment has been made
Route::get('/pay/{id}', function($id) {

	// Has this order already been payed?
	$steem = new Steem($_ENV['STEEM_RPC_SERVER'], $_ENV['STEEM_RPC_PORT']);

	if ($steem->detectDeposit($id)) {

		$response = array(
				'status' => "success",
				'success' => "success",
				'msg' => "message",
		);

		return $response;
	}
	else
	{
	// No, proceed to payment page with instructions
	return View::make('pay')->with('data', $id);
	}
});

// Order has been payed
Route::get('/payed/{id}', function($id)
{
	$details = Steemit_helper::detectDepositDetails($id);

	if($details['memo'] == null) {
		return View::make('pay')->with('data', $id);
	}

	$response = array(
			'status' => "success",
			'memo' => $details['memo'],
			'tx' => array(
				'status' => "success",
				'memo' => $id,
				'from' => $details['from'],
				'to' => $details['to'],
				'amount' => $details['amount'],
				'memo' => $details['memo'],
			),
			'msg' => "message",
	);

	return View::make('payed')->with($response);
});



/**
 * CUSTOM PAYMENT
 */
Route::get('/pay/custom/{user}', function($user) {

	$data = array(
			'paymentID' => Mnemonic_helper::generateRandomString(),
			'user' => $user,
			'msg' => "message",
	);

	return View::make('pay')->with($data);
});

Route::get('/pay/custom/{user}/{id}', function($user, $id) {

	// Has this order already been payed?
	$steem = new Steem($_ENV['STEEM_RPC_SERVER'], $_ENV['STEEM_RPC_PORT']);

	if ($steem->detectCustomDeposit($user, $id)) {

		$response = array(
				'status' => "success",
				'success' => "success",
				'msg' => "message",
		);

		return $response;
	}
	else
	{
		// No, proceed to new page showing instruction
		//$paymentID = Mnemonic_helper::generateRandomString();
		return View::make('pay')->with('data', $id);
	}
});