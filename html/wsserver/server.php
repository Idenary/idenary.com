<?php require '../vendor/autoload.php';

use Ratchet\Server\IoServer;
use Ratchet\Http\HttpServer;
use Ratchet\WebSocket\WsServer;
use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;

require '../inc/config.inc.php';
//require '../inc/idena.class.php';
require '../inc/idenary.class.php';

define('APP_PORT', 8880);

class ServerImpl implements MessageComponentInterface
{
    protected $clients;
    protected $db;
    protected $idenary;

    public function __construct()
    {
        $this->clients = new \SplObjectStorage;
        global $DB;
        $this->db = $DB;
        global $CONFIG;
        $this->idenary = new Idenary($CONFIG);
    }

    public function onOpen(ConnectionInterface $conn)
    {
        $this
            ->clients
            ->attach($conn);
        echo "New connection! ({$conn->resourceId}).\n";
    }

    public function onMessage(ConnectionInterface $conn, $msg)
    {
        echo sprintf("New message from '%s': %s\n\n\n", $conn->resourceId, $msg);
        $message = json_decode($msg, true);
        $methodName = "on" . $message["Action"];
        if (method_exists($this, $methodName))
        {
            $this->$methodName($conn, $message["Data"]);
        }
        else
        {
            error_log("Unknown ws method: " . $methodName);
        }
        /*
        foreach ($this->clients as $client) { // BROADCAST
            if ($conn !== $client) {
                $client->send($msg);
            }
        }*/
    }

    private function sendEventAll($event, $data, $except = null)
    {
        $msg = json_encode(array(
            "Event" => $event,
            "Data" => $data
        ));
        foreach ($this->clients as $client)
        { // BROADCAST
            if ($except !== $client)
            {
                $client->send($msg);
            }
        }
    }

    private function onRegister($conn, $data)
    {
        error_log("onRegister " . $data);
        $conn->token = $data;
        $address = $this
            ->idenary
            ->load_address_for_token($data);
        $conn->address = $address;
        $grid = $this
            ->idenary
            ->getGrid();
        $data = array(
            "Address" => $address,
            "Grid" => $grid
        );
        $msg = array(
            "Event" => "Init",
            "Data" => $data
        );
        $conn->send(json_encode($msg));
        $credits = $this
            ->idenary
            ->addressCredits($conn->address);
        $msg = json_encode(array(
            "Event" => "Credits",
            "Data" => $credits
        ));
        $conn->send($msg);
    }

    private function onPaint($conn, $data)
    {
        error_log("onPaint " . json_encode($data));
        // TODO: check if valid
        $valid = true;
        $data["address"] = $this
            ->idenary
            ->squareAddress($data["id"]);
        if ($data["address"] != "" and $data["address"] != $conn->address)
        {
            $valid = false;
        }
        if (!$valid)
        {
            return;
        }

        // TODO: check if still auth, not timeout.
        if ($data["item"] == "eraser")
        {
            $data['address'] = "";
            $data['started'] = 0;
            $data['item'] = "";
            $data['color'] = "000";
            $data['bgcolor'] = "FFF";
            $data['rotate'] = 0;
        }
        else
        {
            if ($data["address"] == "")
            {
                $credits = $this
                    ->idenary
                    ->addressCredits($conn->address);
                if ($credits <= 0)
                {
                    return;
                }
            }
            $data['address'] = $conn->address;
            $data['started'] = time();
        }
        // Store to db
        $this
            ->idenary
            ->storePaint($data);

        // Send to all
        unset($data['started']);
        $this->sendEventAll("Paint", $data); // send to everyone
        $credits = $this
            ->idenary
            ->addressCredits($conn->address);
        $msg = json_encode(array(
            "Event" => "Credits",
            "Data" => $credits
        ));
        $conn->send($msg);

    }

    public function onClose(ConnectionInterface $conn)
    {
        $this
            ->clients
            ->detach($conn);
        echo "Connection {$conn->resourceId} is gone.\n";
    }

    public function onError(ConnectionInterface $conn, \Exception $e)
    {
        echo "An error occured on connection {$conn->resourceId}: {$e->getMessage() }\n\n\n";
        $conn->close();
    }
}

$server = IoServer::factory(new HttpServer(new WsServer(new ServerImpl())) , APP_PORT);
echo "Server created on port " . APP_PORT . "\n\n";
$server->run();

