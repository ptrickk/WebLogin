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

    protected function getViewData():array
    {
        $data = array();
        if(isset($_SESSION["login_status"])){
            $data[] = $_SESSION["login_status"];
            if (isset($_SESSION["error_code"])){
                $data[] = $_SESSION["error_code"];
            }
            else {
                $data[] = 0;
            }
        }
        else {
            $data[] = 0;
            $data[] = 0;
        }
        return $data;
    }

    protected function generateView():void
    {
		$data = $this->getViewData();
        $this->generatePageHeader('Login');

        $status = $data[0];
        $error = $data[1];

        if($status == 1){
            echo <<< EOD
            <meta http-equiv="refresh" content="0; url=MainPage.php" />
EOD;
        }

        $msg = "";
        if($error == 1){
            $msg = "Passwort ist inkorrekt";
        }
        else if($error == 2){
            $msg = "Username ist nicht vorhanden";
        }
        else if($error == 5){
            $msg = "Konto wurde erfolgreich erstellt";
        }

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

    protected function processReceivedData():void
    {
        parent::processReceivedData();

        if(isset($_POST['username']) && isset($_POST['password'])){
            $username = $_POST['username'];
            $password = $_POST['password'];
            $password = md5($password);

            $header_dest = 'Location: /login/Login.php';

            $sql_query = "SELECT password FROM user WHERE username = '$username'";
            $results = $this->_database->query($sql_query);
            if(!$results) throw new Exception("Fehler in Abfrage: " . $this->_database->error);

            if(mysqli_num_rows($results) == 1){
                $record = $results->fetch_object();

                $acc_password = $record->password;

                if(strcmp($password, $acc_password) == 0){
                    $_SESSION["login_status"] = 1;//Erfolgreicher Login
                    $_SESSION["login_user"] = $username;
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

            header($header_dest);
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