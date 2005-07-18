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
 * Image_3D_Color
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
class Image_3D_Color {
	
	protected $_rgbaValue;
	
//	protected $_image;
	protected $_lastColorID;
	
	protected $_lights;
	protected $_light;

	public function __construct() {
//		$this->_image = null;
		$this->_lastColorID = 0;
		$this->_rgbaValue = array();
		
		$this->_lights = array();
		$this->_light = array(0, 0, 0);
		
		$arglist = func_get_args();
		$argcount = func_num_args();
		
		for ($i = 0; $i < 4; $i++) {
			if ($i >= $argcount) {
				$this->_rgbaValue[$i] = 0;
			} elseif (is_int($arglist[$i])) {
				$this->_rgbaValue[$i] = (float) min(1, max(0, (float) $arglist[$i] / 255));
			} elseif (is_float($arglist[$i])) {
				$this->_rgbaValue[$i] = (float) min(1, max(0, $arglist[$i]));
			} else {
				$this->_rgbaValue[$i] = 0;
			}
		}
	}
	
	public function getValues() {
		return $this->_rgbaValue;
	}
	
	public function addLight(Image_3D_Color $color, $intensity = .5) {
		$this->_lights[] = array($intensity, $color);
	}
	
	protected function _calcLights() {
		foreach ($this->_lights as $light) {
			list($intensity, $color) = $light;
			$colorArray = $color->getValues();
			$this->_light[0] += $colorArray[0] * $intensity * (1 - $colorArray[3]);
			$this->_light[1] += $colorArray[1] * $intensity * (1 - $colorArray[3]);
			$this->_light[2] += $colorArray[2] * $intensity * (1 - $colorArray[3]);
		}
	}
	
	protected function _mixColor() {
		$this->_rgbaValue[0] = min(1, $this->_rgbaValue[0] * $this->_light[0]);
		$this->_rgbaValue[1] = min(1, $this->_rgbaValue[1] * $this->_light[1]);
		$this->_rgbaValue[2] = min(1, $this->_rgbaValue[2] * $this->_light[2]);
	}
	
	public function calculateColor() {
		$this->_calcLights();
		$this->_mixColor();
	}

	public function tostring() {
		return sprintf("Color: r %.2f g %.2f b %.2f a %.2f\n", $this->_rgbaValue[0], $this->_rgbaValue[1], $this->_rgbaValue[2], $this->_rgbaValue[3]);
	}
}

?>