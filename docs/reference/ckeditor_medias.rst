.. index::
    single: Media; MediaBundle
    double: CKEditor; Configuration

Use CKEditor to select medias in SonataMediaBundle
==================================================

When using the ``richhtml`` formatting option with ``CKEditor``, you can
add a feature to select images directly from ``SonataMediaBundle`` or
even upload new medias from the editor in ``SonataMediaBundle`` to add
them to your content.

It can be a quick way for editors to manage medias.

Configuration
-------------

First of all, you have to define your ``FOSCKEditorBundle`` (already
embedded in ``SonataFormatterBundle``) configuration.  Be sure to have
the ``config/packages/fos_ck_editor.yaml`` configuration file available.
It should contain something like this:

.. code-block:: yaml

    # config/packages/fos_ck_editor.yaml

    fos_ck_editor:
        default_config: default
        configs:
            default:
                # default toolbar plus Format button
                toolbar:
                - [Bold, Italic, Underline, -, Cut, Copy, Paste,
                  PasteText, PasteFromWord, -, Undo, Redo, -,
                  NumberedList, BulletedList, -, Outdent, Indent, -,
                  Blockquote, -, Image, Link, Unlink, Table]
                - [Format, Maximize, Source]

                filebrowserBrowseRoute: admin_app_sonata_media_media_browser
                filebrowserImageBrowseRoute: admin_app_sonata_media_media_browser
                # Display images by default when clicking the image dialog browse button
                filebrowserImageBrowseRouteParameters:
                    provider: sonata.media.provider.image
                filebrowserUploadMethod: form
                filebrowserUploadRoute: admin_app_sonata_media_media_upload
                filebrowserUploadRouteParameters:
                    provider: sonata.media.provider.file
                # Upload file as image when sending a file from the image dialog
                filebrowserImageUploadRoute: admin_app_sonata_media_media_upload
                filebrowserImageUploadRouteParameters:
                    provider: sonata.media.provider.image
                    context: my-context # Optional, to upload in a custom context
                    format: my-big # Optional, media format or original size returned to editor

You can provide custom routes and a custom context to match your needs.

Last step takes place in your admin class. You have to specify the
``ckeditor_context`` parameter to activate ``CKEditor``.
Here is an example to alter ``shortDescription`` field of the
``ProductAdmin``::

    use Sonata\FormatterBundle\Form\Type\FormatterType;

    $form
        ->add('shortDescription', FormatterType::class, [
            'source_field' => 'rawDescription',
            'format_field' => 'descriptionFormatter',
            'target_field' => 'description',
            'ckeditor_context' => 'default',
            'listener' => true,
        ]);

And that is it, enjoy browsing and uploading your medias using
``SonataMediaBundle``.

Custom image media format returned to CKEditor
----------------------------------------------

When you upload an image using CKEditor, the image URL returned by the
server leads to the original size. You can configure custom image format
in ``SonataMediaBundle``:

.. code-block:: yaml

    # config/packages/sonata_media.yaml

    sonata_media:
        contexts:
            default:
                formats:
                    big: { width: 1280, quality: 95 }

Then you can pass this format to CKEditor:

.. code-block:: yaml

    # config/packages/fos_ck_editor.yaml

    fos_ck_editor:
        configs:
            default:
                filebrowserImageUploadRoute: admin_sonata_media_media_upload
                filebrowserImageUploadRouteParameters:
                    provider: sonata.media.provider.image
                    context: default
                    format: big

Alternatively you can specify custom return image format per field::

    use Sonata\FormatterBundle\Form\Type\SimpleFormatterType;

    $form
        ->add('details', SimpleFormatterType::class, [
            'format' => 'richhtml',
            'ckeditor_context' => 'default',
            'ckeditor_image_format' => 'big',
        ]);
