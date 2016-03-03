# Http Kernel Logger Bundle

Log request and responses

## Installation
```
# bash
composer require vesax/http-kernel-logger-bundle dev-master

```

```
# AppKernel.php
$bundles = [
   new Vesax\HttpKernelLoggerBundle\VesaxHttpKernelLoggerBundle()
];
```

## Configuration
```
vesax_http_kernel_logger:
    zones:
        "^/api/":
            channel: api
```
