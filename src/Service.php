<?php

namespace byarashboev\aws\s3;

use Aws\S3\S3Client;
use byarashboev\aws\s3\interfaces\commands\Command;
use byarashboev\aws\s3\interfaces\HandlerResolver as HandlerResolverInterface;
use byarashboev\aws\s3\interfaces\Service as ServiceInterface;

/**
 * Class Service
 *
 * Yii1 application component for Amazon S3.
 *
 * @method \Aws\ResultInterface  get(string $filename)
 * @method \Aws\ResultInterface  put(string $filename, $body)
 * @method \Aws\ResultInterface  delete(string $filename)
 * @method \Aws\ResultInterface  upload(string $filename, $source)
 * @method \Aws\ResultInterface  restore(string $filename, int $days)
 * @method \Aws\ResultInterface  list(string $prefix)
 * @method bool                  exist(string $filename)
 * @method string                getUrl(string $filename)
 * @method string                getPresignedUrl(string $filename, $expires)
 *
 * @package byarashboev\aws\s3
 */
class Service extends \CApplicationComponent implements ServiceInterface
{
    /** @var string */
    public $defaultBucket = '';

    /** @var string */
    public $defaultAcl = '';

    /** @var string */
    public $endpoint = '';

    /** @var \Aws\Credentials\CredentialsInterface|array|callable */
    public $credentials;

    /** @var string */
    public $region = '';

    /** @var bool */
    public $usePathStyleEndpoint = false;

    /** @var array|bool */
    public $debug = false;

    /** @var array */
    public $httpOptions = [];

    /** @var array internal components cache */
    private $components = [];

    /**
     * Initializes the component.
     *
     * @throws \CException
     */
    public function init()
    {
        parent::init();

        if (empty($this->credentials)) {
            throw new \CException('The "credentials" property must be set.');
        }

        if (empty($this->region)) {
            throw new \CException('The "region" property must be set.');
        }

        if (empty($this->defaultBucket)) {
            throw new \CException('The "defaultBucket" property must be set.');
        }
    }

    /**
     * Executes a command.
     *
     * @param Command $command
     *
     * @return mixed
     */
    public function execute(Command $command)
    {
        return $this->getBus()->execute($command);
    }

    /**
     * Creates a command with default params.
     *
     * @param string $commandClass
     *
     * @return Command
     */
    public function create(string $commandClass): Command
    {
        return $this->getBuilder()->build($commandClass);
    }

    /**
     * Returns command factory.
     *
     * @return CommandFactory
     */
    public function commands(): CommandFactory
    {
        return $this->getFactory();
    }

    /**
     * Returns handler resolver.
     *
     * @return HandlerResolverInterface
     */
    public function getResolver(): HandlerResolverInterface
    {
        return $this->getComponent('resolver');
    }

    /**
     * @param string $name
     * @param array  $params
     *
     * @return mixed
     */
    public function __call($name, $params)
    {
        if (method_exists($this->commands(), $name)) {
            $result = call_user_func_array([$this->commands(), $name], $params);

            return $result instanceof Command ? $this->execute($result) : $result;
        }

        return parent::__call($name, $params);
    }

    /**
     * @return S3Client
     */
    protected function getClient(): S3Client
    {
        return $this->getComponent('client');
    }

    /**
     * @return Bus
     */
    protected function getBus(): Bus
    {
        return $this->getComponent('bus');
    }

    /**
     * @return CommandBuilder
     */
    protected function getBuilder(): CommandBuilder
    {
        return $this->getComponent('builder');
    }

    /**
     * @return CommandFactory
     */
    protected function getFactory(): CommandFactory
    {
        return $this->getComponent('factory');
    }

    /**
     * @param string $name
     *
     * @return object
     */
    protected function getComponent(string $name)
    {
        if (!isset($this->components[$name])) {
            $this->components[$name] = $this->createComponent($name);
        }

        return $this->components[$name];
    }

    /**
     * @param string $name
     *
     * @return object
     */
    protected function createComponent(string $name)
    {
        switch ($name) {
            case 'client':
                return $this->createS3Client();
            case 'resolver':
                return new HandlerResolver($this->getClient());
            case 'bus':
                return new Bus($this->getResolver());
            case 'builder':
                return new CommandBuilder($this->getBus(), $this->defaultBucket, $this->defaultAcl);
            case 'factory':
                return new CommandFactory($this->getBuilder());
            default:
                throw new \CException("Unknown component: {$name}");
        }
    }

    /**
     * @return S3Client
     */
    protected function createS3Client(): S3Client
    {
        $config = [
            'version'     => '2006-03-01',
            'region'      => $this->region,
            'credentials' => $this->credentials,
        ];

        if (!empty($this->endpoint)) {
            $config['endpoint'] = $this->endpoint;
        }

        if ($this->usePathStyleEndpoint) {
            $config['use_path_style_endpoint'] = true;
        }

        if ($this->debug) {
            $config['debug'] = $this->debug;
        }

        if (!empty($this->httpOptions)) {
            $config['http'] = $this->httpOptions;
        }

        return new S3Client($config);
    }
}
