<?php
/**
 *
 * @package tinymcerte
 * @subpackage build
 */
if ($object->xpdo) {
    switch ($options[xPDOTransport::PACKAGE_ACTION]) {
        case xPDOTransport::ACTION_INSTALL:
        case xPDOTransport::ACTION_UPGRADE:
            /** @var modX $modx */
            $modx =& $object->xpdo;
            // http://forums.modx.com/thread/88734/package-version-check#dis-post-489104
            $c = $modx->newQuery('transport.modTransportPackage');
            $c->where(array(
                'workspace' => 1,
                "(SELECT
                        `signature`
                      FROM {$modx->getTableName('modTransportPackage')} AS `latestPackage`
                      WHERE `latestPackage`.`package_name` = `modTransportPackage`.`package_name`
                      ORDER BY
                         `latestPackage`.`version_major` DESC,
                         `latestPackage`.`version_minor` DESC,
                         `latestPackage`.`version_patch` DESC,
                         IF(`release` = '' OR `release` = 'ga' OR `release` = 'pl','z',`release`) DESC,
                         `latestPackage`.`release_index` DESC
                      LIMIT 1,1) = `modTransportPackage`.`signature`",
            ));
            $c->where(array(
                array(
                    'modTransportPackage.package_name' => 'tinymcerte',
                    'OR:modTransportPackage.package_name:=' => 'TinyMCE Rich Text Editor',
                ),
                'installed:IS NOT' => null
            ));
            /** @var modTransportPackage $oldPackage */
            $oldPackage = $modx->getObject('transport.modTransportPackage', $c);
            $modelPath = $modx->getOption('tinymcerte.core_path', null, $modx->getOption('core_path', null, MODX_CORE_PATH) . 'components/tinymcerte/') . 'model/';
            $modx->addPackage('tinymcerte', $modelPath);
            
            if ($oldPackage && $oldPackage->compareVersion('1.1.0-pl', '>')) {
                $plugins = $modx->getObject('modSystemSetting', array('key' => 'tinymcerte.plugins'));
                if ($plugins) {
                    $pluginsValue = $plugins->get('value');
                    $pluginsValue = str_replace(' link ', ' modxlink ', $pluginsValue);

                    $plugins->set('value', $pluginsValue);
                    $plugins->save();
                }
            }
        
            if ($oldPackage && $oldPackage->compareVersion('1.1.1-pl', '>')) {
                $plugins = $modx->getObject('modSystemSetting', array('key' => 'tinymcerte.plugins'));
                if ($plugins) {
                    $pluginsValue = $plugins->get('value');
                    $pluginsValue = str_replace(' image ', ' modximage ', $pluginsValue);

                    $plugins->set('value', $pluginsValue);
                    $plugins->save();
                }
            }
            
            break;
    }
}
return true;