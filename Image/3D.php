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
 * @package    3D
 * @author     Kore Nordmann <3d@kore-nordmann.de>
 * @copyright  1997-2005 Kore Nordmann
 * @license    http://www.gnu.org/licenses/lgpl.txt lgpl 2.1
 * @version    CVS: $Id$
 * @link       http://pear.php.net/package/PackageName
 * @since      File available since Release 0.1.0
 */

require_once('Image/3D/Paintable.php');
require_once('Image/3D/Enlightenable.php');

require_once('Image/3D/Color.php');
require_once('Image/3D/Coordinate.php');
require_once('Image/3D/Point.php');
require_once('Image/3D/Vector.php');
require_once('Image/3D/Renderer.php');
require_once('Image/3D/Driver.php');

require_once('Image/3D/Paintable/Object.php');
require_once('Image/3D/Paintable/Light.php');
require_once('Image/3D/Paintable/Polygon.php');

/**
 * Image_3D
 *
 *
 *
 * @category   Image
 * @package    3D
 * @author     Kore Nordmann <3d@kore-nordmann.de>
 * @copyright  1997-2005 Kore Nordmann
 * @license    http://www.gnu.org/licenses/lgpl.txt lgpl 2.1
 * @version    Release: @package_version@
 * @link       http://pear.php.net/package/PackageName
 * @since      Class available since Release 0.1.0
 */
class Image_3D {
	
	protected $_color;
	protected $_objects;
	protected $_lights;
	protected $_renderer;
	protected $_driver;
	
	protected $_option;
	protected $_optionSet;
	
	const IMAGE_3D_OPTION_FILLED			= 1;
	const IMAGE_3D_OPTION_BF_CULLING		= 2;
	
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
	
	public function createObject($type, $parameter = array()) {
		$name = ucfirst($type);
		$class = 'Image_3D_Object_' . $name;
		$absolute_path = dirname(__FILE__) . '/3D/Paintable/Object/' . $name . '.php';
		$user_path = dirname(__FILE__) . '/3D/User/Object/' . $name . '.php';
		
		if (is_file($absolute_path) && is_readable($absolute_path)) {
			include_once('Image/3D/Paintable/Object/' . $name . '.php');
		} elseif (is_file($user_path) && is_readable($user_path)) {
			include_once('Image/3D/User/Object/' . $name . '.php');
		} else {
			throw new Exception("Class for object $name not found.");
		}
		
		return $this->_objects[] = new $class($parameter);
	}
	
	public function createLight($x, $y, $z) {
		return $this->_lights[] = new Image_3D_Light($x, $y, $z);
	}
	
	public function createMatrix($type, $parameter = array()) {
		$name = ucfirst($type);
		$class = 'Image_3D_Matrix_' . $name;
		$absolute_path = dirname(__FILE__) . '/3D/Matrix/' . $name . '.php';
		
		if (is_file($absolute_path) && is_readable($absolute_path)) {
			include_once('Image/3D/Matrix/' . $name . '.php');
		} else {
			throw new Exception("Class for matrix $name not found.");
		}
		
		return new $class($parameter);
	}
	
	public function setColor(Image_3D_Color $color, $recusive = true) {
		$this->_color = $color;
	}

	public function createRenderer($type) {
		$name = ucfirst($type);
		$class = 'Image_3D_Renderer_' . $name;
		$absolute_path = dirname(__FILE__) . '/3D/Renderer/' . $name . '.php';
		$user_path = dirname(__FILE__) . '/3D/User/Renderer/' . $name . '.php';
		
		if (is_file($absolute_path) && is_readable($absolute_path)) {
			include_once('Image/3D/Renderer/' . $name . '.php');
		} elseif (is_file($user_path) && is_readable($user_path)) {
			include_once('Image/3D/User/Renderer/' . $name . '.php');
		} else {
			throw new Exception("Class for renderer $name not found.");
		}
		
		return $this->_renderer = new $class();
	}
	
	public function createDriver($type) {
		$name = ucfirst($type);
		$class = 'Image_3D_Driver_' . $name;
		$absolute_path = dirname(__FILE__) . '/3D/Driver/' . $name . '.php';
		$user_path = dirname(__FILE__) . '/3D/User/Driver/' . $name . '.php';
		
		if (is_file($absolute_path) && is_readable($absolute_path)) {
			include_once('Image/3D/Driver/' . $name . '.php');
		} elseif (is_file($user_path) && is_readable($user_path)) {
			include_once('Image/3D/User/Driver/' . $name . '.php');
		} else {
			throw new Exception("Class for driver $name not found.");
		}
		
		return $this->_driver = new $class();
	}
	
	public function setOption($option, $value) {
		$this->_option[$option] = $value;
		$this->_optionSet[$option] = true;
		foreach ($this->_objects as $object) $object->setOption($option, $value);
	}

	public function transform(Image_3D_Matrix $matrix, $id = null) {
		
		if ($id === null) $id = substr(md5(microtime()), 0, 8);
		foreach ($this->_objects as $object) $object->transform($matrix, $id);
	}
	
	public function render($x, $y, $file) {
		if (!is_writable(dirname($file)) && (!is_file($file) || !is_writable($file))) throw new Exception('Cannot write outputfile.');
		
		$x = min(1280, max(0, (int) $x));
		$y = min(1280, max(0, (int) $y));

		$this->_renderer->setSize($x, $y);
		$this->_renderer->setBackgroundColor($this->_color);
		$this->_renderer->addObjects($this->_objects);
		$this->_renderer->addLights($this->_lights);
		$this->_renderer->setDriver($this->_driver);
		
		return $this->_renderer->render($file);
	}
	
	public function stats() {
		printf('
Image 3D

objects:    %d
lights:     %d
polygones:  %d
points:     %d
',
			count($this->_objects),
			$this->_renderer->getLightCount(),
			$this->_renderer->getPolygonCount(),
			$this->_renderer->getPointCount()
		);
	}
}

?>