<?php
declare(strict_types=1);

namespace Tests\Integration;

use Ploi\Ploi;
use PHPUnit\Framework\TestCase as PHPUnitTestCase;

/**
 * Base class for tests that hit the live Ploi API.
 *
 * These need tests/.env with a valid API_TOKEN and are excluded from the
 * default test suite, see phpunit.xml.
 */
abstract class TestCase extends PHPUnitTestCase
{
    /**
     * @var Ploi
     */
    private $ploi;

    /**
     * Returns the Ploi Client
     *
     * @return Ploi
     */
    public function getPloi()
    {
        return $this->ploi;
    }

    protected function setup(): void
    {
        $this->ploi = new Ploi($_ENV['API_TOKEN']);

        parent::setup();
    }

    /**
     * Load the environment file
     */
    public static function setUpBeforeClass(): void
    {
        // Load the test environment
        $dotenv = \Dotenv\Dotenv::createImmutable(dirname(__DIR__));
        $dotenv->load();
        $dotenv->required('API_TOKEN')->notEmpty();

        parent::setUpBeforeClass();
    }
}
