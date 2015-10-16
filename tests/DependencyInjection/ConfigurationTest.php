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
    public function it_should_not_enable_commands_or_events_by_default()
    {
        $this->assertProcessedConfigurationEquals([], [
            'encryption' => [
                'enabled'   => false,
                'encrypter' => 'nelmio',
                'algorithm' => 'rijndael-128',
                'secret'    => '%kernel.secret%',
            ],
        ]);
    }

    /**
     * @test
     *
     * @dataProvider commandsAndEventsProvider
     *
     * @param string $type
     */
    public function it_should_normalize_commands_and_events_to_fixed_queue_name($type)
    {
        $config = [$type => 'my-queue-name'];

        $this->assertProcessedConfigurationEquals([$config], [
            $type => [
                'queue_name_resolver' => 'fixed',
                'queue_name' => 'my-queue-name',
                'queues_map' => [],
            ],
        ], $type);
    }

    /**
     * @test
     *
     * @dataProvider commandsAndEventsProvider
     *
     * @param string $type
     */
    public function it_should_normalize_commands_and_events_to_default_fixed_queue_name($type)
    {
        foreach ([true, null] as $value) {
            $config = [$type => $value];

            $this->assertProcessedConfigurationEquals([$config], [
                $type => [
                    'queue_name_resolver' => 'fixed',
                    'queue_name' => sprintf('asynchronous_%s', $type),
                    'queues_map' => [],
                ],
            ], $type);
        }
    }

    /**
     * @test
     *
     * @dataProvider commandsAndEventsProvider
     *
     * @param string $type
     */
    public function it_should_be_possible_to_set_a_queue_name_map($type)
    {
        $config = [
            $type => [
                'queue_name_resolver' => 'mapped',
                'queue_name' => 'my_default_queue_when_mapping_fails',
                'queues_map' => [
                    'MyBundle\GenerateThumbnail' => 'heavy_lifting',
                ],
            ],
        ];

        $this->assertProcessedConfigurationEquals([$config], [
            $type => [
                'queue_name_resolver' => 'mapped',
                'queue_name' => 'my_default_queue_when_mapping_fails',
                'queues_map' => [
                    'MyBundle\GenerateThumbnail' => 'heavy_lifting',
                ],
            ],
        ], $type);
    }

    /**
     * @test
     *
     * @dataProvider commandsAndEventsProvider
     *
     * @param string $type
     */
    public function it_should_be_possible_to_set_a_class_based_name_resolver($type)
    {
        $config = [
            $type => [
                'queue_name_resolver' => 'class_based',
            ],
        ];

        $this->assertProcessedConfigurationEquals([$config], [
            $type => [
                'queue_name_resolver' => 'class_based',
                'queue_name' => 'asynchronous_'.$type,
                'queues_map' => [],
            ],
        ], $type);
    }

    /**
     * @test
     *
     * @dataProvider commandsAndEventsProvider
     *
     * @param string $type
     */
    public function it_should_be_possible_to_set_a_service_as_name_resolver($type)
    {
        $config = [
            $type => [
                'queue_name_resolver' => 'my.service.id',
            ],
        ];

        $this->assertProcessedConfigurationEquals([$config], [
            $type => [
                'queue_name_resolver' => 'my.service.id',
                'queue_name' => 'asynchronous_'.$type,
                'queues_map' => [],
            ],
        ], $type);
    }

    public function commandsAndEventsProvider()
    {
        return [
            ['commands'],
            ['events'],
        ];
    }
}
