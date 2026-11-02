<?php

// Отразить по горизонтали
$img->flip('x');

// Поворот на 90 гр по часовой стрелке
$img->rotate(90);

// Автоматически задает ориентацио изображения, если информация о ориентации передана через EXIF-заголовки JPEG и TIFF изображений
$img->auto_orient();

// Изменить размер до 320x200
$img->resize(320, 200);

// Обрезать и подогнать под 100x75
$img->adaptive_resize(100, 75);

// Изменить размер по ширине сохраняя пропорции
$img->fit_to_width(320);

// Изменить размер по высоте сохраняя пропорции
$img->fit_to_height(200);

// Изменить размер вместив его в 500x500
$img->best_fit(500, 500);

// Вырезать участои изображения с x1, y1 до x2, y2
$img->crop(100, 100, 400, 400);

// Заполнить изображение цветом (#цвета)
$img->fill('#fff');

// Обесцветить
$img->desaturate();

// Инвертировать цвета
$img->invert();

// Яркость (от -255 до 255)
$img->brightness(100);

// Контраст (от -100 до 100)
$img->contrast(50);

// Раскрасить с прозрачностью 50%
$img->colorize('#FF0000', .5);

// Фильтр грани
$img->edges();

// Фильтр рельеф
$img->emboss();

// Mean removal filter
$img->mean_remove();

// Selective blur (one pass)
$img->blur();

// Gaussian blur (two passes)
$img->blur('gaussian', 2);

// Sketch filter
$img->sketch();

// Smooth filter (-10 to 10)
$img->smooth(5);

// Pixelate using 8px blocks
$img->pixelate(8);

// Sepia effect (simulated)
$img->sepia();

// Изменение прозрачности
$img->opacity(.5);

// Наложение изображения с 50% прозрачности в позиции `снизу справа` и 10px отступами
$img->overlay('watermark.png', 'bottom right', .5, -10, -10);

// Add 32-point white text top-centered (plus 20px) on the image*
$img->text('Your Text', 'font.ttf', 32, '#FFFFFF', 'top', 0, 20);

// Get info about the original image (before any changes were made)
//
// Returns:
//
//  array(
//      width => 320,
//      height => 200,
//      orientation => ['portrait', 'landscape', 'square'],
//      exif => array(...),
//      mime => ['image/jpeg', 'image/gif', 'image/png'],
//      format => ['jpeg', 'gif', 'png']
//  )
$info = $img->get_original_info();

// Get the current width
$width = $img->get_width();

// Get the current height
$height = $img->get_height();

// Get the current orientation (returns 'portrait', 'landscape', or 'square')
$orientation = $img->get_orientation();

// Flip the image and output it directly to the browser (i.e. without saving to file)
$img->flip('x')->output();