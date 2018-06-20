# dimages
Simple Image management for Laravel.

## Installation fo Development Environment

To install dimages to develop for it, follow these steps.

1. Install a new Laravel application that you will use for running and testing your development. Let's call it **laravel_packages**.
2. In the root folder, create a **packages/marcohern** folder.
3. In **packages/marcohern** folder, checkout dimages source in a **dimages** folder. In the end, the path to the cloned repository should be in: */path/to/your/workspace/**laravel_packages**/marcohern/**dimages**/*

At this point, you must register the package in laravel: 

4. Add the DimagesServiceProvider to the *providers* list in the *config/app.php* file.

```php
  'providers' => [
    ...
    
    /*
     * Package Service Providers...
     */
    Marcohern\Dimages\DimagesServiceProvider::class,
    
    ...
  ]    
```

5. Include the package source files in the *composer.json* file *autoload* section. Include the dimages namespace/source folder in the *psr-4* list, and the *Helper.php* in the *files* list.

```json
{

  "autoload":{
  
    "psr-4": {
      "App\\": "app/",
      "Marcohern\\Dimages\\": "packages/marcohern/dimages/src"
    },
    "files": [
      "packages/marcohern/dimages/src/Helpers.php"
    ]
  }
  
}
```

6. Finally, run **composer dump-autoload**.

´´´
>composer dump-autoload
´´´

At this point, the module should be working.
