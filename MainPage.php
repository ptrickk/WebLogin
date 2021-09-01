<?php declare(strict_types=1);

require_once './Page.php';


class MainPage extends Page
{

    protected function __construct()
    {
        parent::__construct();
    }

    public function __destruct()
    {
        parent::__destruct();
    }

    /** In der Funktion wird auf die Datenbank geschrieben, wenn die Seite aufgerufen wird.
     * @return array enthält nur den Status ob der Nutzer angemeldet ist oder nicht
     * @throws Exception falls die SQL-Query einen Fehler zurückgibt
     */
    protected function getViewData():array
    {
        $data = array();

        if(isset($_SESSION["login_status"])){
            $data[] = $_SESSION["login_status"];//Status ob Nutzer angemeldet ist oder nicht
            if($_SESSION["login_status"] == 1){//Wenn der Nutzer angemeldet ist
                $username = $_SESSION["login_user"];
                $data[] = $username;//Nutzername wird gespeichert

                $sql_query = "SELECT userID FROM user WHERE username = '" . $username . "'";//Query zum erhalten der userID des zugehörigen Usernames

                $results = $this->_database->query($sql_query);//Query wird ausgeführt
                if(!$results) throw new Exception("Fehler in Abfrage: " . $this->_database->error);//Fehlermeldung falls SQL-Fehler

                $record = $results->fetch_object();
                $userID = $record->userID;
                $results->free_result();

                $sql_query = "INSERT INTO visits(userID, time) VALUES ('" . $userID . "', now())";//Query um zu speichern, dass die Seite geladen wurde

                $results = $this->_database->query($sql_query);//Query wird ausgeführt
                if(!$results) throw new Exception("Fehler in Abfrage: " . $this->_database->error);//Fehlermeldung falls SQL-Fehler
            }
        }

        return $data;
    }

    /**Seite wird generiert
     */
    protected function generateView():void
    {
		$data = $this->getViewData();//Daten werden geladen und SQL-Insert wird ausgeführt
        $this->generatePageHeader('Main Page');

        $login_status = $data[0];//Status ob Nutzer angemeldet ist

        //Topbar falls der Nutzer nicht angemeldet ist
        if($login_status == 0){
            echo <<<EOD
            <div class="topbar">
                <a href="Login.php">anmelden</a> 
                <a href="Registration.php">registrieren</a>
            </div>
EOD;
        }
        //Topbar falls der Nutzer angemeldet ist
        else if($login_status == 1){
            echo <<< EOD
            <div class="topbar">
                <a href="UserData.php">Nutzerdaten</a> 
                <a href="Logout.php">abmelden</a> 
            </div>
EOD;
        }
        //Seiteninhalt falls der Nutzer nicht angemeldet ist
        if($login_status == 0){
            echo <<< EOD
            <h2>Du bist noch nicht angemeldet!</h2>
            <h3>Melde dich erst <a href="Login.php">hier</a> an oder registriere dich <a href="Registration.php">hier</a></h3>
EOD;

        }
        //Seiteninhalt falls der Nutzer angemeldet ist
        else if($login_status == 1){
            $user = $data[1];
            echo <<< EOD
            
            <h1>Willkommen auf der Seite $user !</h1>
EOD;

        }

        $this->generatePageFooter();
    }

    protected function processReceivedData():void
    {
        parent::processReceivedData();
    }

    public static function main():void
    {
        try {
            session_start();

            $page = new MainPage();
            $page->processReceivedData();
            $page->generateView();
        } catch (Exception $e) {
            //header("Content-type: text/plain; charset=UTF-8");
            header("Content-type: text/html; charset=UTF-8");
            echo $e->getMessage();
        }
    }
}

MainPage::main();