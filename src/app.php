<?php

use Silex\Application;
use Silex\Provider\TwigServiceProvider;
use Silex\Provider\RoutingServiceProvider;
use Silex\Provider\ValidatorServiceProvider;
use Silex\Provider\ServiceControllerServiceProvider;
use Silex\Provider\HttpFragmentServiceProvider;

$app = new Application();
$app->register(new RoutingServiceProvider());
$app->register(new ValidatorServiceProvider());
$app->register(new ServiceControllerServiceProvider());
$app->register(new TwigServiceProvider());
$app->register(new HttpFragmentServiceProvider());



$app['twig'] = $app->extend('twig', function ($twig, $app) {
    // add custom globals, filters, tags, ...

    $twig->addFunction(new \Twig_SimpleFunction('asset', function ($asset) use ($app) {
        return $app['request_stack']->getMasterRequest()->getBasepath().'/'.$asset;
    }));

    return $twig;
});

//
// Configuration
//

// @todo reorganize this out into a config file. [ST 2015/05/12]
$app['placebeet.config.resources_dir'] = dirname(__DIR__) . '/resources';
$app['placebeet.config.image_count'] = 10;

//
// Services
//

// register image generator as a service
$app['placebeet.service.image_generator'] = function($app) {
    return new Placebeet\Application\Service\ImageGeneratorService(
      $app['placebeet.config.resources_dir']);
};

// register the default application controller as a service
$app['placebeet.controller.default'] = function() {
    return new Placebeet\Application\Controller\DefaultController();
};

//
// Application Middleware
//

// set the image name as request attribute.
// pick one at random if none was given as request param.
$app->before(function(\Symfony\Component\HttpFoundation\Request $request,
  Application $app) {
    $imageName = $request->query->get('image');
    if (empty($imageName)) {
        // images on file are named sequentially in increments of one,
        // starting at 1.
        $imageName = rand(1, $app['placebeet.config.image_count']);
    }
    // jpegs is all we got
    $request->attributes->set('image', $imageName . '.jpg');
}, Application::EARLY_EVENT);

return $app;
