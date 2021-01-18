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
    if (!function_exists('changeSetting')) {
        function changeSetting($modx, $key, $old, $new)
        {
            $setting = $modx->getObject('modSystemSetting', [
                'key' => $key
            ]);
            if ($setting) {
                $setting->set('value', str_replace($old, $new, $setting->get('value')));
                $setting->save();
            }
        }
    }

    if (!function_exists('recursiveRemoveFolder')) {
        function recursiveRemoveFolder($dir)
        {
            $files = array_diff(scandir($dir), ['.', '..']);
            foreach ($files as $file) {
                (is_dir("$dir/$file")) ? recursiveRemoveFolder($dir . '/' . $file) : unlink($dir . '/' . $file);
            }
            return rmdir($dir);
        }
    }

    if (!function_exists('cleanupFolders')) {
        function cleanupFolders($modx, $corePath, $assetsPath, $cleanup)
        {
            $paths = [
                'core' => $corePath,
                'assets' => $assetsPath,
            ];
            $countFiles = 0;
            $countFolders = 0;
            foreach ($cleanup as $folder => $files) {
                foreach ($files as $file) {
                    $legacyFile = $paths[$folder] . $file;
                    if (file_exists($legacyFile)) {
                        if (is_dir($legacyFile)) {
                            recursiveRemoveFolder($legacyFile);
                            $countFolders++;
                        } else {
                            unlink($legacyFile);
                            $countFiles++;
                        }
                    }
                }
            }
            if ($countFolders || $countFiles) {
                $modx->log(xPDO::LOG_LEVEL_INFO, 'Removed ' . $countFiles . ' legacy files and ' . $countFolders . ' legacy folders of TinyMCE 1.x');
            }
        }
    }

    switch ($options[xPDOTransport::PACKAGE_ACTION]) {
        case xPDOTransport::ACTION_INSTALL:
        case xPDOTransport::ACTION_UPGRADE:
            /** @var modX $modx */
            $modx =& $object->xpdo;
            // http://forums.modx.com/thread/88734/package-version-check#dis-post-489104
            $c = $modx->newQuery('transport.modTransportPackage');
            $c->where([
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
            ]);
            $c->where([
                [
                    'modTransportPackage.package_name' => 'tinymcerte',
                    'OR:modTransportPackage.package_name:=' => 'TinyMCE Rich Text Editor',
                ],
                'installed:IS NOT' => null
            ]);
            /** @var modTransportPackage $oldPackage */
            $oldPackage = $modx->getObject('transport.modTransportPackage', $c);
            $corePath = $modx->getOption('tinymcerte.core_path', null, $modx->getOption('coee_path', null, MODX_CORE_PATH) . 'components/tinymcerte/');
            $assetsPath = $modx->getOption('tinymcerte.assets_path', null, $modx->getOption('assets_path', null, MODX_ASSETS_PATH) . 'components/tinymcerte/');
            $modelPath = $corePath . 'model/';
            $modx->addPackage('tinymcerte', $modelPath);

            if ($oldPackage && $oldPackage->compareVersion('1.1.0-pl', '>')) {
                changeSetting($modx, 'tinymcerte.plugins', ' link ', ' modxlink ');
            }

            if ($oldPackage && $oldPackage->compareVersion('1.1.1-pl', '>')) {
                changeSetting($modx, 'tinymcerte.plugins', ' image ', ' modximage ');
            }

            if ($oldPackage && $oldPackage->compareVersion('2.0.0-b1', '>')) {
                changeSetting($modx, 'tinymcerte.plugins', ' contextmenu ', ' ');
                changeSetting($modx, 'tinymcerte.inline_format', '"icon": "strikethrough"', '"icon": "strike-through"');
                changeSetting($modx, 'tinymcerte.inline_format', '"icon": "code"', '"icon": "sourcecode"');
                changeSetting($modx, 'tinymcerte.alignment_format', '"icon": "alignleft"', '"icon": "align-left"');
                changeSetting($modx, 'tinymcerte.alignment_format', '"icon": "aligncenter"', '"icon": "align-center"');
                changeSetting($modx, 'tinymcerte.alignment_format', '"icon": "alignright"', '"icon": "align-right"');
                changeSetting($modx, 'tinymcerte.alignment_format', '"icon": "alignjustify"', '"icon": "align-justify"');

                $cleanup = [
                    'core' => [
                        'docs/changelog.txt',
                        'docs/license.txt',
                        'docs/readme.txt',
                    ],
                    'assets' => [
                        'js/mgr/extras/browser.js',
                        'js/vendor/autocomplete.js',
                    ]
                ];
                cleanupFolders($modx, $corePath, $assetsPath, $cleanup);
            }

            break;
    }
}
return true;
