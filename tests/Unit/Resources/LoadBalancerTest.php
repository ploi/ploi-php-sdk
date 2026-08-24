<?php

declare(strict_types=1);

namespace Tests\Unit\Resources;

use Tests\Unit\TestCase;

class LoadBalancerTest extends TestCase
{
    public function testRequestsACertificate(): void
    {
        $this->queue();

        $this->ploi->servers(1)->loadBalancer()->requestCertificate('example.com');

        $this->assertRequest('post', 'servers/1/load-balancer/example.com/request-certificate');
    }

    public function testRevokesACertificate(): void
    {
        $this->queue();

        $this->ploi->servers(1)->loadBalancer()->revokeCertificate('example.com');

        $this->assertRequest('delete', 'servers/1/load-balancer/example.com/revoke-certificate');
    }
}
