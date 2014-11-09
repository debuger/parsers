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

namespace Rym;

use Rym\Filters;
use Rym\Source;
use Rym\Parser;

class ParserManager {

    /**
     * @var Parser\AbstractParser
     */
    protected $parser;

    /**
     * @var Source\AbstractSource
     */
    protected $source;

    /**
     * @var array
     */
    protected $elements;

    /**
     * @var DOMElement
     */
    protected $node = null;

    /**
     * @var DOMXPath
     */
    protected $xpath = null;

    /**
     * Setting node to work with
     * @param DOMElement $node
     */
    public function setNode(DOMElement $node)
    {
        $this->node = $node;
        $this->xpath = new DOMXPath($node->ownerDocument);
//        $this->getStack()->clear();
    }

    /**
     * Set parser for manager
     * @param array $parsers
     */
    public function setParsers($parsers) {
        if (!empty($parsers)) {
            $this->parsers = $parsers;
        }
    }

    /**
     * Run all parsers
     * @return DOMElement
     */
    public function run () {
        $source = $this->source->loadSource();
        $result = $this->parser->parse($source);
        foreach ($this->elements as $el => $data) {
            foreach ($result[$el] as $ind => $subres) {
                if (isset($data['filters'])) {
                    $subres = $data['filters']->run($subres);
                }
            $result[$el][$ind] = $this->parser->format($subres);
            }
        }
        return $result;
    }

    /**
     * Remove nodes from current node according to current stack
     */
    public function delete()
    {
        foreach ($this->getStack()->getDelete() as $value) {
            $tmp = $this->xpath->evaluate($value['path']);
            if ($node = $tmp->item(0)) {
                switch($node->nodeType) {
                    case XML_ATTRIBUTE_NODE:
                        $node->ownerElement->removeAttribute($node->name);
                        break;
                    case XML_ELEMENT_NODE:
                    case XML_TEXT_NODE:
                        $node->parentNode->removeChild($node);
                        break;
                }
            } else {
                //@TODO: add logging of failed searches
            }
        }
    }

    /**
     * Set configuration according to provided data.
     * @param array $config
     */
    public function setConfig($config)
    {
        if (!empty($config['source'])) {
            $this->source = $this->loadSource($config['source']);
        }
        if (!empty($config['parser'])) {
            $this->parser = $this->loadParser($config['parser']);
            foreach ($config['parser']['rules'] as $el => $rule) {
                $this->elements[$el] = array();
                if (!empty($config['filters'][$el])) {
                    $this->elements[$el]['filters'] = $this->loadFilters($config['filters'][$el]);
                }
            }
        }
    }

    /**
     * Load Source\AbstractSource according to provided data.
     * @param $config
     *
     * @return Source\AbstractSource
     */
    public function loadSource($config)
    {
        return Source\SourceFactory::factory($config);
    }

    public function loadParser($config)
    {
        return Parser\ParserFactory::factory($config);
    }
    public function loadFilters($config)
    {
        return new Filters\FilterStack($config);
    }
}