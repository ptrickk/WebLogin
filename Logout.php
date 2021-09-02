<?php declare(strict_types=1);

require_once './Page.php';

/**Diese Klasse erzeugt keine Seite sondern leitet direkt auf eine andere Seite weiter,
 * nachdem der Nutzer der aktuellen Session abgemeldet wurde
 */
class Logout extends Page
{

    protected function __construct()
    {
        parent::__construct();
    }

    public function __destruct()
    {
        parent::__destruct();
    }
    //Der Login-Status wird immer auf 0 gesetzt (0 = nicht angemeldet)
    protected function getViewData():array
    {
        $_SESSION["login_status"] = 0;

        return array();
    }

    /**
     * Es wird keine Seite erzeugt, sondern immer auf die MainPage.php weitergeleitet
     */
    protected function generateView():void
    {
		$data = $this->getViewData();
        $this->generatePageHeader('Logout');

        header('Location: /login/MainPage.php');

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

            $page = new Logout();
            $page->processReceivedData();
            $page->generateView();
        } catch (Exception $e) {
            //header("Content-type: text/plain; charset=UTF-8");
            header("Content-type: text/html; charset=UTF-8");
            echo $e->getMessage();
        }
    }
}

Logout::main();