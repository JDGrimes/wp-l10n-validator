WP L10n Validator
=================

Command-line tool for checking that all strings are properly gettexted for localization in WordPress plugins and themes.

* Finds any untranslated strings in HTML
* Finds any untranslated `'encapsed strings'` in PHP code
* Makes sure all gettext function parameters are valid –no variables, function
  calls, etc., where there should just be an encapsed string– and that all required
  arguments are present.
* Makes sure the expected textdomain(s) are always used
* As a side, it also checks that no l10n functions are deprecated.

Installation
------------

**Requires:** PHP 5.3 or later.

Download or check out the package. Add the `/bin` directory to your `$PATH` (or use
`/path/to/wp-l10n-validator/bin/wp-l10n-validator` instead of just `wp-l10n-validator`
in your commands.

To see the basic usage and check that everything is working, type the command:

`$ wp-l10n-validator`

Usage
-----

`$ wp-l10n-validator -[1c] TEXTDOMAIN [CONFIG]`

This validates all `.php` files in the current directory for proper gettexting.

Arguments:
 * `TEXTDOMAIN` - The textdomain used in your project.
 * `CONFIG` - Configuration to use. Corressponds to one of the directories in `/config` (`wordpress` by default).

Flags:
 * `1` - Parse only one file at a time.
 * `c` - Generate a specific ignores cache. This is a JSON file that contains a list
   of specific occurrences of strings to ignore. When you have fixed all of the real
   problems with your project, there may be left many strings that do not need to be
   gettexted. Running the command with this flag will cache all of those by file name
   and line number, so that they will be ignored in future. This is especially useful
   for strings that you want to ignore only in a specific location. If the line number
   that a string is on changes, but by less than 5 lines, it will continue to be
   ignored and the line number will be updated in the cache.

The validator will display any errors it finds.

Example validating a plugin:

```
$ cd /path/to/my-plugin
$ wp-l10n-validator my-plugin
```

You can also add a `wp-l10n-validator.json` file in the main directory of your
project, which specifies the basic configuration for your project ([see below](#configuration)).
With this file in place you can run the parser without any arguments.

Configuration
-------------

The validator can be configured specifically for your project as needed. Although it
can be completely customized, the main reason for additional configuration is to help
the parser weed out false positives. The strategy employed for weeding out most false
positives is as follows:

* Ignore non-tranlatable strings inside calls to certain functions
* Ignore specific function arguments that don't need to be gettexted
* Ignore certain HTML attributes' values
* Ignore specific strings
* Ignore specific string occurrances

All of these are configurable to match your particular project, though custom
configuration is optional. To configure the parser, you can add a JSON file named
`wp-l10n-validator.json` in the root directory of your project (or wherever you wish
to run the parser from).

These are the options that you can specify in the the JSON config file:

 * `textdomain` - Your project's texdomain.
 * `basedir` - The main directory of your project (if different from the current directory).
 * `config` - The configuration to use ([see CLI arguments above](#usage)).
 * `cache` - The file to store the cache in. The default is `wp-l10n-validator.cache`.
 * `ignores-cache` - The file to store the specific ignores cache in. The default is
   `wp-l10n-validator-ignores.cache`. See the `-c` flag above for more information.
 * `ignored-functions` - An associative array of functions to ignore. The value can be
   an array of specific arguments to be ignored (by argument number), or simply `true`.
   To ignore a class method, add it like this `My_Class::my_method`. This will only
   ignore the method when it is being called statically from outside the class like
   `My_Class::my_method()`, or inside the class with `self::` or `$this->`. The parser
   does not know what class is assigned to a variable, though it does know the
   variable name. So you can ignore `$wpdb->query`, which the parser does ignore by
   default, but adding `wpdb::query` will not match a call to `$wpdb->query()`. Adding
   a class constructor (`My_Class::__construct`) will ignore `new My_Class()`. Calls
   within a class to `parent::method()` will be mapped to the class that is specified
   in the `extends` statement.
 * `ignored-strings` - An array of strings that should always be ignored.
 * `ignored-atts` - An array of HTML attributes to ignore.
 * `bootstrap` - A PHP file providing further, more advanced configuration. You can
   even write your own child class to extend the validator. This allows you to change
   the output method by overriding the `report_*` functions, for example. Just assign
   and instance of your class to the `$parser` variable: `$parser = new My_L10n_Validator`.

See [example-config.json](example-config.json) for an example.

Notes
-----

* Though written primarily as a CLI app, it may also be used directly from within
  another script to validate a single file or a directory. Only the later option is
  available from the default CLI usage.
* Don't let the `WP` fool you. Though written primarily for WordPress, it can easily
  be configured for other frameworks that use similar gettexting methods.

Credits
-------

* [Codestyling Localization](http://wordpress.org/plugins/codestyling-localization/) for initial parser code.
* [@nikola-tmw](https://github.com/nikolov-tmw) for pointing me in the right direction on wp-hackers.
