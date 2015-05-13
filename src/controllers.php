<?php

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

//Request::setTrustedProxies(array('127.0.0.1'));

$app->error(function (\Exception $e, Request $request, $code) use ($app) {
    if ($app['debug']) {
        return;
    }

    // 404.html, or 40x.html, or 4xx.html, or error.html
    $templates = array(
        'errors/'.$code.'.html',
        'errors/'.substr($code, 0, 2).'x.html',
        'errors/'.substr($code, 0, 1).'xx.html',
        'errors/default.html',
    );

    return new Response($app['twig']->resolveTemplate($templates)->render(array('code' => $code)), $code);
});

//
// Placebeet routing
//

// simple callback method, casts a given string to integer.
$intvalConversionCallback = function ($val) {
    return (int) $val;
};

// routes
$app->get('/', 'placebeet.controller.default:indexAction')
  ->value('width', 200)
  ->value('greyscale', FALSE)
  ->value('watermark', FALSE);

$app->get('/{width}/', 'placebeet.controller.default:indexAction')
  ->assert('width', '\d+')
  ->convert('width', $intvalConversionCallback)
  ->value('greyscale', FALSE)
  ->value('watermark', FALSE);

$app->get('/{width}/{height}', 'placebeet.controller.default:indexAction')
  ->assert('width', '\d+')
  ->assert('height', '\d+')
  ->convert('width', $intvalConversionCallback)
  ->convert('height', $intvalConversionCallback)
  ->value('greyscale', FALSE)
  ->value('watermark', FALSE);

$app->get('/{width}x{height}', 'placebeet.controller.default:indexAction')
  ->assert('width', '\d+')
  ->assert('height', '\d+')
  ->convert('width', $intvalConversionCallback)
  ->convert('height', $intvalConversionCallback)
  ->value('greyscale', FALSE)
  ->value('watermark', FALSE);

$app->get('/d/{width}', 'placebeet.controller.default:indexAction')
  ->assert('width', '\d+')
  ->convert('width', $intvalConversionCallback)
  ->value('greyscale', FALSE)
  ->value('watermark', TRUE);

$app->get('/d/{width}/{height}', 'placebeet.controller.default:indexAction')
  ->assert('width', '\d+')
  ->assert('height', '\d+')
  ->convert('width', $intvalConversionCallback)
  ->convert('height', $intvalConversionCallback)
  ->value('greyscale', FALSE)
  ->value('watermark', TRUE);

$app->get('/d/{width}x{height}', 'placebeet.controller.default:indexAction')
  ->assert('width', '\d+')
  ->assert('height', '\d+')
  ->convert('width', $intvalConversionCallback)
  ->convert('height', $intvalConversionCallback)
  ->value('greyscale', FALSE)
  ->value('watermark', TRUE);

$app->get('/d/{width}/g', 'placebeet.controller.default:indexAction')
  ->assert('width', '\d+')
  ->convert('width', $intvalConversionCallback)
  ->value('greyscale', TRUE)
  ->value('watermark', TRUE);

$app->get('/d/{width}/{height}/g', 'placebeet.controller.default:indexAction')
  ->assert('width', '\d+')
  ->assert('height', '\d+')
  ->convert('width', $intvalConversionCallback)
  ->convert('height', $intvalConversionCallback)
  ->value('greyscale', TRUE)
  ->value('watermark', TRUE);

$app->get('/d/{width}x{height}/g', 'placebeet.controller.default:indexAction')
  ->assert('width', '\d+')
  ->assert('height', '\d+')
  ->convert('width', $intvalConversionCallback)
  ->convert('height', $intvalConversionCallback)
  ->value('greyscale', TRUE)
  ->value('watermark', TRUE);


$app->get('/{width}/g', 'placebeet.controller.default:indexAction')
  ->assert('width', '\d+')
  ->convert('width', $intvalConversionCallback)
  ->value('greyscale', TRUE)
  ->value('watermark', FALSE);

$app->get('/{width}/{height}/g', 'placebeet.controller.default:indexAction')
  ->assert('width', '\d+')
  ->assert('height', '\d+')
  ->convert('width', $intvalConversionCallback)
  ->convert('height', $intvalConversionCallback)
  ->value('greyscale', TRUE)
  ->value('watermark', FALSE);

$app->get('/{width}x{height}/g', 'placebeet.controller.default:indexAction')
  ->assert('width', '\d+')
  ->assert('height', '\d+')
  ->convert('width', $intvalConversionCallback)
  ->convert('height', $intvalConversionCallback)
  ->value('greyscale', TRUE)
  ->value('watermark', FALSE);
