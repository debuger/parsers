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
 * Class ParserFactory
 *
 * Factory to load parser.
 *
 * @package Rym\Parser
 */
abstract class ParserFactory {
    protected static $classes = array(
        'dom' => 'Rym\Parser\Dom',
        'xpath' => 'Rym\Parser\XPath'
    );

    /**
     * Factory method.
     *
     * @param mixed $config
     *
     * @return AbstractParser
     * @throws Exception
     */
    public static function factory($config)
    {
        $obj = null;
        $class = strtolower($config['class']);
        $rules = $config['rules'];
        unset($config['class']);
        if (!empty($class) && isset(static::$classes[$class])) {
            $className = static::$classes[$class];
            $obj = new $className($rules);
            if (!$obj instanceof AbstractParser) {
                throw new Exception('Wrong object');
            }
        } else {
            throw new Exception('Wrong config');
        }
        return $obj;
    }
}
