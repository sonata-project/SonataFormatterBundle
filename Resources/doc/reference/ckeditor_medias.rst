Use CKEditor to select medias in SonataMediaBundle
==================================================

When using richhtml formatting option with ``CKEditor``, you can add a feature to select images directly
from ``SonataMediaBundle`` or even upload new medias from the editor in ``SonataMediaBundle`` and add it to your content.

It can be a quick way for editors to manage medias.

Configuration
-------------

First of all, you have to define your ``IvoryCKEditorBundle`` (already embedded in ``SonataFormatterBundle``) configurations like this:

.. code-block:: yaml

    ivory_ck_editor:
        default_config: default
        configs:
            default:
                filebrowserBrowseRoute: admin_sonata_media_media_ckeditor_browser
                filebrowserImageBrowseRoute: admin_sonata_media_media_ckeditor_browser
                # Display images by default when clicking the image dialog browse button
                filebrowserImageBrowseRouteParameters:
                    provider: sonata.media.provider.image
                filebrowserUploadRoute: admin_sonata_media_media_ckeditor_upload
                filebrowserUploadRouteParameters:
                    provider: sonata.media.provider.file
                # Upload file as image when sending a file from the image dialog
                filebrowserImageUploadRoute: admin_sonata_media_media_ckeditor_upload
                filebrowserImageUploadRouteParameters:
                    provider: sonata.media.provider.image
                    context: my-context # Optional, to upload in a custom context

You can provide custom routes and a custom context to match your needs.

Second step is optional but you can also define some custom browsing and upload templates with the following configuration:

.. code-block:: yaml

  # app/config/config.yml

  sonata_formatter:
      ckeditor:
          templates:
              browser: 'SonataFormatterBundle:Ckeditor:browser.html.twig'
              upload: 'SonataFormatterBundle:Ckeditor:upload.html.twig'

Last step takes place in your admin class, you just have to specify the ``ckeditor_context`` parameter.

Here is an example:

.. code-block:: php

    $formMapper->add('shortDescription', 'sonata_formatter_type', array(
        'source_field'         => 'rawDescription',
        'source_field_options' => array('attr' => array('class' => 'span10', 'rows' => 20)),
        'format_field'         => 'descriptionFormatter',
        'target_field'         => 'description',
        'ckeditor_context'     => 'default',
        'event_dispatcher'     => $formMapper->getFormBuilder()->getEventDispatcher()
    ));

And that's it, enjoy browsing and uploading your medias using ``SonataMediaBundle``.