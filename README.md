# DeploymentInfo

Requires PHP >= 8.1 and Laravel >= 8.

## Parses (and makes available within your app) an array (possibly multidimensional) of values from a JSON encoded file that is placed in the project root during project deployment

This package is designed to parse a JSON encoded deployment variables file in your project root. The file should be created in the project by your CI/CD script and include vars that
are available during deployment such as the version tag, git commit hash, commit author, meta information about the deployment itself, etc.

The JSON encoded array can be multidimensional, offering flexibility in how you choose to structure your deployment information.

## Installation

1) Add the package repository to the repositories section of your composer.json. From your project root, use the command below:

```shell
composer config repositories.deploymentinfo vcs https://github.com/charliebis/deploymentinfo.git
```

2) Require the package in your Laravel application. From your project root, use the command below:

````shell
composer require bisutil/deploymentinfo:^1.0.12
````

3) Ensure the JSON file exists in your project root and is called deployment-info.json. Alternatively, you can publish the
config file using...

```shell
php artisan vendor:publish --provider="BISUtil\DeploymentInfo\Providers\DeploymentInfoServiceProvider"
````

...and change the json_file_path in the deployment-info.php config file which now exists in your app's config directory.


## Usage

Your deployment process must create a .json file in your project root. The default filename, as set in the package config file, is deployment-info.json.
As mentioned above, you can publish the config file and set the .json filename to one of your choosing.

Here is an example of a multi-line command that can be used in a Gitlab CI/CD pipeline script to create the file.

```yaml
- |
      echo '{' > $DEPLOYMENT_INFO_FILE
      echo '"CI_COMMIT_TAG": "'$CI_COMMIT_TAG'",' >> $DEPLOYMENT_INFO_FILE
      echo '"CI_COMMIT_TAG_MESSAGE": "'$CI_COMMIT_TAG_MESSAGE'",' >> $DEPLOYMENT_INFO_FILE
      echo '"CI_COMMIT_TIMESTAMP": "'$CI_COMMIT_TIMESTAMP'",' >> $DEPLOYMENT_INFO_FILE
      echo '"CI_COMMIT_SHA": "'$CI_COMMIT_SHA'",' >> $DEPLOYMENT_INFO_FILE
      echo '"CI_COMMIT_SHORT_SHA": "'$CI_COMMIT_SHORT_SHA'",' >> $DEPLOYMENT_INFO_FILE
      echo '"CI_COMMIT_AUTHOR": "'$CI_COMMIT_AUTHOR'",' >> $DEPLOYMENT_INFO_FILE
      echo '"CI_COMMIT_BEFORE_SHA": "'$CI_COMMIT_BEFORE_SHA'",' >> $DEPLOYMENT_INFO_FILE
      echo '"CI_COMMIT_BRANCH": "'$CI_COMMIT_BRANCH'",' >> $DEPLOYMENT_INFO_FILE
      echo '"CI_DEFAULT_BRANCH": "'$CI_DEFAULT_BRANCH'",' >> $DEPLOYMENT_INFO_FILE
      echo '"CI_JOB_ID": "'$CI_JOB_ID'",' >> $DEPLOYMENT_INFO_FILE
      echo '"CI_PROJECT_ID": "'$CI_PROJECT_ID'",' >> $DEPLOYMENT_INFO_FILE
      echo '"CI_PROJECT_NAME": "'$CI_PROJECT_NAME'",' >> $DEPLOYMENT_INFO_FILE
      echo '"CI_PROJECT_TITLE": "'$CI_PROJECT_TITLE'",' >> $DEPLOYMENT_INFO_FILE
      echo '"CI_RUNNER_ID": "'$CI_RUNNER_ID'",' >> $DEPLOYMENT_INFO_FILE
      echo '"CI_RUNNER_DESCRIPTION": "'$CI_RUNNER_DESCRIPTION'"' >> $DEPLOYMENT_INFO_FILE
      echo '}' >> $DEPLOYMENT_INFO_FILE
```

In this case, the $DEPLOYMENT_INFO_FILE var is set near the top of the pipeline script. For example:

```yaml
variables:
  DEPLOYMENT_INFO_FILE: "deployment-info.json"
```

This will save the deployment vars that you set here, into the .json file specified by $DEPLOYMENT_INFO_FILE, when your deployment pipeline runs.

Once your application has deployed, the DeploymentInfo facade will now be available in your app. A number of methods are available for use:
```PHP
/**
 * Sets the version key. This is used to set the $this->version when loading in the values from
 * the JSON file.
 *
 * @param string $versionKey The version key to be set.
 *
 * @return void
 */
DeploymentInfo::setVersionKey()
```

```php
/**
 * Retrieves the deployment information.
 *
 * This method returns an array containing the deployment information.
 *
 * @return array The deployment information represented as an array.
 */
DeploymentInfo::getDeploymentInfo()
```

```php
/**
 * Retrieves the value from the deployment info by the given key.
 *
 * This method returns the value associated with the specified key from the deployment info.
 * The deployment info is a multidimensional array loaded previously.
 * The key can be a dot-separated string representing the path to the desired value.
 *
 * @param string $key The key representing the path to the desired value in the deployment info.
 *
 * @return string|array|null The value associated with the specified key, or null if the key does not exist.
 */
DeploymentInfo::getDeploymentInfoValueByKey()
```

```php
/**
 * Retrieves the error message.
 *
 * This method returns the error message stored in the class.
 *
 * @return string
 */
DeploymentInfo::getError()
```

```php
/**
 * Retrieves the status of the deployment info load-in.
 *
 * This method returns the status of the deployment info load-in.
 *
 * @return string The status of the deployment info load-in.
 */
DeploymentInfo::getStatus()
```

```php
/**
 * Retrieves the version of the app
 *
 * @return string The version of the software.
 */
DeploymentInfo::getTotal()
```

```php
/**
 * Retrieves the version of the app
 *
 * @return string The version of the software.
 */
DeploymentInfo::getVersion()
```

```php
/**
 * Checks the JSON file path is valid by checking the file exists and that its file extension is .json
 *
 * @param string $jsonPath
 *
 * @return bool
 */
DeploymentInfo::isJsonPathValid()
```

```php
/**
 * Set the JSON file path then load the deployment
 * info data from it
 *
 * @param string $jsonPath
 *
 * @return void
 */
DeploymentInfo::reset()
```

Example usage:

```PHP
DeploymentInfo::getStatus()
```

Returns 'success' or 'error'. If it is 'error', you can use

```PHP
DeploymentInfo::getVersiom()
```

Returns the value set as the version of the app (not of the package). You can specify a key in your JSON array to be the "version". This is set in the package config file. This is likely
to be CI_COMMIT_TAG, assuming you use a Gitlab tag-triggered deployment.

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
    @if(!empty(DeploymentInfo::getVersion()))
        <p class="mt-6">{{ DeploymentInfo::getVersion() }}</p>
    @elseif(!empty(DeploymentInfo::getDeploymentInfoValueByKey('CI_COMMIT_SHA')))
        <p class="mt-6">{{ DeploymentInfo::getDeploymentInfoValueByKey('CI_COMMIT_SHA') }}</p>
    @endif
@endif
```
