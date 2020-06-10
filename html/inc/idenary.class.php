<?php

// http://api.idena.io/api/Identity/0xdF06e6552..................31754d7893A7


class Idenary
{

    private $config;
    private $db;

    public function __construct($config)
    {
        global $DB;
        $this->config = $config;
        $this->db = $DB;
        $this->BUNDLES = array(
            "" => 0,
            "Candidate" => 0,
            "Newbie" => 1,
            "Suspended" => 2,
            "Zombie" => 3,
            "Verified" => 4,
            "Human" => 5
        );
    }

    public function get_address_status($address)
    {
        if ($address == '')
        {
            return '';
        }
        // TODO: sanitize $address
        // TODO: shard cache dir
        $file = $this->config['cache_dir'] . $address . ".json";
        if (!is_file($file))
        {
            // get from api
            $api = json_decode(file_get_contents("https://api.idena.io/api/Identity/" . $address) , true);
            @$status = $api['result']['state'];
            if ($status != '')
            {
                file_put_contents($file, $status);
            }
            $bundle_id = $this->BUNDLES[$status];
            for ($i = $bundle_id;$i >= 0;$i--)
            {
                $this->addBundle($address, $i);
            }

        }
        else
        {
            // return cached status
            $status = trim(file_get_contents($file));
        }
        return $status;
    }

    public function get_address_age($address)
    {
        // TODO - Not used atm.
        
    }

    public function clear_cache($address = "")
    {
        // To be called at new epoch with no param for whole refresh.
        // Call with an address t orefresh that one only.
        // TODO: sanitize $address
        if ($address == "")
        {
            $files = glob($this->config['cache_dir'] . "*.json");
            foreach ($files as $file)
            {
                if (is_file($file)) unlink($file);
            }
        }
        else
        {
            $file = $this->config['cache_dir'] . $address . ".json";
            unlink($file);
        }
    }

    private function file_for_token($token)
    {
        return $this->config["tokens_dir"] . $token . ".json";
    }

    public function load_address_for_token($token, $check_auth = false)
    {
        $token = str_replace([" ", ".", "\\", "/", "*", "&", '|'], "", $token);
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

    public function getGrid()
    {
        $sql = "SELECT * FROM squares ORDER BY id ASC";
        $reqStmt = $this
            ->db
            ->prepare($sql);
        $res = array();
        if ($reqStmt->execute())
        {
            $res = $reqStmt->fetchAll(PDO::FETCH_ASSOC);
        }
        return $res;
    }

    public function storePaint($data)
    {
        $keys = "`" . implode("`,`", array_keys($data)) . "`";
        $values = "'" . implode("','", array_values($data)) . "'";
        $sql = "REPLACE INTO squares ($keys) VALUES ($values)";
        $reqStmt = $this
            ->db
            ->prepare($sql);
        $reqStmt->execute();
    }

    public function squareAddress($id)
    {
        $sql = "SELECT address FROM squares WHERE id=$id";
        $reqStmt = $this
            ->db
            ->prepare($sql);
        $res = array(
            "address" => ""
        );
        if ($reqStmt->execute())
        {
            $res = $reqStmt->fetchAll(PDO::FETCH_ASSOC) [0];
        }
        return $res["address"];
    }

    public function addressPalette($address)
    {
        $sql = "SELECT type, data FROM bundles WHERE id in (SELECT bundle_id FROM address_bundle WHERE address='$address') ORDER BY id, data";
        $reqStmt = $this
            ->db
            ->prepare($sql);
        $res = array();
        if ($reqStmt->execute())
        {
            $res = $reqStmt->fetchAll(PDO::FETCH_ASSOC);
        }
        return $res;
    }

    public function addressCredits($address)
    {

        $status = $this->get_address_status($address);
        $sql = "SELECT credits FROM credits WHERE status='$status'";
        $reqStmt = $this
            ->db
            ->prepare($sql);
        $res = array(
            "credits" => 0
        );
        if ($reqStmt->execute())
        {
            $res = $reqStmt->fetchAll(PDO::FETCH_ASSOC) [0];
        }
        $res = $res["credits"];
        if ($res <= 0)
        {
            return 0;
        }

        $sql = "SELECT count(address) FROM squares WHERE address='$address'";
        $reqStmt = $this
            ->db
            ->prepare($sql);
        if ($reqStmt->execute())
        {
            $res2 = $reqStmt->fetchAll(PDO::FETCH_ASSOC) [0]["count(address)"];
        }

        return $res - $res2;
    }
    public function addBundle($address, $bundle_id)
    {
        $sql = "INSERT INTO address_bundle(address, bundle_id) VALUES('$address', $bundle_id)";
        $reqStmt = $this
            ->db
            ->prepare($sql);
        $reqStmt->execute();
    }

}

