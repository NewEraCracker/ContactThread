<?php

    /**
     * extend the Public Misc Controller with my Controller
     */
    class Ragtek_ContactThread_EventListener_CreateThread
    {
        /**
         * extend the xenforo_controllerPublic_misc with my ragtek contactthread class to overwrite the methods
         * @param <type> $class
         * @param array $extend
         */
        public static function listen($class, array &$extend)
        {
            if ($class == 'XenForo_ControllerPublic_Misc') {
                $extend[] = 'Ragtek_ContactThread_ControllerPublic_Misc';
            }
        }
    }