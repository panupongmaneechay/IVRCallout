<?php
date_default_timezone_set("Asia/Bangkok");

require_once 'vendor/autoload.php';

use PAMI\Client\Impl\ClientImpl;
use PAMI\Listener\IEventListener;
use PAMI\Message\Event\EventMessage;
use PAMI\Message\Action\OriginateAction;
use PAMI\Message\Action\StatusAction;


header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

//$authHeader = $_SERVER['HTTP_AUTHORIZATION'];
$authHeader ="xxxx";
$Key ="xxxx";

$dialphone = $_GET['dialphone'];
$Ref_ID = $_GET['Ref_ID'];
$Ticket1 = $_GET['Ticket1'];
class PamiEventListener implements IEventListener
{

    public function __construct($cli)
    {
        $this->cli = $cli;

    }


    /**
     * To Handle the All Pami Events
     * @param EventMessage $event
     */
    public function handle(EventMessage $event)
    {

        $strevt = $event->getKeys()['event'];
        $this->var_error_log($event->getKeys());

        if ($strevt == 'DialBegin') {
            //echo "DialBegin event --- \n";

        }
        if ($strevt == 'DialEnd') {
            //echo "Dial end event --- \n";
        }

        if ($strevt == 'Hangup') {
            //echo "Hangup event --- \n";

        }

    }



    public function var_error_log($object = null)
    {
        $datetime = date("Y-m-d h:i:s a");

        $contents = PHP_EOL . $datetime . " :";
        ob_start();                    // start buffer capture
        var_dump($object);           // dump the values
        $contents .= ob_get_contents(); // put the buffer into a variable
        ob_end_clean();                // end capture
        //create the log file if not exist
        $log_file_path = "log.txt";


        if (file_exists($log_file_path) == false) {
            fopen($log_file_path, "w");
        }
        error_log($contents, 3, $log_file_path);
    }

}

////////////////////////////////////////////////////////////////////////////////
// Code STARTS.
////////////////////////////////////////////////////////////////////////////////


if($authHeader == $Key){
    error_reporting(E_ALL);
    ini_set('display_errors', 0);
    try {
        $options = array(
            'host' => '127.0.0.1',
            'scheme' => 'tcp://',
            'port' => 5038,
            'username' => 'ami',
            'secret' => 'ami123',
            'connect_timeout' => 60,
            'read_timeout' => 10000000
        );
        $sipNumber=$dialphone;

        $sipNumber_sub=substr($sipNumber,1);

        $a = new ClientImpl($options);

        $eventListener = new PamiEventListener($a);
        $a->registerEventListener($eventListener);
        $a->open();
        $time = time();

            $actionid = md5(uniqid());
            $response = $a->send(new StatusAction());

            $originateMsg = new OriginateAction('Local/'.$sipNumber .'@trunktestco13');
            $originateMsg->setContext('ivr-callout');
            $originateMsg->setPriority('1');
            $originateMsg->setExtension('s');
            $originateMsg->setAsync(true);
            $originateMsg->setActionID($actionid);
            $originateMsg->setVariable('SIPCODE',$Ref_ID);
            $originateMsg->setVariable('Ticket1',$Ticket1);
            $orgresp = $a->send($originateMsg);
            $orgStatus = $orgresp->getKeys()['response'];

            $error_code = $orgresp->getKeys();
            $error_code = array('CallID' => $sipNumber ,'RefID'=> $Ref_ID,'Ticket1'=> $Ticket1);
            echo json_encode($error_code);
            usleep(1000); // 1ms delay
            $a->process();
        //}
        $a->close(); // send logoff and close the connection.
    } catch (Exception $e) {
        //echo $e->getMessage() . "\n";
    }
   }else{

$status = array("Auth Fail");
echo json_encode($status);

}
?>
