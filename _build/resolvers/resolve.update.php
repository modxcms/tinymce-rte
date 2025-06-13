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
    if (!function_exists('addToSetting')) {
        /**
         * @param modX $modx
         * @param string $key
         * @param string $old
         * @param string $new
         */
        function addToSetting($modx, $key, $add, $separator = ' ')
        {
            /** @var modSystemSetting $setting */
            $setting = $modx->getObject('modSystemSetting', [
                'key' => $key
            ]);
            if ($setting && strpos($setting->get('value'), $add) === false) {
                $array = explode($separator, $setting->get('value'));
                $array[] = $add;
                $setting->set('value', implode(' ', $array));
                $setting->save();
            }
        }
    }

    if (!function_exists('removeFromSetting')) {
        /**
         * @param modX $modx
         * @param string $key
         * @param string $old
         * @param string $new
         */
        function removeFromSetting($modx, $key, $remove, $separator = ' ')
        {
            /** @var modSystemSetting $setting */
            $setting = $modx->getObject('modSystemSetting', [
                'key' => $key
            ]);
            if ($setting && strpos($setting->get('value'), $remove) !== false) {
                $array = explode($separator, $setting->get('value'));
                $array = array_diff($array, [$remove]);
                $setting->set('value', implode(' ', $array));
                $setting->save();
            }
        }
    }

    if (!function_exists('changeSetting')) {
        /**
         * @param modX $modx
         * @param string $key
         * @param string $old
         * @param string $new
         */
        function changeSetting($modx, $key, $old, $new)
        {
            /** @var modSystemSetting $setting */
            $setting = $modx->getObject('modSystemSetting', [
                'key' => $key
            ]);
            if ($setting) {
                $setting->set('value', str_replace($old, $new, $setting->get('value')));
                $setting->save();
            }
        }
    }

    if (!function_exists('changeSettingArea')) {
        /**
         * @param modX $modx
         * @param string $old
         * @param string $new
         */
        function changeSettingArea($modx, $old, $new)
        {
            /** @var modSystemSetting[] $settings */
            $settings = $modx->getIterator('modSystemSetting', [
                'namespace' => 'tinymcerte',
                'area' => $old
            ]);
            foreach ($settings as $setting) {
                $setting->set('area', $new);
                $setting->save();
            }
        }
    }

    if (!function_exists('recursiveRemoveFolder')) {
        /**
         * @param string $dir
         * @return bool
         */
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
        /**
         * @param modX $modx
         * @param string $corePath
         * @param string $assetsPath
         * @param array $cleanup
         */
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

    if (!function_exists('cleanupPlugin')) {
        /**
         * @param modX $modx
         * @param string $name
         */
        function cleanupPlugin($modx, $name)
        {
            /** @var modPlugin $plugin */
            $plugin = $modx->getObject('modPlugin', [
                'name' => $name
            ]);
            if ($plugin) {
                $plugin->remove();
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
                      FROM {$modx->getTableName('transport.modTransportPackage')} AS `latestPackage`
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

            if ($oldPackage && $oldPackage->compareVersion('1.1.0-pl', '>')) {
                changeSetting($modx, 'tinymcerte.plugins', ' link ', ' modxlink ');
            }

            if ($oldPackage && $oldPackage->compareVersion('1.1.1-pl', '>')) {
                changeSetting($modx, 'tinymcerte.plugins', ' image ', ' modximage ');
            }

            if ($oldPackage && $oldPackage->compareVersion('2.0.0-pl', '>')) {
                changeSetting($modx, 'tinymcerte.skin', 'lightgray', 'modx');
                changeSetting($modx, 'tinymcerte.inline_format', '"icon": "strikethrough"', '"icon": "strike-through"');
                changeSetting($modx, 'tinymcerte.inline_format', '"icon": "code"', '"icon": "sourcecode"');
                changeSetting($modx, 'tinymcerte.alignment_format', '"icon": "alignleft"', '"icon": "align-left"');
                changeSetting($modx, 'tinymcerte.alignment_format', '"icon": "aligncenter"', '"icon": "align-center"');
                changeSetting($modx, 'tinymcerte.alignment_format', '"icon": "alignright"', '"icon": "align-right"');
                changeSetting($modx, 'tinymcerte.alignment_format', '"icon": "alignjustify"', '"icon": "align-justify"');
                removeFromSetting($modx, 'tinymcerte.plugins', 'contextmenu');
                changeSettingArea($modx, 'default', 'tinymcerte.default');

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
                cleanupPlugin($modx, 'TinyMCERTE');
            }

            if ($oldPackage && $oldPackage->compareVersion('2.0.6-pl', '>')) {
                addToSetting($modx, 'tinymcerte.plugins', 'autoresize');
            }

            if ($oldPackage && $oldPackage->compareVersion('2.1.0-pl', '>')) {
                addToSetting($modx, 'tinymcerte.plugins', 'modai');
                addToSetting($modx, 'tinymcerte.toolbar1', '| modai_generate modai_generate_image modai_enhance');
            }

        if ($oldPackage && $oldPackage->compareVersion('2.1.1-pl', '>')) {
            removeFromSetting($modx, 'tinymcerte.toolbar1', 'modai_generate_image');
        }

        if ($oldPackage && $oldPackage->compareVersion('3.0.2-pl', '>')) {
            changeSetting($modx, 'tinymcerte.tiny_url', 'https://cdnjs.cloudflare.com/ajax/libs/tinymce/6.8.5/tinymce.min.js', '{tinymcerte.assets_url}mgr/tinymce/tinymce.min.js');
            removeFromSetting($modx, 'tinymcerte.plugins', 'paste');
            removeFromSetting($modx, 'tinymcerte.plugins', 'print');
        }

        if ($oldPackage && $oldPackage->compareVersion('3.0.3-pl', '>')) {
            removeFromSetting($modx, 'tinymcerte.plugins', 'modximage');
            addToSetting($modx, 'tinymcerte.plugins', 'quickbars');
            changeSetting($modx, 'tinymcerte.toolbar1', 'link', 'modxlink');
            changeSetting($modx, 'tinymcerte.toolbar2', 'link', 'modxlink');
            changeSetting($modx, 'tinymcerte.toolbar3', 'link', 'modxlink');
        }

        if ($oldPackage && $oldPackage->compareVersion('3.0.4-pl', '>')) {
            changeSetting($modx, 'tinymcerte.skin', 'modx', 'oxide');
        }

        if ($oldPackage && $oldPackage->compareVersion('3.1.0-pl', '>')) {
            addToSetting($modx, 'tinymcerte.plugins', 'image');
        }

            break;
    }
}
return true;
