<?php

namespace TinyMCERTE\Processors;

use modContext;
use modProcessor;
use modX;
use PDO;
use TinyMCERTE;

class SearchProcessor extends modProcessor
{
    public $permissions = [];

    public $languageTopics = ['tinymcerte:default'];

    /**
     * {@inheritDoc}
     * @return string
     */
    public function process()
    {
        $id = $this->getProperty('id');
        $ctx = $this->getProperty('wctx');
        $query = $this->getProperty('query');
        $limit = $this->getProperty('limit', 10);
        $crossContext = (bool) $this->modx->getOption('tinymcerte.links_across_contexts', null, false);
        $c = $this->modx->newQuery('modResource');
        $c->where([
            'deleted' => false,
        ]);
        if (!empty($query)) {
            $c->where([
                'pagetitle:LIKE' => '%' . $query . '%'
            ]);
        }
        if (!empty($id)) {
            $c->where([
                'id:=' => $id,
            ]);
        }
        if (!empty($ctx) && !$crossContext) {
            $c->where([
                'context_key:=' => $ctx,
            ]);
        }
        $c->select('id, concat(pagetitle, " <small>(", context_key, ")</small>") as pagetitle, id as value');
        $c->limit($limit);
        $results = [];
        if ($c->prepare() && $c->stmt->execute()) {
            $results = $c->stmt->fetchAll(PDO::FETCH_ASSOC);
        }
        return $this->outputArray($results);
    }
}
