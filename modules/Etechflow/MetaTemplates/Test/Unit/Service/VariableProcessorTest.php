<?php
declare(strict_types=1);

namespace Etechflow\MetaTemplates\Test\Unit\Service;

use Etechflow\MetaTemplates\Service\VariableProcessor;
use Magento\Catalog\Model\Product;
use Magento\Framework\Pricing\PriceCurrencyInterface;
use Magento\Store\Api\Data\StoreInterface;
use Magento\Store\Model\StoreManagerInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class VariableProcessorTest extends TestCase
{
    private VariableProcessor $processor;
    /** @var \Magento\Store\Model\Store&MockObject */
    private $store;

    protected function setUp(): void
    {
        $this->store = $this->getMockBuilder(\Magento\Store\Model\Store::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getFrontendName', 'getBaseUrl', 'getId'])
            ->getMock();
        $this->store->method('getFrontendName')->willReturn('Keystation');
        $this->store->method('getBaseUrl')->willReturn('https://example.test/');
        $this->store->method('getId')->willReturn(1);

        $storeManager = $this->createMock(StoreManagerInterface::class);
        $storeManager->method('getStore')->willReturn($this->store);

        $priceCurrency = $this->createMock(PriceCurrencyInterface::class);
        $priceCurrency->method('format')->willReturn('£3.94');

        $this->processor = new VariableProcessor($storeManager, $priceCurrency);
    }

    public function testEmptyTemplateReturnsEmptyString(): void
    {
        $this->assertSame('', $this->processor->process('', ['store' => $this->store]));
        $this->assertSame('', $this->processor->process(null, ['store' => $this->store]));
    }

    public function testPlainTextPassesThrough(): void
    {
        $this->assertSame('Buy keys online', $this->processor->process('Buy keys online', ['store' => $this->store]));
    }

    public function testStoreNameAndProductName(): void
    {
        $product = $this->productMock(['getName' => 'CN3 Chip']);
        $out = $this->processor->process(
            '{{product.name}} | Buy at {{store.name}}',
            ['store' => $this->store, 'product' => $product]
        );
        $this->assertSame('CN3 Chip | Buy at Keystation', $out);
    }

    public function testProductPriceUsesCurrencyFormatter(): void
    {
        $product = $this->productMock(['getFinalPrice' => 3.94]);
        $out = $this->processor->process('From {{product.price}}', ['store' => $this->store, 'product' => $product]);
        $this->assertSame('From £3.94', $out);
    }

    public function testFallbackUsedWhenEmpty(): void
    {
        $product = $this->productMock([]);
        $out = $this->processor->process('{{product.brand|Genuine Part}}', ['store' => $this->store, 'product' => $product]);
        $this->assertSame('Genuine Part', $out);
    }

    public function testUnknownObjectResolvesEmpty(): void
    {
        $out = $this->processor->process('x{{foo.bar}}y', ['store' => $this->store]);
        $this->assertSame('xy', $out);
    }

    /**
     * @param array<string,mixed> $returns
     * @return Product&MockObject
     */
    private function productMock(array $returns)
    {
        $product = $this->getMockBuilder(Product::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getName', 'getSku', 'getFinalPrice', 'getAttributeText', 'getData'])
            ->getMock();
        $product->method('getName')->willReturn($returns['getName'] ?? '');
        $product->method('getSku')->willReturn($returns['getSku'] ?? '');
        $product->method('getFinalPrice')->willReturn($returns['getFinalPrice'] ?? 0.0);
        $product->method('getAttributeText')->willReturn(false);
        $product->method('getData')->willReturn(null);
        return $product;
    }
}
