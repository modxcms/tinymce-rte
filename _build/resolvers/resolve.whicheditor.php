<?php
/**
 *
 * @package tinymcerte
 * @subpackage build
 */
$success= true;

switch ($options[xPDOTransport::PACKAGE_ACTION]) {
    case xPDOTransport::ACTION_INSTALL:
    case xPDOTransport::ACTION_UPGRADE:
        $object->xpdo->log(xPDO::LOG_LEVEL_INFO,'Attempting to set which_editor setting to TinyMCE RTE.');
        $setting = $object->xpdo->getObject('modSystemSetting',array('key' => 'which_editor'));
        if ($setting) {
            $setting->set('value','TinyMCE RTE');
            $setting->save();
        }
        unset($setting);

        $object->xpdo->log(xPDO::LOG_LEVEL_INFO,'Attempting to set use_editor setting to on.');
        $setting = $object->xpdo->getObject('modSystemSetting',array('key' => 'use_editor'));
        if ($setting) {
            $setting->set('value',1);
            $setting->save();
        }
        unset($setting);

        break;
    case xPDOTransport::ACTION_UNINSTALL:
        $success= true;
        break;
}

return $success;