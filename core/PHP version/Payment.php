<?php
/**
 * SteemPayments.com
 * Payment object
 */

class Payment{

    public  $payid, $to, $amount, $currency, $callback, $success;

    function __construct($data)
    {
        if(array_has($data, "payid")) {$this->payid = $data['payid'];} else {$this->payid = SteemHelper::generatePaymentID();};

        if(array_has($data, "callback")) {$this->callback = $data['callback'];} else {$this->callback = "http://www.steempay.io";};
        //
        if (array_has($data, "amount") && array_has($data, "currency") && array_has($data, "receiver")) {

            $this->amount = $data['amount'];
            $this->currency = $data['currency'];
            $this->to = $data['receiver'];


            if ($this->amount != "0") {
                // Fixed payment handler
                $this->amount = number_format($this->amount, 3, '.', '');
            }

            $this->success = true;

            // Create the data array

            $data = array(
                'success' => true,
                'paymentID' => $this->payid,
                'receiver' => $this->to,
                'amount' => $this->amount,
                'currency' => $this->currency,
                'callback' => $this->callback,
            );
        } else
        {
            $this->success =false;

            $data = array(
                'success' => false,
                'paymentID' => $this->payid,
                'receiver' => $this->to,
                'amount' => $this->amount,
                'currency' => $this->currency,
                'callback' => $this->callback,
            );
        }
    }

    function getPayment() {
        return $this->payid;
    }

    function toArray()
    {
        return array(
            array
            (
                'payid'=>$this->payid,
                'to'=>$this->to,
                'amount'=>$this->amount,
                'callback'=>$this->callback,

            )
        );
    }
}
