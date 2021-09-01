<?php declare(strict_types=1);

require_once './Page.php';


class Login extends Page
{

    protected function __construct()
    {
        parent::__construct();
    }

    public function __destruct()
    {
        parent::__destruct();
    }

    /**Es werden Daten zurückgegeben ob der NUtzer aktuell angemeldet ist und ob zuvor eine Aktion ausgeführt wurde auf die nun eine
     * Fehlermeldung folgen soll
     * @return array Daten ob der Nutzer angemeldet ist und ob ein Fehler übermittelt wurde werden übergeben
     */
    protected function getViewData():array
    {
        $data = array();
        if(isset($_SESSION["login_status"])){
            $data[] = $_SESSION["login_status"];//Login-Status wird übergeben
            if (isset($_SESSION["error_code"])){
                $data[] = $_SESSION["error_code"];//Fehlercode wird übergeben
            }
            else {
                $data[] = 0;//kein Fehlercode wurde gesetzt daher wird 0 eigetragen (kein Fehler)
            }
        }
        else {
            //noch kein login versuch und daher auch keine Fehlermeldung
            $data[] = 0;
            $data[] = 0;
        }
        return $data;
    }

    /**Seite wird hier erzeugt
     */
    protected function generateView():void
    {
		$data = $this->getViewData();
        $this->generatePageHeader('Login');

        $status = $data[0];//Status ob Nutzer angemeldet ist wird gespeichert
        $error = $data[1];//Fehlercode wird übergeben

        //Wenn Nutzer bereits angemeldet ist (wird direkt auch MainPage.php weitergeleitet, da Login unnötig ist)
        if($status == 1){
            echo <<< EOD
            <meta http-equiv="refresh" content="0; url=MainPage.php" />
EOD;
        }

        $msg = "";//Leere Fehlermeldung, falls kein Fehler vorliegt
        //Hier werden die Nachrichten für die Fehlermeldung gesetzt abhängig von dem übergebenen Fehlercode
        if($error == 1){
            $msg = "Passwort ist inkorrekt";
        }
        else if($error == 2){
            $msg = "Username ist nicht vorhanden";
        }
        else if($error == 5){
            $msg = "Konto wurde erfolgreich erstellt";
        }

        //Login-Form
        echo <<< EOD
        <h3>Login</h3>
        <form action="/login/Login.php" method="post" class="login">
        <p>Username: <input type="text" name="username" placeholder="Username"></p>
        <p>Passwort: <input type="password" name="password" placeholder="Passwort"></p>
        <p class="err">$msg</p>
        <input type="submit" value="Login">
        </form>
        <div class="login_reg"><a href="Registration.php" >Registrieren</a></div>
EOD;

        $this->generatePageFooter();
    }

    /**Das mittels POST verschickte Login-Form wird hier bearbeitet
     * @throws Exception
     */
    protected function processReceivedData():void
    {
        parent::processReceivedData();

        if(isset($_POST['username']) && isset($_POST['password'])){//nur wenn beide gesetzt sind wurde das Form korrekt abgeschickt
            $username = $_POST['username'];
            $username = $this->_database->real_escape_string($username);
            $password = $_POST['password'];
            $password = md5($password);//Passwort wird mittels MD5 gehasht

            $header_dest = 'Location: /login/Login.php';//Standard Weiterleitung wenn Prozess abgeschlossen ist

            $sql_query = "SELECT password FROM user WHERE username = '$username'";//SQL-Query um das Passwort zu dem übergebenen Username zu erhalten
            $results = $this->_database->query($sql_query);
            if(!$results) throw new Exception("Fehler in Abfrage: " . $this->_database->error);

            if(mysqli_num_rows($results) == 1){//Es muss genau ein Datensatz in der Tabelle sein
                $record = $results->fetch_object();

                $acc_password = $record->password;

                if(strcmp($password, $acc_password) == 0){//Falls beide Passwort-Hashes identisch sind
                    $_SESSION["login_status"] = 1;//Erfolgreicher Login
                    $_SESSION["login_user"] = $username; //Nutzername wird gespeichert
                    $_SESSION["error_code"] = 0; //Keine Fehlermeldung

                    $header_dest = 'Location: /login/MainPage.php';//es wird auf die MainPage.php weitergeleitet, da Login erfolgreich war
                }
                else {
                    $_SESSION["login_status"] = 0;//unerfolgreicher Login
                    $_SESSION["error_code"] = 1;//Falsches Passwort
                }
            }
            else {
                $_SESSION["login_status"] = 0;//unerfolgreicher Login
                $_SESSION["error_code"] = 2;//nicht existierender Username
            }

            $results->free_result();

            header($header_dest);//Weiterleitung
        }
    }

    public static function main():void
    {
        try {
            session_start();

            $page = new Login();
            $page->processReceivedData();
            $page->generateView();
        } catch (Exception $e) {
            //header("Content-type: text/plain; charset=UTF-8");
            header("Content-type: text/html; charset=UTF-8");
            echo $e->getMessage();
        }
    }
}

Login::main();