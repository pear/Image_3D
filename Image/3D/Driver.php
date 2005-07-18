<?php

abstract class Image_3D_Driver {
	
	protected $_image;
	
	public function __construct() {
		$this->_image = null;
	}
	
	abstract public function createImage($x, $y);
	abstract public function setBackground(Image_3D_Color $color);
	abstract public function drawPolygon(Image_3D_Polygon $polygon);
	abstract public function drawGradientPolygon(Image_3D_Polygon $polygon);
	abstract public function save($file);
	
	public function getSupportedShading() {
		return array(Image_3D_Renderer::SHADE_NO);
	}
}

?>