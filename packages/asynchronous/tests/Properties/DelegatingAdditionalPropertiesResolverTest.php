<?php

namespace SimpleBus\Asynchronous\Tests\Properties;

use PHPUnit\Framework\TestCase;
use SimpleBus\Asynchronous\Properties\AdditionalPropertiesResolver;
use SimpleBus\Asynchronous\Properties\DelegatingAdditionalPropertiesResolver;

/**
 * @internal
 * @coversNothing
 */
class DelegatingAdditionalPropertiesResolverTest extends TestCase
{
    /**
     * @test
     */
    public function itShouldMergeMultipleResolvers()
    {
        $message = $this->messageDummy();

        $resolver = new DelegatingAdditionalPropertiesResolver([
            $this->getResolver($message, ['test' => 'a']),
            $this->getResolver($message, ['test' => 'b', 'priority' => 123]),
        ]);

        $this->assertSame(['test' => 'b', 'priority' => 123], $resolver->resolveAdditionalPropertiesFor($message));
    }

    /**
     * @param object $message
     *
     * @return AdditionalPropertiesResolver|\PHPUnit\Framework\MockObject\MockObject
     */
    private function getResolver($message, array $data)
    {
        $resolver = $this->createMock('SimpleBus\Asynchronous\Properties\AdditionalPropertiesResolver');
        $resolver->expects($this->once())
            ->method('resolveAdditionalPropertiesFor')
            ->with($this->identicalTo($message))
            ->will($this->returnValue($data));

        return $resolver;
    }

    /**
     * @return object|\PHPUnit\Framework\MockObject\MockObject
     */
    private function messageDummy()
    {
        return new \stdClass();
    }
}
