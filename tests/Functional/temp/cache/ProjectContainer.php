<?php
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\DependencyInjection\Exception\InactiveScopeException;
use Symfony\Component\DependencyInjection\Exception\InvalidArgumentException;
use Symfony\Component\DependencyInjection\Exception\LogicException;
use Symfony\Component\DependencyInjection\Exception\RuntimeException;
use Symfony\Component\DependencyInjection\ParameterBag\FrozenParameterBag;
class ProjectContainer extends Container
{
    private $parameters;
    private $targetDirs = array();
    public function __construct()
    {
        $dir = __DIR__;
        for ($i = 1; $i <= 5; ++$i) {
            $this->targetDirs[$i] = $dir = dirname($dir);
        }
        $this->parameters = $this->getDefaultParameters();
        $this->services =
        $this->scopedServices =
        $this->scopeStacks = array();
        $this->set('service_container', $this);
        $this->scopes = array('request' => 'container');
        $this->scopeChildren = array('request' => array());
        $this->methodMap = array(
            'annotation_reader' => 'getAnnotationReaderService',
            'asynchronous_command_bus' => 'getAsynchronousCommandBusService',
            'asynchronous_command_handler' => 'getAsynchronousCommandHandlerService',
            'asynchronous_event_bus' => 'getAsynchronousEventBusService',
            'asynchronous_event_subscriber' => 'getAsynchronousEventSubscriberService',
            'cache_clearer' => 'getCacheClearerService',
            'cache_warmer' => 'getCacheWarmerService',
            'command_bus' => 'getCommandBusService',
            'debug.debug_handlers_listener' => 'getDebug_DebugHandlersListenerService',
            'debug.stopwatch' => 'getDebug_StopwatchService',
            'event_bus' => 'getEventBusService',
            'event_dispatcher' => 'getEventDispatcherService',
            'file_locator' => 'getFileLocatorService',
            'filesystem' => 'getFilesystemService',
            'fragment.handler' => 'getFragment_HandlerService',
            'fragment.renderer.esi' => 'getFragment_Renderer_EsiService',
            'fragment.renderer.hinclude' => 'getFragment_Renderer_HincludeService',
            'fragment.renderer.inline' => 'getFragment_Renderer_InlineService',
            'fragment.renderer.ssi' => 'getFragment_Renderer_SsiService',
            'http_kernel' => 'getHttpKernelService',
            'jms_serializer' => 'getJmsSerializerService',
            'jms_serializer.array_collection_handler' => 'getJmsSerializer_ArrayCollectionHandlerService',
            'jms_serializer.constraint_violation_handler' => 'getJmsSerializer_ConstraintViolationHandlerService',
            'jms_serializer.datetime_handler' => 'getJmsSerializer_DatetimeHandlerService',
            'jms_serializer.doctrine_proxy_subscriber' => 'getJmsSerializer_DoctrineProxySubscriberService',
            'jms_serializer.form_error_handler' => 'getJmsSerializer_FormErrorHandlerService',
            'jms_serializer.handler_registry' => 'getJmsSerializer_HandlerRegistryService',
            'jms_serializer.json_deserialization_visitor' => 'getJmsSerializer_JsonDeserializationVisitorService',
            'jms_serializer.json_serialization_visitor' => 'getJmsSerializer_JsonSerializationVisitorService',
            'jms_serializer.metadata_driver' => 'getJmsSerializer_MetadataDriverService',
            'jms_serializer.naming_strategy' => 'getJmsSerializer_NamingStrategyService',
            'jms_serializer.php_collection_handler' => 'getJmsSerializer_PhpCollectionHandlerService',
            'jms_serializer.templating.helper.serializer' => 'getJmsSerializer_Templating_Helper_SerializerService',
            'jms_serializer.unserialize_object_constructor' => 'getJmsSerializer_UnserializeObjectConstructorService',
            'jms_serializer.xml_deserialization_visitor' => 'getJmsSerializer_XmlDeserializationVisitorService',
            'jms_serializer.xml_serialization_visitor' => 'getJmsSerializer_XmlSerializationVisitorService',
            'jms_serializer.yaml_serialization_visitor' => 'getJmsSerializer_YamlSerializationVisitorService',
            'kernel' => 'getKernelService',
            'locale_listener' => 'getLocaleListenerService',
            'logger' => 'getLoggerService',
            'old_sound_rabbit_mq.asynchronous_commands_consumer' => 'getOldSoundRabbitMq_AsynchronousCommandsConsumerService',
            'old_sound_rabbit_mq.asynchronous_commands_producer' => 'getOldSoundRabbitMq_AsynchronousCommandsProducerService',
            'old_sound_rabbit_mq.asynchronous_events_consumer' => 'getOldSoundRabbitMq_AsynchronousEventsConsumerService',
            'old_sound_rabbit_mq.asynchronous_events_producer' => 'getOldSoundRabbitMq_AsynchronousEventsProducerService',
            'old_sound_rabbit_mq.connection.default' => 'getOldSoundRabbitMq_Connection_DefaultService',
            'old_sound_rabbit_mq.parts_holder' => 'getOldSoundRabbitMq_PartsHolderService',
            'property_accessor' => 'getPropertyAccessorService',
            'request' => 'getRequestService',
            'request_stack' => 'getRequestStackService',
            'response_listener' => 'getResponseListenerService',
            'security.secure_random' => 'getSecurity_SecureRandomService',
            'service_container' => 'getServiceContainerService',
            'simple_bus.asynchronous.command_bus.publishes_unhandled_commands_middleware' => 'getSimpleBus_Asynchronous_CommandBus_PublishesUnhandledCommandsMiddlewareService',
            'simple_bus.asynchronous.envelope_factory' => 'getSimpleBus_Asynchronous_EnvelopeFactoryService',
            'simple_bus.asynchronous.message_serializer' => 'getSimpleBus_Asynchronous_MessageSerializerService',
            'simple_bus.asynchronous.object_serializer' => 'getSimpleBus_Asynchronous_ObjectSerializerService',
            'simple_bus.command_bus.command_name_resolver' => 'getSimpleBus_CommandBus_CommandNameResolverService',
            'simple_bus.event_bus.event_name_resolver' => 'getSimpleBus_EventBus_EventNameResolverService',
            'simple_bus.rabbit_mq.command_publisher' => 'getSimpleBus_RabbitMq_CommandPublisherService',
            'simple_bus.rabbit_mq.commands_consumer' => 'getSimpleBus_RabbitMq_CommandsConsumerService',
            'simple_bus.rabbit_mq.event_publisher' => 'getSimpleBus_RabbitMq_EventPublisherService',
            'simple_bus.rabbit_mq.events_consumer' => 'getSimpleBus_RabbitMq_EventsConsumerService',
            'streamed_response_listener' => 'getStreamedResponseListenerService',
            'translation.dumper.csv' => 'getTranslation_Dumper_CsvService',
            'translation.dumper.ini' => 'getTranslation_Dumper_IniService',
            'translation.dumper.json' => 'getTranslation_Dumper_JsonService',
            'translation.dumper.mo' => 'getTranslation_Dumper_MoService',
            'translation.dumper.php' => 'getTranslation_Dumper_PhpService',
            'translation.dumper.po' => 'getTranslation_Dumper_PoService',
            'translation.dumper.qt' => 'getTranslation_Dumper_QtService',
            'translation.dumper.res' => 'getTranslation_Dumper_ResService',
            'translation.dumper.xliff' => 'getTranslation_Dumper_XliffService',
            'translation.dumper.yml' => 'getTranslation_Dumper_YmlService',
            'translation.extractor' => 'getTranslation_ExtractorService',
            'translation.extractor.php' => 'getTranslation_Extractor_PhpService',
            'translation.loader' => 'getTranslation_LoaderService',
            'translation.loader.csv' => 'getTranslation_Loader_CsvService',
            'translation.loader.dat' => 'getTranslation_Loader_DatService',
            'translation.loader.ini' => 'getTranslation_Loader_IniService',
            'translation.loader.json' => 'getTranslation_Loader_JsonService',
            'translation.loader.mo' => 'getTranslation_Loader_MoService',
            'translation.loader.php' => 'getTranslation_Loader_PhpService',
            'translation.loader.po' => 'getTranslation_Loader_PoService',
            'translation.loader.qt' => 'getTranslation_Loader_QtService',
            'translation.loader.res' => 'getTranslation_Loader_ResService',
            'translation.loader.xliff' => 'getTranslation_Loader_XliffService',
            'translation.loader.yml' => 'getTranslation_Loader_YmlService',
            'translation.writer' => 'getTranslation_WriterService',
            'translator' => 'getTranslatorService',
            'translator.default' => 'getTranslator_DefaultService',
            'translator_listener' => 'getTranslatorListenerService',
            'uri_signer' => 'getUriSignerService',
        );
        $this->aliases = array(
            'serializer' => 'jms_serializer',
            'simple_bus.asynchronous.command_bus.command_name_resolver' => 'simple_bus.command_bus.command_name_resolver',
            'simple_bus.asynchronous.command_publisher' => 'simple_bus.rabbit_mq.command_publisher',
            'simple_bus.asynchronous.event_bus.event_name_resolver' => 'simple_bus.event_bus.event_name_resolver',
            'simple_bus.asynchronous.event_publisher' => 'simple_bus.rabbit_mq.event_publisher',
            'simple_bus.rabbit_mq.command_producer' => 'old_sound_rabbit_mq.asynchronous_commands_producer',
            'simple_bus.rabbit_mq.event_producer' => 'old_sound_rabbit_mq.asynchronous_events_producer',
        );
    }
    public function compile()
    {
        throw new LogicException('You cannot compile a dumped frozen container.');
    }
    protected function getAnnotationReaderService()
    {
        return $this->services['annotation_reader'] = new \Doctrine\Common\Annotations\AnnotationReader();
    }
    protected function getAsynchronousCommandBusService()
    {
        $this->services['asynchronous_command_bus'] = $instance = new \SimpleBus\Message\Bus\Middleware\MessageBusSupportingMiddleware();
        $instance->appendMiddleware(new \SimpleBus\Message\Bus\Middleware\FinishesHandlingMessageBeforeHandlingNext());
        $instance->appendMiddleware(new \SimpleBus\Message\Handler\DelegatesToMessageHandlerMiddleware(new \SimpleBus\Message\Handler\Resolver\NameBasedMessageHandlerResolver($this->get('simple_bus.command_bus.command_name_resolver'), new \SimpleBus\Message\Handler\Map\LazyLoadingMessageHandlerMap(array('SimpleBus\\RabbitMQBundle\\Tests\\Functional\\AsynchronousCommand' => 'asynchronous_command_handler'), new \SimpleBus\SymfonyBridge\DependencyInjection\InvokableServiceLocator($this)))));
        return $instance;
    }
    protected function getAsynchronousCommandHandlerService()
    {
        return $this->services['asynchronous_command_handler'] = new \SimpleBus\RabbitMQBundle\Tests\Functional\LoggingCommandHandler($this->get('logger'));
    }
    protected function getAsynchronousEventBusService()
    {
        $this->services['asynchronous_event_bus'] = $instance = new \SimpleBus\Message\Bus\Middleware\MessageBusSupportingMiddleware();
        $instance->appendMiddleware(new \SimpleBus\Message\Bus\Middleware\FinishesHandlingMessageBeforeHandlingNext());
        $instance->appendMiddleware(new \SimpleBus\Message\Subscriber\NotifiesMessageSubscribersMiddleware(new \SimpleBus\Message\Subscriber\Resolver\NameBasedMessageSubscriberResolver($this->get('simple_bus.event_bus.event_name_resolver'), new \SimpleBus\Message\Subscriber\Collection\LazyLoadingMessageSubscriberCollection(array('SimpleBus\\RabbitMQBundle\\Tests\\Functional\\Event' => array(0 => 'asynchronous_event_subscriber')), new \SimpleBus\SymfonyBridge\DependencyInjection\InvokableServiceLocator($this)))));
        return $instance;
    }
    protected function getAsynchronousEventSubscriberService()
    {
        return $this->services['asynchronous_event_subscriber'] = new \SimpleBus\RabbitMQBundle\Tests\Functional\LoggingEventSubscriber($this->get('logger'));
    }
    protected function getCacheClearerService()
    {
        return $this->services['cache_clearer'] = new \Symfony\Component\HttpKernel\CacheClearer\ChainCacheClearer(array());
    }
    protected function getCacheWarmerService()
    {
        return $this->services['cache_warmer'] = new \Symfony\Component\HttpKernel\CacheWarmer\CacheWarmerAggregate(array());
    }
    protected function getCommandBusService()
    {
        $this->services['command_bus'] = $instance = new \SimpleBus\Message\Bus\Middleware\MessageBusSupportingMiddleware();
        $instance->appendMiddleware(new \SimpleBus\Message\Bus\Middleware\FinishesHandlingMessageBeforeHandlingNext());
        $instance->appendMiddleware(new \SimpleBus\Message\Recorder\HandlesRecordedMessagesMiddleware(new \SimpleBus\Message\Recorder\AggregatesRecordedMessages(array(0 => new \SimpleBus\Message\Recorder\PublicMessageRecorder())), $this->get('event_bus')));
        $instance->appendMiddleware($this->get('simple_bus.asynchronous.command_bus.publishes_unhandled_commands_middleware'));
        $instance->appendMiddleware(new \SimpleBus\Message\Handler\DelegatesToMessageHandlerMiddleware(new \SimpleBus\Message\Handler\Resolver\NameBasedMessageHandlerResolver($this->get('simple_bus.command_bus.command_name_resolver'), new \SimpleBus\Message\Handler\Map\LazyLoadingMessageHandlerMap(array(), new \SimpleBus\SymfonyBridge\DependencyInjection\InvokableServiceLocator($this)))));
        return $instance;
    }
    protected function getDebug_DebugHandlersListenerService()
    {
        return $this->services['debug.debug_handlers_listener'] = new \Symfony\Component\HttpKernel\EventListener\DebugHandlersListener('', $this->get('logger', ContainerInterface::NULL_ON_INVALID_REFERENCE), 85, NULL, true, NULL);
    }
    protected function getDebug_StopwatchService()
    {
        return $this->services['debug.stopwatch'] = new \Symfony\Component\Stopwatch\Stopwatch();
    }
    protected function getEventBusService()
    {
        $this->services['event_bus'] = $instance = new \SimpleBus\Message\Bus\Middleware\MessageBusSupportingMiddleware();
        $instance->appendMiddleware(new \SimpleBus\Message\Bus\Middleware\FinishesHandlingMessageBeforeHandlingNext());
        $instance->appendMiddleware(new \SimpleBus\Asynchronous\Message\Dispatcher\AsynchronousEventDispatcher($this->get('simple_bus.rabbit_mq.event_publisher')));
        $instance->appendMiddleware(new \SimpleBus\Message\Subscriber\NotifiesMessageSubscribersMiddleware(new \SimpleBus\Message\Subscriber\Resolver\NameBasedMessageSubscriberResolver($this->get('simple_bus.event_bus.event_name_resolver'), new \SimpleBus\Message\Subscriber\Collection\LazyLoadingMessageSubscriberCollection(array(), new \SimpleBus\SymfonyBridge\DependencyInjection\InvokableServiceLocator($this)))));
        return $instance;
    }
    protected function getEventDispatcherService()
    {
        $this->services['event_dispatcher'] = $instance = new \Symfony\Component\EventDispatcher\ContainerAwareEventDispatcher($this);
        $instance->addSubscriberService('response_listener', 'Symfony\\Component\\HttpKernel\\EventListener\\ResponseListener');
        $instance->addSubscriberService('streamed_response_listener', 'Symfony\\Component\\HttpKernel\\EventListener\\StreamedResponseListener');
        $instance->addSubscriberService('locale_listener', 'Symfony\\Component\\HttpKernel\\EventListener\\LocaleListener');
        $instance->addSubscriberService('translator_listener', 'Symfony\\Component\\HttpKernel\\EventListener\\TranslatorListener');
        $instance->addSubscriberService('debug.debug_handlers_listener', 'Symfony\\Component\\HttpKernel\\EventListener\\DebugHandlersListener');
        return $instance;
    }
    protected function getFileLocatorService()
    {
        return $this->services['file_locator'] = new \Symfony\Component\HttpKernel\Config\FileLocator($this->get('kernel'), ($this->targetDirs[2].'/Resources'));
    }
    protected function getFilesystemService()
    {
        return $this->services['filesystem'] = new \Symfony\Component\Filesystem\Filesystem();
    }
    protected function getFragment_HandlerService()
    {
        $this->services['fragment.handler'] = $instance = new \Symfony\Component\HttpKernel\Fragment\FragmentHandler(array(), NULL, $this->get('request_stack'));
        $instance->addRenderer($this->get('fragment.renderer.inline'));
        $instance->addRenderer($this->get('fragment.renderer.hinclude'));
        $instance->addRenderer($this->get('fragment.renderer.esi'));
        $instance->addRenderer($this->get('fragment.renderer.ssi'));
        return $instance;
    }
    protected function getFragment_Renderer_EsiService()
    {
        $this->services['fragment.renderer.esi'] = $instance = new \Symfony\Component\HttpKernel\Fragment\EsiFragmentRenderer(NULL, $this->get('fragment.renderer.inline'), $this->get('uri_signer'));
        $instance->setFragmentPath('/_fragment');
        return $instance;
    }
    protected function getFragment_Renderer_HincludeService()
    {
        $this->services['fragment.renderer.hinclude'] = $instance = new \Symfony\Bundle\FrameworkBundle\Fragment\ContainerAwareHIncludeFragmentRenderer($this, $this->get('uri_signer'), '');
        $instance->setFragmentPath('/_fragment');
        return $instance;
    }
    protected function getFragment_Renderer_InlineService()
    {
        $this->services['fragment.renderer.inline'] = $instance = new \Symfony\Component\HttpKernel\Fragment\InlineFragmentRenderer($this->get('http_kernel'), $this->get('event_dispatcher'));
        $instance->setFragmentPath('/_fragment');
        return $instance;
    }
    protected function getFragment_Renderer_SsiService()
    {
        $this->services['fragment.renderer.ssi'] = $instance = new \Symfony\Component\HttpKernel\Fragment\SsiFragmentRenderer(NULL, $this->get('fragment.renderer.inline'), $this->get('uri_signer'));
        $instance->setFragmentPath('/_fragment');
        return $instance;
    }
    protected function getHttpKernelService()
    {
        return $this->services['http_kernel'] = new \Symfony\Component\HttpKernel\DependencyInjection\ContainerAwareHttpKernel($this->get('event_dispatcher'), $this, new \Symfony\Bundle\FrameworkBundle\Controller\ControllerResolver($this, new \Symfony\Bundle\FrameworkBundle\Controller\ControllerNameParser($this->get('kernel')), $this->get('logger', ContainerInterface::NULL_ON_INVALID_REFERENCE)), $this->get('request_stack'));
    }
    protected function getJmsSerializerService()
    {
        $a = new \Metadata\MetadataFactory(new \Metadata\Driver\LazyLoadingDriver($this, 'jms_serializer.metadata_driver'), 'Metadata\\ClassHierarchyMetadata', NULL);
        $a->setCache(new \Metadata\Cache\FileCache((__DIR__.'/jms_serializer')));
        $b = new \JMS\Serializer\EventDispatcher\LazyEventDispatcher($this);
        $b->setListeners(array('serializer.pre_serialize' => array(0 => array(0 => array(0 => 'jms_serializer.doctrine_proxy_subscriber', 1 => 'onPreSerialize'), 1 => NULL, 2 => NULL))));
        return $this->services['jms_serializer'] = new \JMS\Serializer\Serializer($a, $this->get('jms_serializer.handler_registry'), $this->get('jms_serializer.unserialize_object_constructor'), new \PhpCollection\Map(array('json' => $this->get('jms_serializer.json_serialization_visitor'), 'xml' => $this->get('jms_serializer.xml_serialization_visitor'), 'yml' => $this->get('jms_serializer.yaml_serialization_visitor'))), new \PhpCollection\Map(array('json' => $this->get('jms_serializer.json_deserialization_visitor'), 'xml' => $this->get('jms_serializer.xml_deserialization_visitor'))), $b);
    }
    protected function getJmsSerializer_ArrayCollectionHandlerService()
    {
        return $this->services['jms_serializer.array_collection_handler'] = new \JMS\Serializer\Handler\ArrayCollectionHandler();
    }
    protected function getJmsSerializer_ConstraintViolationHandlerService()
    {
        return $this->services['jms_serializer.constraint_violation_handler'] = new \JMS\Serializer\Handler\ConstraintViolationHandler();
    }
    protected function getJmsSerializer_DatetimeHandlerService()
    {
        return $this->services['jms_serializer.datetime_handler'] = new \JMS\Serializer\Handler\DateHandler('Y-m-d\\TH:i:sO', 'Europe/Amsterdam', true);
    }
    protected function getJmsSerializer_DoctrineProxySubscriberService()
    {
        return $this->services['jms_serializer.doctrine_proxy_subscriber'] = new \JMS\Serializer\EventDispatcher\Subscriber\DoctrineProxySubscriber();
    }
    protected function getJmsSerializer_FormErrorHandlerService()
    {
        return $this->services['jms_serializer.form_error_handler'] = new \JMS\Serializer\Handler\FormErrorHandler($this->get('translator'));
    }
    protected function getJmsSerializer_HandlerRegistryService()
    {
        return $this->services['jms_serializer.handler_registry'] = new \JMS\Serializer\Handler\LazyHandlerRegistry($this, array(2 => array('DateTime' => array('json' => array(0 => 'jms_serializer.datetime_handler', 1 => 'deserializeDateTimeFromjson'), 'xml' => array(0 => 'jms_serializer.datetime_handler', 1 => 'deserializeDateTimeFromxml'), 'yml' => array(0 => 'jms_serializer.datetime_handler', 1 => 'deserializeDateTimeFromyml')), 'ArrayCollection' => array('json' => array(0 => 'jms_serializer.array_collection_handler', 1 => 'deserializeCollection'), 'xml' => array(0 => 'jms_serializer.array_collection_handler', 1 => 'deserializeCollection'), 'yml' => array(0 => 'jms_serializer.array_collection_handler', 1 => 'deserializeCollection')), 'Doctrine\\Common\\Collections\\ArrayCollection' => array('json' => array(0 => 'jms_serializer.array_collection_handler', 1 => 'deserializeCollection'), 'xml' => array(0 => 'jms_serializer.array_collection_handler', 1 => 'deserializeCollection'), 'yml' => array(0 => 'jms_serializer.array_collection_handler', 1 => 'deserializeCollection')), 'Doctrine\\ORM\\PersistentCollection' => array('json' => array(0 => 'jms_serializer.array_collection_handler', 1 => 'deserializeCollection'), 'xml' => array(0 => 'jms_serializer.array_collection_handler', 1 => 'deserializeCollection'), 'yml' => array(0 => 'jms_serializer.array_collection_handler', 1 => 'deserializeCollection')), 'Doctrine\\ODM\\MongoDB\\PersistentCollection' => array('json' => array(0 => 'jms_serializer.array_collection_handler', 1 => 'deserializeCollection'), 'xml' => array(0 => 'jms_serializer.array_collection_handler', 1 => 'deserializeCollection'), 'yml' => array(0 => 'jms_serializer.array_collection_handler', 1 => 'deserializeCollection')), 'Doctrine\\ODM\\PHPCR\\PersistentCollection' => array('json' => array(0 => 'jms_serializer.array_collection_handler', 1 => 'deserializeCollection'), 'xml' => array(0 => 'jms_serializer.array_collection_handler', 1 => 'deserializeCollection'), 'yml' => array(0 => 'jms_serializer.array_collection_handler', 1 => 'deserializeCollection')), 'PhpCollection\\Sequence' => array('json' => array(0 => 'jms_serializer.php_collection_handler', 1 => 'deserializeSequence'), 'xml' => array(0 => 'jms_serializer.php_collection_handler', 1 => 'deserializeSequence'), 'yml' => array(0 => 'jms_serializer.php_collection_handler', 1 => 'deserializeSequence')), 'PhpCollection\\Map' => array('json' => array(0 => 'jms_serializer.php_collection_handler', 1 => 'deserializeMap'), 'xml' => array(0 => 'jms_serializer.php_collection_handler', 1 => 'deserializeMap'), 'yml' => array(0 => 'jms_serializer.php_collection_handler', 1 => 'deserializeMap'))), 1 => array('DateTime' => array('json' => array(0 => 'jms_serializer.datetime_handler', 1 => 'serializeDateTime'), 'xml' => array(0 => 'jms_serializer.datetime_handler', 1 => 'serializeDateTime'), 'yml' => array(0 => 'jms_serializer.datetime_handler', 1 => 'serializeDateTime')), 'DateInterval' => array('json' => array(0 => 'jms_serializer.datetime_handler', 1 => 'serializeDateInterval'), 'xml' => array(0 => 'jms_serializer.datetime_handler', 1 => 'serializeDateInterval'), 'yml' => array(0 => 'jms_serializer.datetime_handler', 1 => 'serializeDateInterval')), 'ArrayCollection' => array('json' => array(0 => 'jms_serializer.array_collection_handler', 1 => 'serializeCollection'), 'xml' => array(0 => 'jms_serializer.array_collection_handler', 1 => 'serializeCollection'), 'yml' => array(0 => 'jms_serializer.array_collection_handler', 1 => 'serializeCollection')), 'Doctrine\\Common\\Collections\\ArrayCollection' => array('json' => array(0 => 'jms_serializer.array_collection_handler', 1 => 'serializeCollection'), 'xml' => array(0 => 'jms_serializer.array_collection_handler', 1 => 'serializeCollection'), 'yml' => array(0 => 'jms_serializer.array_collection_handler', 1 => 'serializeCollection')), 'Doctrine\\ORM\\PersistentCollection' => array('json' => array(0 => 'jms_serializer.array_collection_handler', 1 => 'serializeCollection'), 'xml' => array(0 => 'jms_serializer.array_collection_handler', 1 => 'serializeCollection'), 'yml' => array(0 => 'jms_serializer.array_collection_handler', 1 => 'serializeCollection')), 'Doctrine\\ODM\\MongoDB\\PersistentCollection' => array('json' => array(0 => 'jms_serializer.array_collection_handler', 1 => 'serializeCollection'), 'xml' => array(0 => 'jms_serializer.array_collection_handler', 1 => 'serializeCollection'), 'yml' => array(0 => 'jms_serializer.array_collection_handler', 1 => 'serializeCollection')), 'Doctrine\\ODM\\PHPCR\\PersistentCollection' => array('json' => array(0 => 'jms_serializer.array_collection_handler', 1 => 'serializeCollection'), 'xml' => array(0 => 'jms_serializer.array_collection_handler', 1 => 'serializeCollection'), 'yml' => array(0 => 'jms_serializer.array_collection_handler', 1 => 'serializeCollection')), 'PhpCollection\\Sequence' => array('json' => array(0 => 'jms_serializer.php_collection_handler', 1 => 'serializeSequence'), 'xml' => array(0 => 'jms_serializer.php_collection_handler', 1 => 'serializeSequence'), 'yml' => array(0 => 'jms_serializer.php_collection_handler', 1 => 'serializeSequence')), 'PhpCollection\\Map' => array('json' => array(0 => 'jms_serializer.php_collection_handler', 1 => 'serializeMap'), 'xml' => array(0 => 'jms_serializer.php_collection_handler', 1 => 'serializeMap'), 'yml' => array(0 => 'jms_serializer.php_collection_handler', 1 => 'serializeMap')), 'Symfony\\Component\\Form\\Form' => array('xml' => array(0 => 'jms_serializer.form_error_handler', 1 => 'serializeFormToxml'), 'json' => array(0 => 'jms_serializer.form_error_handler', 1 => 'serializeFormTojson'), 'yml' => array(0 => 'jms_serializer.form_error_handler', 1 => 'serializeFormToyml')), 'Symfony\\Component\\Form\\FormError' => array('xml' => array(0 => 'jms_serializer.form_error_handler', 1 => 'serializeFormErrorToxml'), 'json' => array(0 => 'jms_serializer.form_error_handler', 1 => 'serializeFormErrorTojson'), 'yml' => array(0 => 'jms_serializer.form_error_handler', 1 => 'serializeFormErrorToyml')), 'Symfony\\Component\\Validator\\ConstraintViolationList' => array('xml' => array(0 => 'jms_serializer.constraint_violation_handler', 1 => 'serializeListToxml'), 'json' => array(0 => 'jms_serializer.constraint_violation_handler', 1 => 'serializeListTojson'), 'yml' => array(0 => 'jms_serializer.constraint_violation_handler', 1 => 'serializeListToyml')), 'Symfony\\Component\\Validator\\ConstraintViolation' => array('xml' => array(0 => 'jms_serializer.constraint_violation_handler', 1 => 'serializeViolationToxml'), 'json' => array(0 => 'jms_serializer.constraint_violation_handler', 1 => 'serializeViolationTojson'), 'yml' => array(0 => 'jms_serializer.constraint_violation_handler', 1 => 'serializeViolationToyml')))));
    }
    protected function getJmsSerializer_JsonDeserializationVisitorService()
    {
        return $this->services['jms_serializer.json_deserialization_visitor'] = new \JMS\Serializer\JsonDeserializationVisitor($this->get('jms_serializer.naming_strategy'), $this->get('jms_serializer.unserialize_object_constructor'));
    }
    protected function getJmsSerializer_JsonSerializationVisitorService()
    {
        $this->services['jms_serializer.json_serialization_visitor'] = $instance = new \JMS\Serializer\JsonSerializationVisitor($this->get('jms_serializer.naming_strategy'));
        $instance->setOptions(0);
        return $instance;
    }
    protected function getJmsSerializer_MetadataDriverService()
    {
        $a = new \Metadata\Driver\FileLocator(array('Symfony\\Bundle\\FrameworkBundle' => ($this->targetDirs[4].'/vendor/symfony/framework-bundle/Symfony/Bundle/FrameworkBundle/Resources/config/serializer'), 'OldSound\\RabbitMqBundle' => ($this->targetDirs[4].'/vendor/oldsound/rabbitmq-bundle/OldSound/RabbitMqBundle/Resources/config/serializer'), 'SimpleBus\\SymfonyBridge' => ($this->targetDirs[4].'/vendor/simple-bus/symfony-bridge/src/Resources/config/serializer'), 'SimpleBus\\AsynchronousBundle' => ($this->targetDirs[4].'/vendor/simple-bus/asynchronous-bundle/src/Resources/config/serializer'), 'SimpleBus\\RabbitMQBundle' => ($this->targetDirs[4].'/src/Resources/config/serializer'), 'JMS\\SerializerBundle' => ($this->targetDirs[4].'/vendor/jms/serializer-bundle/JMS/SerializerBundle/Resources/config/serializer'), 'SimpleBus\\JMSSerializerBundle' => ($this->targetDirs[4].'/vendor/simple-bus/jms-serializer-bundle/src/Resources/config/serializer'), 'SimpleBus\\Asynchronous' => ($this->targetDirs[4].'/vendor/simple-bus/jms-serializer-bridge/src/metadata')));
        return $this->services['jms_serializer.metadata_driver'] = new \Metadata\Driver\DriverChain(array(0 => new \JMS\Serializer\Metadata\Driver\YamlDriver($a), 1 => new \JMS\Serializer\Metadata\Driver\XmlDriver($a), 2 => new \JMS\Serializer\Metadata\Driver\PhpDriver($a), 3 => new \JMS\Serializer\Metadata\Driver\AnnotationDriver($this->get('annotation_reader'))));
    }
    protected function getJmsSerializer_NamingStrategyService()
    {
        return $this->services['jms_serializer.naming_strategy'] = new \JMS\Serializer\Naming\CacheNamingStrategy(new \JMS\Serializer\Naming\SerializedNameAnnotationStrategy(new \JMS\Serializer\Naming\CamelCaseNamingStrategy('_', true)));
    }
    protected function getJmsSerializer_PhpCollectionHandlerService()
    {
        return $this->services['jms_serializer.php_collection_handler'] = new \JMS\Serializer\Handler\PhpCollectionHandler();
    }
    protected function getJmsSerializer_Templating_Helper_SerializerService()
    {
        return $this->services['jms_serializer.templating.helper.serializer'] = new \JMS\SerializerBundle\Templating\SerializerHelper($this->get('jms_serializer'));
    }
    protected function getJmsSerializer_XmlDeserializationVisitorService()
    {
        $this->services['jms_serializer.xml_deserialization_visitor'] = $instance = new \JMS\Serializer\XmlDeserializationVisitor($this->get('jms_serializer.naming_strategy'), $this->get('jms_serializer.unserialize_object_constructor'));
        $instance->setDoctypeWhitelist(array());
        return $instance;
    }
    protected function getJmsSerializer_XmlSerializationVisitorService()
    {
        return $this->services['jms_serializer.xml_serialization_visitor'] = new \JMS\Serializer\XmlSerializationVisitor($this->get('jms_serializer.naming_strategy'));
    }
    protected function getJmsSerializer_YamlSerializationVisitorService()
    {
        return $this->services['jms_serializer.yaml_serialization_visitor'] = new \JMS\Serializer\YamlSerializationVisitor($this->get('jms_serializer.naming_strategy'));
    }
    protected function getKernelService()
    {
        throw new RuntimeException('You have requested a synthetic service ("kernel"). The DIC does not know how to construct this service.');
    }
    protected function getLocaleListenerService()
    {
        return $this->services['locale_listener'] = new \Symfony\Component\HttpKernel\EventListener\LocaleListener('en', NULL, $this->get('request_stack'));
    }
    protected function getLoggerService()
    {
        return $this->services['logger'] = new \SimpleBus\RabbitMQBundle\Tests\Functional\FileLogger(($this->targetDirs[1].'/logs/test.log'));
    }
    protected function getOldSoundRabbitMq_AsynchronousCommandsConsumerService()
    {
        $this->services['old_sound_rabbit_mq.asynchronous_commands_consumer'] = $instance = new \OldSound\RabbitMqBundle\RabbitMq\Consumer($this->get('old_sound_rabbit_mq.connection.default'));
        $instance->setExchangeOptions(array('name' => 'asynchronous_commands', 'type' => 'direct', 'passive' => false, 'durable' => true, 'auto_delete' => false, 'internal' => false, 'nowait' => false, 'declare' => true, 'arguments' => NULL, 'ticket' => NULL));
        $instance->setQueueOptions(array('name' => 'asynchronous_commands', 'passive' => false, 'durable' => true, 'exclusive' => false, 'auto_delete' => false, 'nowait' => false, 'arguments' => NULL, 'ticket' => NULL, 'routing_keys' => array()));
        $instance->setCallback(array(0 => $this->get('simple_bus.rabbit_mq.commands_consumer'), 1 => 'execute'));
        return $instance;
    }
    protected function getOldSoundRabbitMq_AsynchronousCommandsProducerService()
    {
        $this->services['old_sound_rabbit_mq.asynchronous_commands_producer'] = $instance = new \OldSound\RabbitMqBundle\RabbitMq\Producer($this->get('old_sound_rabbit_mq.connection.default'));
        $instance->setExchangeOptions(array('name' => 'asynchronous_commands', 'type' => 'direct', 'passive' => false, 'durable' => true, 'auto_delete' => false, 'internal' => false, 'nowait' => false, 'declare' => true, 'arguments' => NULL, 'ticket' => NULL));
        $instance->setQueueOptions(array('name' => NULL));
        return $instance;
    }
    protected function getOldSoundRabbitMq_AsynchronousEventsConsumerService()
    {
        $this->services['old_sound_rabbit_mq.asynchronous_events_consumer'] = $instance = new \OldSound\RabbitMqBundle\RabbitMq\Consumer($this->get('old_sound_rabbit_mq.connection.default'));
        $instance->setExchangeOptions(array('name' => 'asynchronous_events', 'type' => 'direct', 'passive' => false, 'durable' => true, 'auto_delete' => false, 'internal' => false, 'nowait' => false, 'declare' => true, 'arguments' => NULL, 'ticket' => NULL));
        $instance->setQueueOptions(array('name' => 'asynchronous_events', 'passive' => false, 'durable' => true, 'exclusive' => false, 'auto_delete' => false, 'nowait' => false, 'arguments' => NULL, 'ticket' => NULL, 'routing_keys' => array()));
        $instance->setCallback(array(0 => $this->get('simple_bus.rabbit_mq.events_consumer'), 1 => 'execute'));
        return $instance;
    }
    protected function getOldSoundRabbitMq_AsynchronousEventsProducerService()
    {
        $this->services['old_sound_rabbit_mq.asynchronous_events_producer'] = $instance = new \OldSound\RabbitMqBundle\RabbitMq\Producer($this->get('old_sound_rabbit_mq.connection.default'));
        $instance->setExchangeOptions(array('name' => 'asynchronous_events', 'type' => 'direct', 'passive' => false, 'durable' => true, 'auto_delete' => false, 'internal' => false, 'nowait' => false, 'declare' => true, 'arguments' => NULL, 'ticket' => NULL));
        $instance->setQueueOptions(array('name' => NULL));
        return $instance;
    }
    protected function getOldSoundRabbitMq_Connection_DefaultService()
    {
        return $this->services['old_sound_rabbit_mq.connection.default'] = new \PhpAmqpLib\Connection\AMQPLazyConnection('localhost', 5672, 'guest', 'guest', '/');
    }
    protected function getOldSoundRabbitMq_PartsHolderService()
    {
        $a = $this->get('old_sound_rabbit_mq.asynchronous_events_producer');
        $b = $this->get('old_sound_rabbit_mq.asynchronous_commands_producer');
        $c = $this->get('old_sound_rabbit_mq.asynchronous_events_consumer');
        $d = $this->get('old_sound_rabbit_mq.asynchronous_commands_consumer');
        $this->services['old_sound_rabbit_mq.parts_holder'] = $instance = new \OldSound\RabbitMqBundle\RabbitMq\AmqpPartsHolder();
        $instance->addPart('old_sound_rabbit_mq.base_amqp', $a);
        $instance->addPart('old_sound_rabbit_mq.base_amqp', $b);
        $instance->addPart('old_sound_rabbit_mq.base_amqp', $c);
        $instance->addPart('old_sound_rabbit_mq.base_amqp', $d);
        $instance->addPart('old_sound_rabbit_mq.producer', $a);
        $instance->addPart('old_sound_rabbit_mq.producer', $b);
        $instance->addPart('old_sound_rabbit_mq.consumer', $c);
        $instance->addPart('old_sound_rabbit_mq.consumer', $d);
        return $instance;
    }
    protected function getPropertyAccessorService()
    {
        return $this->services['property_accessor'] = new \Symfony\Component\PropertyAccess\PropertyAccessor(false, false);
    }
    protected function getRequestService()
    {
        if (!isset($this->scopedServices['request'])) {
            throw new InactiveScopeException('request', 'request');
        }
        throw new RuntimeException('You have requested a synthetic service ("request"). The DIC does not know how to construct this service.');
    }
    protected function getRequestStackService()
    {
        return $this->services['request_stack'] = new \Symfony\Component\HttpFoundation\RequestStack();
    }
    protected function getResponseListenerService()
    {
        return $this->services['response_listener'] = new \Symfony\Component\HttpKernel\EventListener\ResponseListener('UTF-8');
    }
    protected function getSecurity_SecureRandomService()
    {
        return $this->services['security.secure_random'] = new \Symfony\Component\Security\Core\Util\SecureRandom((__DIR__.'/secure_random.seed'), $this->get('logger', ContainerInterface::NULL_ON_INVALID_REFERENCE));
    }
    protected function getServiceContainerService()
    {
        throw new RuntimeException('You have requested a synthetic service ("service_container"). The DIC does not know how to construct this service.');
    }
    protected function getSimpleBus_Asynchronous_CommandBus_PublishesUnhandledCommandsMiddlewareService()
    {
        return $this->services['simple_bus.asynchronous.command_bus.publishes_unhandled_commands_middleware'] = new \SimpleBus\Asynchronous\Message\Bus\PublishesUnhandledMessages($this->get('simple_bus.rabbit_mq.command_publisher'), $this->get('logger'));
    }
    protected function getSimpleBus_Asynchronous_EnvelopeFactoryService()
    {
        return $this->services['simple_bus.asynchronous.envelope_factory'] = new \SimpleBus\Asynchronous\Message\Envelope\DefaultEnvelopeFactory();
    }
    protected function getSimpleBus_Asynchronous_MessageSerializerService()
    {
        return $this->services['simple_bus.asynchronous.message_serializer'] = new \SimpleBus\Asynchronous\Message\Envelope\Serializer\StandardMessageInEnvelopeSerializer($this->get('simple_bus.asynchronous.envelope_factory'), $this->get('simple_bus.asynchronous.object_serializer'));
    }
    protected function getSimpleBus_Asynchronous_ObjectSerializerService()
    {
        return $this->services['simple_bus.asynchronous.object_serializer'] = new \SimpleBus\JMSSerializerBridge\JMSSerializerObjectSerializer($this->get('jms_serializer'), 'json');
    }
    protected function getSimpleBus_CommandBus_CommandNameResolverService()
    {
        return $this->services['simple_bus.command_bus.command_name_resolver'] = new \SimpleBus\Message\Name\ClassBasedNameResolver();
    }
    protected function getSimpleBus_EventBus_EventNameResolverService()
    {
        return $this->services['simple_bus.event_bus.event_name_resolver'] = new \SimpleBus\Message\Name\ClassBasedNameResolver();
    }
    protected function getSimpleBus_RabbitMq_CommandPublisherService()
    {
        return $this->services['simple_bus.rabbit_mq.command_publisher'] = new \SimpleBus\RabbitMQBundle\RabbitMQPublisher($this->get('simple_bus.asynchronous.message_serializer'), $this->get('old_sound_rabbit_mq.asynchronous_commands_producer'));
    }
    protected function getSimpleBus_RabbitMq_CommandsConsumerService()
    {
        $a = $this->get('asynchronous_command_bus');
        return $this->services['simple_bus.rabbit_mq.commands_consumer'] = new \SimpleBus\RabbitMQBundle\RabbitMQMessageConsumer(new \SimpleBus\Asynchronous\Message\Envelope\Consumer\StandardSerializedEnvelopeConsumer($this->get('simple_bus.asynchronous.message_serializer'), $a), $a);
    }
    protected function getSimpleBus_RabbitMq_EventPublisherService()
    {
        return $this->services['simple_bus.rabbit_mq.event_publisher'] = new \SimpleBus\RabbitMQBundle\RabbitMQPublisher($this->get('simple_bus.asynchronous.message_serializer'), $this->get('old_sound_rabbit_mq.asynchronous_events_producer'));
    }
    protected function getSimpleBus_RabbitMq_EventsConsumerService()
    {
        $a = $this->get('asynchronous_event_bus');
        return $this->services['simple_bus.rabbit_mq.events_consumer'] = new \SimpleBus\RabbitMQBundle\RabbitMQMessageConsumer(new \SimpleBus\Asynchronous\Message\Envelope\Consumer\StandardSerializedEnvelopeConsumer($this->get('simple_bus.asynchronous.message_serializer'), $a), $a);
    }
    protected function getStreamedResponseListenerService()
    {
        return $this->services['streamed_response_listener'] = new \Symfony\Component\HttpKernel\EventListener\StreamedResponseListener();
    }
    protected function getTranslation_Dumper_CsvService()
    {
        return $this->services['translation.dumper.csv'] = new \Symfony\Component\Translation\Dumper\CsvFileDumper();
    }
    protected function getTranslation_Dumper_IniService()
    {
        return $this->services['translation.dumper.ini'] = new \Symfony\Component\Translation\Dumper\IniFileDumper();
    }
    protected function getTranslation_Dumper_JsonService()
    {
        return $this->services['translation.dumper.json'] = new \Symfony\Component\Translation\Dumper\JsonFileDumper();
    }
    protected function getTranslation_Dumper_MoService()
    {
        return $this->services['translation.dumper.mo'] = new \Symfony\Component\Translation\Dumper\MoFileDumper();
    }
    protected function getTranslation_Dumper_PhpService()
    {
        return $this->services['translation.dumper.php'] = new \Symfony\Component\Translation\Dumper\PhpFileDumper();
    }
    protected function getTranslation_Dumper_PoService()
    {
        return $this->services['translation.dumper.po'] = new \Symfony\Component\Translation\Dumper\PoFileDumper();
    }
    protected function getTranslation_Dumper_QtService()
    {
        return $this->services['translation.dumper.qt'] = new \Symfony\Component\Translation\Dumper\QtFileDumper();
    }
    protected function getTranslation_Dumper_ResService()
    {
        return $this->services['translation.dumper.res'] = new \Symfony\Component\Translation\Dumper\IcuResFileDumper();
    }
    protected function getTranslation_Dumper_XliffService()
    {
        return $this->services['translation.dumper.xliff'] = new \Symfony\Component\Translation\Dumper\XliffFileDumper();
    }
    protected function getTranslation_Dumper_YmlService()
    {
        return $this->services['translation.dumper.yml'] = new \Symfony\Component\Translation\Dumper\YamlFileDumper();
    }
    protected function getTranslation_ExtractorService()
    {
        $this->services['translation.extractor'] = $instance = new \Symfony\Component\Translation\Extractor\ChainExtractor();
        $instance->addExtractor('php', $this->get('translation.extractor.php'));
        return $instance;
    }
    protected function getTranslation_Extractor_PhpService()
    {
        return $this->services['translation.extractor.php'] = new \Symfony\Bundle\FrameworkBundle\Translation\PhpExtractor();
    }
    protected function getTranslation_LoaderService()
    {
        $a = $this->get('translation.loader.xliff');
        $this->services['translation.loader'] = $instance = new \Symfony\Bundle\FrameworkBundle\Translation\TranslationLoader();
        $instance->addLoader('php', $this->get('translation.loader.php'));
        $instance->addLoader('yml', $this->get('translation.loader.yml'));
        $instance->addLoader('xlf', $a);
        $instance->addLoader('xliff', $a);
        $instance->addLoader('po', $this->get('translation.loader.po'));
        $instance->addLoader('mo', $this->get('translation.loader.mo'));
        $instance->addLoader('ts', $this->get('translation.loader.qt'));
        $instance->addLoader('csv', $this->get('translation.loader.csv'));
        $instance->addLoader('res', $this->get('translation.loader.res'));
        $instance->addLoader('dat', $this->get('translation.loader.dat'));
        $instance->addLoader('ini', $this->get('translation.loader.ini'));
        $instance->addLoader('json', $this->get('translation.loader.json'));
        return $instance;
    }
    protected function getTranslation_Loader_CsvService()
    {
        return $this->services['translation.loader.csv'] = new \Symfony\Component\Translation\Loader\CsvFileLoader();
    }
    protected function getTranslation_Loader_DatService()
    {
        return $this->services['translation.loader.dat'] = new \Symfony\Component\Translation\Loader\IcuDatFileLoader();
    }
    protected function getTranslation_Loader_IniService()
    {
        return $this->services['translation.loader.ini'] = new \Symfony\Component\Translation\Loader\IniFileLoader();
    }
    protected function getTranslation_Loader_JsonService()
    {
        return $this->services['translation.loader.json'] = new \Symfony\Component\Translation\Loader\JsonFileLoader();
    }
    protected function getTranslation_Loader_MoService()
    {
        return $this->services['translation.loader.mo'] = new \Symfony\Component\Translation\Loader\MoFileLoader();
    }
    protected function getTranslation_Loader_PhpService()
    {
        return $this->services['translation.loader.php'] = new \Symfony\Component\Translation\Loader\PhpFileLoader();
    }
    protected function getTranslation_Loader_PoService()
    {
        return $this->services['translation.loader.po'] = new \Symfony\Component\Translation\Loader\PoFileLoader();
    }
    protected function getTranslation_Loader_QtService()
    {
        return $this->services['translation.loader.qt'] = new \Symfony\Component\Translation\Loader\QtFileLoader();
    }
    protected function getTranslation_Loader_ResService()
    {
        return $this->services['translation.loader.res'] = new \Symfony\Component\Translation\Loader\IcuResFileLoader();
    }
    protected function getTranslation_Loader_XliffService()
    {
        return $this->services['translation.loader.xliff'] = new \Symfony\Component\Translation\Loader\XliffFileLoader();
    }
    protected function getTranslation_Loader_YmlService()
    {
        return $this->services['translation.loader.yml'] = new \Symfony\Component\Translation\Loader\YamlFileLoader();
    }
    protected function getTranslation_WriterService()
    {
        $this->services['translation.writer'] = $instance = new \Symfony\Component\Translation\Writer\TranslationWriter();
        $instance->addDumper('php', $this->get('translation.dumper.php'));
        $instance->addDumper('xlf', $this->get('translation.dumper.xliff'));
        $instance->addDumper('po', $this->get('translation.dumper.po'));
        $instance->addDumper('mo', $this->get('translation.dumper.mo'));
        $instance->addDumper('yml', $this->get('translation.dumper.yml'));
        $instance->addDumper('ts', $this->get('translation.dumper.qt'));
        $instance->addDumper('csv', $this->get('translation.dumper.csv'));
        $instance->addDumper('ini', $this->get('translation.dumper.ini'));
        $instance->addDumper('json', $this->get('translation.dumper.json'));
        $instance->addDumper('res', $this->get('translation.dumper.res'));
        return $instance;
    }
    protected function getTranslatorService()
    {
        return $this->services['translator'] = new \stdClass();
    }
    protected function getTranslator_DefaultService()
    {
        return $this->services['translator.default'] = new \Symfony\Bundle\FrameworkBundle\Translation\Translator($this, new \Symfony\Component\Translation\MessageSelector(), array('translation.loader.php' => array(0 => 'php'), 'translation.loader.yml' => array(0 => 'yml'), 'translation.loader.xliff' => array(0 => 'xlf', 1 => 'xliff'), 'translation.loader.po' => array(0 => 'po'), 'translation.loader.mo' => array(0 => 'mo'), 'translation.loader.qt' => array(0 => 'ts'), 'translation.loader.csv' => array(0 => 'csv'), 'translation.loader.res' => array(0 => 'res'), 'translation.loader.dat' => array(0 => 'dat'), 'translation.loader.ini' => array(0 => 'ini'), 'translation.loader.json' => array(0 => 'json')), array('cache_dir' => (__DIR__.'/translations'), 'debug' => NULL));
    }
    protected function getTranslatorListenerService()
    {
        return $this->services['translator_listener'] = new \Symfony\Component\HttpKernel\EventListener\TranslatorListener($this->get('translator'), $this->get('request_stack'));
    }
    protected function getUriSignerService()
    {
        return $this->services['uri_signer'] = new \Symfony\Component\HttpKernel\UriSigner('secret');
    }
    protected function getJmsSerializer_UnserializeObjectConstructorService()
    {
        return $this->services['jms_serializer.unserialize_object_constructor'] = new \JMS\Serializer\Construction\UnserializeObjectConstructor();
    }
    public function getParameter($name)
    {
        $name = strtolower($name);
        if (!(isset($this->parameters[$name]) || array_key_exists($name, $this->parameters))) {
            throw new InvalidArgumentException(sprintf('The parameter "%s" must be defined.', $name));
        }
        return $this->parameters[$name];
    }
    public function hasParameter($name)
    {
        $name = strtolower($name);
        return isset($this->parameters[$name]) || array_key_exists($name, $this->parameters);
    }
    public function setParameter($name, $value)
    {
        throw new LogicException('Impossible to call set() on a frozen ParameterBag.');
    }
    public function getParameterBag()
    {
        if (null === $this->parameterBag) {
            $this->parameterBag = new FrozenParameterBag($this->parameters);
        }
        return $this->parameterBag;
    }
    protected function getDefaultParameters()
    {
        return array(
            'kernel.root_dir' => $this->targetDirs[2],
            'kernel.environment' => NULL,
            'kernel.debug' => NULL,
            'kernel.name' => NULL,
            'kernel.cache_dir' => __DIR__,
            'kernel.logs_dir' => ($this->targetDirs[1].'/logs'),
            'kernel.bundles' => array(
                'FrameworkBundle' => 'Symfony\\Bundle\\FrameworkBundle\\FrameworkBundle',
                'OldSoundRabbitMqBundle' => 'OldSound\\RabbitMqBundle\\OldSoundRabbitMqBundle',
                'SimpleBusCommandBusBundle' => 'SimpleBus\\SymfonyBridge\\SimpleBusCommandBusBundle',
                'SimpleBusEventBusBundle' => 'SimpleBus\\SymfonyBridge\\SimpleBusEventBusBundle',
                'SimpleBusAsynchronousBundle' => 'SimpleBus\\AsynchronousBundle\\SimpleBusAsynchronousBundle',
                'SimpleBusRabbitMQBundle' => 'SimpleBus\\RabbitMQBundle\\SimpleBusRabbitMQBundle',
                'JMSSerializerBundle' => 'JMS\\SerializerBundle\\JMSSerializerBundle',
                'SimpleBusJMSSerializerBundle' => 'SimpleBus\\JMSSerializerBundle\\SimpleBusJMSSerializerBundle',
            ),
            'kernel.charset' => 'UTF-8',
            'kernel.container_class' => 'ProjectContainer',
            'kernel.secret' => 'secret',
            'log_file' => ($this->targetDirs[1].'/logs/test.log'),
            'controller_resolver.class' => 'Symfony\\Bundle\\FrameworkBundle\\Controller\\ControllerResolver',
            'controller_name_converter.class' => 'Symfony\\Bundle\\FrameworkBundle\\Controller\\ControllerNameParser',
            'response_listener.class' => 'Symfony\\Component\\HttpKernel\\EventListener\\ResponseListener',
            'streamed_response_listener.class' => 'Symfony\\Component\\HttpKernel\\EventListener\\StreamedResponseListener',
            'locale_listener.class' => 'Symfony\\Component\\HttpKernel\\EventListener\\LocaleListener',
            'event_dispatcher.class' => 'Symfony\\Component\\EventDispatcher\\ContainerAwareEventDispatcher',
            'http_kernel.class' => 'Symfony\\Component\\HttpKernel\\DependencyInjection\\ContainerAwareHttpKernel',
            'filesystem.class' => 'Symfony\\Component\\Filesystem\\Filesystem',
            'cache_warmer.class' => 'Symfony\\Component\\HttpKernel\\CacheWarmer\\CacheWarmerAggregate',
            'cache_clearer.class' => 'Symfony\\Component\\HttpKernel\\CacheClearer\\ChainCacheClearer',
            'file_locator.class' => 'Symfony\\Component\\HttpKernel\\Config\\FileLocator',
            'uri_signer.class' => 'Symfony\\Component\\HttpKernel\\UriSigner',
            'request_stack.class' => 'Symfony\\Component\\HttpFoundation\\RequestStack',
            'fragment.handler.class' => 'Symfony\\Component\\HttpKernel\\Fragment\\FragmentHandler',
            'fragment.renderer.inline.class' => 'Symfony\\Component\\HttpKernel\\Fragment\\InlineFragmentRenderer',
            'fragment.renderer.hinclude.class' => 'Symfony\\Bundle\\FrameworkBundle\\Fragment\\ContainerAwareHIncludeFragmentRenderer',
            'fragment.renderer.hinclude.global_template' => '',
            'fragment.renderer.esi.class' => 'Symfony\\Component\\HttpKernel\\Fragment\\EsiFragmentRenderer',
            'fragment.path' => '/_fragment',
            'translator.class' => 'Symfony\\Bundle\\FrameworkBundle\\Translation\\Translator',
            'translator.identity.class' => 'Symfony\\Component\\Translation\\IdentityTranslator',
            'translator.selector.class' => 'Symfony\\Component\\Translation\\MessageSelector',
            'translation.loader.php.class' => 'Symfony\\Component\\Translation\\Loader\\PhpFileLoader',
            'translation.loader.yml.class' => 'Symfony\\Component\\Translation\\Loader\\YamlFileLoader',
            'translation.loader.xliff.class' => 'Symfony\\Component\\Translation\\Loader\\XliffFileLoader',
            'translation.loader.po.class' => 'Symfony\\Component\\Translation\\Loader\\PoFileLoader',
            'translation.loader.mo.class' => 'Symfony\\Component\\Translation\\Loader\\MoFileLoader',
            'translation.loader.qt.class' => 'Symfony\\Component\\Translation\\Loader\\QtFileLoader',
            'translation.loader.csv.class' => 'Symfony\\Component\\Translation\\Loader\\CsvFileLoader',
            'translation.loader.res.class' => 'Symfony\\Component\\Translation\\Loader\\IcuResFileLoader',
            'translation.loader.dat.class' => 'Symfony\\Component\\Translation\\Loader\\IcuDatFileLoader',
            'translation.loader.ini.class' => 'Symfony\\Component\\Translation\\Loader\\IniFileLoader',
            'translation.loader.json.class' => 'Symfony\\Component\\Translation\\Loader\\JsonFileLoader',
            'translation.dumper.php.class' => 'Symfony\\Component\\Translation\\Dumper\\PhpFileDumper',
            'translation.dumper.xliff.class' => 'Symfony\\Component\\Translation\\Dumper\\XliffFileDumper',
            'translation.dumper.po.class' => 'Symfony\\Component\\Translation\\Dumper\\PoFileDumper',
            'translation.dumper.mo.class' => 'Symfony\\Component\\Translation\\Dumper\\MoFileDumper',
            'translation.dumper.yml.class' => 'Symfony\\Component\\Translation\\Dumper\\YamlFileDumper',
            'translation.dumper.qt.class' => 'Symfony\\Component\\Translation\\Dumper\\QtFileDumper',
            'translation.dumper.csv.class' => 'Symfony\\Component\\Translation\\Dumper\\CsvFileDumper',
            'translation.dumper.ini.class' => 'Symfony\\Component\\Translation\\Dumper\\IniFileDumper',
            'translation.dumper.json.class' => 'Symfony\\Component\\Translation\\Dumper\\JsonFileDumper',
            'translation.dumper.res.class' => 'Symfony\\Component\\Translation\\Dumper\\IcuResFileDumper',
            'translation.extractor.php.class' => 'Symfony\\Bundle\\FrameworkBundle\\Translation\\PhpExtractor',
            'translation.loader.class' => 'Symfony\\Bundle\\FrameworkBundle\\Translation\\TranslationLoader',
            'translation.extractor.class' => 'Symfony\\Component\\Translation\\Extractor\\ChainExtractor',
            'translation.writer.class' => 'Symfony\\Component\\Translation\\Writer\\TranslationWriter',
            'property_accessor.class' => 'Symfony\\Component\\PropertyAccess\\PropertyAccessor',
            'kernel.http_method_override' => true,
            'kernel.trusted_hosts' => array(
            ),
            'kernel.trusted_proxies' => array(
            ),
            'kernel.default_locale' => 'en',
            'security.secure_random.class' => 'Symfony\\Component\\Security\\Core\\Util\\SecureRandom',
            'data_collector.templates' => array(
            ),
            'annotations.reader.class' => 'Doctrine\\Common\\Annotations\\AnnotationReader',
            'annotations.cached_reader.class' => 'Doctrine\\Common\\Annotations\\CachedReader',
            'annotations.file_cache_reader.class' => 'Doctrine\\Common\\Annotations\\FileCacheReader',
            'debug.debug_handlers_listener.class' => 'Symfony\\Component\\HttpKernel\\EventListener\\DebugHandlersListener',
            'debug.stopwatch.class' => 'Symfony\\Component\\Stopwatch\\Stopwatch',
            'debug.error_handler.throw_at' => 0,
            'old_sound_rabbit_mq.connection.class' => 'PhpAmqpLib\\Connection\\AMQPConnection',
            'old_sound_rabbit_mq.lazy.connection.class' => 'PhpAmqpLib\\Connection\\AMQPLazyConnection',
            'old_sound_rabbit_mq.producer.class' => 'OldSound\\RabbitMqBundle\\RabbitMq\\Producer',
            'old_sound_rabbit_mq.consumer.class' => 'OldSound\\RabbitMqBundle\\RabbitMq\\Consumer',
            'old_sound_rabbit_mq.multi_consumer.class' => 'OldSound\\RabbitMqBundle\\RabbitMq\\MultipleConsumer',
            'old_sound_rabbit_mq.anon_consumer.class' => 'OldSound\\RabbitMqBundle\\RabbitMq\\AnonConsumer',
            'old_sound_rabbit_mq.rpc_client.class' => 'OldSound\\RabbitMqBundle\\RabbitMq\\RpcClient',
            'old_sound_rabbit_mq.rpc_server.class' => 'OldSound\\RabbitMqBundle\\RabbitMq\\RpcServer',
            'old_sound_rabbit_mq.logged.channel.class' => 'OldSound\\RabbitMqBundle\\RabbitMq\\AMQPLoggedChannel',
            'old_sound_rabbit_mq.data_collector.class' => 'OldSound\\RabbitMqBundle\\DataCollector\\MessageDataCollector',
            'old_sound_rabbit_mq.parts_holder.class' => 'OldSound\\RabbitMqBundle\\RabbitMq\\AmqpPartsHolder',
            'old_sound_rabbit_mq.fallback.class' => 'OldSound\\RabbitMqBundle\\RabbitMq\\Fallback',
            'jms_serializer.metadata.file_locator.class' => 'Metadata\\Driver\\FileLocator',
            'jms_serializer.metadata.annotation_driver.class' => 'JMS\\Serializer\\Metadata\\Driver\\AnnotationDriver',
            'jms_serializer.metadata.chain_driver.class' => 'Metadata\\Driver\\DriverChain',
            'jms_serializer.metadata.yaml_driver.class' => 'JMS\\Serializer\\Metadata\\Driver\\YamlDriver',
            'jms_serializer.metadata.xml_driver.class' => 'JMS\\Serializer\\Metadata\\Driver\\XmlDriver',
            'jms_serializer.metadata.php_driver.class' => 'JMS\\Serializer\\Metadata\\Driver\\PhpDriver',
            'jms_serializer.metadata.doctrine_type_driver.class' => 'JMS\\Serializer\\Metadata\\Driver\\DoctrineTypeDriver',
            'jms_serializer.metadata.doctrine_phpcr_type_driver.class' => 'JMS\\Serializer\\Metadata\\Driver\\DoctrinePHPCRTypeDriver',
            'jms_serializer.metadata.lazy_loading_driver.class' => 'Metadata\\Driver\\LazyLoadingDriver',
            'jms_serializer.metadata.metadata_factory.class' => 'Metadata\\MetadataFactory',
            'jms_serializer.metadata.cache.file_cache.class' => 'Metadata\\Cache\\FileCache',
            'jms_serializer.event_dispatcher.class' => 'JMS\\Serializer\\EventDispatcher\\LazyEventDispatcher',
            'jms_serializer.camel_case_naming_strategy.class' => 'JMS\\Serializer\\Naming\\CamelCaseNamingStrategy',
            'jms_serializer.serialized_name_annotation_strategy.class' => 'JMS\\Serializer\\Naming\\SerializedNameAnnotationStrategy',
            'jms_serializer.cache_naming_strategy.class' => 'JMS\\Serializer\\Naming\\CacheNamingStrategy',
            'jms_serializer.doctrine_object_constructor.class' => 'JMS\\Serializer\\Construction\\DoctrineObjectConstructor',
            'jms_serializer.unserialize_object_constructor.class' => 'JMS\\Serializer\\Construction\\UnserializeObjectConstructor',
            'jms_serializer.version_exclusion_strategy.class' => 'JMS\\Serializer\\Exclusion\\VersionExclusionStrategy',
            'jms_serializer.serializer.class' => 'JMS\\Serializer\\Serializer',
            'jms_serializer.twig_extension.class' => 'JMS\\Serializer\\Twig\\SerializerExtension',
            'jms_serializer.templating.helper.class' => 'JMS\\SerializerBundle\\Templating\\SerializerHelper',
            'jms_serializer.json_serialization_visitor.class' => 'JMS\\Serializer\\JsonSerializationVisitor',
            'jms_serializer.json_serialization_visitor.options' => 0,
            'jms_serializer.json_deserialization_visitor.class' => 'JMS\\Serializer\\JsonDeserializationVisitor',
            'jms_serializer.xml_serialization_visitor.class' => 'JMS\\Serializer\\XmlSerializationVisitor',
            'jms_serializer.xml_deserialization_visitor.class' => 'JMS\\Serializer\\XmlDeserializationVisitor',
            'jms_serializer.xml_deserialization_visitor.doctype_whitelist' => array(
            ),
            'jms_serializer.yaml_serialization_visitor.class' => 'JMS\\Serializer\\YamlSerializationVisitor',
            'jms_serializer.handler_registry.class' => 'JMS\\Serializer\\Handler\\LazyHandlerRegistry',
            'jms_serializer.datetime_handler.class' => 'JMS\\Serializer\\Handler\\DateHandler',
            'jms_serializer.array_collection_handler.class' => 'JMS\\Serializer\\Handler\\ArrayCollectionHandler',
            'jms_serializer.php_collection_handler.class' => 'JMS\\Serializer\\Handler\\PhpCollectionHandler',
            'jms_serializer.form_error_handler.class' => 'JMS\\Serializer\\Handler\\FormErrorHandler',
            'jms_serializer.constraint_violation_handler.class' => 'JMS\\Serializer\\Handler\\ConstraintViolationHandler',
            'jms_serializer.doctrine_proxy_subscriber.class' => 'JMS\\Serializer\\EventDispatcher\\Subscriber\\DoctrineProxySubscriber',
            'jms_serializer.stopwatch_subscriber.class' => 'JMS\\SerializerBundle\\Serializer\\StopwatchEventSubscriber',
            'jms_serializer.infer_types_from_doctrine_metadata' => true,
            'console.command.ids' => array(
            ),
        );
    }
}
