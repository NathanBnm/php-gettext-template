<?php

//Translation domain
$domain = "messages";

//Supported languages (Fallback is English)
$languages = [
    "en",
    "fr"
];

require "vendor/autoload.php";

use Gettext\Generator\ArrayGenerator;
use Gettext\Loader\PoLoader;

foreach ($languages as $lang) {
    print("Generating {$lang}…\n");

    print("Generating {$domain} domain for {$lang}…\n");

    $path = "locale/{$lang}/LC_MESSAGES/{$domain}";

    if (file_exists($path . ".po")) {
        print("Loading existing translations…\n");
        $translations = (new PoLoader())->loadFile($path . ".po");
    } else {
        die("{$path}.po file does not exist. Please use the extractor first");
    }

    print("Generating PHP translation file for {$lang}…\n");
    (new ArrayGenerator())->generateFile($translations, $path . ".php");
}

print("Generation complete\n");
