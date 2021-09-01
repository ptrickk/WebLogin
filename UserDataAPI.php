<?php declare(strict_types=1);

require_once './Page.php';


class UserDataAPI extends Page
{
    private $userID;
    private $username;

    protected function __construct()
    {
        parent::__construct();
    }

    public function __destruct()
    {
        parent::__destruct();
    }

    protected function getViewData():array
    {
        $data = array();

        if($this->userID != -1){
            ///ABFRAGEN DER AUFRUFE
            $sql_query = "SELECT time FROM visits WHERE userID = '" . $this->userID . "'";

            $results = $this->_database->query($sql_query);
            if(!$results) throw new Exception("Fehler in Abfrage: " . $this->_database->error);

            $logins = array();
            while($record = $results->fetch_assoc()){
                $time = $record["time"];
                $logins[] = $time;
            }

            $results->free_result();

            ///ABFRAGEN DER NUTZERDATEN
            $sql_query = "SELECT * FROM user WHERE userID = '" . $this->userID . "'";
            $results = $this->_database->query($sql_query);
            if(!$results) throw new Exception("Fehler in Abfrage: " . $this->_database->error);

            $record = $results->fetch_object();

            $userData = array();
            $userData[] = $record->userID;
            $userData[] = $record->username;
            $userData[] = $record->firstname;
            $userData[] = $record->surname;
            $userData[] = $record->email;
            $userData[] = $record->birthday;

            $data[] = 1;
            $data[] = $userData;
            $data[] = $logins;
        }
        else {
            $data[] = 0;
            $data[] = $this->username;
        }


        return $data;
    }

    protected function generateView():void
    {
        header("Content-Type: application/json; charset=UTF-8");
		$data = $this->getViewData();
        $serializedData = json_encode($data);
        echo $serializedData;
    }

    protected function processReceivedData():void
    {
        parent::processReceivedData();

        if(isset($_GET["usr"])){
            $username = $_GET["usr"];

            $sql_query = "SELECT userID FROM user WHERE username = '" . $username . "'";

            $results = $this->_database->query($sql_query);
            if(!$results) throw new Exception("Fehler in Abfrage: " . $this->_database->error);

            if(mysqli_num_rows($results) > 0){
                $record = $results->fetch_object();
                $this->userID = $record->userID;
                $this->username = $username;
            }
            else {
                $this->userID = -1;
                $this->username = $username;
            }


            $results->free_result();


        }
    }

    public static function main():void
    {
        try {
            $page = new UserDataAPI();
            $page->processReceivedData();
            $page->generateView();
        } catch (Exception $e) {
            //header("Content-type: text/plain; charset=UTF-8");
            header("Content-type: text/html; charset=UTF-8");
            echo $e->getMessage();
        }
    }
}

UserDataAPI::main();