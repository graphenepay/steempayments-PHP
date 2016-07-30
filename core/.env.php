<?php

/** Configuration file **/

return array(

    'ENABLE_DEBUG'    => true,
	
	// The account to receive payments with
    'STEEMPAY_ACCOUNT' => 'your-account-to-receive-payments',

	// Node to plugin
    'STEEM_RPC_SERVER' => '127.0.0.1',
    'STEEM_RPC_PORT' => 8090,
	
	// If you like a prefix in your memo, use it here
	'MEMO_PREFIX' => '',
	
	// Exchange settings
	'PRICE_TICKER' => 'POLONIEX',
	/* If you want to add a % above the live market data - NOT ADVICED! */
	'PRICE_CONVERT_PERCENTAGE_EXTRA' => 0,

);