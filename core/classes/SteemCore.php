<?php
/**
 * SteemPayments.com
 */
require_once(app_path() . '/classes/Core/JSON_RPC.php');

class SteemCore extends JsonRPC
{

    function __construct()
    {
        $this->host = $_ENV['STEEM_RPC_SERVER'];
        $this->port = $_ENV['STEEM_RPC_PORT'];
        $this->version = $_ENV['STEEM_RPC_VERSION'];
    }

    private function steempay_request($method, $params = array())
    {
        try {
            $ret = $this->request($method, $params);
            return $ret->result;
        } catch (RPCException $e) {
            throw $e;
        }
    }

    function about()
    {
        return $this->steempay_request(__FUNCTION__);
    }

    function info()
    {
        return $this->steempay_request(__FUNCTION__);
    }

    function get_block($input)
    {
        return $this->steempay_request(__FUNCTION__, array($input));
    }

    function get_account($input)
    {
        return $this->steempay_request(__FUNCTION__, array($input));
    }

    function get_account_history($input, $seq, $limit)
    {
        return $this->steempay_request(__FUNCTION__, array($input, $seq, $limit));
    }

    public static function makePayment($data)
    {
        // Load our $data array into variables
        $paymentID = $data['paymentID'];
        $receiver = $data['receiver'];
        $amount = $data['amount'];
        $callback = $data['callback'];

        // Create a response array
        $data = array(
            'status' => "error",
            'success' => false,
            'message' => "",
        );

        // Basic error checking
        if($paymentID === '' || $paymentID === null) {
            $data['status'] = "error";
            $data['success'] = false;
            $data['message'] = "No paymentID provided.";
            return $data;
        };
        if($receiver === '' || $receiver === null) {
            $data['status'] = "error";
            $data['success'] = false;
            $data['message'] = "No user set to receive payments.";
            return $data;
        };


        // Read receiver transaction history
        $steem = new Steem();
        $tx = $steem->get_account_history($_ENV['STEEMPAY_ACCOUNT'], -1, 1000);

        // Loop trough each object in our history
        foreach($tx as $transactions) {

            // Itterate over the operations array to detect a deposit
            foreach($transactions[1] as $operation) {

                // Is there any deposit?
                if ($operation[0] === 'transfer') {

                    // Deposit found. Does it match the generated memo?
                    if ($operation[1]->memo === $paymentID) {

                        // Variable amount is requested, so payment ends here
                        if($amount === 0 || $amount === "0") {
                            $data['status'] = "success";
                            $data['success'] = true;
                            $data['message'] = "Payment completed with variable amount of " . $amount . "";
                            return $data;
                        } else {
                            // Does the amount matches the requested amount?
                            if ($operation[1]->amount === $amount) {
                                $data['status'] = "success";
                                $data['success'] = true;
                                $data['message'] = "Payment completed with fixed amount (" . $amount . ").";
                                return $data;
                            } else {
                                $data['status'] = "success";
                                $data['success'] = false;
                                $data['message'] = "The deposited amount (" . $operation[1]->amount . ")  does not match the requested (" . $amount . ").";
                                return $data;
                            }
                        }
                    }
                }
            }
        }
            $data['status'] = false;
            $data['success'] = false;
            $data['message'] = "No transaction has been found so far.";
            return $data;
    }


}
