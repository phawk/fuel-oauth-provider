<?php
/**
 * Oauth Provider package for Fuel PHP
 *
 * @package    Oauth Provider
 * @version    1.0
 * @author     Pete Hawkins <pete@phawk.co.uk>
 * @license    MIT License
 * @copyright  2011 Pete Hawkins
 * @link       http://phawk.co.uk/
 */

Autoloader::add_core_namespace('Oauthprovider', true);

Autoloader::add_classes(array(
    'Oauthprovider\\Oauth_provider' => __DIR__.'/classes/oauth_provider.php',
));