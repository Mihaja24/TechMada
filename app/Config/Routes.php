<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Auth::login');
$routes->match(['get', 'post'], 'login', 'Auth::login');
$routes->get('logout', 'Auth::logout');

$routes->group('rh', ['filter' => 'auth:rh|admin'], static function ($routes) {
	$routes->get('/', 'Rh::index');
	$routes->get('demandes', 'Rh::demandes');
	$routes->post('approuver/(:num)', 'Rh::approuver/$1');
	$routes->post('refuser/(:num)', 'Rh::refuser/$1');
});

$routes->get('liste-rh', 'Rh::demandes', ['filter' => 'auth:rh|admin']);
