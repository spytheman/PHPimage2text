PHPimage2text - a simple OCR library
------------------------------------

PHPimage2text allows you to recognize text characters extracted from images, produced for example by imagettftext().
It assumes that each character is separated by the other characters by transparent lines thick at least 1 pixel.

Each character to be recognized, is stored inside a PNG image in the folder cuts/ , in a suitably named file.

Requirements:
--------------

GD Version => 2.0

PHP Version => 5.0

Usage:
--------------

    php recognizer.php images/example7.png
    php recognizer.php images/example3.png

TODO:
-----

Easy changing of the sample folder cuts/ to allow something like this usage:

    php recognizer.php gsmnumbers/ a_gsm_number_written_as_a_picture.png
    php recognizer.php letters/ captcha_picture.png

and so on ...

