# production-dependencies-guard

Prevents development packages from getting into production environment. In practical field 
prevents e.g. debug tool-bars deployment into production environments (and similar cases).

> Note: normally `composer install --no-dev` installing the production dependencies

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

will trigger failed installation:

```
./composer.json has been updated

Installation failed, reverting ./composer.json to its original content.

  [ErrorException]
  Following dev-dependencies has been found in require-section: phpunit/phpunit
```

# Stability

This package is only available in its `dev-master` version: according to the package purpose.