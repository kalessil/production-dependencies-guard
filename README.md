# production-dependencies-guard

Prevents development packages from being added into `require` and getting into production environment. In practical field 
prevents e.g. debug tool-bars deployment into production environments (and similar cases).

Additionally, you can configure the guard to decline packages with missing license, abandoned or mentioning `debug` in description
and analyze packages on basis of composer.lock (more precise analysis).

# Installation

`composer require --dev kalessil/production-dependencies-guard:dev-master`

# Configuration

Additional guard checks can enabled in composer.json file:
```
{
    "name": "...",

    "extra": {
        "production-dependencies-guard": ["check-lock-file", "check-description", "check-license", "check-abandoned"]
    }
}
```

- `check-lock-file` uses composer.lock instead of composer.json, allowing deeper dependencies analysis
- `check-description` enables description and keywords analysis, allowing to detect custom dev-packages (should contain `debug` in keywords)
- `check-license` enables license checking (should be specified)
- `check-abandoned` enables abandoned packages checking (should not be used)

# Usage

When the package is added to require-dev section of your `composer.json` file (`"kalessil/production-dependencies-guard": "dev-master"`),
it'll **prevent adding dev-packages into `require` section**. Since dev-packages has no security guaranties, this also 
improves your application security.

```
composer require --dev kalessil/production-dependencies-guard:dev-master

composer require phpunit/phpunit:*
# it should be `composer require --dev phpunit/phpunit:*` here
```

will run with an error (profit!):

```
./composer.json has been updated

Installation failed, reverting ./composer.json to its original content.

[RuntimeException]                                                                   
  Dependencies guard has found violations in require-dependencies (source: manifest):  
   - phpunit/phpunit: dev-package-name
```

# Stability

This package is only available in its `dev-master` version: according to the package purpose.