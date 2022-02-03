<?php
/**
 * TinyMCE Rich Tech Editor Plugin
 *
 * @package tinymcerte
 * @subpackage plugin
 *
 * @var modX $modx
 * @var array $scriptProperties
 */

$className = 'TinyMCERTE\Plugins\Events\\' . $modx->event->name;

$corePath = $modx->getOption('tinymcerte.core_path', null, $modx->getOption('core_path') . 'components/tinymcerte/');
/** @var TinyMCERTE $tinymcerte */
$tinymcerte = $modx->getService('tinymcerte', 'TinyMCERTE', $corePath . 'model/tinymcerte/', [
    'core_path' => $corePath
]);

if ($tinymcerte) {
    if (class_exists($className)) {
        $handler = new $className($modx, $scriptProperties);
        if (get_class($handler) == $className) {
            $handler->run();
        } else {
            $modx->log(xPDO::LOG_LEVEL_ERROR, $className. ' could not be initialized!', '', 'TinyMCE RTE Plugin');
        }
    } else {
        $modx->log(xPDO::LOG_LEVEL_ERROR, $className. ' was not found!', '', 'TinyMCE RTE Plugin');
    }
}

return;
