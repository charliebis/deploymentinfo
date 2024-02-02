<?php

namespace Bisutil\DeploymentInfo\Tests;

use Bisutil\DeploymentInfo\DeploymentInfoLoader;
use PHPUnit\Framework\TestCase;

/**
 * Class DeploymentInfoLoaderTest
 *
 * This class provides unit tests for the DeploymentInfoLoader class.
 */
class DeploymentInfoLoaderTest extends TestCase
{
    /**
     * @var array|string[]
     */
    private array $mockJsonFiles = [];
    /**
     * @var DeploymentInfoLoader
     */
    private DeploymentInfoLoader $loader;


    /**
     * Set up the test environment before each test method is run.
     *
     * This method is called automatically before each test method is executed.
     * It is used to perform any necessary setup actions, such as initializing objects and variables.
     *
     * @return void
     */
    protected function setUp(): void {
        parent::setUp();
        //  Mock data files. Includes valid JSON and a some types of invalid data
        $fixturesPath        = __DIR__ . '/fixtures/mocks/';
        $this->mockJsonFiles = [
            'valid_one_dimensional'     => $fixturesPath . 'valid_one_dimensional.json',
            'valid_two_dimensional'     => $fixturesPath . 'valid_two_dimensional.json',
            'valid_three_dimensional'   => $fixturesPath . 'valid_three_dimensional.json',
            'invalid_trailing_comma'    => $fixturesPath . 'invalid_trailing_comma.json',
            'invalid_not_object'        => $fixturesPath . 'invalid_not_object.json',
            'not_a_json_file_extension' => $fixturesPath . 'not_a_json_file_extension.txt',
            'empty_string'              => $fixturesPath . 'empty_string.json'
        ];
        $this->loader = new DeploymentInfoLoader($this->mockJsonFiles['valid_one_dimensional']);
    }


    /**
     * Test if the 'DeploymentInfoLoader' class can be instantiated.
     *
     * This method verifies that the 'DeploymentInfoLoader' class can be successfully instantiated
     * by using the 'assertInstanceOf' assertion. It checks if the class being tested is an instance
     * of the specified class, which in this case is 'DeploymentInfoLoader'.
     *
     * @return void
     */
    public function testCanBeInstantiated(): void {
        $this->assertInstanceOf(DeploymentInfoLoader::class, $this->loader);
    }


    /**
     * Test the load result method.
     *
     * This method is used to test the loadResult() method of the DeploymentInfoLoader class.
     * It checks the status returned by the getStatus() method and asserts that it is either 'success' or 'error'.
     *
     * @return void
     */
    public function testLoadResult(): void {
        //  Test a successful load of valid data
        $this->loader->reset($this->mockJsonFiles['valid_one_dimensional']);
        $this->assertEquals('success', $this->loader->getStatus());
        //  Test a failed load of an empty data file
        $this->loader->reset($this->mockJsonFiles['empty_string']);
        $this->assertEquals('error', $this->loader->getStatus());
    }


    /**
     * Test the errorMessage() method of the DeploymentInfoLoader class.
     *
     * This method retrieves the error message from the DeploymentInfoLoader object and asserts that it is of type string.
     *
     * @return void
     */
    public function testNotAnErrorWhenJsonFileIsValid(): void {
        //  Instantiate DeploymentInfoLoader with a valid JSON path
        $this->loader->reset($this->mockJsonFiles['valid_one_dimensional']);
        $this->assertEquals('success', $this->loader->getStatus());
        $errorMessage = $this->loader->getError();
        //  Error message should be empty, since valid JSON file was given
        $this->assertIsString($errorMessage);
        $this->assertEmpty($errorMessage);
    }


    /**
     * Test how DeploymentInfoLoader handles bad JSON files.
     *
     * @return void
     */
    public function testErrorWhenJsonFileIsInvalid(): void {
        //  Instantiate DeploymentInfoLoader with an invalid JSON path
        $this->loader->reset($this->mockJsonFiles['invalid_trailing_comma']);
        $this->assertEquals('error', $this->loader->getStatus());
        $errorMessage = $this->loader->getError();
        //  Error message should not be empty, since invalid JSON file was given
        $this->assertIsString($errorMessage);
        $this->assertNotEmpty($errorMessage);
        //  Instantiate DeploymentInfoLoader with another invalid JSON path
        $this->loader->reset($this->mockJsonFiles['invalid_not_object']);
        $this->assertEquals('error', $this->loader->getStatus());
        $errorMessage = $this->loader->getError();
        //  Error message should not be empty, since invalid JSON file was given
        $this->assertIsString($errorMessage);
        $this->assertNotEmpty($errorMessage);
        //  Instantiate DeploymentInfoLoader with another invalid JSON path
        $this->loader->reset($this->mockJsonFiles['empty_string']);
        $this->assertEquals('error', $this->loader->getStatus());
        $errorMessage = $this->loader->getError();
        //  Error message should not be empty, since invalid JSON file was given
        $this->assertIsString($errorMessage);
        $this->assertNotEmpty($errorMessage);
        //  Instantiate DeploymentInfoLoader with another invalid JSON path
        $this->loader->reset($this->mockJsonFiles['not_a_json_file_extension']);
        $this->assertEquals('error', $this->loader->getStatus());
        $errorMessage = $this->loader->getError();
        //  Error message should not be empty, since invalid JSON file was given
        $this->assertIsString($errorMessage);
        $this->assertNotEmpty($errorMessage);
    }


    /**
     * Checks the countConfigs() function is counting leaf nodes correctly. The total returned
     * by getTotal() should be the number of leaf nodes in the array, not the number of top level elements
     *
     * @return void
     */
    public function testCountOfTotalConfigs(): void {
        //  Test that a 1d array with 5 elements returns a total of 5
        $this->loader->reset($this->mockJsonFiles['valid_one_dimensional']);
        $total  = $this->loader->getTotal();
        $this->assertIsInt($total);
        $this->assertEquals(5, $total);
        //  Test that a 2d array with 10 leaf elements returns a total of 10
        $this->loader->reset($this->mockJsonFiles['valid_two_dimensional']);
        $total = $this->loader->getTotal();
        $this->assertIsInt($total);
        $this->assertEquals(10, $total);
        //  Test that a 3d array with 15 leaf elements returns a total of 15
        $this->loader->reset($this->mockJsonFiles['valid_three_dimensional']);
        $total = $this->loader->getTotal();
        $this->assertIsInt($total);
        $this->assertEquals(15, $total);
        //  Test that an invalid JSON file causes getTotal() to return 0
        $this->loader->reset($this->mockJsonFiles['empty_string']);
        $total = $this->loader->getTotal();
        $this->assertIsInt($total);
        $this->assertEquals(0, $total);
    }


    /**
     * Test if the deployment info is valid.
     *
     * This method retrieves the deployment info using the DeploymentInfoLoader,
     * and then asserts that the retrieved info is an array.
     * It is used to ensure that the deployment info is formatted correctly.
     *
     * @return void
     */
    public function testDeploymentInfoIsValid(): void {
        //  The valid_one_dimensional file is a 1d array of 5 elements
        $this->loader->reset($this->mockJsonFiles['valid_one_dimensional']);
        $deploymentInfo = $this->loader->getDeploymentInfo();
        $this->assertIsArray($deploymentInfo);
        $this->assertCount(5, $deploymentInfo);
        //  The valid_two_dimensional file is a 2d array of 6 elements (one of which is an array of x elements)
        $this->loader->reset($this->mockJsonFiles['valid_two_dimensional']);
        $deploymentInfo = $this->loader->getDeploymentInfo();
        $this->assertIsArray($deploymentInfo);
        //  So there should be 6 elements
        $this->assertCount(6, $deploymentInfo);
        //  The valid_two_dimensional file is a 3d array of 6 elements (one of which is an array of x elements, one of those being an array of x elements)
        $this->loader->reset($this->mockJsonFiles['valid_three_dimensional']);
        $deploymentInfo = $this->loader->getDeploymentInfo();
        $this->assertIsArray($deploymentInfo);
        //  So there should still be 6 elements
        $this->assertCount(6, $deploymentInfo);
    }


    /**
     * Test the behavior of the getDeploymentInfoValueByKey method when the key is valid.
     *
     * This method verifies that the getDeploymentInfoValueByKey method returns a value
     * when the specified key is valid. Includes 2d and 3d arrays.
     *
     * @return void
     */
    public function testGetDeploymentInfoByKeyIsValid(): void {
        //  Test that getDeploymentInfoValueByKey() returns the expected value for the given key in a 1d array
        $this->loader->reset($this->mockJsonFiles['valid_one_dimensional']);
        $deploymentInfoValue = $this->loader->getDeploymentInfoValueByKey('CI_COMMIT_SHA');
        $this->assertEquals('ThisIsATestCommitSHA', $deploymentInfoValue);
        //  Test that getDeploymentInfoValueByKey() returns the expected value for the given key in the 2nd dim of a 2d array
        $this->loader->reset($this->mockJsonFiles['valid_two_dimensional']);
        $deploymentInfoValue = $this->loader->getDeploymentInfoValueByKey('MULTI.CI_COMMIT_SHA_2ND');
        $this->assertEquals('ThisIsATestCommitSHAMulti2', $deploymentInfoValue);
        //  Test that getDeploymentInfoValueByKey() returns the expected value for the given key in the 3rd dim of a 3d array
        $this->loader->reset($this->mockJsonFiles['valid_three_dimensional']);
        $deploymentInfoValue = $this->loader->getDeploymentInfoValueByKey('MULTI.MULTI_2ND.CI_COMMIT_SHA_3RD');
        $this->assertEquals('ThisIsATestCommitSHAMulti3', $deploymentInfoValue);
    }


    /**
     * Test for the getDeploymentInfoValueByKey() method when the key is bad.
     *
     * This test ensures that the getDeploymentInfoValueByKey() method returns null when given a non-existent key.
     * It uses a mock JSON file containing valid one-dimensional data to create a DeploymentInfoLoader object,
     * and then calls the getDeploymentInfoValueByKey() method with a non-existent key.
     * Finally, it asserts that the returned value is null.
     *
     * @return void
     */
    public function testGetDeploymentInfoByKeyIsNull(): void {
        //  Test that getDeploymentInfoValueByKey() returns null when given a non-existent key
        $this->loader->reset($this->mockJsonFiles['valid_one_dimensional']);
        $deploymentInfoValue = $this->loader->getDeploymentInfoValueByKey('non_existent_key');
        $this->assertNull($deploymentInfoValue);
    }
}
