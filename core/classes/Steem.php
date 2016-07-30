<?php

require_once(app_path() . '/classes/JSON_RPC.php');

class Steem extends JSON_RPC
{
    private function steempay_request($method, $params=array())
    {
        try
        {
            $ret = $this->request($method, $params);
            return $ret->result;
        }
        catch(RPCException $e)
        {
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
	
}
?>