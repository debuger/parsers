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
 * Class FilterFactory
 *
 * @package Rym\Filters
 */
abstract class FilterFactory {

    /**
     * Class map of filters.
     *
     * @var array
     */
    protected static $classes = array(
        'standard' => 'Rym\Filters\Standard',
    );

    /**
     * Factory.
     *
     * @param FilterStack $stack
     * @param mixed $config
     *
     * @return AbstractFilter
     * @throws Exception
     */
    public static function factory(FilterStack $stack, $config)
    {
        $obj = null;
        if (!is_array($config)) {
            $config = array(
                'class' => 'standard',
                'config' => $config
            );
        }
        $class = strtolower($config['class']);
        unset($config['class']);
        if (!empty($class) && isset(self::$classes[$class])) {
            $className = self::$classes[$class];
            $obj = new $className($stack, $config);
            if (!$obj instanceof AbstractFilter) {
                throw new Exception('Wrong object');
            }
        } else {
            throw new Exception('Wrong config');
        }
        return $obj;
    }
}