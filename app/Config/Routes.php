<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Auth::login');
$routes->match(['get', 'post'], 'login', 'Auth::login');
$routes->get('logout', 'Auth::logout');

$routes->group('admin', ['filter' => 'auth:admin'], static function ($routes) {
	$routes->get('/', 'Admin::index');
	$routes->get('employes', 'Admin::employes');
	$routes->post('employes/save', 'Admin::saveEmploye');
	$routes->post('employes/desactiver/(:num)', 'Admin::deactivate/$1');
});

$routes->group('rh', ['filter' => 'auth:rh|admin'], static function ($routes) {
	$routes->get('/', 'Rh::index');
	$routes->get('demandes', 'Rh::demandes');
	$routes->get('employes', 'Rh::employes');
	$routes->post('approuver/(:num)', 'Rh::approuver/$1');
	$routes->post('refuser/(:num)', 'Rh::refuser/$1');
});

$routes->get('liste-rh', 'Rh::demandes', ['filter' => 'auth:rh|admin']);
$routes->get('soldes', 'Rh::employes', ['filter' => 'auth:rh|admin']);
