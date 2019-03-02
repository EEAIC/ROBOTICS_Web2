<?php

class calendar extends ModuleObject {

    function moduleInstall() {
        return new Object();
    }

    function checkUpdate() {
        $oModuleModel = getModel('module');
        if(!$oModuleModel->getTrigger('menu.getModuleListInSitemap', 'calendar', 'model', 'triggerModuleListInSitemap', 'after')) return true;
        return false;
    }

    function moduleUpdate() {
        $oModuleModel = getModel('module');
        $oModuleController = getController('module');

        if(!$oModuleModel->getTrigger('menu.getModuleListInSitemap', 'calendar', 'model', 'triggerModuleListInSitemap', 'after'))
        {
            $oModuleController->insertTrigger('menu.getModuleListInSitemap', 'calendar', 'model', 'triggerModuleListInSitemap', 'after');
        }
        return new Object(0, 'success_updated');
    }

    function moduleUninstall() {
        return new Object();
    }

    function recompileCache() {
        
    }
}


?>