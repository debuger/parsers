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

/**
 * Class Standard
 *
 * @package Rym\Filters
 */
class Standard extends AbstractFilter{

    /**
     * @inheritdoc
     */
    protected $type = AbstractFilter::TYPE_DOM;

    /**
     * Tags for cutting while parsing.
     *
     * @var array
     */
    protected $black_list = array('br', 'p');

    /**
     * Tags list for replace.
     *
     * @var array
     */
    protected $replace_list = array('h1' => 'h3', 'h2' => 'h3');

    /**
     * Allowed attributes for tags.
     *
     * @var array
     */
    protected $allowed_attributes = array('img' => array('src'), 'a' => array('src', 'href'));

    /**
     * @var bool Allow empty tags.
     */
    protected $allow_empty_tags = false;

    /**
     * @inheritdoc
     */
    protected $options = array(
        'black_list',
        'replace_list',
        'allowed_attributes',
        'allow_empty_tags'
    );

    /**
     * @inheritdoc
     */
    protected function runDom(\DOMNode $node)
    {
        if (empty($node)) {
            return;
        }
        $path =  $node->getNodePath();
        switch ($node->nodeType) {
            case XML_ELEMENT_NODE:
                if($node->hasAttributes()) {
                    foreach ($node->attributes as $child) {
                        self::runDom($child);
                    }
                }
                if ($node->hasChildNodes()) {
                    foreach ($node->childNodes as $child) {
                        self::runDom($child);
                    }
                }
                if (array_key_exists($node->tagName, $this->replace_list)) {
                    $newNode = $node->ownerDocument->createElement($this->replace_list[$node->tagName]);
                    $this->cloneNode($node, $newNode);
                    $this->replaceNode($path, $newNode);
                } elseif (in_array($node->tagName, $this->black_list)) {
                    $this->deleteNode($path);
                }
                if (!$this->allow_empty_tags && !$node->hasChildNodes()
                    && trim($node->nodeValue) == '' && !$node->hasAttributes()) {
                    $this->deleteNode($path);
                }
                break;
            case XML_ATTRIBUTE_NODE:
                if(!isset($this->allowed_attributes[$node->parentNode->tagName])
                            || !in_array($node->name, $this->allowed_attributes[$node->parentNode->tagName])) {
                    $this->deleteNode($path);
                }
                break;
            case XML_TEXT_NODE:
                if(!$this->allow_empty_tags && trim($node->nodeValue) == '') {
                    $this->deleteNode($path);
                }
                break;
            default:
                break;
        }
    }

    /**
     * Clone attributes from one \DOMNode to another
     *
     * @param \DOMNode $node
     * @param \DOMNode $nodeNew
     */
    public static function cloneNode($node, $nodeNew)
    {
        if ($node->hasAttributes()) {
            foreach ($node->attributes as $attr) {
                $nodeNew->setAttribute($attr->name, $attr->nodeValue);
            }
        }
        if ($node->hasChildNodes()) {
            foreach ($node->childNodes as $child) {
                switch ($child->nodeType) {
                    case XML_TEXT_NODE:
                        $nd = $nodeNew->ownerDocument->createTextNode('');
                        break;
                    default:
                        $nd = $nodeNew->ownerDocument->createElement($child->tagName);
                        break;
                }
                static::cloneNode($child, $nd);
                $nodeNew->appendChild($nd);
            }
        }
        if ($node->nodeType == XML_TEXT_NODE) {
            $nodeNew->nodeValue .= $node->nodeValue;
        }
    }
}
