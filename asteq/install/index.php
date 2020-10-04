<?php

class Asteq extends CModule
{
    var $MODULE_ID = "asteq";
    var $MODULE_VERSION;
    var $MODULE_VERSION_DATE;
    var $MODULE_NAME;
    var $MODULE_DESCRIPTION;
    var $MODULE_CSS;
    var $MODULE_GROUP_RIGHTS = "Y";

    function __construct()
    {
        $arModuleVersion = array();

        $path = str_replace("\\", "/", __FILE__);
        $path = substr($path, 0, strlen($path) - strlen("/index.php"));
        include($path . "/version.php");

        $this->MODULE_VERSION = $arModuleVersion["VERSION"];
        $this->MODULE_VERSION_DATE = $arModuleVersion["VERSION_DATE"];
        $this->PARTNER_NAME = "Asteq.Digital";
        $this->PARTNER_URI = "https://asteq.ru/";

        $this->MODULE_NAME = "Asteq";
        $this->MODULE_DESCRIPTION = "Модуль от команды Asteq для упрощения разработки сайтов с использованием Битрикс";
    }

    function InstallDB()
    {
        RegisterModule("Asteq");
        return true;
    }

    function UnInstallDB()
    {
        UnRegisterModule("Asteq");
        return true;
    }

    function DoInstall()
    {
        global $APPLICATION;

        if (!IsModuleInstalled("Asteq")) {
            $this->InstallDB();
        }
    }

    function DoUninstall()
    {
        $this->UnInstallDB();
    }
}
