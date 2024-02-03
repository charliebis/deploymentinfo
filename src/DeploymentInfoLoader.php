<?php

namespace BISUtil\DeploymentInfo;

/**
 * Class DeploymentInfoLoader
 *
 * @package BISUtil\DeploymentInfo
 *
 * Represents information about a deployment.
 */
class DeploymentInfoLoader
{
    /**
     * @var string
     */
    protected string $status = '';
    /**
     * @var string
     */
    protected string $versionKey = '';
    /**
     * @var string
     */
    protected string $error = '';
    /**
     * @var int
     */
    protected int $total = 0;
    /**
     * @var array
     */
    protected array $deploymentInfo = [];
    /**
     * @var string
     */
    private string $jsonPath;
    /**
     * @var bool
     */
    private bool $isJsonPathValid = false;


    /**
     * @param string $jsonPath
     * @param string $versionKey
     */
    public function __construct(string $jsonPath, string $versionKey) {
        $this->setVersionKey($versionKey);
        $this->reset($jsonPath);
    }


    /**
     * Counts the number of configurations in the deployment information.
     *
     * This method recursively counts the number of configurations in the given deployment information array.
     *
     * @param array $deploymentInfo The deployment information array.
     *
     * @return int The number of configurations in the deployment information.
     */
    protected function countConfigs(array $deploymentInfo): int {
        $count = 0;
        foreach ($deploymentInfo as $element) {
            if (is_array($element)) {
                $count += $this->countConfigs($element);
            }
            else {
                $count++;
            }
        }

        return $count;
    }


    /**
     * Load the data from the file and parse the JSON data into an associative array
     *
     * @return void
     */
    protected function loadDeploymentInfo(): void {
        if ($this->isJsonPathValid) {
            $deploymentInfo = trim(file_get_contents($this->jsonPath));
            $deploymentInfo = json_decode($deploymentInfo, true);
            if (json_last_error() === JSON_ERROR_NONE) {
                $this->deploymentInfo = $deploymentInfo;
                $totalConfigVars      = $this->countConfigs($deploymentInfo);
                $this->status         = 'success';
                $this->total          = $totalConfigVars;
            }
            else {
                $this->status = 'error';
                $this->total  = 0;
                $this->error  = 'Deployment info file does not contain valid JSON';
            }
        }
        else {
            $this->status = 'error';
            $this->total  = 0;
            $this->error  = 'Deployment info file does not exist or does not have a .json extension';
        }
    }


    /**
     * Set the path of the JSON file that contains the deployment info values
     * we need to load in. Checks the file path is valid first.
     *
     * @param string $jsonPath
     *
     * @return void
     */
    protected function setJsonPath(string $jsonPath): void {
        if ($this->isJsonPathValid($jsonPath)) {
            $this->isJsonPathValid = true;
            $this->jsonPath        = $jsonPath;
        }
    }


    /**
     * Sets the version key. This is used to set the $this->version when loading in the values from
     * the JSON file.
     *
     * @param string $versionKey The version key to be set.
     *
     * @return void
     */
    public function setVersionKey(string $versionKey): void {
        $this->versionKey = $versionKey;
    }


    /**
     * Retrieves the deployment information.
     *
     * This method returns an array containing the deployment information.
     *
     * @return array The deployment information represented as an array.
     */
    public function getDeploymentInfo(): array {
        return $this->deploymentInfo;
    }


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
    public function getDeploymentInfoValueByKey(string $key): mixed {
        $keys           = explode('.', $key);
        $deploymentInfo = $this->deploymentInfo;

        foreach ($keys as $segment) {
            if (!is_array($deploymentInfo) || !array_key_exists($segment, $deploymentInfo)) {
                return null;
            }

            $deploymentInfo = $deploymentInfo[$segment];
        }

        return $deploymentInfo;
    }


    /**
     * Retrieves the error message.
     *
     * This method returns the error message stored in the class.
     *
     * @return string
     */
    public function getError(): string {
        return $this->error;
    }


    /**
     * Retrieves the status of the deployment info load-in.
     *
     * This method returns the status of the deployment info load-in.
     *
     * @return string The status of the deployment info load-in.
     */
    public function getStatus(): string {
        return $this->status;
    }


    /**
     * Returns the total number of configs.
     *
     * @return int The total value.
     */
    public function getTotal(): int {
        return $this->total;
    }


    /**
     * Retrieves the version of the app
     *
     * @return string The version of the software.
     */
    public function getVersion(): string {
        return $this->getDeploymentInfoValueByKey($this->versionKey);
    }


    /**
     * Checks the JSON file path is valid by checking the file exists and that its file extension is .json
     *
     * @param string $jsonPath
     *
     * @return bool
     */
    public function isJsonPathValid(string $jsonPath): bool {
        if (!file_exists($jsonPath)) {
            return false;
        }

        $extension = pathinfo($jsonPath, PATHINFO_EXTENSION);
        if ($extension !== 'json') {
            return false;
        }

        return true;
    }


    /**
     * Set the JSON file path then load the deployment
     * info data from it
     *
     * @param string $jsonPath
     *
     * @return void
     */
    public function reset(string $jsonPath): void {
        $this->setJsonPath($jsonPath);
        $this->loadDeploymentInfo();
    }
}
