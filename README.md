# dimages

Simple Image management for Laravel. Very usefull if you are developing a Laravel API for Mobile Applications,
dimages can resample images depending on what the image will be used for and the devices density.

## Installation

Install dimages with composer:

```bash
$ composer require marcohern/dimages
```

Next, publish the **dimages.php** configuration. Type the following command:

```bash
$ php artisan vendor:publish
```

You will be prompted to select a provider. Select **Marcohern\Dimages\DimagesServiceProvider** by
typing the number, then press RETURN.

A message will confirm that the dimages config has been copied to your config directory.

```bash
Copied File [\vendor\marcohern\dimages\publishables\config\dimages.php] To [\config\dimages.php]
Publishing complete.
```
You are now all set to use the Image Management API.

## Using dimages

**dimages** exposes endpoints that help you manage images, and download them for specific 
profiles and density.

To make sure to get a result in JSON format, make sure to add header **Accept: application/json**.

### Uploading one or more images

```bash
POST /mh/dim/api/{entity}/{identity}
```
#### Example
```bash
POST /mh/dim/api/games/death-stranding
```
#### Parameters

**entity**: Entity of the image. such as **user**, **user-profile** or **albums**.

**identity**: Reference to the object attached to the image, such as the username or a slug. Examples: **john-doe**, **my-album-2020-01-22**.

**image**: (in body) Upload a file in a field called **image**. Content-Type must be **application/x-www-form-urlencoded**.

#### Returns

```javascript
{
  "index": 0
}
```
You can only upload one image per request. If you upload more than one image (with multiple requests),
each image will return an index number, the first one being 0, then 1, and so on.

### Downloading uploaded images

```bash
GET /mh/dim/api/{entity}/{identity}/{index?}
```
#### Examples
```bash
GET /mh/dim/api/games/death-stranding
GET /mh/dim/api/games/death-stranding/0
GET /mh/dim/api/games/death-stranding/2
```
#### Parameters

**entity**: Entity of the image. such as **user**, **user-profile** or **albums**.

**identity**: Reference to the object attached to the image, such as the username or a slug. Examples: **john-doe**, **my-album-2020-01-22**.

**index**: (optional) the index of the image. If no index is specified, index zero (0) is used.

#### Returns

Returns the source image, in it's original size, as it was uploaded.

### Downloading uploaded images in different sizes

```bash
GET /mh/dim/api/{entity}/{identity}/{profile}/{density}/{index?}
```
#### Examples
```bash
GET /mh/dim/api/games/death-stranding/icons/ldpi
GET /mh/dim/api/games/death-stranding/launcher-icons/mdpi/0
GET /mh/dim/api/games/death-stranding/ref/hdpi/2
```
#### Parameters

**entity**: Entity of the image. such as **user**, **user-profile** or **albums**.

**identity**: Reference to the object attached to the image, such as the username or a slug. Examples: **john-doe**, **my-album-2020-01-22**.

**profile**: The profile of the image. The profile is essentially a reference to what the image will be
used for, such as, as an **icon**, or a **cover**. Essentially it defines the **aspect ratio**.

**density**: Reference to the object attached to the image, such as the username or a slug. Examples: **john-doe**, **my-album-2020-01-22**.

**index**: (optional) the index of the image. If no index is specified, index zero (0) is used.

#### Returns

The image in the appropriate size.

### Other endpoint available

```bash
GET    /mh/dim/api/
GET    /mh/dim/api/{entity}
POST   /mh/dim/api/{entity}/{identity}
GET    /mh/dim/api/{entity}/{identity}
GET    /mh/dim/api/{entity}/{identity}/{index?}
DELETE /mh/dim/api/{entity}/{identity}/{index?}
POST   /mh/dim/api/{entity}/{identity}/{index}
GET    /mh/dim/api/{entity}/{identity}/{profile}/{identity}/{index?}
GET    /mh/dim/api/{entity}/{identity}/derivatives
GET    /mh/dim/api/{entity}/{identity}/sources
GET    /mh/dim/api/{entity}/{identity}/images
GET    /mh/dim/api/{entity}/{identity}/dimages
POST   /mh/dim/api/{entity}/{identity}/normalize
POST   /mh/dim/api/{entity}/{identity}/switch/{source}/with/{target}
POST   /mh/dim/api/move/{src_ent}/{src_idn}/to/{trg_ent}/{trg_idn}
GET    /mh/dim/api/status
```

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

5. Include the package source files in the **composer.json** file *autoload* section. Include the dimages namespace/source folder in the *psr-4* list, and the *Helper.php* in the *files* list.

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

```dos
.../laravel_packages>composer dump-autoload
```

At this point, the package module should be working. Test it by opening the module's main route.

7. Run **php artisan serve** on the laravel project.

```dos
.../laravel_packages>php artisan serve
```

8. Open a browser and access the package module with the following url: http://localhost:8000/mh/dim/api/status. This should return a 200 and a json with **success = true**. You are all set!