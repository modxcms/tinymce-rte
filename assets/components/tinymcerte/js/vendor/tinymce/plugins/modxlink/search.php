<?php
/**
 * Handles dynamic search
 *
 * @package tinymce
 */
require_once dirname(dirname(dirname(dirname(dirname(dirname(dirname(dirname(dirname(__FILE__))))))))).'/config.core.php';
require_once MODX_CORE_PATH.'config/'.MODX_CONFIG_KEY.'.inc.php';
require_once MODX_CONNECTORS_PATH.'index.php';

$id = (int) $modx->getOption('id', $_REQUEST, 0);
if ($id > 0) {
    $resource = $modx->getObject('modResource', $id);
    if ($resource) {
        exit(json_encode([
            'id'        => $resource->get('id'),
            'pagetitle' => htmlspecialchars($resource->get('pagetitle')) . " (" . $resource->get('id') . ")",
            'title'     => $resource->get('pagetitle'),
            'alias'     => $resource->get('alias')
        ]));
    }
}

$query = $modx->getOption('q',$_REQUEST,'');
if(strlen($query) < 3) exit();

// System setting defines if we should list links across contexts or not
$across_contexts = $modx->getOption('tinymcerte.links_across_contexts', null, true);

$c = $modx->newQuery('modResource');

// Note, reason we nest the query in two array is because if we are going to filter on context, we want the final query on the form:
// ((pagetitle or alias) and context). If we don't filter on context, it will make no difference that the where clause has an extra
// parenthesis
$where_clause = array(array(
  'pagetitle:LIKE' => '%'.$query.'%',
  'OR:alias:LIKE' => '%'.$query.'%',
));

if (!$across_contexts) {
    if (isset($_GET['context']) and strlen($_GET['context']) and $_GET['context'] == 'undefined') {
        $where_clause[] = array(
            'context_key' => $_GET['context']
        );
    }
}

$c->where($where_clause);

$count = $modx->getCount('modResource',$c);

$c->select(array('id','pagetitle','alias'));
$c->limit(10);

$resources = $modx->getCollection('modResource',$c);

$a = array();
foreach ($resources as $resource) {
	$a[] = array(
		'id' => $resource->get('id')
		,'pagetitle' => htmlspecialchars($resource->get('pagetitle')) . " (" . $resource->get('id') . ")"
		,'title' => $resource->get('pagetitle')
		,'alias' => $resource->get('alias')
	);
}
exit(json_encode($a));
