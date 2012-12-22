<?php

if (!@include __DIR__ . '/../../../vendor/autoload.php') {
    die("You must set up the project dependencies, run the following commands:
wget http://getcomposer.org/composer.phar
php composer.phar install
After that, a autoload.php file will be generated in the vendor directory. ");
}