Formatter Block
===============

The formatter block must be used with the ``SonataBlockBundle`` as it provides
a native block to handle rich editor.

It can be a quick way to build small websites with rich blocks.


Installation
------------

 - Make sure ``SonataBlockBundle`` is installed
 - Make sure the block bundle is correctly configured
 - Add the ``sonata.formatter.block.formatter`` ID to the ``sonata_block`` configuration

 The block service is now available.

.. note::

    While this block can be used to handle and to manage contents, this is not a good
    solution to persist content. If you want to build a large website, then review
    the `Symfony CMF project <http://cmf.symfony.com/>`_.
