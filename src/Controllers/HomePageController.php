<?php

declare(strict_types=1);

$callController = new HomePageController();

class HomePageController
{
    private const VIEW_PATH = "../Views/index.html";

    public function __construct()
    {
        echo $this->showHomePage();
    }

    public function showHomePage(): string
    {
        $file = file_get_contents(self::VIEW_PATH);
        $result = sprintf($file);

        return $result;
    }

}
