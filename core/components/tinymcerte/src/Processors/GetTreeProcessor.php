<?php
/**
 * Get tree processor for TinyMCE RTE
 *
 * @package tinymcerte
 * @subpackage processors
 */

namespace TinyMCERTE\Processors;

use modContext;
use modProcessor;
use modX;
use PDO;
use TinyMCERTE;

/**
 * Class GetTreeProcessor
 */
class GetTreeProcessor extends modProcessor
{
    public $contextKey = false;
    public $startNode = 0;
    public $permissions = [];

    public $languageTopics = array('tinymcerte:default');

    /** @var TinyMCERTE */
    public $tinymcerte;

    /**
     * {@inheritDoc}
     * @param modX $modx A reference to the modX instance
     * @param array $properties An array of properties
     */
    function __construct(modX &$modx, array $properties = array())
    {
        parent::__construct($modx, $properties);

        $corePath = $this->modx->getOption('tinymcerte.core_path', null, $this->modx->getOption('core_path') . 'components/tinymcerte/');
        $this->tinymcerte =& $this->modx->getService('tinymcerte', 'TinyMCERTE', $corePath . 'model/tinymcerte/');
    }

    /**
     * {@inheritDoc}
     * @return bool
     */
    public function checkPermissions(): bool
    {
        return $this->modx->hasPermission('resource_tree');
    }

    /**
     * {@inheritDoc}
     * @return string[]
     */
    public function getLanguageTopics(): array
    {
        return ['tinymcerte:default'];
    }

    /**
     * {@inheritDoc}
     * @return mixed
     */
    public function process()
    {
        $this->getRootNode();
        $this->getResources($this->contextKey);
        if (!$this->tinymcerte->getOption('links_across_contexts')) {
            $items = $this->getContextTree($this->contextKey);
        } else {
            $items = array();
            /** @var modContext[] $contexts */
            $contexts = $this->modx->getCollection('modContext', array(
                'key:!=' => 'mgr'
            ));
            foreach ($contexts as $context) {
                $items[] = array(
                    'title' => $context->get('key'),
                    'menu' => $this->getContextTree($context->get('key'))
                );
            }
        }
        return $this->outputArray($items);
    }

    /**
     * Get the MODX tree for a context.
     *
     * @param $contextKey
     * @return array
     */
    private function getContextTree($contextKey): array
    {
        $tree = $this->modx->getTree($this->startNode, 10, array(
            'context' => $contextKey
        ));
        $resources = $this->getResources($contextKey);
        return $this->fillTree($tree, $resources);
    }

    /**
     * Get the MODX resources id and pagetitle in a context.
     *
     * @param string $context
     * @return array
     */
    private function getResources(string $context): array
    {
        $c = $this->modx->newQuery('modResource');
        $c->where([
            'context_key' => $context,
            'deleted' => false,
        ]);
        $c->select('id, pagetitle');
        if ($c->prepare() && $c->stmt->execute()) {
            $resoures = $c->stmt->fetchAll(PDO::FETCH_KEY_PAIR);
        } else {
            $resoures = [];
        }

        return $resoures;
    }

    /**
     * Fill the tree recursive with the node/resource values.
     *
     * @param array $nodes
     * @param array $resources
     * @return array
     */
    private function fillTree(array $nodes, array $resources): array
    {
        $result = [];
        if (is_array($nodes)) {
            foreach ($nodes as $node => $subtree) {
                if (isset($resources[$node])) {
                    if (is_array($subtree)) {
                        $result[] = [
                            'title' => $resources[$node] . ' (' . $node . ')',
                            'menu' => array_merge([
                                [
                                    'title' => '◁ ' . $resources[$node] . ' (' . $node . ')',
                                    'value' => '[[~' . $node . ']]',
                                    'display' => $resources[$node]
                                ]
                            ],
                                $this->fillTree($subtree, $resources))
                        ];
                    } else {
                        $result[] = [
                            'title' => $resources[$node] . ' (' . $node . ')',
                            'value' => '[[~' . $node . ']]',
                            'display' => $resources[$node]
                        ];
                    }
                }
            }
        }
        return $result;
    }

    /**
     * Determine the context and root and start nodes for the tree.
     *
     * @return void
     */
    public function getRootNode()
    {
        $ctx = $this->getProperty('wctx');
        if ($ctx && $this->modx->switchContext($ctx)) {
            $this->contextKey = $ctx;
            $this->startNode = 0;
        } else {
            $this->contextKey = $this->modx->getOption('default_context');
            $this->startNode = $this->modx->getOption('tree_root_id', null, 0);
        }
    }
}
