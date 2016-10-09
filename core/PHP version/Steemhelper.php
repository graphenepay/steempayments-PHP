<?php
/**
 * SteemPay.io developer project
 * Helper class
 */

class SteemHelper {

    /*
     * Generates a basic random string for the paymentID
     */
    static function generatePaymentID() {
        $length = 18;
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $payID = '';
        for ($i = 0; $i < $length; $i++) {
            $payID .= $characters[rand(0, $charactersLength - 1)];
        }
        return $payID;
    }

    /*
     * Provide this one for Javascript users as well :)
     *
      function generatePaymentID()
        {
            var length = 18;
            var text = "";
            var characters = "0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ";

            for( var i=0; i < 5; i++ )
                text += characters.charAt(Math.floor(Math.random() * characters.length));

            return text;
        }
     */

    static function formatCurrency($amount) {
        $amount = number_format($amount, 3, '.', '');
        return $amount;
    }

    static function validatePayment($data) {
        $payID = "";
        $amount = "";
        $currency = "";
        $receiver = "";
        $callback = "";



        // Fixed payID found?
        if (Input::has('payid')) {$payID = Input::get('payid');} else {$payID = SteemHelper::generatePaymentID();};

        if (Input::has('callback')) {$callback = Input::get('callback');} else {$callback = "http://www.steempay.io";};

        if (Input::has('amount') && Input::has('currency') && Input::has('receiver')) {

            $amount = Input::get('amount');
            $currency = Input::get('currency');
            $receiver = Input::get('receiver');


            if ($amount === 0) {
                // Donation handler

            } else {
                // Fixed payment handler
                $amount = number_format($amount, 3, '.', '');

            }

            // Create the data array

            $data = array(
                'paymentID' => $payID,
                'receiver' => $receiver,
                'amount' => $amount,
                'currency' => $currency,
                'callback' => $callback,
            );

        }
        // No amount, currency or receiver set, show some error
        else {

        }
        return $data;
    }

    // Still in development, hold on!
    static function makeCallback($url, $data){
        $url = 'http://www.someurl.com';
        ;

        $ch = curl_init( $url );
        curl_setopt( $ch, CURLOPT_POST, 1);
        curl_setopt( $ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt( $ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt( $ch, CURLOPT_HEADER, 0);
        curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1);

        $response = curl_exec( $ch );
    }

    public static function detectDepositDetails($paymentID, $receiver)
    {
        $steem = new Steem();
        $tx = $steem->get_account_history($receiver, -1, 1000);

        $tx_time = "";

        // Loop trough each object in our history
        foreach($tx as $transactions) {

            // Itterate over the operations array to detect a deposit
            foreach($transactions[1] as $operation) {

                $tx_time = $transactions[1]->timestamp;
                $tx_trx_id = $transactions[1]->trx_id;
                $tx_block = $transactions[1]->block;

                // Is there any deposit?
                if ($operation[0] === 'transfer') {

                    // Deposit found. Does it match the generated memo?
                    if ($operation[1]->memo === $paymentID) {

                        $operation = array(
                            'block' => $tx_block,
                            'trx_id' => $tx_trx_id,
                            'operation' => 'transfer',
                            'from' => $operation[1]->from,
                            'to' => $operation[1]->to,
                            'amount' => $operation[1]->amount,
                            'memo' => $operation[1]->memo,
                            'timestamp' => $tx_time,
                        );

                        return $operation;
                    }
                }
            }
        }
        return false;
    }

}

