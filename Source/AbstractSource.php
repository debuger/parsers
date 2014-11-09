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

namespace Rym\Source;

use Rym;

abstract class AbstractSource
{
    /**
     * @var String path to source.
     */
    protected $path;

    /**
     * @var
     */
    protected $source;

    /**
     * @var mixed Options.
     */
    protected $options;

    /**
     * @var array Info of a source.
     */
    protected $info;


    public function setPath($path)
    {
        $this->path = $path;
    }

    public function getOptions()
    {
        return $this->options;
    }

    public function toArray()
    {
        $result = array();
        foreach ($this->__sleep() as $key) {
            if (isset($this->$key)) {
                $result[$key] = $this->$key;
            }
        }
        return $result;
    }

    public function __sleep()
    {
        return array('path', 'options');
    }

    public function fromArray($data)
    {
        $this->path = $data['path'];
        $this->options = $data['options'];
    }

    public function setOptions($data)
    {
        $this->options = $data;
    }

    public abstract function loadSource($source = null);

    public function modified()
    {
        return $this->getInfo('modified');
    }

    public function setInfo($key, $value)
    {
        $result = false;
        if (array_key_exists($key, $this->info)) {
            $this->info[$key] = $value;
            $result = true;
        }
        return $result;
    }

    public function getInfo($key) {
        if (array_key_exists($key, $this->info)) {
            return $this->info[$key];
        }
        return null;
    }

    public function resetInfo()
    {
        $this->info = array(
            'source' => '',
            'modified' => '',
            'loaded' => false,
            'timestamp' => ''
        );
    }

    public function __construct($path = '', $options = array())
    {
        if (!empty($path)) {
            $this->setPath($path);
        }
        if (!empty($options)) {
            $this->setOptions($options);
        }
        $this->resetInfo();
    }
}