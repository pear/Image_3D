<?php

class Image_3D_Driver_SVGRotate extends Image_3D_Driver {
	
	protected $_x;
	protected $_y;
	
	protected $_id;
	
	protected $_gradients;
	protected $_polygones;
	
	public function __construct() {
		$this->_image = '';
		$this->_id = 1;
		$this->_gradients = array();
		$this->_polygones = array();
	}
	
	public function createImage($x, $y) {
		$this->_x = (int) $x;
		$this->_y = (int) $y;

		$this->_image = <<<EOF
<?xml version="1.0" ?>
EOF;
		$this->_image .= "\n\n";
	}
	
	public function setBackground(Image_3D_Color $color) {
	}
	
	protected function _getStyle(Image_3D_Color $color) {
	}
	
	protected function _getStop(Image_3D_Color $color, $offset = 0, $alpha = null) {
	}
	
	protected function _addGradient($string) {
	}
	
	protected function _addPolygon($string) {
		$id = 'polygon' . $this->_id++;
		$this->_polygones[] = str_replace('[id]', $id, $string);
		return $id;
	}
	
	public function drawPolygon(Image_3D_Polygon $polygon) {
		
		$list = '';
		$array = array();
		$points = $polygon->getPoints();
		foreach ($points as $point) {
			$array[0][] = $point->getX();
			$array[1][] = $point->getY();
			$array[2][] = $point->getZ();
		}
		
		$this->_addPolygon(sprintf('<polygon id="[id]"
                        x1="%.2f" x2="%.2f" x3="%.2f"
                        y1="%.2f" y2="%.2f" y3="%.2f"
                        z1="%.2f" z2="%.2f" z3="%.2f" />' . "\n", 
			$array[0][0], $array[0][1], $array[0][2], 
			$array[1][0], $array[1][1], $array[1][2], 
			$array[2][0], $array[2][1], $array[2][2] ));
	}
	
	public function drawGradientPolygon(Image_3D_Polygon $polygon) {
		$this->drawPolygon($polygon);
	}
	
	public function save($file) {
		$this->_image .= implode('', $this->_polygones);
		file_put_contents($file, $this->_image);
	}

	public function getSupportedShading() {
		return array(Image_3D_Renderer::SHADE_NO, Image_3D_Renderer::SHADE_FLAT);
	}
}

?>
