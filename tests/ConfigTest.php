<?php

use \ASAP\Config;

class ConfigTest extends PHPUnit_Framework_TestCase
{
    public function testConfig()
    {
        $c = new Config();
        $c->addPath(array(            
            __DIR__.'/configs/prior3',
            __DIR__.'/configs/prior2',
            __DIR__.'/configs/prior1'
        ));

        $this->assertEquals('TestAppPrior1', $c->get('foo.app'));
        $this->assertEquals(
            array('bar.mail' => 'prior1@bar.com', 'bar.smtp' => 'prior1gmail'),
            $c->get(array('bar.mail', 'bar.smtp'))
        );
        $this->assertEquals('prior1supported', $c['bar.nested.config.are']);

        $c = new Config();
        $c->addPath(array(            
            __DIR__.'/configs/prior1',
            __DIR__.'/configs/prior2',
            __DIR__.'/configs/prior3'
        ));

        $this->assertEquals('TestAppPrior3', $c->get('foo.app'));
        $this->assertEquals(
            array('bar.mail' => 'prior3@bar.com', 'bar.smtp' => 'prior3gmail'),
            $c->get(array('bar.mail', 'bar.smtp'))
        );
        $this->assertEquals('prior3supported', $c['bar.nested.config.are']);

        $c = new Config();
        $c->addPath(__DIR__.'/configs/prior3');

        $this->assertEquals('TestAppPrior3', $c->get('foo.app'));

        $c->addPath(__DIR__.'/configs/prior2');

        $this->assertEquals('TestAppPrior2', $c->get('foo.app'));

        $c->addpath(__DIR__.'/configs/prior1', true);

        $this->assertEquals('TestAppPrior2', $c->get('foo.app'));
    }
}