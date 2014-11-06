<?php
/**
 * TinyMCE Rich Tech Editor
 *
 */
$corePath = $modx->getOption('tinymcerte.core_path', null, $modx->getOption('core_path', null, MODX_CORE_PATH) . 'components/tinymcerte/');
/** @var TinyMCERTE $tinymcerte */
$tinymcerte = $modx->getService(
    'tinymcerte',
    'TinyMCERTE',
    $corePath . 'model/tinymcerte/',
    array(
        'core_path' => $corePath
    )
);

$className = 'TinyMCERTE' . $modx->event->name;
$modx->loadClass('TinyMCERTEPlugin', $tinymcerte->getOption('modelPath') . 'tinymcerte/events/', true, true);
$modx->loadClass($className, $tinymcerte->getOption('modelPath') . 'tinymcerte/events/', true, true);
if (class_exists($className)) {
    /** @var TinyMCERTEPlugin $handler */
    $handler = new $className($modx, $scriptProperties);
    $handler->run();
}
return;