<?php declare(strict_types=1);

require_once './Page.php';


class Registration extends Page
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
        if(isset($_SESSION["error_code"])){
            $data[] = $_SESSION["error_code"];
            $_SESSION["error_code"] = 0; //Fehlermeldung soll nur einmal angezeigt werden
        }
        else {
            $data[] = 0;
        }
        return $data;
    }

    protected function generateView():void
    {
		$data = $this->getViewData();
        $this->generatePageHeader('Registrieren', 'Control.js');

        $error = $data[0];

        $msg = "";
        if($error == 3){
            $msg = "Dieser Nutzername ist bereits vergeben";
        }
        else if($error == 4){
            $msg = "Unter dieser Email-Adresse ist bereits ein Konto registriert";
        }

        echo <<< EOD
        <h3>Registrieren</h3>
        <form action="/login/Registration.php" method="post" onsubmit="return RegistrationFunctions.check();" class="registration">
            <p>Username*:<input type="text" name="username" id="username" value="" placeholder="Username"></p>
            <p id="username_text" class="err" hidden>*Es muss ein Username eingetragen werden</p>
            <p>Vorname: <input type="text" name="firstname" value="" placeholder="Vorname"></p>
            <p>Nachname: <input type="text" name="surname" value="" placeholder="Nachname"></p>
            <p>Geburtsdatum: <input type="date" name="birthday" value=""></p>
            <p>Email-Adresse*:<input type="text" name="email" id="email" value="" placeholder="Emailadresse"></p>
            <p id="email_text" class="err" hidden>*Es muss ein gültige Email-Adresse eingetragen werden</p>
            <p>Password*:<input type="password" name="password1" id="password1" value="" placeholder="Password"></p>
            <p id="pwd_text" class="err" hidden>*Es muss ein Passwort eingetragen werden</p>
            <p>Password wiederholen*: <input type="password" name="password2" id="password2" value="" placeholder="Password (wiederholen)"></p>
            <input type="submit" value="Registrieren">
            <p>$msg</p>
        </form>
        <p>*Pflichtfelder</p>
EOD;


        $this->generatePageFooter();
    }

    protected function processReceivedData():void
    {
        parent::processReceivedData();

        if(isset($_POST['username']) && isset($_POST['email']) && isset($_POST['password1'])){
            $header_dest = 'Location: /login/Registration.php';

            $username = $_POST['username'];
            $email = $_POST['email'];
            $password = $_POST['password1'];
            $password = md5($password);

            $firstname = "";
            if(isset($_POST['firstname'])){
                $firstname = $_POST['firstname'];
            }

            $surname = "";
            if(isset($_POST['surname'])){
                $surname = $_POST['surname'];
            }

            $bday = "";
            if(isset($_POST['birthday'])){
                $bday = $_POST['birthday'];
            }



            $sql_query = "SELECT * FROM user WHERE username = '$username'";
            $results = $this->_database->query($sql_query);
            if(!$results) throw new Exception("Fehler in Abfrage: " . $this->_database->error);

            $match_username = mysqli_num_rows($results);
            $results->free_result();

            if($match_username != 0){
                $_SESSION["error_code"] = 3;//username adresse ist bereits registriert
            }
            else {
                $sql_query = "SELECT * FROM user WHERE email = '$email'";
                $results = $this->_database->query($sql_query);
                if(!$results) throw new Exception("Fehler in Abfrage: " . $this->_database->error);

                $match_email = mysqli_num_rows($results);
                $results->free_result();

                if($match_email != 0){
                    $_SESSION["error_code"] = 4;//email adresse ist bereits registriert
                }
                else {
                    $sql_query = "INSERT INTO user(username, firstname, surname, email, birthday, password) VALUES('". $username
                        ."','". $firstname ."','". $surname ."','". $email ."','". $bday ."','". $password . "')";

                    $results = $this->_database->query($sql_query);
                    if(!$results) throw new Exception("Fehler in Abfrage: " . $this->_database->error);

                    $_SESSION["error_code"] = 5;
                    $header_dest = 'Location: /login/Login.php';
                }
            }

            header($header_dest);
        }
    }

    public static function main():void
    {
        try {
            session_start();

            $page = new Registration();
            $page->processReceivedData();
            $page->generateView();
        } catch (Exception $e) {
            //header("Content-type: text/plain; charset=UTF-8");
            header("Content-type: text/html; charset=UTF-8");
            echo $e->getMessage();
        }
    }
}

Registration::main();