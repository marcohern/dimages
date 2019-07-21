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

### Configuring image sizes

The image sizes can be configured in the **config/dimages.php**  file.
There are settings to begin with.

```php
return [
  'densities' => [
    'ldpi'    => 0.75,
    'mdpi'    => 1.00,
    'hdpi'    => 1.50,
    'xhdpi'   => 2.00,
    'xxhdpi'  => 3.00,
    'xxxhdpi' => 4.00,

    'single'  => 1.00,
    'double'  => 2.00,
  ],
  'profiles' => [
    'ref'                => [480, 320],
    'launcher-icons'     => [48, 48],
    'actionbar-icons'    => [24, 24],
    'small-icons'        => [16, 16],
    'notification-icons' => [22, 22],
  ]
];
```
**densities**: A list of densities in inches per pixel. This will used to calculate the size
of any image requested with the specified density.

**profiles**: A list of image sizes in pixels. It is the size of the image if the density was
equal to 1 (mdpi). Sizes are calculated by multiplying the specified density with each dimension
of the specified profile.

Examples:
```bash
Request    Profile Image Size  Density Mult  Requested Size
ref/ldpi   ref     [480, 320]  ldpi    0.75  [360, 240]
ref/mdpi   ref     [480, 320]  mdpi    1.00  [480, 320]
ref/hdpi   ref     [480, 320]  hdpi    1.50  [720, 480]
ref/xhdpi  ref     [480, 320]  xhdpi   2.00  [950, 640]
ref/xxhdpi ref     [480, 320]  xhdpi   3.00  [1440, 960]
```

### Deleting images
```bash
DELETE /mh/dim/api/{entity}/{identity}/{index?}
```
#### Examples
```bash
# Delete a single image
DELETE /mh/dim/api/games/death-stranding/1

# Delete all images associated with identity
DELETE /mh/dim/api/games/death-stranding
```
#### Parameters
**entity**: Entity of the image.

**identity**: Identity.

**index**: (optional) the index of the image. Images with the matching index will be deleted.
If not specified, all images associated with the identity will be deleted.

### Other endpoints available

```bash
# get a list of entities
GET    /mh/dim/api/

# get a list of identities
GET    /mh/dim/api/{entity}

# add a new image
POST   /mh/dim/api/{entity}/{identity}

# get the source image
GET    /mh/dim/api/{entity}/{identity}

# get the specified source image
GET    /mh/dim/api/{entity}/{identity}/{index?}

# delete the specified image
DELETE /mh/dim/api/{entity}/{identity}/{index?}

# update an existing image
POST   /mh/dim/api/{entity}/{identity}/{index}

# get an resampled image of the source.
GET    /mh/dim/api/{entity}/{identity}/{profile}/{identity}/{index?}

# get a list of existing derivate images
GET    /mh/dim/api/{entity}/{identity}/derivatives

# get a list of existing source images
GET    /mh/dim/api/{entity}/{identity}/sources

# get a file list of all existing images
GET    /mh/dim/api/{entity}/{identity}/images

# get a tabulated list of all existing images
GET    /mh/dim/api/{entity}/{identity}/dimages

# make sure there is a default image (index 0)
# and remove all gaps in indexes,
POST   /mh/dim/api/{entity}/{identity}/normalize

# switch an image index for another index
POST   /mh/dim/api/{entity}/{identity}/switch/{source}/with/{target}

# move files from one entity/identity to another
POST   /mh/dim/api/move/{src_ent}/{src_idn}/to/{trg_ent}/{trg_idn}

# get a status of the service. 200 means it is ok.
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