<?php
/**
 * TinyMCE Rich Text Editor Connector
 *
 * @package tinymcerte
 * @subpackage connector
 *
 * @var modX $modx
 */

require_once dirname(dirname(dirname(dirname(__FILE__)))) . '/config.core.php';
require_once MODX_CORE_PATH . 'config/' . MODX_CONFIG_KEY . '.inc.php';
require_once MODX_CONNECTORS_PATH . 'index.php';

$corePath = $modx->getOption('tinymcerte.core_path', null, $modx->getOption('core_path', null, MODX_CORE_PATH) . 'components/tinymcerte/');
/** @var TinyMCERTE $tinymcerte */
$tinymcerte = $modx->getService('tinymcerte', 'TinyMCERTE', $corePath . 'model/tinymcerte/', [
    'core_path' => $corePath
]);

$processorsPath = $tinymcerte->getOption('processorsPath');

// Handle request
$modx->request->handleRequest([
    'processors_path' => $processorsPath,
    'location' => ''
]);
