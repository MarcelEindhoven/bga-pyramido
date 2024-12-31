# bga-pyramido
Boardgamearena implementation of Pyramido

## GitHub
git clone https://github.com/MarcelEindhoven/bga-pyramido.git
git config user.email "Marcel.Eindhoven@Gmail.com"
git config user.name "MarcelEindhoven"

## Development site boardgame arena:
user MarcelEindhoven0
https://studio.boardgamearena.com/controlpanel
https://studio.boardgamearena.com/studio
https://studio.boardgamearena.com/studiogame?game=pyramidocannonfodder

## Development environment
### PHP
Installing PHP is tricky. For example, you cannot simply install PHP in "Program Files" because that directory name contains a space.
The messages you get assume you are already an expert in PHP terminology.

PHP version of BGA according to phpversion(): 8.2.22
Corresponding PHPunit version: 9


First download a PHP package without words like debug, develop or test in the package name. Possibly useful links
- https://www.sitepoint.com/how-to-install-php-on-windows/
- https://windows.php.net/downloads/releases/php-8.2.27-Win32-vs16-x64.zip
- https://www.ionos.com/digitalguide/server/configuration/php-composer-installation-on-windows-10/

### Composer
When PHP is available in the PATH, installation is straightforward
- https://getcomposer.org/Composer-Setup.exe
- In the git directory, type "composer install" to download all PHP packages into the vendor directory

### Visual studio code
Install visual studio code (https://code.visualstudio.com/docs?dv=win)

Extensions:
- PHP Intelephense 
- HTML CSS Support
- StandardJS - JavaScript Standard Style
- Git History
- Git Tree Compare
- GitHub Pull Requests and Issues
- Compare Folders
- Markdown plantUML
- Print

### JavaScript unit testing
To install Mocha, first install npm and node js

npm install --save-dev mocha
npm install --save-dev sinon
npm install --save-dev dojo
npm install --save-dev amd-loader

edit package.json: add test script
npm test

### PHP unit testing
./test.bat
