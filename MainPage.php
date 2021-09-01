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

    protected function getViewData():array
    {
        $data = array();

        if(isset($_SESSION["login_status"])){
            $data[] = $_SESSION["login_status"];
            if($_SESSION["login_status"] == 1){
                $username = $_SESSION["login_user"];
                $data[] = $username;

                $sql_query = "SELECT userID FROM user WHERE username = '" . $username . "'";

                $results = $this->_database->query($sql_query);
                if(!$results) throw new Exception("Fehler in Abfrage: " . $this->_database->error);

                $record = $results->fetch_object();
                $userID = $record->userID;
                $results->free_result();

                $sql_query = "INSERT INTO visits(userID, time) VALUES ('" . $userID . "', now())";

                $results = $this->_database->query($sql_query);
                if(!$results) throw new Exception("Fehler in Abfrage: " . $this->_database->error);
            }
        }

        return $data;
    }

    protected function generateView():void
    {
		$data = $this->getViewData();
        $this->generatePageHeader('Main Page');

        $login_status = $data[0];

        if($login_status == 0){
            echo <<<EOD
            <div class="topbar">
                <a href="Login.php">anmelden</a> 
                <a href="Registration.php">registrieren</a>
            </div>
EOD;
        }
        else if($login_status == 1){
            echo <<< EOD
            <div class="topbar">
                <a href="UserData.php">Nutzerdaten</a> 
                <a href="Logout.php">abmelden</a> 
            </div>
EOD;
        }

        if($login_status == 0){
            echo <<< EOD
            <h2>Du bist noch nicht angemeldet!</h2>
            <h3>Melde dich erst <a href="Login.php">hier</a> an oder registriere dich <a href="Registration.php">hier</a></h3>
EOD;

        }
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