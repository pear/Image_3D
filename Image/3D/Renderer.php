<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * 3d Library
 *
 * PHP versions 5
 *
 * LICENSE: 
 * This library is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation; either
 * version 2.1 of the License, or (at your option) any later version.
 * 
 * This library is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
 * Lesser General Public License for more details.
 * 
 * You should have received a copy of the GNU Lesser General Public
 * License along with this library; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301  USA
 *
 * @category   Image
 * @package    Image_3D
 * @author     Kore Nordmann <3d@kore-nordmann.de>
 * @copyright  1997-2005 Kore Nordmann
 * @license    http://www.gnu.org/licenses/lgpl.txt lgpl 2.1
 * @version    CVS: $Id$
 * @link       http://pear.php.net/package/Image_3D
 * @since      File available since Release 0.1.0
 */

// {{{ Image_3D_Renderer

/**
 * Image_3D_Renderer
 *
 * 
 *
 * @category   Image
 * @package    Image_3D
 * @author     Kore Nordmann <3d@kore-nordmann.de>
 * @copyright  1997-2005 Kore Nordmann
 * @license    http://www.gnu.org/licenses/lgpl.txt lgpl 2.1
 * @version    Release: @package_version@
 * @link       http://pear.php.net/package/Image_3D
 * @since      Class available since Release 0.1.0
 */
abstract class Image_3D_Renderer {
	
    // {{{ properties

    /**
     * Worlds polygones
     *
     * @var array
     */
	protected $_polygones;

    /**
     * Worlds points
     *
     * @var array
     */
	protected $_points;

    /**
     * Worlds lights
     *
     * @var array
     */
	protected $_lights;
	
    /**
     * Driver we use
     *
     * @var array
     */
	protected $_driver;
	
    /**
     * Size of the Image
     *
     * @var array
     */
	protected $_size;

    /**
     * Backgroundcolol
     *
     * @var Image_3D_Color
     */
	protected $_background;
	
    /**
     * Type of Shading used
     *
     * @var integer
     */
	protected $_shading;
	
    // }}}
    // {{{ Constants

	/*
     * No Shading
     */
    const SHADE_NO			= 0;
	/*
     * Flat Shading
     */
	const SHADE_FLAT		= 1;
	/*
     * Gauroud Shading
     */
	const SHADE_GAUROUD		= 2;
	/*
     * Phong Shading
     */
	const SHADE_PHONG		= 3;

    // }}}
    // {{{ __construct()

    /**
     * Constructor for Image_3D_Renderer
     *
     * Initialises the environment
     *
     * @return  Image_3D_Renderer           Instance of Renderer
     */
	public function __construct() {
	    $this->reset();
	}
	
    // }}}
    // {{{ reset()

    /**
     * Reset all changeable variables
     *
     * Initialises the environment
     *
     * @return  void
     */
	public function reset() {
		$this->_objects = array();
		$this->_polygones = array();
		$this->_points = array();
		$this->_lights = array();
		$this->_size = array(0, 0);
		$this->_background = null;
		
		$this->_driver = null;
		
		$this->_shading = self::SHADE_PHONG;
	}

    // }}}
    // {{{ _getPolygones()
	
    /**
     * Get and merge polygones
     *
     * Get polygones and points from an object and merge them unique to local
     * polygon- and pointarrays.
     *
     * @param   Image_3D_Object $object Object to merge
     * @return  void
     */
	protected function _getPolygones(Image_3D_Object $object) {
		$newPolygones = $object->getPolygones();
		$this->_polygones = array_merge($this->_polygones, $newPolygones);
		
		// Add points unique to points-Array
		foreach ($newPolygones as $polygon) {
			$points = $polygon->getPoints();
			foreach ($points as $point) {
				if (!$point->isProcessed()) {
					$point->processed();
					array_push($this->_points, $point);
				}
			}
		}
	}
	
    // }}}
    // {{{ _calculateScreenCoordiantes()