<?php
namespace Placebeet\Application\Controller;

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;

class DefaultController
{
    public function indexAction(Request $request, Application $app)
    {
        /** @var \Placebeet\Application\Service\ImageGeneratorService $imageGenerator */
        $imageGenerator = $app['placebeet.service.image_generator'];

        $width = $request->attributes->get('width');
        $height = $request->attributes->get('height', $width);
        $useWatermark = $request->attributes->get('watermark', false);
        $useGreyscale = $request->attributes->get('greyscale', false);

        $imageName = $request->attributes->get('image');
        $image = $imageGenerator->create($imageName, $width, $height, $useGreyscale, $useWatermark);

        if (false === $image) {
            $app->abort(500, "Unable to generate image.");
        }

        $stream = function () use ($image) {
            imagejpeg($image, null, 100);
        };

        return $app->stream($stream, 200, array('Content-Type' => 'image/jpeg'));
    }
}
