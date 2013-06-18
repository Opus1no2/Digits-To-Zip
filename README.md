Digits to Canadian Postal Code Conversion
=========================================

This is a conveniance class that can be used to convert a series of 6 digits to an array Canadian postal codes.

More information about the rules that govern the structure of Canadian postal codes can be found here:

http://www.canadapost.ca/tools/pg/manual/PGaddress-e.asp

Usage
-----

```php

require_once('DigitsToZip.php');

try {
    $app = new DigitsToZip(323895);
    $codes = $app->process();
    print_r($codes);
} catch (Exception $e) {
    echo $e->getMessage();
}

```