# dimages

Simple Image management for Laravel. Very usefull if you are developing a Laravel API for Mobile Applications,
dimages can resample images depending on what the image will be used for and the devices density.

## Installation

Download and install laravel.

```bash
$ laravel new app1
$ cd app1
```

Dimages allows images to be downloaded by the public. However, uploading
images can only be done through authentication, by way of the auth:api middleware.
The easiest way to achieve api authentication is to use laravel/passport.
Note: laravel/passport requires a database, so make sure you have one set up.

So download, install, and configure laravel passport:

```bash
$ composer require laravel/passport
```
This will install passport and all it's dependencies.

```bash
$ php artisan passport:install
```

You will get an output similar to:

```bash
Encryption keys generated successfully.
Personal access client created successfully.
Client ID: 1
Client secret: XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX
Password grant client created successfully.
Client ID: 2
Client secret: YYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYY
```

This creates 2 grants for Signing in. The first one is a Personal access
client, which we will ignore for now. The second one is a Password grand
which is the one we will use to log in. So remember the second
Client secret, we will use it as an API key of sorts to log into the api.
Now, In the mean time, you need to create a user to log in with,
lets create a seeder to add a user.

```bash
$ php artisan make:seeder UserSeeder
Seeder created successfully.
```

This creates the seeder class in database/seeds folder, lets add the code:

```php
<?php

use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
  protected $users = [
    ['name' => 'Son Goku', 'email' => 'goku@dbz.com', 'password' => 'goku'],
    ['name' => 'Bulma', 'email' => 'bulma@dbz.com', 'password' => 'bulma'],
    ['name' => 'Master Roshi', 'email' => 'master.roshi@dbz.com', 'password' => 'masterroshi'],
    ['name' => 'Yamcha', 'email' => 'yamcha@dbz.com', 'password' => 'yamcha'],
    ['name' => 'Krillin', 'email' => 'krillin@dbz.com', 'password' => 'krillin'],
    ['name' => 'Tien Shinhan', 'email' => 'tien.shinhan@dbz.com', 'password' => 'tienshinhan'],
    ['name' => 'Picollo', 'email' => 'picollo@dbz.com', 'password' => 'picollo']
  ];

  public function run()
  {
    foreach ($this->users as $k => $u) {
      $now = (new \Datetime("now"))->format('Y-m-d H:i:s');
      $this->users[$k]['email_verified_at'] = $now;
      $this->users[$k]['created_at'] = $now;
      $this->users[$k]['updated_at'] = $now;
      $this->users[$k]['password'] = bcrypt($u['password']);
    }

    DB::table('users')->insert($this->users);
  }
}
```

The code contains a bunch of users for testing, we can use any of those
to log in. To run the seeder, run the following command:

```bash
$ php artisan db:seed --class=UserSeeder
Database seeding completed successfully.
```

So now we have passport working, and users to log in with. 
We can now use composer to install marcohern/dimages

```bash
$ composer require marcohern/dimages
```

At this point, the library is installed. But you need to install the configuration file.

Run the following command:

```bash
$ php artisan vendor:publish
```

A list of publishables appear, pick the one labeled config.

At that moment you will see the following message output:

```bash
Copied File [\vendor\marcohern\dimages\publishables\config\dimages.php] To [\config\dimages.php]
```
This means that the configuration has been deployed.
Finally, by typing the following command:

```bash
$ php artisan route:list --columns=method,uri
```

A list of routes will appear. If routes with prefixes **dimage**, **dimaages** 
and **dimagesettings** appear, then the library is working and installed.
```bash
+----------+--------------------------------------------------------------------+
| Method   | URI                                                                |
+----------+--------------------------------------------------------------------+
| GET|HEAD | /                                                                  |
| GET|HEAD | api/user                                                           |
| POST     | dimage/attach/{tenant}/{session}/{entity}/{identity}               |
| POST     | dimage/stage/{tenant}/{session}                                    |
| POST     | dimage/{tenant}/{entity}/{identity}                                |
| DELETE   | dimage/{tenant}/{entity}/{identity}                                |
| GET|HEAD | dimage/{tenant}/{entity}/{identity}/{index?}                       |
| DELETE   | dimage/{tenant}/{entity}/{identity}/{index}                        |
| POST     | dimage/{tenant}/{entity}/{identity}/{index}                        |
| GET|HEAD | dimage/{tenant}/{entity}/{identity}/{profile}/{density}/{index?}   |
| GET|HEAD | dimages                                                            |
| GET|HEAD | dimages/session                                                    |
| GET|HEAD | dimages/status                                                     |
| GET|HEAD | dimages/{tenant}                                                   |
| GET|HEAD | dimages/{tenant}/{entity}                                          |
| POST     | dimages/{tenant}/{entity}/{identity}/normalize                     |
| GET|HEAD | dimages/{tenant}/{entity}/{identity}/sources                       |
| POST     | dimages/{tenant}/{entity}/{identity}/switch/{source}/with/{target} |
| GET|HEAD | dimagesettings/{tenant}                                            |
| POST     | dimagesettings/{tenant}/density                                    |
| DELETE   | dimagesettings/{tenant}/density/{density}                          |
| POST     | dimagesettings/{tenant}/profile                                    |
| DELETE   | dimagesettings/{tenant}/profile/{profile}                          |
| POST     | dimagesettings/{tenant}/reset                                      |
+----------+--------------------------------------------------------------------+
```
You are now ready to use the library.

## Using dimages

**dimages** exposes endpoints that help you manage images, and download them for specific 
profiles and density.

To make sure to get a result in JSON format, make sure to add header **Accept: application/json**.

### Uploading one or more images

```bash
POST /dimage/{tenant}/{entity}/{identity}
```
#### Example
```bash
POST /dimage/john-doe/games/death-stranding
```
#### Parameters
**tenant**: The user or tenant of the image, it can be a code, slug, or username. such as **mike** or **user1234**.

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
GET /dimage/{tenant}/{entity}/{identity}/{index?}
```
#### Examples
```bash
GET /dimage/john-doe/games/death-stranding
GET /dimage/john-doe/games/death-stranding/0
GET /dimage/john-doe/games/death-stranding/2
```
#### Parameters
**tenant**: The user or tenant of the image, it can be a code, slug, or username. such as **mike** or **user1234**.

**entity**: Entity of the image. such as **user**, **user-profile** or **albums**.

**identity**: Reference to the object attached to the image, such as the username or a slug. Examples: **john-doe**, **my-album-2020-01-22**.

**index**: (optional) the index of the image. If no index is specified, index zero (0) is used.

#### Returns

Returns the source image, in it's original size, as it was uploaded.

### Downloading uploaded images in different sizes

```bash
GET /dimage/{tenant}/{entity}/{identity}/{profile}/{density}/{index?}
```
#### Examples
```bash
GET /dimage/john-doe/games/death-stranding/icons/ldpi
GET /dimage/john-doe/games/death-stranding/launcher-icons/mdpi/0
GET /dimage/john-doe/games/death-stranding/ref/hdpi/2
```
#### Parameters

**tenant**: The user or tenant of the image, it can be a code, slug, or username. such as **mike** or **user1234**.

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
of any image requested with the specified density. You can add new density multipliers if you
like.

**profiles**: A list of image sizes in pixels. It is the size of the image if the density was
equal to 1 (mdpi). Sizes are calculated by multiplying the specified density with each dimension
of the specified profile. In principle, you will be adding more profiles as your app requires.

Examples:
```bash
Request    Profile Size        Density Mult  Requested Size
ref/ldpi   ref     [480, 320]  ldpi    0.75  [360, 240]
ref/mdpi   ref     [480, 320]  mdpi    1.00  [480, 320]
ref/hdpi   ref     [480, 320]  hdpi    1.50  [720, 480]
ref/xhdpi  ref     [480, 320]  xhdpi   2.00  [950, 640]
ref/xxhdpi ref     [480, 320]  xhdpi   3.00  [1440, 960]
```

### Deleting images
```bash
DELETE /dimage/{tenant}/{entity}/{identity}/{index?}
```
#### Examples
```bash
# Delete a single image
DELETE /dimage/john-doe/games/death-stranding/1

# Delete all images associated with identity
DELETE /dimage/john-doe/games/death-stranding
```
#### Parameters
**tenant**: The user or tenant of the image.

**entity**: Entity of the image.

**identity**: Identity.

**index**: (optional) the index of the image. Images with the matching index will be deleted.
If not specified, all images associated with the identity will be deleted.

### Other endpoints available

```bash
#Update an image
POST dimage/attach/{tenant}/{session}/{entity}/{identity}

#Upload an image into staging
POST dimage/stage/{tenant}/{session}

#Upload an image directly to an identity
POST dimage/{tenant}/{entity}/{identity}

#Delete all images associated to an identity
DELETE dimage/{tenant}/{entity}/{identity}

#Get a source image
GET dimage/{tenant}/{entity}/{identity}/{index?}

#Delete a single image and all its derivatives
DELETE dimage/{tenant}/{entity}/{identity}/{index}

#Update an existing image
POST dimage/{tenant}/{entity}/{identity}/{index}

#Get a derivative image
GET dimage/{tenant}/{entity}/{identity}/{profile}/{density}/{index?}

#Get list of tenants
GET dimages

#Get a usable session Id
GET dimages/session

#get status
GET dimages/status

#Get list of entities
GET dimages/{tenant}

#Get list of identities
GET dimages/{tenant}/{entity}

#Remove any index gaps if they exists
POST dimages/{tenant}/{entity}/{identity}/normalize

#Get list of existing source images
GET dimages/{tenant}/{entity}/{identity}/sources

#Switch an index to another. If target image allready exists, switch them.
POST dimages/{tenant}/{entity}/{identity}/switch/{source}/with/{target}

# Get tenant settings
GET dimagesettings/{tenant}

# Add or Update density entry
POST dimagesettings/{tenant}/density
{
  "name": "the_density",
  "value": 2.00
}

# Delete density entry
DELETE dimagesettings/{tenant}/density/{density}

# Add or Update profile entry
POST dimagesettings/{tenant}/profile
{
  "name": "the_profile",
  "width": 300,
  "height": 400
}

# Delete profile entry
DELETE dimagesettings/{tenant}/profile/{profile}

#Reset settings to default
POST dimagesettings/{tenant}/reset
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