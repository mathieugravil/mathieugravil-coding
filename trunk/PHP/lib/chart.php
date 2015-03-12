<?php
/************************************************************************
* AT Pie Chart developed by the ATokar.net Developer Team               *
* This copyright must remain intact.                                    *
* Project site: http://www.atokar.net/                                  *
* For news, updates and support visit: http://www.atokar.net/           *
* Version: 1.2                                                          *
*                                                                       *
* This program is free software: you can redistribute it and/or modify  *
* it under the terms of the GNU General Public License as published by  *
* the Free Software Foundation, either version 3 of the License, or     *
* (at your option) any later version.                                   *
*                                                                       *
* This program is distributed in the hope that it will be useful,       *
* but WITHOUT ANY WARRANTY; without even the implied warranty of        *
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the         *
* GNU General Public License for more details.                          *
*                                                                       *
* You should have received a copy of the GNU General Public License     *
* along with this program.  If not, see <http://www.gnu.org/licenses/>. *
************************************************************************/

/***************************************************
* Configure to suit your needs.                    *
****************************************************/

// true = show label, false = don't show label.
$show_label = true;

// true = show percentage, false = don't show percentage.
$show_percent = true;

// true = show text, false = don't show text.
$show_text = true;

// true = show parts, false = don't show parts.
$show_parts = true;

// 'square' or 'round' label.
$label_form = 'round';

// Width of the chart
$width = 200;

// Colors of the slices.
$colors = array('003366', 'CCD6E0', '7F99B2', 'F7EFC6', 'C6BE8C', 'CC6600', '990000', '520000', 'BFBFC1', '808080', '9933FF', 'CC6699', '99FFCC', 'FF6666', '3399CC', '99FF66', '3333CC', 'FF0033', '996699', 'FF00FF', 'CCCCFF', '000033', '99CC33', '996600', '996633', '996666', '3399CC', '663333');

// true = use random colors, false = use colors defined above
$random_colors = true;

// Background color of the chart
$background_color = 'F6F6F6';

// Text color.
$text_color = '000000';

// Height on shadow.
$shadow_height = 30;

// true = darker shadow, false = lighter shadow...
$shadow_dark = true;

/***************************************************
* DO NOT CHANGE ANYTHING BELOW THIS LINE!!!        *
****************************************************/

if (!function_exists('imagecreate'))
	die('Sorry, the script requires GD2 to work.');

$data = @$_GET['data'];
$label = @$_GET['label'];

$height = $width / 2;
$data = explode('*', $data);
$xtra_height = 0;
$xtra_width = 0;

if (!empty($label))
	$label = explode('*', strtr($label, array('&quot;' => '"', '&amp;' => '&', '&#039;' => "'")));
else
	$label = array();

if ($random_colors == true)
{
	$colors = array();
	while (count($colors) <= count($data))
	{
		$color = random_color();
		if (!in_array($color, $colors))
			$colors[] = $color;
	}
}

if (array_sum($data) == 0)
	exit;

$text_length = 0;
$number = array();

for ($i = 0; $i < count($data); $i++) 
{
	if ($data[$i] / array_sum($data) < 0.1)
		$number[$i] = ' ' . number_format(($data[$i] / array_sum($data)) * 100, 1, ',', '.') . '%';
	else
		$number[$i] = number_format(($data[$i] / array_sum($data)) * 100, 1, ',', '.') . '%';
	if (!isset($label[$i]))
		$label[$i] = '';
	if (isset($label[$i]) && strlen($label[$i]) > $text_length)
		$text_length = strlen($label[$i]);
}

if (is_array($label))
{
	$antal_label = count($label);
	$xtra = (5 + 15 * $antal_label) - ($height + ceil($shadow_height));
	if ($xtra > 0)
		$xtra_height = (5 + 15 * $antal_label) - ($height + ceil($shadow_height));

	$xtra_width = 5;
	if ($show_label)
		$xtra_width += 20;
	if ($show_percent)
		$xtra_width += 45;
	if ($show_text)
		$xtra_width += $text_length * 8;
	if ($show_parts)
		$xtra_width += 35;
}

$img = ImageCreateTrueColor($width + $xtra_width, $height + ceil($shadow_height) + $xtra_height);

ImageFill($img, 0, 0, colorHex($img, $background_color));

foreach ($colors as $colorkode) 
{
	$fill_color[] = colorHex($img, $colorkode);
	$shadow_color[] = colorHexshadow($img, $colorkode, $shadow_dark);
}

$label_place = 5;

if (is_array($label))
{
	for ($i = 0; $i < count($label); $i++) 
	{
		if ($label_form == 'round' && $show_label)
		{
			imagefilledellipse($img, $width + 11,$label_place + 5, 10, 10, colorHex($img, $colors[$i % count($colors)]));
			imageellipse($img, $width + 11, $label_place + 5, 10, 10, colorHex($img, $text_color));
		}
		else if ($label_form == 'square' && $show_label)
		{
			imagefilledrectangle($img, $width + 6, $label_place, $width + 16, $label_place + 10,colorHex($img, $colors[$i % count($colors)]));
			imagerectangle($img, $width + 6, $label_place, $width + 16, $label_place + 10, colorHex($img, $text_color));
		}

		if ($show_percent)
			$label_output = $number[$i] . ' ';
		if ($show_text)
			$label_output = $label_output.$label[$i] . ' ';
		if ($show_parts)
			$label_output = $label_output . '- ' . $data[$i];

		imagestring($img, '2', $width + 20, $label_place, $label_output, colorHex($img, $text_color));
		$label_output = '';

		$label_place = $label_place + 15;
	}
}

$centerX = round($width / 2);
$centerY = round($height / 2);
$diameterX = $width - 4;
$diameterY = $height - 4;

$data_sum = array_sum($data);

$start = 270;

$value_counter = 0;
$value = 0;

for ($i = 0; $i < count($data); $i++) 
{
	$value += $data[$i];
	$end = ceil(($value/$data_sum) * 360) + 270;
	$slice[] = array($start, $end, $shadow_color[$value_counter % count($shadow_color)], $fill_color[$value_counter % count($fill_color)]);
	$start = $end;
	$value_counter++;
}

for ($i = ($centerY + $shadow_height); $i > $centerY; $i--) 
{
	for ($j = 0; $j < count($slice); $j++)
	{
		if ($slice[$j][0] == $slice[$j][1])
			continue;
		ImageFilledArc($img, $centerX, $i, $diameterX, $diameterY, $slice[$j][0], $slice[$j][1], $slice[$j][2], IMG_ARC_PIE);
	}
}

for ($j = 0; $j < count($slice); $j++)
{
	if ($slice[$j][0] == $slice[$j][1])
		continue;
	ImageFilledArc($img, $centerX, $centerY, $diameterX, $diameterY, $slice[$j][0], $slice[$j][1], $slice[$j][3], IMG_ARC_PIE);
}

header('Content-type: image/jpg');
ImageJPEG($img, NULL, 100);
ImageDestroy($img);

function colorHex($img, $HexColorString) 
{
	$R = hexdec(substr($HexColorString, 0, 2));
	$G = hexdec(substr($HexColorString, 2, 2));
	$B = hexdec(substr($HexColorString, 4, 2));
	return ImageColorAllocate($img, $R, $G, $B);
}

function colorHexshadow($img, $HexColorString, $mork) 
{
	$R = hexdec(substr($HexColorString, 0, 2));
	$G = hexdec(substr($HexColorString, 2, 2));
	$B = hexdec(substr($HexColorString, 4, 2));

	if ($mork)
	{
		($R > 99) ? $R -= 100 : $R = 0;
		($G > 99) ? $G -= 100 : $G = 0;
		($B > 99) ? $B -= 100 : $B = 0;
	}
	else
	{
		($R < 220) ? $R += 35 : $R = 255;
		($G < 220) ? $G += 35 : $G = 255;
		($B < 220) ? $B += 35 : $B = 255;				
	}			
	
	return ImageColorAllocate($img, $R, $G, $B);
}

function random_color()
{
	mt_srand((double)microtime()*1000000);
	$c = '';
	while (strlen($c) < 6)
	{
		$c .= sprintf("%02X", mt_rand(0, 255));
	}
	return $c;
}
?>
