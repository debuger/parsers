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


abstract class AbstractFilter {

    const TYPE_DOM = 1;

    const TYPE_PLAIN = 2;

    /**
     * Type of filter.
     *
     * @var string
     */
    protected $type;

    /**
     * Stack of filters.
     *
     * @var FilterStack
     */
    protected $stack;

    /**
     * List of configurable options.
     *
     * @var array
     */
    protected $options = array();

    public function __construct(FilterStack $stack, $config)
    {
        $this->setStack($stack);
        $this->setConfig($config);
    }

    /**
     * Getter for type.
     *
     * @return string
     */
    public function getFilterType()
    {
        return $this->type;
    }

    /**
     * Run filter.
     *
     * @param mixed $input
     * @param array $config
     */
    public function run($input, $config = array())
    {
        if (!empty($config)) {
            $this->setConfig($config);
        }
        $this->filter($input);
    }

    /**
     * Run filter based on it's type.
     *
     * @param mixed $input
     */
    protected function filter($input)
    {
        switch($this->type) {
            case static::TYPE_DOM:
                $this->runDom($input);
                break;
            case static::TYPE_PLAIN:
                $this->runPlain($input);
                break;
        }
    }

    /**
     * Setter for config.
     *
     * @param mixed $config
     */
    public function setConfig($config)
    {
        foreach ($config as $key => $value) {
            if (in_array($key, $this->options)) {
                $this->$key = $value;
            }
        }
    }

    /**
     * Getter for config.
     *
     * @return mixed array
     */
    public function getConfig()
    {
        $config = array();
        foreach ($this->options as $key) {
            if (isset($this->$key)) {
                $config[$key] = $this->$key;
            }
        }
        return $config;
    }

    /**
     * Set FilterStack.
     * @param FilterStack $stack
     */
    protected function setStack(FilterStack $stack)
    {
        $this->stack = $stack;
    }

    public function replaceNode($path, $node) {
        $this->stack->addReplace($path, $node);
    }

    /**
     * Add node to delete
     * @param $path string
     */
    public function deleteNode($path) {
        $this->stack->addDelete($path);
    }

    /**
     * Run filter for static::TYPE_DOM.
     * @param \DOMNode $input
     * @return null
     */
    protected function runDom(\DOMNode $input) {}

    /**
     * Run filter for static::TYPE_TEXT.
     * @param string $input
     * @return null
     */
    protected function runPlain($input) {}
}
