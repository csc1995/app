<?php

/**
 * This file contains package_quiqqer_app_getMenu
 */

/**
 * Returns the menu entries from the project
 *
 * @param {String} $project - Project JSON data
 * @param {String} $menu - Which menu to get ('sideMenu' or 'bottomMenu')
 *
 * @return array
 */
QUI::$Ajax->registerFunction(
    'package_quiqqer_app_ajax_getMenu',
    function ($project, $menu) {

        $Project = QUI::getProjectManager()->decode($project);
        $Config  = QUI::getPackage('quiqqer/app')->getConfig();

        $menuItems = $Config->getValue($menu, $Project->getName() . '_' . $Project->getLang());

        if (!$menuItems) {
            return array();
        }

        $result    = array();
        $menuItems = json_decode($menuItems, true);

        foreach ($menuItems as $menuItem) {
            try {
                $Site = $Project->get($menuItem['id']);

                $resultData = array(
                    'id'    => $Site->getId(),
                    'title' => $Site->getAttribute('title'),
                    'name'  => $Site->getAttribute('name')
                );

                if (isset($menuItem['icon'])) {
                    $resultData['icon'] = $menuItem['icon'];
                } else {
                    $resultData['icon'] = false;
                }

                $result[] = $resultData;
            } catch (QUI\Exception $Exception) {
            }
        }

        return $result;
    },
    array('project', 'menu'),
    'Permission::checkAdminUser'
);
