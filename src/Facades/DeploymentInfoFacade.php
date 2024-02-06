<?php

namespace BISUtil\DeploymentInfo\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * Class DeploymentInfoFacade
 *
 * @package BISUtil\DeploymentInfo
 * @see \BISUtil\DeploymentInfo\DeploymentInfoServiceProvider
 * @method __construct(string $jsonPath, string $versionKey)
 * @method void setVersionKey(string $versionKey)
 * @method array getDeploymentInfo()
 * @method mixed getDeploymentInfoValueByKey(string $key)
 * @method string getError()
 * @method string getStatus()
 * @method int getTotal()
 * @method string getVersion()
 * @method bool isJsonPathValid(string $jsonPath)
 * @method bool reset(string $jsonPath)
 */
class DeploymentInfoFacade extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor(): string {
        // This should match the alias you will bind your service to in the service provider
        return 'DeploymentInfoFacade';
    }
}
