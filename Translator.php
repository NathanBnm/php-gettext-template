<?php

require "vendor/autoload.php";

use League\CLImate\CLImate;
use Gettext\Generator\ArrayGenerator;
use Gettext\Generator\PoGenerator;
use Gettext\Loader\PoLoader;
use Gettext\Merge;
use Gettext\Scanner\PhpScanner;
use Gettext\Translations;

$cli = new CLImate();

$copyright = "Nathan Bonnemains"; //Copyright owner

$languages = ["en", "fr"]; //Supported languages

$files = glob("*.php"); //Array of files to extract

$cli->arguments->add([
    'extract' => [
        'prefix'       => 'e',
        'longPrefix'   => 'extract',
        'description'  => 'Extract translation files for every language',
        'noValue'      => true,
    ],
    'generate' => [
        'prefix'       => 'g',
        'longPrefix'   => 'generate',
        'description'  => 'Generate translation files for every language',
        'noValue'      => true,
    ],
    'language' => [
        'prefix'       => 'l',
        'longPrefix'   => 'language',
        'description'  => 'Specific language to extract',
    ],
    'domain' => [
        'prefix'      => 'd',
        'longPrefix'  => 'domain',
        'description' => 'Specific domain to extract',
    ],
    'file' => [
        'prefix'      => 'p',
        'longPrefix'  => 'file',
        'description' => 'Specific file to extract',
    ],
    'comments' => [
        'prefix'      => 'c',
        'longPrefix'  => 'comments',
        'description' => 'Extract or not the comments',
        'noValue'      => true,
    ],
    'verbose' => [
        'prefix'      => 'v',
        'longPrefix'  => 'verbose',
        'description' => 'Verbose output',
        'noValue'     => true,
    ],
    'help' => [
        'prefix'      => 'h',
        'longPrefix'  => 'help',
        'description' => 'Prints a usage statement',
        'noValue'     => true,
    ]
]);

$cli->arguments->parse();

if ($cli->arguments->defined('help')) {
    $cli->usage();
    die();
}

if ($cli->arguments->defined('domain') !== false) {
    $domain = $cli->arguments->get('domain');
} else {
    $domain = "messages";
}

$translations = Translations::create($domain);

$isVerbose = $cli->arguments->defined('verbose');

if ($cli->arguments->defined('extract') !== false) {
    $phpScanner = new PhpScanner($translations);
    $phpScanner->setDefaultDomain("messages");

    if ($cli->arguments->defined('comments') !== false) {
        $phpScanner->extractCommentsStartingWith("Translators:");
    }

    if ($cli->arguments->defined('language') !== false) {
        $cliLanguage = $cli->arguments->get('language');

        if (in_array($cliLanguage, $languages) === false) {
            $cli->shout("Error: Language '{$cliLanguage}' is not part of the website");
            die();
        }

        $languages = array($cliLanguage);
    }

    if ($cli->arguments->defined('file') !== false) {
        $cliFile = $cli->arguments->get('file');

        if (in_array($cliFile, $files) === false) {
            $cli->shout("Error: File '{$cliFile}' is not part of the website");
            die();
        }

        $files = array($cliFile);
    }

    if ($isVerbose) {
        $count = count($files);
        if ($count > 1) {
            $cli->info("Scanning {$count} files…");
        } else {
            $cli->info("Scanning {$count} file…");
        }
    }

    foreach ($files as $file) {
        if ($isVerbose) {
            $cli->comment("Scanning file {$file}…");
        }

        $phpScanner->scanFile($file);
    }

    if ($isVerbose) {
        $progress = $cli->progress(count($languages) * count($phpScanner->getTranslations()));
        $cli->info("Extracting translations…");
    }

    foreach ($languages as $language) {
        if ($isVerbose) {
            $cli->info("Extracting {$language}…");
        }

        foreach ($phpScanner->getTranslations() as $domain => $translations) {
            if ($isVerbose) {
                $cli->comment("Extracting {$domain} for {$language}…");
            }

            $l = Gettext\Languages\Language::getById($language);

            $name = $l->name;
            $year = date('Y');

            $description = "{$name} translations for {$domain}\nCopyright (C) {$year} {$copyright}.\nThis file is distributed under the same license as the {$domain} package.\nAutomatically generated, {$year}";

            $translations->setDescription($description);
            $translations->setLanguage($language);
            $translations->setDomain($domain);

            $translations->getHeaders()->set("Last-Translator", "Automatically generated");
            $translations->getHeaders()->set("PO-Revision-Date", date("Y-m-d H:iO"));

            $path = "locale/{$language}/LC_MESSAGES";

            if (!file_exists($path)) {
                if ($isVerbose) {
                    $cli->whisper("New language detected…");
                    $cli->whisper("Creating {$path} directory…");
                }

                mkdir($path, 0777, true);
            }

            $path .= "/{$domain}";

            if (file_exists($path . ".po")) {
                if ($isVerbose) {
                    $cli->comment("Merging existing translations…");
                }

                $translations = $translations->mergeWith((new PoLoader())->loadFile($path . ".po"), Merge::SCAN_AND_LOAD);
            }

            if ($isVerbose) {
                $cli->comment("Generating PO translation file for {$language}…");
            }

            (new PoGenerator())->generateFile($translations, $path . ".po");
        }
        if ($isVerbose) {
            $progress->advance();
        }
    }
}

if ($cli->arguments->defined('generate') !== false) {
    if ($isVerbose) {
        $cli->info("Generating translations…");
    }

    if ($cli->arguments->defined('language') !== false) {
        $cliLanguage = $cli->arguments->get('language');

        if (in_array($cliLanguage, $languages) === false) {
            $cli->shout("Error: Language '{$cliLanguage}' is not part of the website");
            die();
        }

        $languages = array($cliLanguage);
    }

    if ($cli->arguments->defined('file') !== false) {
        $cliFile = $cli->arguments->get('file');

        if (in_array($cliFile, $files) === false) {
            $cli->shout("Error: File '{$cliFile}' is not part of the website");
            die();
        }

        $files = array($cliFile);
    }

    if ($isVerbose) {
        $progress = $cli->progress(count($languages));
    }

    foreach ($languages as $language) {
        if ($isVerbose) {
            $cli->info("Generating {$language}…");
            $cli->comment("Generating {$domain} domain for {$language}…");
        }

        $path = "locale/{$language}/LC_MESSAGES/{$domain}";

        if (file_exists($path . ".po")) {
            if ($isVerbose) {
                $cli->comment("Loading existing translations…");
            }
            $translations = (new PoLoader())->loadFile($path . ".po");
        } else {
            $cli->shout("'{$path}.po' file does not exist. Please extract the translations first using -e");
            die();
        }


        if ($isVerbose) {
            $cli->comment("Generating PO translation file for {$language}…");
        }

        (new ArrayGenerator())->generateFile($translations, $path . ".php");

        if ($isVerbose) {
            $progress->advance();
        }
    }
}
