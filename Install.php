<?php


class Ragtek_ContactThread_Install
{
    public static function install()
    {
        self::_checkDependencies();
    }

    ## autogenerated dependencies from config file ##
    protected static function _checkDependencies()
    {
        // this add-on requires xf 1.1.0 because of the thread prefixes
        if (XenForo_Application::$versionId < 1010070) {
            throw new XenForo_Exception('This Add-on requires XenForo 1.1.0 or higher', true);
        }
    }
}