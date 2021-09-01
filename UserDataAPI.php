<?php declare(strict_types=1);

require_once './Page.php';


class UserDataAPI extends Page
{
    private $userID;

    protected function __construct()
    {
        parent::__construct();
    }

    public function __destruct()
    {
        parent::__destruct();
    }

    /** Hier werden die Daten des abgefrage
     * @return array gibt die Daten des Nutzers zurück
     */
    protected function getViewData():array
    {
        $data = array();

        if($this->userID != -1){//Wenn eine valide userId gefunden wurde
            $sql_query = "SELECT time FROM visits WHERE userID = '" . $this->userID . "'";//Alle Reloads des Nutzers werden geladen

            $results = $this->_database->query($sql_query);//Query wird ausgeführt
            if(!$results) throw new Exception("Fehler in Abfrage: " . $this->_database->error);

            //Alle Reload-Zeiten werden in einem Array gespeichert
            $logins = array();
            while($record = $results->fetch_assoc()){
                $time = $record["time"];
                $logins[] = $time;
            }

            $results->free_result();

            $sql_query = "SELECT * FROM user WHERE userID = '" . $this->userID . "'";//SQL-Query um alle Daten des abgefragten Nutzers zu laden
            $results = $this->_database->query($sql_query);//Query wird ausgeführt
            if(!$results) throw new Exception("Fehler in Abfrage: " . $this->_database->error);//Ausgabe falls Fehlermeldung auftritt

            $record = $results->fetch_object();

            //Daten des Nutzers werden in Array gespeichert
            $userData = array();
            $userData[] = $record->userID;
            $userData[] = $record->username;
            $userData[] = $record->firstname;
            $userData[] = $record->surname;
            $userData[] = $record->email;
            $userData[] = $record->birthday;

            $data[] = 1;//Zeichen, dass das Laden der Nutzerdaten erfolgreich war
            $data[] = $userData;//Nutzerdaten werden übergeben
            $data[] = $logins;//Zeiten werden übergeben
        }
        else {
            $data[] = 0;//Zeichen, dass Fehler aufgetreten ist
            $data[] = "error";
        }

        return $data;
    }
    /*Seite wird erstellt, jedoch keine HTML Seite, sondern nur der JSON-Array
     */
    protected function generateView():void
    {
        header("Content-Type: application/json; charset=UTF-8");
		$data = $this->getViewData();
        $serializedData = json_encode($data);
        echo $serializedData;
    }

    /**Verarbeitet die GET-Request und speichert den übergebenen User anhand seiner userID
     * @throws Exception falls SQL-Query einen Fehler zurückgibt
     */
    protected function processReceivedData():void
    {
        parent::processReceivedData();

        if(isset($_GET["usr"])){//Nur wenn usr gesetzt wurde
            $username = $_GET["usr"];//Username wird gespeichert

            $sql_query = "SELECT userID FROM user WHERE username = '" . $username . "'";//SQL-Query die die userId zum zugehörigen Username abfragt

            $results = $this->_database->query($sql_query);//Query wird ausgeführt
            if(!$results) throw new Exception("Fehler in Abfrage: " . $this->_database->error);//Fehlermeldung wird ausgegeben

            if(mysqli_num_rows($results) == 1){//Es darf nur exakt einen Datensatz mit dem übergebenen Username geben
                $record = $results->fetch_object();
                $this->userID = $record->userID;//userID wird in Variable gespeichert
            }
            else {
                $this->userID = -1;//falls ein Fehler passiert ist wird als userID -1 gespeichert, was auf einen Fehler hinweist
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