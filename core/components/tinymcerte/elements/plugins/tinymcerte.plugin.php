<?php
/**
 * TinyMCE Rich Tech Editor Plugin
 *
 * @package tinymcerte
 * @subpackage pluginfile
 *
 * @var modX $modx
 * @var array $scriptProperties
 */

$className = 'TinyMCERTE' . $modx->event->name;

$corePath = $modx->getOption('tinymcerte.core_path', null, $modx->getOption('core_path') . 'components/tinymcerte/');
/** @var TinyMCERTE $tinymcerte */
$tinymcerte = $modx->getService('tinymcerte', 'TinyMCERTE', $corePath . 'model/tinymcerte/', [
    'core_path' => $corePath
]);

if ($tinymcerte) {
    $modx->loadClass('TinyMCERTEPlugin', $tinymcerte->getOption('modelPath') . 'tinymcerte/events/', true, true);
    $modx->loadClass($className, $tinymcerte->getOption('modelPath') . 'tinymcerte/events/', true, true);
    if (class_exists($className)) {
        /** @var TinyMCERTEPlugin $handler */
        $handler = new $className($modx, $scriptProperties);
        $handler->run();
    }
}

return;
