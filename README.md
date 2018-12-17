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
    at internal/util/inspect.js:31:15
    at req_ (D:\Google Drive\ENSSAT\IMR3\Projet GL\into-the-woods-webapp\web\assets\node_modules\natives\index.js:140:5)
    at require (D:\Google Drive\ENSSAT\IMR3\Projet GL\into-the-woods-webapp\web\assets\node_modules\natives\index.js:113:12)
    at util.js:25:21
    at req_ (D:\Google Drive\ENSSAT\IMR3\Projet GL\into-the-woods-webapp\web\assets\node_modules\natives\index.js:140:5)
    at require (D:\Google Drive\ENSSAT\IMR3\Projet GL\into-the-woods-webapp\web\assets\node_modules\natives\index.js:113:12)
    at fs.js:42:21
    at req_ (D:\Google Drive\ENSSAT\IMR3\Projet GL\into-the-woods-webapp\web\assets\node_modules\natives\index.js:140:5)
    at Object.req [as require] (D:\Google Drive\ENSSAT\IMR3\Projet GL\into-the-woods-webapp\web\assets\node_modules\natives\index.js:54:10)
    at Object.<anonymous> (D:\Google Drive\ENSSAT\IMR3\Projet GL\into-the-woods-webapp\web\assets\node_modules\vinyl-fs\node_modules\graceful-fs\fs.js:1:99)
`

Run `npm install natives@1.1.6` and then `./node_modules/.bin/gulp`

### Symfony checker with GrumPHP
