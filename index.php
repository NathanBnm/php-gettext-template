<?php

//Translation domain
$domain = "messages";

if (isset($_GET["lang"]))
    $lang = $_GET["lang"];
else
    $lang = "en"; //Fallback language

$path = "locale/{$lang}/LC_MESSAGES/{$domain}.php";

require "vendor/autoload.php";

use Gettext\Translator;
use Gettext\TranslatorFunctions;

$translator = new Translator();
$translator->loadTranslations($path);
TranslatorFunctions::register($translator);

echo __("Hello world!");

echo "<br>";

$value = 10;

echo sprintf(/* %s is a number */n__("%s item", "%s items", $value), $value, $value);
