# Lexicon Template Engine
___

[![Build Status](https://travis-ci.org/anomalylabs/lexicon.svg?branch=master)](https://travis-ci.org/anomalylabs/lexicon)
[![Total Downloads](https://poser.pugx.org/anomaly/lexicon/downloads.svg)](https://packagist.org/packages/anomaly/lexicon)
[![Latest Stable Version](https://poser.pugx.org/anomaly/lexicon/v/stable.svg)](https://packagist.org/packages/anomaly/lexicon)
[![Latest Unstable Version](https://poser.pugx.org/anomaly/lexicon/v/unstable.svg)](https://packagist.org/packages/anomaly/lexicon)
[![License](https://poser.pugx.org/anomaly/lexicon/license.svg)](https://packagist.org/packages/anomaly/lexicon)

Lexicon is a template engine that encourages the design of simple and maintainable templates.

## Documentation

The complete documentation for Lexicon can be found at [Lexicon Documentation](http://lexicon.anomaly.is) website.

## Installation

Lexicon is a Composer package named `anomaly/lexicon`. To use it, simply add it to the require section of you `composer.json` file.

```language-php
{
    "require": {
        "anomaly/lexicon": "~0.1"
    }
}
```

Next, update `app/config/app.php` to include a reference to this package's service provider in the providers array.

```language-php
'providers' => [
    'Anomaly\Lexicon\LexiconServiceProvider'
]
```
___

## Credits

- [Osvaldo Brignoni](http://twitter.com/obrignoni)
- Lexicon is inspired on the [PyroCMS Lex Parser](https://github.com/pyrocms/lex), created by [Dan Horrigan](https://twitter.com/dhrrgn). 

We use the following packages.

- `illuminate/view`
- `phpspec/phpspec`

## Contributing

The contribution guide can be found in the [Lexicon Documentation](http://lexicon.anomaly.is/contributing).

### License

Lexicon is open-sourced software licensed under the [MIT license](http://opensource.org/licenses/MIT)