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

/**
 * Image_3D_Renderer
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
abstract class Image_3D_Renderer {
	
	protected $_polygones;
	protected $_points;
	protected $_lights;
	
	protected $_driver;
	
	protected $_size;
	protected $_background;
	
	protected $_shading;
	
	const SHADE_NO			= 0;
	const SHADE_FLAT		= 1;
	const SHADE_GAUROUD		= 2;
	const SHADE_PHONG		= 3;
	
	public function __construct() {
		$this->_objects = array();
		$this->_polygones = array();
		$this->_points = array();
		$this->_lights = array();
		$this->_size = array(0, 0);
		$this->_background = null;
		
		$this->_driver = null;
		
		$this->_shading = self::SHADE_PHONG;
	}
	
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
	
	// Calculate perspective
	abstract protected function _calculateScreenCoordiantes(Image_3D_Point $point);
	
	// Implement polygon-based Z-buffer
	abstract protected function _sortPolygones();
	
	public function addObjects($objects) {
		if (is_array($objects)) {
			foreach ($objects as $object) {
				if (is_a($object, 'Image_3D_Object')) {
					$this->_getPolygones($object);
				}
			}
		} elseif (is_a($objects, 'Image_3D_Object')) {
			$this->_getPolygones($objects);
		}
	}
	
	public function addLights($lights) {
		$this->_lights = array_merge($this->_lights, $lights);
	}
	
	public function setSize($x, $y) {
		$this->_size = array($x, $y);
	}
	
	public function setBackgroundColor(Image_3D_Color $color) {
		$this->_background = $color;
	}
	
	public function setShading($shading) {
		$this->_shading = min($this->_shading, (int) $shading);
	}
	
	public function setDriver(Image_3D_Driver $driver) {
		$this->_driver = $driver;
		
		$this->setShading(max($driver->getSupportedShading()));
	}
	
	public function getPolygonCount() {
		return count($this->_polygones);
	}
	
	public function getPointCount() {
		return count($this->_points);
	}
	
	public function getLightCount() {
		return count($this->_lights);
	}
	
	protected function _calculatePolygonColors() {
		foreach ($this->_polygones as $polygon) {
			$polygon->calculateColor($this->_lights);
		}
	}
	
	protected function _calculatePointColors() {
		foreach ($this->_polygones as $polygon) {
			$normale = $polygon->getNormale();
			$color = $polygon->getColor();
			
			$points = $polygon->getPoints();
			foreach ($points as $point) {
				$point->addVector($normale);
				$point->addColor($color);
			}
		}
		
		foreach ($this->_points as $point) $point->calculateColor($this->_lights);
	}
	
	protected function _shade() {
		switch ($this->_shading) {
			case self::SHADE_NO:
				foreach ($this->_polygones as $polygon) $this->_driver->drawPolygon($polygon);
			break;
			
			case self::SHADE_FLAT:
				$this->_calculatePolygonColors();
				foreach ($this->_polygones as $polygon) $this->_driver->drawPolygon($polygon);
			break;
			
			case self::SHADE_GAUROUD:
				$this->_calculatePointColors();
				foreach ($this->_polygones as $polygon) $this->_driver->drawGradientPolygon($polygon);
			break;
			
			default:
				throw new Exception('Shading type not supported.');
			break;
		}
	}
	
	public function render($file) {
		if (empty($this->_driver)) return false;
		
		// Calculate screen coordinates
		foreach ($this->_points as $point) $this->_calculateScreenCoordiantes($point);
		$this->_sortPolygones();
		
		// Draw background
		$this->_driver->createImage($this->_size[0], $this->_size[1]);
		$this->_driver->setBackground($this->_background);
		
		// Calculate Colors
		$this->_shade();
		
		// Save
		$this->_driver->save($file);
	}
}

?>