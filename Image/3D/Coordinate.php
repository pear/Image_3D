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
 * Image_3D_Coordinate
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
class Image_3D_Coordinate {
	
	protected $_x;
	protected $_y;
	protected $_z;
	
	public function __construct($x, $y, $z) {
		$this->_x = (float) $x;
		$this->_y = (float) $y;
		$this->_z = (float) $z;
	}
	
	public function transform(Image_3D_Matrix $matrix, $id = null) {
		// Point already transformed?
		if (($id !== null) && ($this->_lastTransformation === $id)) return false;
		$this->_lastTransformation = $id;
		
		$point = clone($this);
		
		$this->_x =	$point->getX() * $matrix->getValue(0, 0) +
					$point->getY() * $matrix->getValue(1, 0) +
					$point->getZ() * $matrix->getValue(2, 0) +
					$matrix->getValue(3, 0);
		$this->_y =	$point->getX() * $matrix->getValue(0, 1) +
					$point->getY() * $matrix->getValue(1, 1) +
					$point->getZ() * $matrix->getValue(2, 1) +
					$matrix->getValue(3, 1);
		$this->_z =	$point->getX() * $matrix->getValue(0, 2) +
					$point->getY() * $matrix->getValue(1, 2) +
					$point->getZ() * $matrix->getValue(2, 2) +
					$matrix->getValue(3, 2);
		$this->_screenCoordinates = null;
	}
	
	public function processed() {
		$this->_processed = true;
	}
	
	public function isProcessed() {
		return $this->_processed;
	}
	
	public function getX() {
		return $this->_x;
	}
	
	public function getY() {
		return $this->_y;
	}
	
	public function getZ() {
		return $this->_z;
	}

	public function setScreenCoordinates($x, $y) {
		$this->_screenCoordinates = array((float) $x, (float) $y);
	}
	
	public function getScreenCoordinates() {
		return $this->_screenCoordinates;
	}
	
	public function __toString() {
		return sprintf('Coordinate: %2.f %2.f %2.f', $this->_x, $this->_y, $this->_z);
	}
}

?>