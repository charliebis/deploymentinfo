<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Deployment Info JSON file path
    |--------------------------------------------------------------------------
    |
    | Your deployment script should write a file (name of your choosing but the
    | default is deployment-info.json) containing a JSON encoded array of
    | variables related to the deployment. This is expected to be Gitlab CI/CD
    | vars like CI_COMMIT_SHA, but it doesn't have to be. It can handle
    | multidimensional arrays.
    | The json_file_path should be the path to the JSON file, relative to the project
    | root. If the file exists in the project root, only the file name is needed.
    |
    */
    'json_file_path' => env('DEPLOYMENT_INFO_JSON_FILE_PATH', base_path('deployment-info.json')),
    /*
    |--------------------------------------------------------------------------
    | version_key tells DeploymentInfo which value in the JSON file constitutes the version of the app
    |--------------------------------------------------------------------------
    |
    | This is likely to be a Git version tag.
    |
    */
    'version_key' => env('CI_COMMIT_TAG', 'deployment-info.json'),
];
