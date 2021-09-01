<?php declare(strict_types=1);
// UTF-8 marker äöüÄÖÜß€

require_once './Page.php';

class Index extends Page
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
        return array();
    }

    protected function generateView():void
    {
		$data = $this->getViewData();
        $this->generatePageHeader('Startseite');
        echo <<< EOD
        <div class="start">
        <h1>Willkommen was möchten sie tun:</h1>
            <a href="Login.php">Login</a><br>
            <a href="Registration.php">Registrieren</a>
        </div>
EOD;

        $this->generatePageFooter();
    }

    protected function processReceivedData():void
    {
        parent::processReceivedData();
    }

    public static function main():void
    {
        try {
            $page = new Index();
            $page->processReceivedData();
            $page->generateView();
        } catch (Exception $e) {
            header("Content-type: text/html; charset=UTF-8");
            echo $e->getMessage();
        }
    }
}

Index::main();