# Http Kernel Logger Bundle

Log request and responses

## Installation
```
# bash
composer require zent/http-kernel-logger-bundle ~0.1

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
