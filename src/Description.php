<?php

namespace Shopify;

use GuzzleHttp\Command\Guzzle;
use GuzzleHttp\Command\Guzzle\DescriptionInterface;
use GuzzleHttp\Url;

/**
 * Class Description
 *
 * Originally this class appeared to attempt to make the baseUrl attribute over-writable, however,
 * it wouldn't have ever worked because the parent class has the attribute set as private, so that
 * no child class can access it anywhere.
 *
 * To keep in the spirit of the original implementation, this has been updated to a version that
 * works, which basically requires wholesale copying and pasting of the original parent
 * implementation.
 *
 * All protected properties have also been modified to be protected so changes like originally
 * intended can work.
 *
 * @package Shopify
 */
class Description implements DescriptionInterface
{
    const CONFIG_BASE_URL_KEY = 'baseUrl';

    /** @var array Array of {@see OperationInterface} objects */
    protected $operations = [];

    /** @var array Array of API models */
    protected $models = [];

    /** @var string Name of the API */
    protected $name;

    /** @var string API version */
    protected $apiVersion;

    /** @var string Summary of the API */
    protected $description;

    /** @var array Any extra API data */
    protected $extraData = [];

    /** @var Url baseUrl/basePath */
    protected $baseUrl;

    /** @var Guzzle\SchemaFormatter */
    protected $formatter;

    /**
     * @param array $config  Service description data
     * @param array $options Custom options to apply to the description
     *     - formatter: Can provide a custom SchemaFormatter class
     *
     * @throws \InvalidArgumentException
     */
    public function __construct(array $config, array $options = [])
    {
        // Keep a list of default keys used in service descriptions that is
        // later used to determine extra data keys.
        static $defaultKeys = ['name', 'models', 'apiVersion', 'description'];

        // Pull in the default configuration values
        foreach ($defaultKeys as $key) {
            if (isset($config[$key])) {
                $this->{$key} = $config[$key];
            }
        }

        // Set the baseUrl
        $this->baseUrl = Url::fromString(isset($config['baseUrl']) ? $config['baseUrl'] : '');

        // Ensure that the models and operations properties are always arrays
        $this->models = (array) $this->models;
        $this->operations = (array) $this->operations;

        // We want to add operations differently than adding the other properties
        $defaultKeys[] = 'operations';

        // Create operations for each operation
        if (isset($config['operations'])) {
            foreach ($config['operations'] as $name => $operation) {
                if (!is_array($operation)) {
                    throw new \InvalidArgumentException('Operations must be arrays');
                }
                $this->operations[$name] = $operation;
            }
        }

        // Get all of the additional properties of the service description and
        // store them in a data array
        foreach (array_diff(array_keys($config), $defaultKeys) as $key) {
            $this->extraData[$key] = $config[$key];
        }

        // Configure the schema formatter
        if (isset($options['formatter'])) {
            $this->formatter = $options['formatter'];
        } else {
            static $defaultFormatter;
            if (!$defaultFormatter) {
                $defaultFormatter = new Guzzle\SchemaFormatter();
            }
            $this->formatter = $defaultFormatter;
        }
    }

    /**
     * Get the basePath/baseUrl of the description
     *
     * @return Url
     */
    public function getBaseUrl()
    {
        return $this->baseUrl;
    }

    /**
     * @param Url $baseUrl
     */
    public function setBaseUrl($baseUrl)
    {
        $this->baseUrl = $baseUrl;
    }

    /**
     * Get the API operations of the service
     *
     * @return Guzzle\Operation[] Returns an array of {@see Operation} objects
     */
    public function getOperations()
    {
        return $this->operations;
    }

    /**
     * Check if the service has an operation by name
     *
     * @param string $name Name of the operation to check
     *
     * @return bool
     */
    public function hasOperation($name)
    {
        return isset($this->operations[$name]);
    }

    /**
     * Get an API operation by name
     *
     * @param string $name Name of the command
     *
     * @return Guzzle\Operation
     * @throws \InvalidArgumentException if the operation is not found
     */
    public function getOperation($name)
    {
        if (!$this->hasOperation($name)) {
            throw new \InvalidArgumentException("No operation found named $name");
        }

        // Lazily create operations as they are retrieved
        if (!($this->operations[$name] instanceof Guzzle\Operation)) {
            $this->operations[$name]['name'] = $name;
            $this->operations[$name] = new Guzzle\Operation($this->operations[$name], $this);
        }

        return $this->operations[$name];
    }

    /**
     * Get a shared definition structure.
     *
     * @param string $id ID/name of the model to retrieve
     *
     * @return Guzzle\Parameter
     * @throws \InvalidArgumentException if the model is not found
     */
    public function getModel($id)
    {
        if (!$this->hasModel($id)) {
            throw new \InvalidArgumentException("No model found named $id");
        }

        // Lazily create models as they are retrieved
        if (!($this->models[$id] instanceof Guzzle\Parameter)) {
            $this->models[$id] = new Guzzle\Parameter(
                $this->models[$id],
                ['description' => $this]
            );
        }

        return $this->models[$id];
    }

    /**
     * Get all models of the service description.
     *
     * @return array
     */
    public function getModels()
    {
        $models = [];
        foreach ($this->models as $name => $model) {
            $models[$name] = $this->getModel($name);
        }

        return $models;
    }

    /**
     * Check if the service description has a model by name.
     *
     * @param string $id Name/ID of the model to check
     *
     * @return bool
     */
    public function hasModel($id)
    {
        return isset($this->models[$id]);
    }

    /**
     * Get the API version of the service
     *
     * @return string
     */
    public function getApiVersion()
    {
        return $this->apiVersion;
    }

    /**
     * Get the name of the API
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Get a summary of the purpose of the API
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Format a parameter using named formats.
     *
     * @param string $format Format to convert it to
     * @param mixed  $input  Input string
     *
     * @return mixed
     */
    public function format($format, $input)
    {
        return $this->formatter->format($format, $input);
    }

    /**
     * Get arbitrary data from the service description that is not part of the
     * Guzzle service description specification.
     *
     * @param string $key Data key to retrieve or null to retrieve all extra
     *
     * @return null|mixed
     */
    public function getData($key = null)
    {
        if ($key === null) {
            return $this->extraData;
        } elseif (isset($this->extraData[$key])) {
            return $this->extraData[$key];
        } else {
            return null;
        }
    }
}
