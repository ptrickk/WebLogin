<?php declare(strict_types=1);

require_once './Page.php';


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

    protected function getViewData():array
    {
        $data = array();

        if(isset($_SESSION["login_status"])){
            $data[] = $_SESSION["login_status"];
            if($data[0] == 1){
                $data[] = $_SESSION["login_user"];
            }
            else {
                $data[] = "";
            }
        }
        else {
            $data[] = 0;
            $data[] = "";
        }

        return $data;
    }

    protected function generateView():void
    {
		$data = $this->getViewData();
        $this->generatePageHeader('Nutzerdaten','ScriptAPI.js');

        $login_status = $data[0];
        $username = $data[1];

        if($login_status == 0){
            echo <<<EOD
            <p class="topbar"><a href="Login.php">anmelden</a> <a href="Registration.php">registrieren</a></p>
EOD;
        }
        else if($login_status == 1){
            echo <<< EOD
            <p class="topbar"><a href="Logout.php">abmelden</a></p>
EOD;
        }

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