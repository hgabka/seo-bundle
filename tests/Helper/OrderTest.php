<?php

namespace Hgabka\SeoBundle\Tests\Helper;

use Hgabka\SeoBundle\Helper\Order;
use Hgabka\SeoBundle\Helper\OrderConverter;
use Hgabka\SeoBundle\Helper\OrderItem;
use Hgabka\SeoBundle\Helper\OrderPreparer;
use PHPUnit\Framework\TestCase;

class OrderTest extends TestCase
{
    private function item(string $sku, float $price, float $quantity, float $taxes): OrderItem
    {
        return (new OrderItem())->setSKU($sku)->setName('Item ' . $sku)->setCategoryOrVariation('Cat')->setUnitPrice($price)->setQuantity($quantity)->setTaxes($taxes);
    }

    public function testItemValueIsUnitPriceTimesQuantity(): void
    {
        $this->assertSame(270.0, $this->item('A', 13.5, 20, 56.7)->getValue());
    }

    public function testItemSettersCastNumbers(): void
    {
        $item = (new OrderItem())->setUnitPrice('12.5')->setQuantity('3')->setTaxes('1.5');

        $this->assertSame(12.5, $item->getUnitPrice());
        $this->assertSame(3.0, $item->getQuantity());
        $this->assertSame(1.5, $item->getTaxes());
    }

    public function testEmptyOrderTotalsAreEmptyStrings(): void
    {
        $order = new Order();

        $this->assertSame('', $order->getTotal());
        $this->assertSame('', $order->getTaxesTotal());
    }

    public function testOrderTotalsAccumulateItems(): void
    {
        $order = new Order();
        $order->orderItems[] = $this->item('A', 10, 2, 4);
        $order->orderItems[] = $this->item('B', 5, 1, 1);

        $this->assertSame(25.0, $order->getTotal());
        $this->assertSame(5.0, $order->getTaxesTotal());
    }

    public function testOrderAccessors(): void
    {
        $order = (new Order())->setTransactionID('T1')->setStoreName('Shop')->setShippingTotal('5')->setCity('Budapest')->setStateOrProvince('BP')->setCountry('HU');

        $this->assertSame(['T1', 'Shop', 5.0, 'Budapest', 'BP', 'HU'], [
            $order->getTransactionID(), $order->getStoreName(), $order->getShippingTotal(), $order->getCity(), $order->getStateOrProvince(), $order->getCountry(),
        ]);
    }

    public function testPreparerMergesItemsWithTheSameSku(): void
    {
        $order = new Order();
        $order->orderItems = [$this->item('A', 10, 1, 2), $this->item('B', 5, 1, 1), $this->item('A', 10, 3, 6)];

        $prepared = (new OrderPreparer())->prepare($order);

        $this->assertSame($order, $prepared);
        $this->assertSame(['A', 'B'], array_keys($prepared->orderItems));
        $this->assertSame(4.0, $prepared->orderItems['A']->getQuantity());
        $this->assertSame(8.0, $prepared->orderItems['A']->getTaxes());
        $this->assertSame(1.0, $prepared->orderItems['B']->getQuantity());
    }

    public function testConverterFormatsNumbersWithTwoDecimals(): void
    {
        $order = (new Order())->setTransactionID('T1')->setStoreName('Shop')->setShippingTotal(5)->setCity('Budapest')->setCountry('HU');
        $order->orderItems[] = $this->item('A', 13.5, 20, 56.7);

        $this->assertSame(
            [
                'transaction_id' => 'T1',
                'store_name' => 'Shop',
                'total' => '270.00',
                'taxes_total' => '56.70',
                'shipping_total' => '5.00',
                'city' => 'Budapest',
                'state_or_province' => '',
                'country' => 'HU',
                'order_items' => [
                    [
                        'sku' => 'A',
                        'quantity' => '20.00',
                        'unit_price' => '13.50',
                        'taxes' => '56.70',
                        'category_or_variation' => 'Cat',
                        'name' => 'Item A',
                    ],
                ],
            ],
            (new OrderConverter())->convert($order)
        );
    }
}
