<?php

define('IMAGE_3D_DRIVER_ASCII_GRAY',	0.5);

class Image_3D_Driver_ASCII extends Image_3D_Driver {
	
    protected $_size;
	protected $_filetype;
	protected $_points;
	protected $_heigth;
	protected $_image;

	protected $_charArray = array(
        0   => ' ',
        1   => '`',
        2   => '\'',
        3   => '^',
        4   => '-',
        5   => '`',
        6   => '/',
        7   => '/',
        8   => '-',
        9   => '\\',
        10  => '\'',
        11  => '\\',
        12  => '~',
        13  => '+',
        14  => '+',
        15  => '*',
        16  => '.',
        17  => '|',
        18  => '/',
        19  => '/',
        20  => '|',
        21  => '|',
        22  => '/',
        23  => '/',
        24  => '/',
        25  => ')',
        26  => '/',
        27  => 'Y',
        28  => 'r',
        29  => '}',
        30  => '/',
        31  => 'P',
        32  => '.',
        33  => '\\',
        34  => '|',
        35  => '^',
        36  => '\\',
        37  => '\\',
        38  => '(',
        39  => '(',
        40  => ':',
        41  => '\\',
        42  => '|',
        43  => 'I',
        44  => ';',
        45  => '\\',
        46  => '{',
        47  => '9',
        48  => '_',
        49  => '_',
        50  => '_',
        51  => 'C',
        52  => '<',
        53  => 'L',
        54  => 'l',
        55  => 'C',
        56  => '>',
        57  => 'J',
        58  => 'J',
        59  => 'J',
        60  => 'o',
        61  => 'b',
        62  => 'd',
        63  => '#',
	);

	public function __construct() {
		parent::__construct();
		
		$this->reset();
	}

	public function reset() {
		$this->_filetype = 'txt';
		$this->_points = array();
		$this->_heigth = array();
		
		$this->_image = array();
	}
	
	public function createImage($x, $y) {
	    $this->_size = array($x, $y);
	}
	
	protected function _getColor(Image_3D_Color $color, $alpha = 1.) {
	    $values = $color->getValues();
		return array($values[0], $values[1], $values[2], (1 - $values[3]) * $alpha);
	}
	
	protected function _mixColor($old, $new) {
	    return array(
            $old[0] * (1 - $new[3]) * $old[3] + $new[0] * $new[3],
            $old[1] * (1 - $new[3]) * $old[3] + $new[1] * $new[3],
            $old[2] * (1 - $new[3]) * $old[3] + $new[2] * $new[3],
            $old[3] * $old[3] + $new[3]
	    );
	    
	}
	
	public function setBackground(Image_3D_Color $color) {
		$bg = $this->_getColor($color);

		for ($x = 0; $x < $this->_size[0]; $x++) {
    	    for ($y = 0; $y < $this->_size[1]; $y++) {
    	        $this->_image[$x][$y] = $bg;
    	    }
	    }
	}
	
	protected function _drawLine(Image_3D_Point $p1, Image_3D_Point $p2) {
	    list($x1, $y1) = $p1->getScreenCoordinates();
	    list($x2, $y2) = $p2->getScreenCoordinates();
	    $z1 = $p1->getZ(); $z2 = $p2->getZ();
	    
	    $steps = ceil(max(abs($x1 - $x2), abs($y1 - $y2)));
	    
	    $xdiff = ($x2 - $x1) / $steps;
	    $ydiff = ($y2 - $y1) / $steps;
	    $zdiff = ($z2 - $z1) / $steps;
	    
	    $points = array('height' => array(), 'coverage' => array());
	    for ($i = 0; $i < $steps; $i++) {
    		$x = $x1 + $i * $xdiff;
    		$xFloor = floor($x);
    		$xCeil = ceil($x);
    		$xOffset = $x - $xFloor;
    		
    		$y = $y1 + $i * $ydiff;
    		$yFloor = floor($y);
    		$yCeil = ceil($y);
    		$yOffset = $y - $yFloor;
	        
    		if (!isset($points['coverage'][(int) $xFloor][(int) $yCeil])) {
    		    $points['height'][(int) $xFloor][(int) $yCeil] = $z1 + $i * $zdiff;
    		    $points['coverage'][(int) $xFloor][(int) $yCeil] = (1 - $xOffset) * $yOffset;
    		} else {
    		    $points['coverage'][(int) $xFloor][(int) $yCeil] += (1 - $xOffset) * $yOffset;
    		}
	        
    		if (!isset($points['coverage'][(int) $xFloor][(int) $yFloor])) {
    		    $points['height'][(int) $xFloor][(int) $yFloor] = $z1 + $i * $zdiff;
    		    $points['coverage'][(int) $xFloor][(int) $yFloor] = (1 - $xOffset) * (1 - $yOffset);
    		} else {
    		    $points['coverage'][(int) $xFloor][(int) $yFloor] += (1 - $xOffset) * (1 - $yOffset);
    		}
	        
    		if (!isset($points['coverage'][(int) $xCeil][(int) $yCeil])) {
    		    $points['height'][(int) $xCeil][(int) $yCeil] = $z1 + $i * $zdiff;
    		    $points['coverage'][(int) $xCeil][(int) $yCeil] = $xOffset * $yOffset;
    		} else {
    		    $points['coverage'][(int) $xCeil][(int) $yCeil] += $xOffset * $yOffset;
    		}
	        
    		if (!isset($points['coverage'][(int) $xCeil][(int) $yFloor])) {
    		    $points['height'][(int) $xCeil][(int) $yFloor] = $z1 + $i * $zdiff;
    		    $points['coverage'][(int) $xCeil][(int) $yFloor] = $xOffset * (1 - $yOffset);
    		} else {
    		    $points['coverage'][(int) $xCeil][(int) $yFloor] += $xOffset * (1 - $yOffset);
    		}
	    }
	    return $points;
	}
	
	protected function _getPolygonOutlines($pointArray) {
	    $map = array('height' => array(), 'coverage' => array());
	    
	    $last = end($pointArray);
	    foreach ($pointArray as $point) {
	        $line = $this->_drawLine($last, $point);
	        $last = $point;
	        // Merge line to map
	        foreach ($line['height'] as $x => $row) {
	            foreach ($row as $y => $height) {
	                $map['height'][(int) $x][(int) $y] = $height;
	                $map['coverage'][(int) $x][(int) $y] = $line['coverage'][(int) $x][(int) $y];
	            }
	        }
	    }
	    
	    return $map;
	}
	
	public function drawPolygon(Image_3D_Polygon $polygon) {
		$points = $this->_getPolygonOutlines($polygon->getPoints());

		foreach ($points['coverage'] as $x => $row) {
	        if (count($row) < 2) continue;
	        
	        $start = min(array_keys($row));
	        $end = max(array_keys($row));
	        
	        $zStart = $points['height'][$x][$start];
	        $zEnd = $points['height'][$x][$end];
	        $zStep = ($zEnd - $zStart) / ($end - $start);

	        // Starting point
            $this->_heigth[$x][$start][(int) ($zStart * 100)] = $this->_getColor($polygon->getColor(), $points['coverage'][$x][$start]);
            
            // the way between
            for ($y = $start + 1; $y < $end; $y++) {
                $this->_heigth[$x][$y][(int) (($zStart + $zStep * ($y - $start)) * 100)] = $this->_getColor($polygon->getColor());
	        }

	        // Ending point
            $this->_points[$x][$end][(int) ($zEnd * 100)] = $this->_getColor($polygon->getColor(), $points['coverage'][$x][$end]);
		}
	}
	
	public function drawGradientPolygon(Image_3D_Polygon $polygon) {
		$this->drawPolygon($polygon);
	}
	
	public function setFiletye($type) {
		$type = strtolower($type);
		if (in_array($type, array('png', 'jpeg'))) {
			$this->_filetype = $type;
			return true;
		} else {
			return false;
		}
	}
	
	public function _getAnsiColorCode($color, $last = '') {
	    $code = "\033[0;" . (30 + bindec((int) round($color[2]) . (int) round($color[1]) . (int) round($color[0]))) . 'm';
		if ($last !== $code) return $code;
		return '';
	}
	
	public function save($file) {
	    
		$asciiWidth = (int) ceil($this->_size[0] / 2);
		$asciiHeight = (int) ceil($this->_size[1] / 6); 
		
		$output = "\033[2J";
		$lastColor = '';
		
		for ($y = 0; $y < $asciiHeight; $y++) {
			for ($x = 0; $x < $asciiWidth; $x++) {
				// Get pixelarray
				$char = 0;
				$charColor = array(0, 0, 0);
				for ($xi = 0; $xi < 2; $xi++) {
					for ($yi = 0; $yi < 3; $yi++) {
						$xPos = $x * 2 + $xi;
						$yPos = $y * 6 + $yi;
						$color = $this->_image[$xPos][$yPos];

						if (isset($this->_heigth[$xPos][$yPos])) {
						    $points = $this->_heigth[$xPos][$yPos];
						    krsort($points);
					        foreach ($points as $newColor) $color = $this->_mixColor($color, $newColor);
					    }
						if ((($color[0] + $color[1] + $color[2]) / 3) < IMAGE_3D_DRIVER_ASCII_GRAY) $char |= pow(2, $yi + ($xi * 3));
						$charColor[0] += $color[0];
						$charColor[1] += $color[1];
						$charColor[2] += $color[2];
					}
				}
				$lastColor = $this->_getAnsiColorCode(array($charColor[0] / 6, $charColor[1] / 6, $charColor[2] / 6), $lastColor);
				$output .= $lastColor . $this->_charArray[$char];
			}
			$lastColor = '';
			$output .= "\n";
		}
		$fp = fopen($file, 'w');
		fwrite($fp, $output);
		fclose($fp);
	}

	public function getSupportedShading() {
		return array(	Image_3D_Renderer::SHADE_NO, 
						Image_3D_Renderer::SHADE_FLAT, 
//						Image_3D_Renderer::SHADE_GAUROUD,
						);
	}
}

?>
