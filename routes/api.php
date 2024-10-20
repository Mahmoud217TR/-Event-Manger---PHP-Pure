<?php

use Http\Controllers\API\EventController;
use Http\Controllers\API\EventParticipantController;
use Http\Controllers\API\IPController;
use Http\Controllers\API\LocationController;
use Http\Controllers\API\ParticipantController;
use Http\Middleware\DynamicAPIServiceMiddleware;

/**
 * API middleware are defined here.
 */

 $dynamicApiServe = new DynamicAPIServiceMiddleware();

/**
 * API routes are defined here.
 */

$router->get('/api/events/participants', [EventParticipantController::class, 'index'], [$dynamicApiServe]);
$router->get('/api/events/participants/:id', [EventParticipantController::class, 'show'], [$dynamicApiServe]);
$router->post('/api/events/participants', [EventParticipantController::class, 'store'], [$dynamicApiServe]);
$router->delete('/api/events/participants/:id', [EventParticipantController::class, 'destroy'], [$dynamicApiServe]);

$router->get('/api/events', [EventController::class, 'index'], [$dynamicApiServe]);
$router->get('/api/events/:id', [EventController::class, 'show'], [$dynamicApiServe]);
$router->post('/api/events', [EventController::class, 'store'], [$dynamicApiServe]);
$router->patch('/api/events/:id', [EventController::class, 'update'], [$dynamicApiServe]);
$router->delete('/api/events/:id', [EventController::class, 'destroy'], [$dynamicApiServe]);

$router->get('/api/locations', [LocationController::class, 'index'], [$dynamicApiServe]);
$router->get('/api/locations/:id', [LocationController::class, 'show'], [$dynamicApiServe]);
$router->post('/api/locations', [LocationController::class, 'store'], [$dynamicApiServe]);
$router->patch('/api/locations/:id', [LocationController::class, 'update'], [$dynamicApiServe]);
$router->delete('/api/locations/:id', [LocationController::class, 'destroy'], [$dynamicApiServe]);

$router->get('/api/participants', [ParticipantController::class, 'index'], [$dynamicApiServe]);
$router->get('/api/participants/:id', [ParticipantController::class, 'show'], [$dynamicApiServe]);
$router->post('/api/participants', [ParticipantController::class, 'store'], [$dynamicApiServe]);
$router->patch('/api/participants/:id', [ParticipantController::class, 'update'], [$dynamicApiServe]);
$router->delete('/api/participants/:id', [ParticipantController::class, 'destroy'], [$dynamicApiServe]);

$router->get('/api/ips', [IPController::class, 'index'], [$dynamicApiServe]);
$router->get('/api/ips/:id', [IPController::class, 'show'], [$dynamicApiServe]);
$router->post('/api/ips', [IPController::class, 'store'], [$dynamicApiServe]);
$router->delete('/api/ips/:id', [IPController::class, 'destroy'], [$dynamicApiServe]);