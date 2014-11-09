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

use Zend\Http;

class Html extends AbstractSource
{
    public function loadSource($source = null)
    {
        if (!empty($source) && is_string($source)) {
            $this->setPath($source);
        }
        if ($this->path !== null) {
            $dt = new \DateTime();
            $client = new Http\Client($this->path);
            $response = $client->send();
            if ($response->getStatusCode() == Http\Response::STATUS_CODE_200) {
                $lm = $response->getHeaders()->get('Last-Modified');
                if ($lm) {
                    $lm = strtotime($lm);
                } else {
                    $lm = $dt->getTimestamp();
                }
                $this->source = $response->getBody();
                $this->setInfo('loaded', true);
                $this->setInfo('modified', $lm);
            } else {
                $this->source = '';
            }
            $this->setInfo('timestamp', $dt->getTimestamp());
            $this->setInfo('source', $this->path);
            return $this->source;
        }
        return null;
    }
}