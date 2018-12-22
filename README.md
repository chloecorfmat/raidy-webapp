into-the-woods-webapp
=====================

## Install

`composer install` to download the dependencies

`php bin/console doctrine:schema:update` to update scheme database

`php bin/console server:start` or `php bin/console server:run` to start the development server

## Install on windows

### Composer
1. Install Composer with : https://getcomposer.org/doc/00-intro.md#installation-windows 
2. Execute the .exe file
3. Select your PHP repository
4. Go on your into-the-woods-webapp repository location (with Git Bash for example)
5. Run `composer install`

### Gulp

1. Into web/asset run `npm install`
2. Then, run `./node_modules/.bin/gulp`
3. If **this** issue is encountered (may happen with node 11):
	`internal/util/inspect.js:31
const types = internalBinding('types');
              ^
ReferenceError: internalBinding is not defined
   [...]
`

Run `npm install natives@1.1.6` and then `./node_modules/.bin/gulp`

### Symfony checker with GrumPHP

1. Install **phpcs** locally: `composer --dev require "squizlabs/php_codesniffer=2.9.2"`
2. Install the coding standard: `composer require endouble/symfony3-custom-coding-standard`
3. Configure path: `vendor/bin/phpcs --config-set installed_paths ../../endouble/symfony3-custom-coding-standard`



	