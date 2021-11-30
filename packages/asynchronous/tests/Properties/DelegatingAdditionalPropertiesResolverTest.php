<?php

namespace SimpleBus\Asynchronous\Tests\Properties;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use SimpleBus\Asynchronous\Properties\AdditionalPropertiesResolver;
use SimpleBus\Asynchronous\Properties\DelegatingAdditionalPropertiesResolver;
use stdClass;

class DelegatingAdditionalPropertiesResolverTest extends TestCase
{
    /**
     * @test
     */
    public function itShouldMergeMultipleResolvers(): void
    {
        $message = $this->messageDummy();

        $resolver = new DelegatingAdditionalPropertiesResolver([
            $this->getResolver($message, ['test' => 'a']),
            $this->getResolver($message, ['test' => 'b', 'priority' => 123]),
        ]);

        $this->assertSame(['test' => 'b', 'priority' => 123], $resolver->resolveAdditionalPropertiesFor($message));
    }

    /**
     * @param mixed[] $data
     *
     * @return AdditionalPropertiesResolver|MockObject
     */
    private function getResolver(object $message, array $data)
    {
        $resolver = $this->createMock(AdditionalPropertiesResolver::class);
        $resolver->expects($this->once())
            ->method('resolveAdditionalPropertiesFor')
            ->with($this->identicalTo($message))
            ->will($this->returnValue($data));

        return $resolver;
    }

    private function messageDummy(): stdClass
    {
        return new stdClass();
    }
}
