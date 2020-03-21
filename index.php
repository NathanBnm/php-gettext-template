<?php

require "vendor/autoload.php";

use Gettext\Translator;
use Gettext\TranslatorFunctions;

$translator = new Translator();

if (isset($_GET["lang"]))
    $lang = $_GET["lang"]; //Detecting language from URL
else if (isset($_COOKIE["language"]))
    $lang = $_COOKIE["language"]; //Detecting language from cookie
else
    $lang = substr(locale_accept_from_http($_SERVER['HTTP_ACCEPT_LANGUAGE']), 0, 2); //Detecting language from browser

//Supported languages
$languages = [
    "en" => "English",
    "fr" => "Français"
];

if (!in_array($lang, array_keys($languages))) {
    $lang = 'en'; //Default language
}

if(!isset($_COOKIE['language']) || $_COOKIE['language'] != $lang)
    setcookie("language", $lang, strtotime('+30 days')); //Setting a 30 days cookie for the selected language

$domain = "messages"; //Translation domain

$path = "locale/{$lang}/LC_MESSAGES/{$domain}.php"; //Translation files location

$translator->loadTranslations($path); //Loading the appropriate translation file

TranslatorFunctions::register($translator);

$value = 10;

?>

<!DOCTYPE html>
<!-- Setting the HTML lang attribute dynamically -->
<html lang="<?= $lang ?>">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PHP Gettext Template</title>
</head>

<body>
    <!-- Translation of a simple string using gettext() -->
    <h1><?= __("Hello world!"); ?></h1>
    <!-- Translation of a plural string using sprintf() and ngettext() -->
    <p><?= sprintf(/* Translators: %d is the number of items */n__("There is %d item", "There are %d items", $value), $value, $value); ?></p>
    <!-- Listing available languages -->
    <ul>
        <?php foreach ($languages as $l => $name) { ?>
            <li><a href="?lang=<?= $l; ?>"><?= $name; ?></a></li>
        <?php } ?>
    </ul>
</body>

</html>