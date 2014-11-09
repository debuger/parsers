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

namespace Rym\Parser;
use Zend\Dom\Query;

/**
 * Class Dom
 *
 * Parse DOMDocument.
 *
 * @package Rym\Parser
 */
class Dom extends AbstractParser{

    /**
     * @inheritdoc
     */
    protected $type = AbstractParser::TYPE_DOM;

    /**
     * @inheritdoc
     *
     * @return \DOMDocument
     */
    public function getEmpty()
    {
        return new DOMDocument('1.0', 'UTF-8');
    }

    /**
     * @inheritdoc
     */
    public function parse($input, $rules = array())
    {
        $result = array();
        if (!empty($rules)) {
            $this->setRules($rules);
        }
        $rules = $this->getRules();
        if(empty($rules)) {
            return $result;
        }
        $zq = new Query();
        $zq->setDocumentHtml('<?xml encoding="UTF-8">' . $input, 'UTF-8');

        foreach ($rules as $key => $keyRules) {
            $result[$key] = array();
            foreach($keyRules as $rule) {
                $qresult = $zq->execute($rule);
                foreach($qresult as $node) {
                    array_push($result[$key], $node);
                }
            }
        }
        return $result;
    }

    /**
     *
     * @inheritdoc
     * @return string
     */
    public function format($input)
    {
        return $input->ownerDocument->saveXML($input);
    }
}
