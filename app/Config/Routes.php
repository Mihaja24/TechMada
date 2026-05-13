<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');
$routes->match(['get', 'post'], 'employee/demande', 'CongeController::demande');
$routes->get('employee/mes_demandes', 'CongeController::mes_demandes');
$routes->post('employee/annuler/(:num)', 'CongeController::annuler/$1');
$routes->get('employee/solde', 'CongeController::solde');
$routes->match(['get', 'post'], 'employee/profil', 'CongeController::profil');
