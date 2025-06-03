<?php

if ($object->xpdo) {
    /** @var modX $modx */
    $modx =& $object->xpdo;

    switch ($options[xPDOTransport::PACKAGE_ACTION]) {
        case xPDOTransport::ACTION_INSTALL:
        case xPDOTransport::ACTION_UPGRADE:
            $setting = $modx->getObject('modSystemSetting', ['key' => 'tinymcerte.lit']);
            if (!$setting) {
                $setting = $modx->newObject('modSystemSetting');
                $setting->fromArray([
                    'key' => 'tinymcerte.lit',
                    'namespace' => 'tinymcerte',
                    'xtype' => 'textfield',
                    'area' => 'tinymcerte.default',
                    'editedon' => time(),
                    'editedby' => 0,
                ]);
            }
            $setting->set('value', time());
            $setting->save();
            break;
    }
}
