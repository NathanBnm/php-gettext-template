<?php

//Translation domain
$domain = "messages";

//Supported languages (Fallback is English)
$languages = [
    "en",
    "fr"
];

//Files to extract translations from
$files = glob("*.php");

require "vendor/autoload.php";

use Gettext\Generator\PoGenerator;
use Gettext\Loader\PoLoader;
use Gettext\Merge;
use Gettext\Scanner\PhpScanner;
use Gettext\Translations;

$translations = Translations::create($domain);
$phpScanner = new PhpScanner($translations);

//Set a default domain, so any translations with no domain specified, will be added to that domain
$phpScanner->setDefaultDomain("messages");

//Extract all comments starting with 'Translators:'
$phpScanner->extractCommentsStartingWith("Translators:");

//Scan files
print("Scanning files…\n");
foreach ($files as $file) {
    print("Scanning file {$file}…\n");
    $phpScanner->scanFile($file);
}

foreach ($languages as $lang) {
    print("Extracting {$lang}…\n");

    foreach ($phpScanner->getTranslations() as $domain => $translations) {
        print("Extracting {$domain} domain for {$lang}…\n");

        $translations->setLanguage($lang);
        $translations->setDomain($domain);
        $translations->getHeaders()->set("Last-Translator", "Automatically generated");
        $translations->getHeaders()->set("PO-Revision-Date", date("Y-m-d H:iO"));

        $path = "locale/{$lang}/LC_MESSAGES";

        if (!file_exists($path)) {
            print("New language detected\n");
            print("Creating {$path} directory…\n");
            mkdir($path, 0777, true);
        }

        $path .= "/{$domain}";

        if (file_exists($path . ".po")) {
            print("Merging existing translations…\n");
            $translations = $translations->mergeWith((new PoLoader())->loadFile($path . ".po"), Merge::SCAN_AND_LOAD);
        }

        print("Generating PO translation file for {$lang}…\n");
        (new PoGenerator())->generateFile($translations, $path . ".po");
    }
}

print("Extraction complete\n");
