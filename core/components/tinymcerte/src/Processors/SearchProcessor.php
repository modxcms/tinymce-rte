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

    /** @var TinyMCERTE $tinymcerte */
    public $tinymcerte;

    /**
     * {@inheritDoc}
     * @param modX $modx A reference to the modX instance
     * @param array $properties An array of properties
     */
    public function __construct(modX &$modx, array $properties = [])
    {
        parent::__construct($modx, $properties);

        $corePath = $this->modx->getOption('tinymcerte.core_path', null, $this->modx->getOption('core_path') . 'components/tinymcerte/');
        $this->tinymcerte = $this->modx->getService('tinymcerte', 'TinyMCERTE', $corePath . 'model/tinymcerte/');
    }

    /**
     * {@inheritDoc}
     * @return string
     */
    public function process()
    {
        $query = $this->getProperty('query');
        $ctx = $this->getProperty('ctx');
        $limit = $this->getProperty('limit', 10);
        $c = $this->modx->newQuery('modResource');
        $c->where([
            'deleted' => false,
        ]);
        if (!empty($query)) {
            $c->where([
                'pagetitle:LIKE' => '%' . $query . '%'
            ]);
        }
        if (!empty($ctx)) {
            $c->where([
                'context_key' => $ctx
            ]);
        }
        $c->select('id, pagetitle, context_key');
        $c->limit($limit);
        $results = [];
        if ($c->prepare() && $c->stmt->execute()) {
            $results = $c->stmt->fetchAll(PDO::FETCH_ASSOC);
        }
        return $this->outputArray($results);

    }
}