<?php
/**
 * TinyMCE Rich Text Editor Connector
 *
 * @package tinymcerte
 */
require_once dirname(dirname(dirname(dirname(__FILE__)))).'/config.core.php';
require_once MODX_CORE_PATH . 'config/' . MODX_CONFIG_KEY . '.inc.php';
require_once MODX_CONNECTORS_PATH . 'index.php';

$corePath = $modx->getOption('tinymcerte.core_path', null, $modx->getOption('core_path', null, MODX_CORE_PATH) . 'components/tinymcerte/');
$tinymcerte = $modx->getService(
    'tinymcerte',
    'TinyMCE Rich Text Editor',
    $corePath . 'model/tinymcerte/',
    array(
        'core_path' => $corePath
    )
);

/* handle request */
$modx->request->handleRequest(
    array(
        'processors_path' => $tinymcerte->getOption('processorsPath', null, $corePath . 'processors/'),
        'location' => '',
    )
);