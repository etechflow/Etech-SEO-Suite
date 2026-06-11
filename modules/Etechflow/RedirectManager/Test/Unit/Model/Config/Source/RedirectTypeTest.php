<?php
declare(strict_types=1);

namespace Etechflow\RedirectManager\Test\Unit\Model\Config\Source;

use Etechflow\RedirectManager\Model\Config\Source\RedirectType;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Etechflow\RedirectManager\Model\Config\Source\RedirectType
 */
class RedirectTypeTest extends TestCase
{
    private RedirectType $source;

    protected function setUp(): void
    {
        $this->source = new RedirectType();
    }

    public function testReturnsExactlyTwoOptions(): void
    {
        $options = $this->source->toOptionArray();
        $this->assertCount(2, $options, 'Redirect type must offer exactly 301 and 302');
    }

    public function testOffers301And302(): void
    {
        $values = array_column($this->source->toOptionArray(), 'value');
        $this->assertContains(301, $values, '301 must be available');
        $this->assertContains(302, $values, '302 must be available');
    }

    public function testEveryOptionHasAValueAndLabel(): void
    {
        foreach ($this->source->toOptionArray() as $option) {
            $this->assertArrayHasKey('value', $option);
            $this->assertArrayHasKey('label', $option);
            $this->assertNotEmpty((string)$option['label']);
        }
    }
}
