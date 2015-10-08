<?php

namespace SimpleBus\BernardBundleBridge\Tests\DependencyInjection;

use Matthias\SymfonyConfigTest\PhpUnit\ConfigurationTestCaseTrait;
use SimpleBus\BernardBundleBridge\DependencyInjection\Configuration;

class ConfigurationTest extends \PHPUnit_Framework_TestCase
{
    use ConfigurationTestCaseTrait;

    protected function getConfiguration()
    {
        return new Configuration('simple_bus_bernard_bundle_bridge');
    }

    /**
     * @test
     */
    public function it_should_work_with_an_empty_configuration()
    {
        $this->assertProcessedConfigurationEquals(array(), array(
            'commands' => array(
                'queue_name_resolver' => 'fixed',
                'queue_name' => 'asynchronous_commands',
                'queues_map' => array(),
            ),
            'events' => array(
                'queue_name_resolver' => 'fixed',
                'queue_name' => 'asynchronous_events',
                'queues_map' => array(),
            ),
            'encryption' => array(
                'enabled' => false,
                'encrypter' => 'nelmio',
                'algorithm' => 'rijndael-128',
                'secret' => '%kernel.secret%',
            ),
        ));
    }

    /**
     * @test
     * @dataProvider commandsAndEventsProvider
     */
    public function it_should_normalize_commands_and_events_to_fixed_queue_name($type)
    {
        $this->assertProcessedConfigurationEquals(array(
            array($type => 'my-queue-name')
        ), array(
            $type => array(
                'queue_name_resolver' => 'fixed',
                'queue_name' => 'my-queue-name',
                'queues_map' => array(),
            )
        ), $type);
    }

    /**
     * @test
     * @dataProvider commandsAndEventsProvider
     */
    public function it_should_be_possible_to_set_a_queue_name_map($type)
    {
        $this->assertProcessedConfigurationEquals(array(
            array($type => array(
                'queue_name_resolver' => 'mapped',
                'queue_name' => 'my_default_queue_when_mapping_fails',
                'queues_map' => array(
                    'MyBundle\GenerateThumbnail' => 'heavy_lifting'
                )
            ))
        ), array(
            $type => array(
                'queue_name_resolver' => 'mapped',
                'queue_name' => 'my_default_queue_when_mapping_fails',
                'queues_map' => array(
                    'MyBundle\GenerateThumbnail' => 'heavy_lifting'
                ),
            )
        ), $type);
    }

    /**
     * @test
     * @dataProvider commandsAndEventsProvider
     */
    public function it_should_be_possible_to_set_a_class_based_name_resolver($type)
    {
        $this->assertProcessedConfigurationEquals(array(
            array($type => array(
                'queue_name_resolver' => 'class_based',
            ))
        ), array(
            $type => array(
                'queue_name_resolver' => 'class_based',
                'queue_name' => 'asynchronous_' . $type,
                'queues_map' => array(),
            )
        ), $type);
    }

    /**
     * @test
     * @dataProvider commandsAndEventsProvider
     */
    public function it_should_be_possible_to_set_a_service_as_name_resolver($type)
    {
        $this->assertProcessedConfigurationEquals(array(
            array($type => array(
                'queue_name_resolver' => 'my.service.id',
            ))
        ), array(
            $type => array(
                'queue_name_resolver' => 'my.service.id',
                'queue_name' => 'asynchronous_' . $type,
                'queues_map' => array(),
            )
        ), $type);
    }

    public function commandsAndEventsProvider()
    {
        return [
            ['commands'],
            ['events'],
        ];
    }
}
