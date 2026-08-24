<?php

declare(strict_types=1);

namespace Tests\Unit\Resources;

use Tests\Unit\TestCase;

class WebserverTemplateTest extends TestCase
{
    public function testListsWebserverTemplates(): void
    {
        $this->queue();

        $this->ploi->webserverTemplates()->get();

        $this->assertRequest('get', 'webserver-templates?page=1');
    }

    public function testGetsASingleWebserverTemplate(): void
    {
        $this->queue();

        $this->ploi->webserverTemplates(1)->get();

        $this->assertRequest('get', 'webserver-templates/1');
    }
}
