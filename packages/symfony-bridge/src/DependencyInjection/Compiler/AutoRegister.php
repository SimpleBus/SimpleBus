<?php

namespace SimpleBus\SymfonyBridge\DependencyInjection\Compiler;

use ReflectionClass;
use ReflectionMethod;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

final class AutoRegister implements CompilerPassInterface
{
    private string $tagName;
    private string $tagAttribute;

    public function __construct(string $tagName, string $tagAttribute)
    {
        $this->tagName = $tagName;
        $this->tagAttribute = $tagAttribute;
    }

    public function process(ContainerBuilder $container): void
    {
        foreach ($container->findTaggedServiceIds($this->tagName) as $serviceId => $tags) {
            foreach ($tags as $tagAttributes) {
                // if tag attribute is set, skip
                if (isset($tagAttributes[$this->tagAttribute])) {
                    continue;
                }

                $registerPublicMethods = false;
                if (isset($tagAttributes['register_public_methods']) && true === $tagAttributes['register_public_methods']) {
                    $registerPublicMethods = true;
                }

                $definition = $container->getDefinition($serviceId);

                // check if service id is class name
                $reflectionClass = new ReflectionClass($definition->getClass() ?: $serviceId);

                $methods = $reflectionClass->getMethods(ReflectionMethod::IS_PUBLIC);

                $tagAttributes = [];
                foreach ($methods as $method) {
                    if (true === $method->isConstructor()) {
                        continue;
                    }

                    if (true === $method->isDestructor()) {
                        continue;
                    }

                    if (false === $registerPublicMethods && '__invoke' !== $method->getName()) {
                        continue;
                    }

                    $parameters = $method->getParameters();

                    // if no param, optional param or non-class param, skip
                    if (1 !== count($parameters) || $parameters[0]->isOptional() || null === $parameters[0]->getType()) {
                        continue;
                    }

                    // get the class name
                    $handles = $parameters[0]->getType()->getName();

                    $tagAttributes[] = [
                        $this->tagAttribute => $handles,
                        'method' => $method->getName(),
                    ];
                }

                if (0 !== count($tags)) {
                    // auto handle
                    $definition->clearTag($this->tagName);

                    foreach ($tagAttributes as $attributes) {
                        $definition->addTag($this->tagName, $attributes);
                    }
                }
            }
        }
    }
}
