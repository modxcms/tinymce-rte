<?php
/**
 *
 * @package tinymcerte
 * @subpackage build
 *
 * @var array $options
 * @var xPDOObject $object
 */

if ($object->xpdo) {
    switch ($options[xPDOTransport::PACKAGE_ACTION]) {
        case xPDOTransport::ACTION_INSTALL:
        case xPDOTransport::ACTION_UPGRADE:
            $object->xpdo->log(xPDO::LOG_LEVEL_INFO, 'Attempting to set which_editor setting to TinyMCE RTE.');
            $setting = $object->xpdo->getObject('modSystemSetting', [
                'key' => 'which_editor'
            ]);
            if ($setting) {
                $setting->set('value', 'TinyMCE RTE');
                $setting->save();
            }
            unset($setting);

            $object->xpdo->log(xPDO::LOG_LEVEL_INFO, 'Attempting to set use_editor setting to on.');
            $setting = $object->xpdo->getObject('modSystemSetting', [
                'key' => 'use_editor',
                'value' => '0'
            ]);
            if ($setting) {
                $setting->set('value', 1);
                $setting->save();
            }
            unset($setting);

            break;
        case xPDOTransport::ACTION_UNINSTALL:
            $setting = $object->xpdo->getObject('modSystemSetting', [
                'key' => 'which_editor',
                'value' => 'TinyMCE RTE'
            ]);
            if ($setting) {
                $setting->set('value', '');
                $setting->save();
            }

            break;
    }
}
return true;
