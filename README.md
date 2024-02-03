# DeploymentInfo

## Parses (and makes available within your app) an array (possibly multidimensional) of values from a JSON encoded file that is placed in the project root during project deployment

This package is designed to parse a JSON encoded deployment variables file in your project root. The file should be created in the project by your CI/CD script and include vars that
are available during deployment such as the version tag, git commit hash, commit author, meta information about the deployment itself, etc.

The JSON encoded array can be multidimensional, offering flexibility in how you choose to structure your deployment information.

## Installation

1) Add the package repository to the repositories section of your composer.json, as in the below example:

```JSON
"repositories": [
  {
    "type": "vcs",
    "url": "https://github.com/charliebis/deploymentinfo.git"
  }
]
```

2) Add the service provider to the providers array in your app's config/app.php file, e.g.

```PHP
/*
* Other Service Providers...
*/
Bisutil\DeploymentInfo\Providers\DeploymentInfoServiceProvider::class,
```

3) Add the facade alias, also in your config/app.php file, e.g.

```PHP
'aliases' => Facade::defaultAliases()->merge([
    // 'ExampleClass' => App\Example\ExampleClass::class,
    'DeploymentInfo' => \Bisutil\DeploymentInfo\Facades\DeploymentInfoFacade::class,
])->toArray(),
```

4) Require the package in your Laravel application with the command:

````shell
composer require bisutil/deploymentinfo:^1.0.5
````

5) Ensure the JSON file exists in your project root and is called deployment-info.json. Alternatively, you can publish the
config file using...

```shell
php artisan vendor:publish --provider="Bisutil\DeploymentInfo\Providers\DeploymentInfoServiceProvider"
````

...and change the json_file_path in the deployment-info.php config file which now exists in your app's config directory.


## Usage

The DeploymentInfo facade will now be available in your app. Example usage:

```PHP
DeploymentInfo::getStatus()
```

Returns 'success' or 'error'. If it is 'error', you can use

```PHP
DeploymentInfo::getError()
```

To get the error message. It is likely due to the JSON file not existing or containing invalid JSON

```PHP
DeploymentInfo::getVersion()
```

Get the version number of the deployment.

```PHP
DeploymentInfo::getTotal()
```

Returns the total number of vars found in the JSON. It counts the leaf nodes of the (potentially) multidimensional array

```PHP
DeploymentInfo::getDeploymentInfo()
```

Returns an array of the vars that are in the JSON file

```PHP    
DeploymentInfo::getDeploymentInfoValueByKey(string $key)
```

Returns a specific var from the JSON file, as named in the $key parameter. Use dot-notation to specify a key in a
sub-array eg

```PHP
DeploymentInfo::getDeploymentInfoValueByKey('subarraykey.varname')
```

## Example

A real-world use for this package would be to display the hash (or short hash) of the most recent commit in the footer of your site. Example snippet that could be included in your footer blade:

```php
@if(DeploymentInfo::getStatus() === 'success')
    <p class="mt-6">{{ DeploymentInfo::getDeploymentInfoValueByKey('CI_COMMIT_SHORT_SHA') }}</p>
@endif
```
