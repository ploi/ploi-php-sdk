<?php
declare(strict_types=1);

namespace Tests;

use Ploi\Ploi;
use PHPUnit\Framework\TestCase;

/**
 * Class BaseTest
 * @package Tests
 */
class BaseTest extends TestCase
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
        $this->ploi = new Ploi(getenv('API_TOKEN'));

        parent::setup();
    }

    /**
     * Load the environment file
     */
    public static function setUpBeforeClass(): void
    {
        // Load the test environment
        $dotenv = new \Dotenv\Dotenv(__DIR__);
        $dotenv->load();

        parent::setUpBeforeClass();
    }

    /**
     * Base test to make sure it's running
     */
    public function testTrue()
    {
        $this->assertTrue(true);
    }
}
