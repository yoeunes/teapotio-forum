# Teapotio forum

## Introduction

Teapotio forum provides a full feature forum solution for small to large communities.

If you'd like to download the standalone version of the forum, please refer [to the README file located in the teapotio-forum-standard repository](https://github.com/teapotio/teapotio-forum-standard/blob/master/README.md).

## Integrate to your app

### composer.json

Add the following to your composer file:
```json
{
    "require": {
        "teapotio/teapotio-forum": "dev-master",
    }
}
```

Run the following command: ` $ composer update `

### config.yml

Add the following to your config.yml file:
```yml
imports:
    - { resource: @TeapotioSiteBundle/Resources/config/config.yml }
```

### AppKernel.php

Extends your AppKernel class with Teapotio's AppKernel class:
```php
use Teapotio\Components\AppKernel as TeapotioAppKernel;

class AppKernel extends TeapotioAppKernel
{
    $bundles = array(
        ...
    );

    $bundles = array_merge($bundles, parent::registerBundles());

    ...
}
```

Otherwise you can register the different bundle manually based the bundles registered in Teapotio's AppKernel class.
