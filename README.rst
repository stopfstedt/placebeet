Placebeet
==============

This is the code repository for `placebeet.com <http://placebeet.com/500>`_, a placeholder image service featuring Beetlejuice the French Bulldog.

.. image:: http://placebeet.com/500

Usage
-----

Get a random 300 pixel wide square image.::

    http://placebeet.com/300

Get a random rectangular image that's 300px wide by 200px high.::

    http://placebeet.com/300/200
    http://placebeet.com/300x200

Get an image that shows its dimensions.::

    http://placebeet.com/d/300x200

Get a greyscale image.::

    http://placebeet.com/300x200/g

All together now, whilst requesting a specific image (1-9).::

    http://placebeet.com/d/300x200/g?image=7

Installing and running the application locally
-------------------------------
Clone this repo, then

.. code-block:: console

    $ cd placebeet
    $ composer update # installs dependencies
    $ composer run # runs the PHP internal server

Then, browse to http://localhost:8888/.

Copyright and Attribution
-------------------------

`Silex <http://silex.sensiolabs.org/>`_ and `Silex Skeleton <https://github.com/silexphp/Silex-Skeleton>`_:
Copyright (c) 2010 - 2015 Fabien Potencier, published under MIT license.

`Droid Sans Mono font <http://www.fontsquirrel.com/fonts/droid-sans-mono>`_ :
Copyright by Google and published under Apache 2.0 license.

All other code by Stefan Topfstedt, published under MIT license.

Regal poses by Beet. All photos by `Vanessa <http://www.atomic-canine.com/>`_, published in the public domain.

100% inspired by `{placekitten} <http://placekitten.com>`_ and `placehold.it <http://placekitten.com>`_.

