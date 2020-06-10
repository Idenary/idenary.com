<?php

use Elliptic\EC\Signature;
use Web3p\RLP\RLP;

function encode($s)
{
    $s = str_replace("/", "%2F", $s);
    $s = str_replace(":", "%3A", $s);
    return $s;
}

class IdenaAuth
{

    private $config;

    public function __construct($config)
    {
        $this->config = $config;
        session_start();
    }

    public function is_auth()
    {
        // Read cookie
        @$token = $_SESSION["idena_token"];
        if (!$token)
        {
            return false;
        }
        // if cookie gives a token, check token validity
        // if token valid, returns matching address
        return $this->load_address_for_token($token, true);

    }

    public function logout()
    {
        // remove token file
        @$token = $_SESSION["idena_token"];
        if (file_exists($this->file_for_token($token)))
        {
            @unlink($this->file_for_token($token));
        }
        // Clear session token
        $_SESSION["idena_token"] = '';
        error_log("logout " . $token);
        header('Location: ' . $this->config["logout_url"]);
        die();
    }

    public function gc()
    {
        // TODO
        // Parse token files and delete too old ones.
        // No need to call too often.
        // can also be based upon last modification time.
        
    }

    public function get_nonce($token, $address)
    {
        $load = $this->config["secret_salt"] . $token . $address;
        $eth_util = new Web3p\EthereumUtil\Util();
        $load_hash = $eth_util->sha3($load);
        $nonce = "signin-" . $load_hash; // + str(int(token.encode('utf-8').hex()) | int(address.encode('utf-8').hex())) // hash avec salt
        //error_log("get_nonce $nonce");
        return $nonce;
    }

    public function get_token()
    {
        $res = "";
        for ($i = 0;$i < 16;$i++) $res .= chr(rand(0, 255));
        return bin2hex($res);
    }

    public function get_dna_url($token = "")
    {
        if ($token == "")
        {
            $token = $this->get_token();
        }
        $callback_url = encode($this->config["callback_url"]);
        $nonce_endpoint = encode($this->config["nonce_endpoint"]);
        $authentication_endpoint = encode($this->config["authentication_endpoint"]);
        $url = "dna://signin/v1?token=$token&callback_url=$callback_url&nonce_endpoint=$nonce_endpoint&authentication_endpoint=$authentication_endpoint";
        if (isset($this->config["favicon_url"]))
        {
            $url .= "&favicon_url=" . $this->config["favicon_url"];
        }
        //error_log("Saving session token ".$token);
        $_SESSION["idena_token"] = $token;
        return $url;
    }

    private function file_for_token($token)
    {
        return $this->config["tokens_dir"] . $token . ".json";
    }

    private function save_address_for_token($token, $address, $auth = 0)
    {
        if ($token == '')
        {
            return;
        }
        $token_file = $this->file_for_token($token);
        file_put_contents($token_file, trim($address) . "\n" . (string)(time()) . $this->config["timeout"] . "\n" . $auth);
    }

    private function load_address_for_token($token, $check_auth = false)
    {
        $token_file = $this->file_for_token($token);
        if (!file_exists($token_file))
        {
            error_log("no file for  " . $token);
            return '';
        }
        $lines = file($token_file);
        $timeout = trim($lines[1]);
        if ($timeout <= time())
        {
            error_log("timeout for  " . $token . " " . $timeout . " vs time " . time());
            return '';
        }
        @$auth = trim($lines[2]);
        if ($check_auth && $auth == 0)
        {
            return '';
        }
        return trim($lines[0]);
    }

    public function get_nonce_response($request, $as_json = false)
    {
        if (gettype($request) == 'string')
        {
            $request = json_decode($request, true);
        }
        // TODO: sanitize user input to avoid malicious entries - token should be hex only, have a better filter.
        $request["token"] = str_replace([" ", ".", "\\", "/", "*", "&", '|'], "", $request["token"]);
        // save address and ts matching the token
        $this->save_address_for_token($request["token"], $request["address"]);
        $response = array(
            "success" => true,
            "data" => array()
        );
        $response["data"]["nonce"] = $this->get_nonce($request["token"], $request["address"]);
        if ($as_json == true)
        {
            return json_encode($response);
        }
        else
        {
            return $response;
        }
    }

    public function get_authentication_response($request, $as_json = false)
    {
        if (gettype($request) == 'string')
        {
            $request = json_decode($request, true);
        }
        $token = str_replace([" ", ".", "\\", "/", "*", "&", '|'], "", $request["token"]);
        //error_log("get_authentication_response ".json_encode($request));
        $response = array(
            "success" => true,
            "data" => array()
        );
        $response["data"]["authenticated"] = true;
        // get address from token
        $address = $this->load_address_for_token($token);
        if ($address == '')
        {
            //error_log("No file for token ".$token);
            $response["data"]["authenticated"] = false;
            $auth = 0;
        }
        else
        {
            //error_log("Expecting address ".$address);
            
        }
        if ($response["data"]["authenticated"] == true)
        {
            $eth_util = new Web3p\EthereumUtil\Util();
            $rlp = new RLP;
            $nonce = $this->get_nonce($token, $address);
            $encoded = $rlp->encode($nonce);
            $nonce_hash = $eth_util->sha3(HEX2BIN($encoded));
            $nonce_hash2 = $eth_util->sha3(HEX2BIN($eth_util->sha3($nonce)));
            $signature = substr($request["signature"], 2);
            $options = array(
                "r" => substr($signature, 0, 64) ,
                "s" => substr($signature, 64, 64) ,
                "v" => substr($signature, 128, 2) ,
                "recoveryParam" => base_convert(substr($signature, 128, 2) , 16, 10)
            );
            $sig = new Signature($options);
            $pubkey = $eth_util->recoverPublicKey($nonce_hash, $sig
                ->r
                ->toString(16) , $sig
                ->s
                ->toString(16) , $sig->recoveryParam);
            $new_address = $eth_util->publicKeyToAddress(substr($pubkey, 2));
            $pubkey2 = $eth_util->recoverPublicKey($nonce_hash2, $sig
                ->r
                ->toString(16) , $sig
                ->s
                ->toString(16) , $sig->recoveryParam);
            $new_address2 = $eth_util->publicKeyToAddress(substr($pubkey2, 2));
            $auth = 1;
            if (($new_address != $address) && ($new_address2 != $address))
            {
                $response["data"]["authenticated"] = false;
                $auth = 0;
            }
        }
        $this->save_address_for_token($token, $address, $auth);
        if ($as_json == true)
        {
            return json_encode($response);
        }
        else
        {
            return $response;
        }
    }
}

