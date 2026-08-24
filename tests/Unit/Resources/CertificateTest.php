<?php

declare(strict_types=1);

namespace Tests\Unit\Resources;

use Ploi\Exceptions\Resource\RequiresId;
use Tests\Unit\TestCase;

class CertificateTest extends TestCase
{
    public function testListsCertificates(): void
    {
        $this->queue();

        $this->ploi->servers(1)->sites(2)->certificates()->get();

        $this->assertRequest('get', 'servers/1/sites/2/certificates?page=1');
    }

    public function testGetsASingleCertificate(): void
    {
        $this->queue();

        $this->ploi->servers(1)->sites(2)->certificates(3)->get();

        $this->assertRequest('get', 'servers/1/sites/2/certificates/3');
    }

    public function testCreatesACertificate(): void
    {
        $this->queue(['data' => ['id' => 3]]);

        $certificate = $this->ploi->servers(1)->sites(2)->certificates();
        $certificate->create('example.com');

        $this->assertRequest('post', 'servers/1/sites/2/certificates', [
            'certificate' => 'example.com',
            'type'        => 'letsencrypt',
            'force'       => false,
        ]);

        $this->assertSame(3, $certificate->getId());
    }

    public function testForcesACustomCertificate(): void
    {
        $this->queue(['data' => ['id' => 3]]);

        $this->ploi->servers(1)->sites(2)->certificates()->create('example.com', 'custom', true);

        $this->assertRequest('post', 'servers/1/sites/2/certificates', [
            'certificate' => 'example.com',
            'type'        => 'custom',
            'force'       => true,
        ]);
    }

    public function testDeletesACertificate(): void
    {
        $this->queue();

        $this->ploi->servers(1)->sites(2)->certificates(3)->delete();

        $this->assertRequest('delete', 'servers/1/sites/2/certificates/3');
    }

    public function testDeleteRequiresAnId(): void
    {
        $this->expectException(RequiresId::class);

        $this->ploi->servers(1)->sites(2)->certificates()->delete();
    }
}
