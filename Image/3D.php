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

require_once 'Image/3D/Paintable.php';
require_once 'Image/3D/Enlightenable.php';

require_once 'Image/3D/Color.php';
require_once 'Image/3D/Coordinate.php';
require_once 'Image/3D/Point.php';
require_once 'Image/3D/Vector.php';
require_once 'Image/3D/Renderer.php';
require_once 'Image/3D/Driver.php';

require_once 'Image/3D/Paintable/Object.php';
require_once 'Image/3D/Paintable/Light.php';
require_once 'Image/3D/Paintable/Polygon.php';

// {{{ Image_3D

/**
 * Image_3D
 *
 * Class for creation of 3d images only with native PHP.
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
class Image_3D {
	
    // {{{ properties
    
    /**
     * Backgroundcolor
     *
     * @var Image_3D_Color
     */
	protected $_color;
    
    /**
     * List of known objects
     *
     * @var array
     */
	protected $_objects;
    
    /**
     * List of lights
     *
     * @var array
     */
	protected $_lights;
    
    /**
     * Active renderer
     *
     * @var Image_3D_Renderer
     */
	protected $_renderer;
    
    /**
     * Active outputdriver
     *
     * @var Image_3D_Driver
     */
	protected $_driver;
	
    
    /**
     * Options for rendering
     */
	protected $_option;
    
    /**
     * Options set by the user
     *
     * @var array
     */
	protected $_optionSet;
	
	// }}}
	// {{{ constants
	
    /**
     * Option for filled polygones (depreceated)
     */
	const IMAGE_3D_OPTION_FILLED			= 1;

    /**
     * Option for backface culling (depreceated)
     */
	const IMAGE_3D_OPTION_BF_CULLING		= 2;
	
	// }}}
	// {{{ __construct()

	/**
     * Constructor for Image_3D
     * 
     * Initialises the environment
     *
     * @return  Image_3D                World instance
     */
	public function __construct() {
		$this->_objects = array();
		$this->_lights = array();
		$this->_renderer = null;
		$this->_driver = null;
		$this->_color = null;
		
		$this->_option[self::IMAGE_3D_OPTION_FILLED]			= true;
		$this->_option[self::IMAGE_3D_OPTION_BF_CULLING]		= true;
		$this->_optionSet = array();
	}
	
	// }}}
	// {{{ createObject()

    /**
     * Factory method for Objects
     * 
     * Creates and returns a printable object. 
     * Standard objects with parameters:
     * 	- cube		array(float $x, float $y, float $z)
     * 	- sphere	array(float $r, int $detail)
     * 	- 3ds		string $filename
     * 	- map		[array(array(Image_3D_Point))]
     * 	- text		string $string
     *
     * @param   string      $type       Objectname
     * @param   array       $parameter  Parameters
     * @return  Image_3D_
