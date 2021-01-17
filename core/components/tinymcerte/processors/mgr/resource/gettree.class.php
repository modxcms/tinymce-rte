<?php
/**
 * Get resources
 *
 * @package tinymcerte
 * @subpackage processors
 */

include_once MODX_CORE_PATH . 'model/modx/processors/resource/getlist.class.php';

class TinyMCERTEResourceGetTreeProcessor extends modProcessor
{
    /** @var int $defaultRootId */
    public $defaultRootId;
    public $itemClass = 'modResource';
    public $contextKey = false;
    public $startNode = 0;
    public $permissions = [];

    public function checkPermissions()
    {
        return $this->modx->hasPermission('resource_tree');
    }

    public function getLanguageTopics()
    {
        return ['tinymcerte:default'];
    }

    /**
     * {@inheritDoc}
     *
     * @return mixed
     */
    public function process()
    {
        $this->getRootNode();
        $this->getResources($this->contextKey);
        $tree = $this->modx->getTree($this->startNode);
        $resources = $this->getResources($this->contextKey);
        $items = $this->fillTree($tree, $resources);
        return $this->outputArray($items);
    }

    /**
     * @param string $context
     * @return array
     */
    private function getResources($context)
    {
        $c = $this->modx->newQuery($this->itemClass);
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
     * @param array $nodes
     * @param array $resources
     * @return array
     */
    private function fillTree($nodes, $resources)
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
                                    'title' => '◀ ' . $resources[$node] . ' (' . $node . ')',
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
     * Determine the context and root and start nodes for the tree
     * @return void
     */
    public function getRootNode()
    {
        $this->defaultRootId = $this->modx->getOption('tree_root_id', null, 0);
        $ctx = $this->getProperty('wctx');
        if ($ctx && $this->modx->switchContext($ctx)) {
            $this->contextKey = $ctx;
            $this->startNode = 0;
        } else {
            $this->contextKey = $this->modx->getOption('default_context');
            $this->startNode = $this->defaultRootId;
        }
    }
}

return 'TinyMCERTEResourceGetTreeProcessor';
