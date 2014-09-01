CHANGELOG
=========

A [BC BREAK] means the update will break the project for many reasons:

* new mandatory configuration
* new dependencies
* class refactoring

### 2014-09-01

* [BC BREAK] Dependency to SonataMarkItUpBundle has been set to require-dev only. You'll need to update your assets path as follows:

```
    <link rel="stylesheet" href="{{ asset('bundles/sonataformatter/markitup/skins/sonata/style.css') }}" type="text/css" media="all" />
    <link rel="stylesheet" href="{{ asset('bundles/sonataformatter/markitup/sets/markdown/style.css') }}" type="text/css" media="all" />
    <link rel="stylesheet" href="{{ asset('bundles/sonataformatter/markitup/sets/html/style.css') }}" type="text/css" media="all" />
    <link rel="stylesheet" href="{{ asset('bundles/sonataformatter/markitup/sets/textile/style.css') }}" type="text/css" media="all" />

    <script src="{{ asset('bundles/ivoryckeditor/ckeditor.js') }}" type="text/javascript"></script>
    <script src="{{ asset('bundles/sonataformatter/vendor/markitup-markitup/markitup/jquery.markitup.js') }}" type="text/javascript"></script>
    <script src="{{ asset('bundles/sonataformatter/markitup/sets/markdown/set.js') }}" type="text/javascript"></script>
    <script src="{{ asset('bundles/sonataformatter/markitup/sets/html/set.js') }}" type="text/javascript"></script>
    <script src="{{ asset('bundles/sonataformatter/markitup/sets/textile/set.js') }}" type="text/javascript"></script>
```

### 2014-03-12

* [BC BREAK] SecurityPolicyContenairAware has been renamed to SecurityPolicyContainerAware (typo).

### 2013-07-26

* remove the sonata_formatter_type_selector and it is now named sonata_formatter_type

### 2013-02-05

* Add support for dynamic text editor changes