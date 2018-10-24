<?php

class Tools extends ToolsCore
{

    public static function isXmlHttpRequest()
    {
        return (!empty($_SERVER['HTTP_X_REQUESTED_WITH'])
            && $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest')
            ? true : false;
    }

    public function isPost()
    {
        return ($_SERVER['REQUEST_METHOD'] === 'POST') ? true : false;
    }
}