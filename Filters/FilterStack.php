<?php
/**
 * Copyright (C) 2014 rym <rym.the.great@gmail.com>
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @license http://www.gnu.org/licenses/ GPLv3
 */

namespace Rym\Filters;


class FilterStack {

    /**
     * Storage for nodes to manipulate with.
     *
     * @var array
     */
    protected $stack;

    /**
     * Filters to run.
     *
     * @var array
     */
    protected $filters = array();

    public function __construct($config) {
        $this->clear();
        if(!empty($config)) {
            $this->loadConfig($config);
        }
    }

    /**
     * Run filters.
     *
     * @param $input
     *
     * @return mixed
     */
    public function run($input)
    {
        $this->clear();
        foreach ($this->filters as $filter) {
            $filter->run($input);
        }
        $this->filter($input);
        return $input;
    }

    /**
     * Replace node from given path to another one.
     *
     * @param String $what XPath to the node.
     * @param \DOMElement $with Node to replace with.
     *
     * @return bool
     */
    public function addReplace($what, $with) {
        $hash = hash('md5', $what);
        $result = false;
        $counter = $this->get('counter');
        if (!array_key_exists($hash, $this->get('replace'))) {
            $this->set('replace', $hash, array('path' => $what, 'node' => $with, 'priority' => $counter['replace']));
            $this->set('counter', 'replace', $counter['replace']++);
            if (array_key_exists($hash, $this->get('delete'))) {
                $this->removeDelete($what);
            }
            $result = true;
        }
        return $result;
    }

    /**
     * Add node for deletion.
     * @param String $what XPath of node.
     *
     * @return bool
     */
    public function addDelete($what) {
        $hash = hash('md5', $what);
        $result = false;
        if (!array_key_exists($hash, $this->get('replace'))) {
            $counter = $this->get('counter');
            $this->set('delete', $hash, array('path' => $what, 'priority' => $counter['delete']));
            $this->set('counter', 'delete', $counter++);
            $result = true;
        }
        return $result;
    }

    /**
     * Remove node from deletion.
     *
     * @param String $what XPath of node to prevent deletion.
     */
    public function removeDelete($what) {
        $hash = hash('md5', $what);
        if (array_key_exists($hash, $this->delete)) {
            unset($this->delete[$hash]);
        }
    }

    /**
     * @return null
     */
    public function getDelete()
    {
        return $this->get('delete');
    }

    public function getReplace()
    {
        return $this->get('replace');
    }

    public function clear()
    {
        $this->stack = array(
            'replace' => array(),
            'delete' => array(),
            'counter' => array(
                'replace' => 0,
                'delete' => 0
            )
        );
    }

    /**
     * Getter for internal data.
     *
     * @param $key
     *
     * @return array|null
     */
    protected function get($key)
    {
        if (isset($this->stack[$key])) {
            return $this->stack[$key];
        }
        return null;
    }

    /**
     * Setter for internal data
     * @param String $key
     * @param String $hash
     * @param mixed $value
     */
    protected function set($key, $hash, $value)
    {
        if (isset($this->stack[$key])) {
            $this->stack[$key][$hash] = $value;
        }
    }

    /**
     * Load configuration.
     * @param array $config
     */
    public function loadConfig($config)
    {
        if (!is_array($config)) {
            return;
        }
        foreach($config as $key => $f) {
            if (!is_array($f)) {
                $f = array('class' => $f);
            } elseif (empty($f['class'])) {
                $f['class'] = $key;
            }
            $filter = FilterFactory::factory($this, $f);
            if ($filter) {
                array_push($this->filters, $filter);
            }
        }
    }

    /**
     * Filter according to rules.
     * @param $input
     */
    protected function filter($input)
    {
        $nodes = array();
        $xpath = new \DOMXPath($input->ownerDocument);
        foreach ($this->getDelete() as $delete) {
            $list = $xpath->evaluate($delete['path']);
            if ($node = $list->item(0)) {
                array_push($nodes, array('what' => $node));
            }
        }
        foreach ($this->getReplace() as $replace) {
            $list = $xpath->query($replace['path']);
            if ($node = $list->item(0)) {
                array_push($nodes, array('what' => $node, 'with' => $replace['with']));
            }
        }
        foreach ($nodes as $node) {
            if (empty($node['with'])) {
                $this->deleteNode($node['what']);
            } else {
                $this->replaceNode($node['what'], $node['with']);
            }
        }
    }

    /**
     * Replace node
     * @param \DOMElement $what
     * @param \DOMElement $with
     */
    protected  function replaceNode($what, $with)
    {
        $what->parentNode->replaceChild($what, $with);
        //@TODO: Add filter for new node
    }

    /**
     * Remove node.
     * @param \DOMElement $node
     */
    public function deleteNode($node)
    {
        switch($node->nodeType) {
            case XML_ATTRIBUTE_NODE:
                $node->ownerElement->removeAttribute($node->name);
                break;
            case XML_ELEMENT_NODE: case XML_TEXT_NODE:
            $node->parentNode->removeChild($node);
            break;
        }
    }
}
