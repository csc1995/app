<?php

/**
 * This file contains QUI\APP\EventHandler
 */
namespace QUI\APP;

use QUI;

/**
 * Class RestProvider
 *
 * @package QUI\OAuth
 */
class EventHandler
{
    /**
     * @param $project
     * @param array $config
     * @param array $params
     */
    public static function onProjectConfigSave($project, array $config, array $params)
    {
        if (!isset($params['quiqqerApp.settings.title'])) {
            return;
        }

        try {
            $Project = QUI::getProject($project);
        } catch (QUI\Exception $Exception) {
            return;
        }

        // title & desc
        $Package = QUI::getPackage('quiqqer/app');
        $group   = 'quiqqer/app';

        $var_title    = 'app.title.' . $Project->getName();
        $var_desc     = 'app.description.' . $Project->getName();
        $titles       = json_decode($params['quiqqerApp.settings.title'], true);
        $descriptions = json_decode($params['quiqqerApp.settings.description'], true);

        try {
            QUI\Translator::add($group, $var_title, $Package->getName());
        } catch (QUI\Exception $Exception) {
            // Throws error if lang var already exists
        }

        try {
            QUI\Translator::add($group, $var_desc, $Package->getName());
        } catch (QUI\Exception $Exception) {
            // Throws error if lang var already exists
        }

        try {
            QUI\Translator::update(
                'quiqqer/app',
                $var_title,
                $Package->getName(),
                $titles
            );
        } catch (QUI\Exception $Exception) {
            QUI\System\Log::writeException($Exception);
        }

        try {
            QUI\Translator::update(
                'quiqqer/app',
                $var_desc,
                $Package->getName(),
                $descriptions
            );
        } catch (QUI\Exception $Exception) {
            QUI\System\Log::writeException($Exception);
        }

        QUI\Translator::publish('quiqqer/app');

        // clear cache
        QUI\Cache\Manager::clear(
            'quiqqer/app/settings/' . $Project->getName()
        );
    }


    /**
     * @param QUI\Rewrite $Rewrite
     * @param $url
     */
    public static function onRequest(QUI\Rewrite $Rewrite, $url)
    {
        // If request comes from a QUIQQER app
        if (Validate::isAppRequest()) {
            // Save that this is an QUIQQER app session
            QUI::getSession()->set('__APP__', 1);

            // Remove SAMEORIGIN Policy for iframes inside the app
            QUI::getGlobalResponse()->headers->remove("X-Frame-Options");
        }
    }
}
