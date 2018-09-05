<p align="center">
    <a href="https://developers.google.com/products/" target="_blank">
        <img src="https://developers.google.com/_static/2bced1f68f/images/redesign-14/lockup-color.png" width="300px">
    </a>
    <h1 align="center">Google API Client Helper</h1>
    <br>
</p>

Google APIs Client Helper - Easy way to accessing Google APIs with PHP

[![Latest Stable Version](https://poser.pugx.org/yidas/google-apiclient-helper/v/stable?format=flat-square)](https://packagist.org/packages/yidas/google-apiclient-helper)
[![Latest Unstable Version](https://poser.pugx.org/yidas/google-apiclient-helper/v/unstable?format=flat-square)](https://packagist.org/packages/yidas/google-apiclient-helper)
[![License](https://poser.pugx.org/yidas/google-apiclient-helper/license?format=flat-square)](https://packagist.org/packages/yidas/google-apiclient-helper)


FEATURES
--------

- *Easy way to develop and manage Google API application*

- *Documentation supported for Service SDK*

- *Simple usage for each Service*

This Helper is based on [google-api-php-client](https://github.com/google/google-api-php-client) and [google-api-php-client-services](https://github.com/google/google-api-php-client-services).

---

OUTLINE
-------

- [Demonstration](#demonstration)
- [Requirements](#requirements)
- [Installation](#installation)
- [Google Client](#google-client)
    - [Configuration](#configuration)
        - [Config Array Method](#config-array-method)
        - [Config Chain Method](#config-chain-method)
        - [Encapsulating Method](#encapsulating-method)
    - [AccessToken Usage](#accesstoken-usage)
        - [refreshAccessToken()](#refreshaccesstoken)
        - [verifyAccessToken()](#verifyaccesstoken)
        - [verifyScopes()](#verifyscopes)
    - [Implementation](#implementation)
- [Google Services](#google-services)
    - [People](#people)
        - [Attributes](#attributes)
        - [getSimpleContacts()](#getsimplecontacts)
        - [createContact()](#createcontact)
        - [updateContact()](#updatecontact)
        - [deleteContact()](#deletecontact)
- [Exceptions](#exceptions)
- [Reference](#reference)
---

DEMONSTRATION
-------------

```php
$client = \yidas\google\apiHelper\Client::setClient()
    ->setApplicationName('Google API')
    ->setAuthConfig('/path/google_api_secret.json')
    ->setRedirectUri("http://{$_SERVER['HTTP_HOST']}" . dirname($_SERVER['PHP_SELF'])
    ->setAccessToken($accessToken)
    ->getClient();

if ($accessToken = ClientHelper::refreshAccessToken()) {
    // saveAccessToken($accessToken)
}

// People Service uses Google_Client from Client helper above
$contacts = \yidas\google\apiHelper\services\People::getSimpleContacts();
```

---


REQUIREMENTS
------------
This library requires the following:

- PHP 5.4.0+
- google/apiclient 2.0+

---

INSTALLATION
------------

Run Composer in your project:

    composer require yidas/google-apiclient-helper
    
Then you could call it after Composer is loaded depended on your PHP framework:

```php
require __DIR__ . '/vendor/autoload.php';

use yidas\google\apiHelper\Client;
```

---

GOOGLE CLIENT
-------------

### Configuration

There are many way to set `Google_Client` by Helper:

#### Config Array Method

The config keys refer to the methods of `Google_Client`. For exmaple, `authConfig` refers to `Google_Client->setAuthConfig()`.

```php
$client = \yidas\google\apiHelper\Client::setClient([
        'applicationName' => 'Google API',
        'authConfig' => '/path/google_api_secret.json',
        'redirectUri' => "http://{$_SERVER['HTTP_HOST']}" . dirname($_SERVER['PHP_SELF'],
        ])
    ->getClient();
```

#### Config Chain Method

The methods refer to the same method names of `Google_Client`. For exmaple, `setAuthConfig()` refers to `Google_Client->setAuthConfig()`.

```php
$client = \yidas\google\apiHelper\Client::setClient()
    ->setApplicationName('Google API')
    ->setAuthConfig('/path/google_api_secret.json')
    ->setRedirectUri("http://{$_SERVER['HTTP_HOST']}" . dirname($_SERVER['PHP_SELF'])
    ->getClient();
````

#### Encapsulating Method

```php
$client = new Google_Client();
$client->setAuthConfig('/path/google_api_secret.json');
\yidas\google\apiHelper\Client::setClient($client);
```

> After encapsulating Google_Client into Helper, the Helper would share with the same Google_Client object.

### AccessToken Usage

#### refreshAccessToken()

Simple way to get refreshed access token or false expired to skip

```php
public static array|false refreshAccessToken()
```

*Example:*

```php
$client = \yidas\google\apiHelper\Client::setClient()
    ->setApplicationName('Google API')
    ->setAuthConfig('/path/google_api_secret.json')
    ->setRedirectUri("http://{$_SERVER['HTTP_HOST']}" . dirname($_SERVER['PHP_SELF'])
    ->setAccessToken($accessToken)
    ->getClient();

// Simple way to get refreshed access token or false expired to skip
if ($accessToken = ClientHelper::refreshAccessToken()) {
    // saveAccessToken($accessToken)
}
```

> Helper handles the setting `setAccessType('offline')` & `setApprovalPrompt('force')` for refresh token.

#### verifyAccessToken()

Verify an access_token. This method will verify the current access_token by Google API, if one isn't provided.

```php
public static array|false verifyAccessToken(string $accessToken=null)
```

#### verifyScopes()

Verify scopes of tokenInfo by access_token. This method will verify the current access_token by Google API, if one isn't provided.

```php
public static array|false verifyScopes(array $scopes, string $accessToken=null)
```

*Example:*
```php
$result = \yidas\google\apiHelper\Client::verifyScopes([
    'https://www.googleapis.com/auth/userinfo.profile',
]);
```

### Implementation

There are more implementations such as `addScope()` or `createAuthUrl()` for OAuth register, you cloud refer following sample code:

[yidas/php-google-api-sample](https://github.com/yidas/php-google-api-sample)

---

GOOGLE SERVICES
---------------

You could directly use any Service Helpers which uses `Google_Client` from `yidas\google\apiHelper\Client`:

```php
use \yidas\google\apiHelper\services\People as PeopleHelper;
\yidas\google\apiHelper\Client::setClient([...])

$contacts = PeopleHelper::getSimpleContacts();
```

Or you could reset a `Google_Client` for each Service Helper:

```php
use \yidas\google\apiHelper\services\People as PeopleHelper;

PeopleHelper::setClient($googleClient);
// PeopleHelper::method()...
```

Use `getService()` to get back current Google Service object for advanced usage:

```php
$service = \yidas\google\apiHelper\services\People::getService();
// $service->people_connections->...
```

### People

People helper has smart call refered to [Google_Service_PeopleService_Person](https://github.com/google/google-api-php-client-services/blob/master/src/Google/Service/PeopleService/Person.php) methods, which provides easy interface to `setValue()` for a person.

```php
// Simple setValue() example
\yidas\google\apiHelper\services\People::newPerson
    ->setEmailAddresses('myintaer@gmail.com')
    ->setPhoneNumbers('+886')
    ->setBiographies("I'm a note");
```

#### Attributes

It's easy to set attributes for a person by Helper, which provides three types for input data:

##### 1. Origin Object

Input by original Google Attribute Classes that are not so convenience.

```php
$gPhoneNumber = new Google_Service_PeopleService_PhoneNumber;
$gPhoneNumber->setValue('+886');
\yidas\google\apiHelper\services\People::setPhoneNumbers($gPhoneNumber);
```

##### 2. Array

Input by array type would map to the API key-value setting.

```php
\yidas\google\apiHelper\services\People::setPhoneNumbers(['value' => '+886']);
```

##### 3. String

Input by string type would enable Helper attribute handler which automatically settles value for all attributes.

```php
\yidas\google\apiHelper\services\People::setPhoneNumbers('+886');
```

#### getSimpleContacts()

Get simple contact data with parser

```php
public static array getContacts()
```

*Example:*
```php
// Get formated list by Helper
$contacts = \yidas\google\apiHelper\services\People::getSimpleContacts();
```

Result:

```php
Array
(
    [0] => Array
        (
            [id] => people/c26081557840316580
            [name] => Mr.Nick
            [email] => 
            [phone] => 0912 345 678
        )
    ...
```

> This is simple fields parser, you could use `listPeopleConnections()` if you need all fields.

#### createContact()

Create a People Contact

```php
public static Google_Service_PeopleService_Person createContact()
```

*Example:*
```php
$person = \yidas\google\apiHelper\services\People::newPerson()
    ->setNames('Nick')
    ->setEmailAddresses('myintaer@gmail.com')
    ->setPhoneNumbers('+886')
    ->createContact();
```

> Resource Name: `$person->resourceName` or `$person['resourceName']`.

#### updateContact()

Update a People Contact

```php
public static Google_Service_PeopleService_PeopleEmpty updateContact(array $optParams=null)
```

*Example:*
```php
$person = \yidas\google\apiHelper\services\People::findByResource($resourceName)
    ->setNames('Nick')
    ->setEmailAddresses('myintaer@gmail.com')
    ->setPhoneNumbers('+886')
    ->updateContact();
```

#### deleteContact

Delete a People Contact

```php
public static Google_Service_PeopleService_PeopleEmpty deleteContact(string $resourceName=null, array $optParams=[])
```

*Example:*
```php
$person = \yidas\google\apiHelper\services\People::deleteContact($resourceName);
```

You could also use find pattern:

```php
$person = \yidas\google\apiHelper\services\People::findByResource($resourceName)
    ->deleteContact();
```

---

EXCEPTIONS
----------

For all Google Exception including Client and Services:

```php
try {} catch (\Google_Exception $e) {}
``` 

Otherwise, for Google Services only:

```php
try {} catch (\Google_Service_Exception $e) {}
```

---

REFERENCE
---------

- [Google API PHP Client SDK](https://github.com/google/google-api-php-client)

- [Google API PHP Client Serivces SDK](https://github.com/google/google-api-php-client-services)

- [Google Identity Platform](https://developers.google.com/identity/)

- [Google People API](https://developers.google.com/people/)

