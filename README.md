# php-gettext-template

A php-gettext template to make translations easier and more accessible for a Web PHP-based project.

This templates uses the [PHP implementation of gettext (php-gettext)](https://github.com/php-gettext/Gettext).

Any suggestion or contribution to make? Do not hesitate to post an issue or a PR!

## Prerequisites

In order to make this template work, you have to install gettext, php-scanner and translator from the [php-gettext project](https://github.com/php-gettext/Gettext):

```bash
composer require gettext/gettext gettext/php-scanner gettext/translator
```

## File structure

```
| locale                        //Translations directory (after first run)
    | - fr
        | - LC_MESSAGES
            | - messages.php
            | - messages.po
    | ...
| extractor.php                 //Generate PO translations files
| generator.php                 //Generate PHP translations files
| index.php
```

## How to use

In order to translate your project, you have to use the extractor first. Then you will be able to fill in the PO files with your translations. Then you have to run the generator to create the PHP files containing the translations which will be used to load them.

**Warning**: Do *NOT* edit translations directly into the generated PHP files or else they will be overwritten as soon as you will launch the generator again. You have to edit the PO files instead.

### Extractor

The extractor scans the specified PHP files for translatable strings and exports them into a generated PO file for each specified domain and language.

You can set up some variables at the beginning of the file:

```php
//Translation domain
$domain = "messages";

//Supported languages (Fallback is English)
$languages = [
    "en",
    "fr"
];

//Files to extract translations from (array of PHP files)
$files = glob("*.php");
```

**Note**: English is required as it is the fallback language and should *not* be removed from the available languages. You can still change the fallback language but at least one language should be set by default.

To run the extractor, simply use the PHP CLI:

```bash
php extractor.php
```

Now feel free to edit your translations directly within the PO files. You can also use [Poedit](https://poedit.net/).

**Note**: Translations will never be overwritten within the PO files, only updated depending of the content of your project.

# Generator

The extractor generates PHP translation file for each specified domain and language which will be used to load the translations.

You can set up some variables at the beginning of the file:

```php
//Translation domain
$domain = "messages";

//Supported languages (Fallback is English)
$languages = [
    "en",
    "fr"
];
```

**Note**: You should use the same parameters as in the extractor to be sure that every file will be correctly generated

To run the generator, simply use the PHP CLI:

```bash
php generator.php
```

Now you can load the PHP translation file you want on your project in order to apply translations.

The example in the `index.php` file loads the appropriate language depending of the URL.
For French translation the URL would be *www.yourdomain.com/index.php?lang=fr*

**Note**: If the language is not supported or not specified the fallback language will be used instead (default is English).

# License

This project is licensed under a MIT License, see [LICENSE](LICENSE) for more information.