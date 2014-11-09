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

/**
 * Class AbstractParser
 *
 * Abstract parent for parsers.
 *
 * @package Rym\Parser
 */
abstract class AbstractParser {

    const TYPE_DOM = 'Dom';

    const TYPE_PLAIN = 'Plain';

    /**
     * Rules to work with during parse.
     * @var mixed
     */
    protected $rules;

    /**
     * Type of parser.
     * @var String
     */
    protected $type;

    public function __construct($rules)
    {
        $this->setRules($rules);
    }

    /**
     * @return String
     */
    public function getParserType()
    {
        return $this->type;
    }

    /**
     * Parse $input with provided $rules.
     * @param mixed $input
     * @param array $rules
     *
     * @return mixed
     */
    public abstract function parse($input, $rules = array());

    /**
     * Get empty result.
     *
     * @return mixed
     */
    public abstract function getEmpty();

    /**
     * Set rules to work with.
     *
     * @param mixed $rules
     */
    public function setRules($rules)
    {
        if (empty($rules)) {
            return;
        }
        foreach ($rules as $key => $rule) {
            if (!is_array($rule)) {
                $rules[$key] = array($rule);
            }
        }
        $this->rules = $rules;
    }

    /**
     * Return rules to work with.
     *
     * @return mixed
     */
    public function getRules()
    {
        return $this->rules;
    }

    /**
     * Format according to parser type.
     *
     * @param mixed $input
     *
     * @return mixed
     */
    public function format($input)
    {
        return $input;
    }
}
