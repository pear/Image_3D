<?php

$images = 'php://output';
$times_file = 'times.txt';
$iterations = 50;

$GLOBALS['times'] = array();
$GLOBALS['times_fp'] = fopen($times_file, 'a');

function getTime($string = null) {
    if (is_string($string)) {
        $GLOBALS['times'][$string] = microtime(true);
        fwrite($GLOBALS['times_fp'], sprintf("%sStart '%s'\n", str_repeat("\t", count($GLOBALS['times']) - 1), $string));
    } else {
        $keys = array_keys($GLOBALS['times']);
        $last = end($keys);
        fwrite($GLOBALS['times_fp'], sprintf("%s-> %2.4fs\n", str_repeat("\t", count($GLOBALS['times']) - 1), microtime(true) - $GLOBALS['times'][$last]));
        unset($GLOBALS['times'][$last]);
    }
}

getTime('Start');

getTime('Initialize classes');
set_time_limit(0);
ini_set('memory_limit', '24M');
set_include_path(realpath(dirname(__FILE__) . '/../..'));
require_once('Image/3D.php');
getTime();

getTime('Create world');
$world = new Image_3D();
$world->setColor(new Image_3D_Color(0, 0, 0));
getTime();

getTime('Create lights');
$light1 = $world->createLight(-500, -500, -500);
$light1->setColor(new Image_3D_Color(255, 255, 255));

$light2 = $world->createLight(0, 500, -550);
$light2->setColor(new Image_3D_Color(0, 255, 0));
getTime();

getTime('Create objects');
$p1 = $world->createObject('cube', array(80, 80, 80));
$p1->setColor(new Image_3D_Color(200, 200, 200));
$p1->transform($world->createMatrix('Rotation', array(45, 45, 0)));
getTime();

getTime('Set options');
$world->setOption(Image_3D::IMAGE_3D_OPTION_BF_CULLING, false);
$world->setOption(Image_3D::IMAGE_3D_OPTION_FILLED, true);
getTime();

getTime('Create renderer and driver');
$rotation = $world->createMatrix('Rotation', array(2, 5, 0));
$renderer = $world->createRenderer('perspectively');
$driver = $world->createDriver('ASCII');
getTime();

getTime('Create initial picture');
$world->render(2 * 80, 6 * 30, $images);
getTime();

getTime('Start animation');
$start = microtime(true);
$i = 0;
while ($i++ < $iterations) {
    getTime('Render image ' . $i);
	$p1->transform($rotation);
	$driver->reset();
	$renderer->render($images);
    getTime();
}
getTime();

$time = microtime(true) - $start;
printf("%2.2f fps\n", $iterations / $time);
getTime();

?>
