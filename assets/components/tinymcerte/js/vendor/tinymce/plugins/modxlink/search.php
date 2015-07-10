<?php
/**
 * Handles dynamic search
 *
 * @package tinymce
 */
require_once dirname(dirname(dirname(dirname(dirname(dirname(dirname(dirname(dirname(__FILE__))))))))).'/config.core.php';
require_once MODX_CORE_PATH.'config/'.MODX_CONFIG_KEY.'.inc.php';
require_once MODX_CONNECTORS_PATH.'index.php';

$query = $modx->getOption('q',$_REQUEST,'');
if(strlen($query) < 3) exit();

$c = $modx->newQuery('modResource');
$c->where(array(
    'pagetitle:LIKE' => '%'.$query.'%',
    'OR:alias:LIKE' => '%'.$query.'%',
));

$count = $modx->getCount('modResource',$c);

$c->select(array('id','pagetitle','alias'));
$c->limit(10);

$resources = $modx->getCollection('modResource',$c);
$a = array();
foreach ($resources as $resource) {
	$a[] = array(
		'id' => $resource->get('id')
		,'pagetitle' => $resource->get('pagetitle') . " (".$resource->get('id').")"
		,'title' => $resource->get('pagetitle')
		,'alias' => $resource->get('alias')
	);
}
exit(json_encode($a));
