# php-gettext template

A php-gettext template to make translation easier and more accessible for PHP-based Web projects.

This is made possible thanks to the [php-gettext](https://github.com/php-gettext/Gettext) and [climate](https://github.com/thephpleague/climate) projects.

Any suggestion or contribution to make? Do not hesitate to submit an issue or a PR!

## Dependencies

In order for this template to work, the following dependencies are needed:

* gettext/gettext >= 5.4
* gettext/php-scanner >= 1.1
* gettext/translator >= 1.0
* league/climate >= 3.5

You can install them using [composer](https://getcomposer.org/) by running the following command at your project root.

```
composer require gettext/gettext gettext/php-scanner gettext/translator league/climate
```

## Template structure

This is how the files are going to set up:

```
| locale                        //Translations directory (generated during first run)
    | - en
        | - LC_MESSAGES
            | - messages.php
            | - messages.po
    | - fr
    | ...
| Translator.php                //Translation logic
| index.php                     //Demo index.php file
```

## How to use

For the translation to work you have to surround your content with gettext methods. You can use both `gettext()` or `__()` for singular and `ngettext()` or `n__()` for plural.

If you need further info about gettext in PHP, see [PHP documentation](https://www.php.net/manual/fr/book.gettext.php).

You can use the provided simple `index.php` file for a full example.

### Configuration

You can customize the supported languages, change the default one and the translation domain as following in the `index.php` file:

```php
//Supported languages
$languages = [
    "en" => "English",
    "fr" => "Français"
];

$lang = 'en'; //Default language

$domain = "messages"; //Translation domain (name of the translation files)
```

**Notice**: The default language has to be set for translation to work correctly.

Then there is the `Translator.php` file which contains all the extraction and generation logic for the translation process.

There you can edit the copyright owner, the supported languages and the files you want to extract for translation with the following variables:

```php
$copyright = "Nathan Bonnemains"; //Copyright owner

$languages = ["en", "fr"]; //Supported languages

$files = glob("*.php"); //Array of files to extract
```

**Notice**: Make sure to set up the same supported languages in both files.

### How to run

When everything has been set, you can run the extractor by using PHP CLI with the following command:

```
php Translator.php --extract --comments --verbose
```

**Note**: For the list of available options, see below.

This will create a PO file for every language you set up before. Now feel free to edit your translations directly within the PO files. You can also use [Poedit](https://poedit.net/).

When your files are ready, you can simply run the generator to convert the PO files into PHP files with the following command:

```
php Translator.php --generate --verbose
```

**Note**: For the list of available options, see below.

The generated PHP files will be used to load the translation.

Here it is, now you can see your `index.php` file translated when switching from a language to another!

### List of available options

You can get the list of all the available commands by running `php Translator.php --help`.

```
-e, --extract
        Extract translation files for every language
-g, --generate
        Generate translation files for every language
-l language, --language <language>
        Specific language to extract
-d domain, --domain <domain>
        Specific domain to extract
-p file, --file <file>
        Specific file to extract
-c comments, --comments comments
        Extract or not the comments
-v, --verbose
        Verbose output
-h, --help
        Prints a usage statement
```

# License

This project is licensed under a MIT License, see [LICENSE](LICENSE) for more information.
