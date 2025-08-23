<?php
session_start();

// Generate random captcha code
$captcha_code = '';
$characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
for ($i = 0; $i < 6; $i++) {
    $captcha_code .= $characters[rand(0, strlen($characters) - 1)];
}

// Store in session
$_SESSION['captcha'] = $captcha_code;

// Create image
$width = 150;
$height = 50;
$image = imagecreate($width, $height);

// Colors
$bg_color = imagecolorallocate($image, 240, 240, 240);
$text_color = imagecolorallocate($image, 50, 50, 50);
$line_color = imagecolorallocate($image, 150, 150, 150);
$dot_color = imagecolorallocate($image, 100, 100, 100);

// Fill background
imagefill($image, 0, 0, $bg_color);

// Add noise lines
for ($i = 0; $i < 5; $i++) {
    imageline($image, rand(0, $width), rand(0, $height), rand(0, $width), rand(0, $height), $line_color);
}

// Add noise dots
for ($i = 0; $i < 50; $i++) {
    imagesetpixel($image, rand(0, $width), rand(0, $height), $dot_color);
}

// Add text
$font_size = 16;
$angle = rand(-10, 10);
$x = 20;
$y = 35;

// Try to use a TTF font if available, otherwise use built-in font
$font_path = __DIR__ . '/assets/fonts/arial.ttf';
if (file_exists($font_path)) {
    imagettftext($image, $font_size, $angle, $x, $y, $text_color, $font_path, $captcha_code);
} else {
    // Use built-in font with character spacing
    for ($i = 0; $i < strlen($captcha_code); $i++) {
        $char_x = $x + ($i * 20);
        $char_y = rand(25, 35);
        imagestring($image, 5, $char_x, $char_y, $captcha_code[$i], $text_color);
    }
}

// Set headers
header('Content-Type: image/png');
header('Cache-Control: no-store, no-cache, must-revalidate');
header('Expires: Thu, 19 Nov 1981 08:52:00 GMT');
header('Pragma: no-cache');

// Output image
imagepng($image);
imagedestroy($image);
?>
