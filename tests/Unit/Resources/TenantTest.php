<?php

declare(strict_types=1);

namespace Tests\Unit\Resources;

use Tests\Unit\TestCase;

class TenantTest extends TestCase
{
    public function testListsTenants(): void
    {
        $this->queue();

        $this->ploi->servers(1)->sites(2)->tenants()->get();

        $this->assertRequest('get', 'servers/1/sites/2/tenants');
    }

    public function testCreatesTenants(): void
    {
        $this->queue();

        $this->ploi->servers(1)->sites(2)->tenants()->create(['one.example.com', 'two.example.com']);

        $this->assertRequest('post', 'servers/1/sites/2/tenants', [
            'tenants' => ['one.example.com', 'two.example.com'],
        ]);
    }

    public function testDeletesATenant(): void
    {
        $this->queue();

        $this->ploi->servers(1)->sites(2)->tenants()->delete('one.example.com');

        $this->assertRequest('delete', 'servers/1/sites/2/tenants/one.example.com');
    }

    public function testRequestsACertificateForATenant(): void
    {
        $this->queue();

        $this->ploi->servers(1)->sites(2)->tenants()->requestCertificate(
            'one.example.com',
            'https://hooks.example.com/ploi',
            'one.example.com,www.one.example.com'
        );

        $this->assertRequest('post', 'servers/1/sites/2/tenants/one.example.com/request-certificate', [
            'webhook' => 'https://hooks.example.com/ploi',
            'domains' => 'one.example.com,www.one.example.com',
        ]);
    }

    public function testRevokesACertificateForATenant(): void
    {
        $this->queue();

        $this->ploi->servers(1)->sites(2)->tenants()->revokeCertificate(
            'one.example.com',
            'https://hooks.example.com/ploi'
        );

        $this->assertRequest('post', 'servers/1/sites/2/tenants/one.example.com/revoke-certificate', [
            'webhook' => 'https://hooks.example.com/ploi',
        ]);
    }
}
