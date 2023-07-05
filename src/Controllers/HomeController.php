<?php

declare(strict_types=1);

$callController = new HomeController();

class HomeController
{
    private const VIEW_PATH = "../Views/index.html";

    public function __construct()
    {
        echo $this->showHomePage();
    }

    private function showHomePage()
    {
        require_once '../Views/index.php';
    }

}
