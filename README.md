# production-dependencies-guard

Prevents development packages from being added into `require` and getting into production environment. In practical field 
prevents e.g. debug tool-bars deployment into production environments (and similar cases).

# Installation

`composer require --dev kalessil/production-dependencies-guard:dev-master`

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

  [ErrorException]
  Following dev-dependencies has been found in require-section: phpunit/phpunit
```

# Stability

This package is only available in its `dev-master` version: according to the package purpose.