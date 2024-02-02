# DeploymentInfo

## Parses JSON encoded deployment vars from text file in the project

This package is designed to parse a JSON encoded deployment variables file within your project. The file should be created in the project by your CI/CD script and include vars that are available during deployment such as the git commit hash, commit author, meta information about the deployment itself, etc.

The JSON encoded array can be multidimensional, offering flexibility in how you choose to structure your deployment information.

## Installation

Add the following line to your composer.json file:
"Bisutil\\DeploymentInfo\\": "packages/bisutil/deploymentinfo/src/"

to the autoload section. For example:

```JSON
"autoload": {
    "psr-4": {
        "App\\": "app/",
        "Database\\Factories\\": "database/factories/",
        "Database\\Seeders\\": "database/seeders/",
        "Bisutil\\DeploymentInfo\\": "packages/bisutil/deploymentinfo/src/"
    }
},
```

## Usage

Ensure the JSON file exists in the project root and is called deployment-info.json. Alternatively, you can publish the
config file using

```shell
php artisan vendor:publish
````

and change the json_file_path in the deployment-info.php config file.

Add the service provider in the config/app.php file, e.g.

```PHP
/*
* Other Service Providers...
*/
Bisutil\DeploymentInfo\Providers\DeploymentInfoServiceProvider::class,
```

and the facade alias, e.g.

```PHP
'aliases' => Facade::defaultAliases()->merge([
    // 'ExampleClass' => App\Example\ExampleClass::class,
    'DeploymentInfo' => \Bisutil\DeploymentInfo\Facades\DeploymentInfoFacade::class,
])->toArray(),
```

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
<p class="mt-6">{{ DeploymentInfo::getDeploymentInfoValueByKey('CI_COMMIT_SHA') }}</p>
@endif
```
