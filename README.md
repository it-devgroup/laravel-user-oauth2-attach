## 
## Description

Joining a profile from a social network to the site using Auth2.
Can be used to implement authorization via social networks.
Available services: google and facebook.

## Install for Lumen

**1.** Open file `bootstrap/app.php`

Uncommented strings

```
$app->withFacades();
$app->withEloquent();
```

Added after **$app->configure('app');**

```
$app->configure('user_oauth2_attach');
```

add new service provider

```
$app->register(\ItDevgroup\LaravelUserOAuth2Attach\Providers\UserOAuth2AttachServiceProvider::class);
```

**2.** Run commands

For creating config file

```
php artisan user:oauth2:attach:publish --tag=config
```

For creating migration file

```
php artisan user:oauth2:attach:publish --tag=migration
```

For generate table

```
php artisan migrate
```

## Install for laravel

**1.** Open file **config/app.php** and search
```
    'providers' => [
        ...
    ]
```
Add to section
```
        \ItDevgroup\LaravelUserOAuth2Attach\Providers\UserOAuth2AttachServiceProvider::class,
```
Example
```
    'providers' => [
        ...
        \ItDevgroup\LaravelUserOAuth2Attach\Providers\UserOAuth2AttachServiceProvider::class,
    ]
```

**2.** Run commands

For creating config file

```
php artisan vendor:publish --provider="ItDevgroup\LaravelUserOAuth2Attach\Providers\UserOAuth2AttachServiceProvider" --tag=config
```

For creating migration file

```
php artisan user:oauth2:attach:publish --tag=migration
```

For generate table

```
php artisan migrate
```

## Custom model

###### Step 1

Create custom model for user OAuth2

Example:

File: **app/CustomFile.php**

Content:

```
<?php

namespace App;

class CustomFile extends \ItDevgroup\LaravelUserOAuth2Attach\Model\UserOAuth2
{
}
```

If need change table name or need added other code:

```
<?php

namespace App;

class CustomFile extends \ItDevgroup\LaravelUserOAuth2Attach\Model\UserOAuth2
{
    protected $table = 'YOUR_TABLE_NAME';
    
    // other code
}
```

###### Step 2

Open **config/user_oauth2_attach.php** and change parameter "model", example:

```
...
// replace
'model' => \ItDevgroup\LaravelUserOAuth2Attach\Model\UserOAuth2::class,
// to
'model' => \App\CustomFile::class,
```

###### Step 3

Use custom **\App\CustomFile** model everywhere instead of standard model **\ItDevgroup\LaravelUserOAuth2Attach\Model\UserOAuth2**

## Custom social network service for login by OAuth2

1. Create custom class

Example:

File: **app/CustomSocialFile.php**

Content:

```
<?php

namespace App;

use ItDevgroup\LaravelUserOAuth2Attach\Data\UserData;
use Illuminate\Support\Collection;

class CustomSocialFile implements ServiceInterface
{
    /**
     * @return string
     */
    public function getLoginUrl(): string
    {
        return 'http://';
    }

    /**
     * @param Collection $data
     * @return UserData
     */
    public function authorize(Collection $data): UserData
    {
        return UserData::register(...);
    }
}
```

2. Add new social network service in file config/user_oauth2_attach.php in section "services"

## Customization user model (optional)

If configured, then the standard email search is disabled in method **getOrCreateModelFromData**.

In the user model, you need to add a public method **userOAuth2Find** and return a user object or null.

```
class User {
    ...
    
    public function userOAuth2Find(UserData $userData)
    {
        return self::query()
                ->where('email', '=', $userData->getEmail())
                ->first();
    }
}
```

## Usage

#### Initialize service

```
$service = app(\ItDevgroup\LaravelUserOAuth2Attach\UserOAuth2AttachServiceInterface::class);
```

or injected

```
// use
use ItDevgroup\LaravelUserOAuth2Attach\UserOAuth2AttachServiceInterface;
// constructor
public function __construct(
    UserOAuth2AttachServiceInterface $userOAuth2AttachService
)
```

further we will use the variable **$service**

#### List of active services for login

```
// $service->getServiceList(): array
$array = $service->getServiceList();
```

#### Initialize social network service

```
// $service->loadService(string $service): void
$service->loadService('google');
```

#### Get url for login by OAuth2

```
// $service->getLoginLink(): string
$url = $service->getLoginLink();
```

#### Authorize user by OAuth2 (after get url for login)

```
// $service->authorize(Collection $data): UserData
$userData = $service->authorize(collect($request->all()));
```

#### Get or create user model from data

$newUser - New fully populated user entity but not saved.

```
// $service->getOrCreateModelFromData(UserData $userData, Model $newUser): UserOAuth2
$userOAuth2 = $service->getOrCreateModelFromData($userData, $newUser);
```

#### CRUD methods

```
// $service->getModelListByUser(int $userId): CollectionEloquent
$eloquentCollection = $service->getModelListByUser(1);

// $service->getModelById(int $id): UserOAuth2
$userOAuth2 = $service->getModelById(1);

// $service->getModelByUserAndService(int $userId, string $service): UserOAuth2
$userOAuth2 = $service->getModelByUserAndService(1, 'google');

// $service->getModelByServiceAndExternalId(string $service, string $externalId): UserOAuth2
$userOAuth2 = $service->getModelByServiceAndExternalId('google', 'abcd...');

// $service->modelCreate(UserOAuth2 $model): bool
$service->modelCreate($userOAuth2);

// $service->modelUpdate(UserOAuth2 $model): bool
$service->modelUpdate($userOAuth2);

// $service->modelDelete(UserOAuth2 $model): bool
$service->modelDelete($userOAuth2);
```

#### User oauth2 model create

```
$userOAuth2 = \ItDevgroup\LaravelUserOAuth2Attach\Model\UserOAuth2::register(
    $user, // User model
    'google',
    $userData->getId(),
    $userData->getEmail(),
    $userData->getPhone(),
    $userData->getFirstName(),
    $userData->getLastName(),
    $userData->getGender(),
    $userData->getLink(),
    $userData->getProperties()
);
```

## Basic usage

#### Get url for redirect to login page social networks

```
$service->loadService('google');
$url = $service->getLoginLink();
```

#### Authorize social network and get token

After authorization using the link obtained above and after redirecting to the site from the social network

```
$service->loadService('google');
// $request - Request class
$userData = $service->authorize(collect($request->all()));
$newUser = new User();
$newUser->email = $userData->getEmail();
$userOAuth2 = $service->getOrCreateModelFromData($userData, $newUser);
$user = $userOAuth2->user;
```
