<?php declare(strict_types=1);

require_once './Page.php';

/**UserData
 * Auf dieser Seite soll man als angemeldeter Nutzer
 * Nuternamen von sich oder anderen Konten eingeben und bekommt dann über eine API die
 * regelmäßig abgefragt wird alle wichtigen Daten über dieses Konto und wie öft es auf der
 * MainPage.php war und wann es dort zuletzt war
 */
class UserData extends Page
{

    protected function __construct()
    {
        parent::__construct();
    }

    public function __destruct()
    {
        parent::__destruct();
    }

    /**Es werden die Informationen ob der Nutzer der aktuellen Session
     * angemeldet ist und wenn ja, welchen Nutzername er hat
     * @return array enthält den Status ob der Nutzer eingelogt ist und gegebenenfalls den Nutzernamen
     */
    protected function getViewData():array
    {
        $data = array();

        if(isset($_SESSION["login_status"])){
            $data[] = $_SESSION["login_status"];
            if($data[0] == 1){
                //Nutzer ist angemeldet
                $data[] = $_SESSION["login_user"];//Username wird sich gemerkt
            }
            else {
                $data[] = "";//Nutzer ist nicht angemeldet (daher leerer Nutzername)
            }
        }
        else {
            //Es wurde noch kein Login Status eingetragen
            $data[] = 0;//deshalb nicht eingelogt (0)
            $data[] = "";//und kein Nutzername
        }

        return $data;
    }

    /**Hier wird die Seite erzeugt
     */
    protected function generateView():void
    {
		$data = $this->getViewData();
        $this->generatePageHeader('Nutzerdaten','ScriptAPI.js');

        $login_status = $data[0];
        $username = $data[1];

        //Topbar falls der Nutzer nicht angemeldet ist
        if($login_status == 0){
            echo <<<EOD
            <p class="topbar"><a href="Login.php">anmelden</a> <a href="Registration.php">registrieren</a></p>
EOD;
        }
        //Topbar falls der Nutzer angemeldet ist
        else if($login_status == 1){
            echo <<< EOD
            <p class="topbar"><a href="MainPage.php">Hauptseite</a><a href="Logout.php">abmelden</a></p>
EOD;
        }

        //Seiteninhalt falls der Nutzer angemeldet ist
        if($login_status == 1){
            echo <<< EOD
        <div class="userdata">
        <input type="text" name="username" id="username_data" value="" placeholder="Username">
        <p id="visits-field"></p>
        <p id="last-visit-field"></p>
        <p id="name-field"></p>
        <p id="email-field"></p>
        <p id="birthday-field"></p>
        </div>
EOD;
        }
        //Seiteninhalt falls der Nutzer nicht angemeldet ist
        else {
           echo <<< EOD
        Sie müssen sich erst <a href="Login.php">anmelden</a> oder <a href="Registration.php">registrieren</a>
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

            $page = new UserData();
            $page->processReceivedData();
            $page->generateView();
        } catch (Exception $e) {
            //header("Content-type: text/plain; charset=UTF-8");
            header("Content-type: text/html; charset=UTF-8");
            echo $e->getMessage();
        }
    }
}

UserData::main();