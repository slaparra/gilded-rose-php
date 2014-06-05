<?php

namespace GildedRose\Test\Console;

use GildedRose\Console\Item;
use GildedRose\Console\Program;
use PHPUnit_Framework_TestCase;

class ProgramTest extends PHPUnit_Framework_TestCase
{
    private $app;

    public function setUp()
    {
        $this->app = new Program([
            new Item(['name' => '+5 Dexterity Vest', 'sellIn' => 10, 'quality' => 20]),
            new Item(['name' => 'Aged Brie', "sellIn" => 2, 'quality' => 0]),
            new Item(['name' => 'Elixir of the Mongoose', 'sellIn' => 5, 'quality' => 7]),
            new Item(['name' => 'Sulfuras, Hand of Ragnaros', 'sellIn' => 0, 'quality' => 80]),
            new Item([
                'name'      => 'Backstage passes to a TAFKAL80ETC concert',
                'sellIn'    => 15,
                'quality'   => 20
            ]),
            new Item(array('name' => 'Conjured Mana Cake','sellIn' => 3,'quality' => 6)),
        ]);
    }

    /**
     * @test
     */
    public function executeMainFunctionAndReturnTrue()
    {
        ob_start();
        Program::main();
        $output = ob_get_clean();

        $temp = 'OMGHAI!Name-SellIn-Quality+5DexterityVest-9-19AgedBrie-1-1ElixiroftheMongoose-4-6Sulfuras,HandofRagnaros-0-80BackstagepassestoaTAFKAL80ETCconcert-14-21ConjuredManaCake-2-4';
        $this->assertTrue(str_replace(array(" ","\n"),"",$output) == $temp);
    }

    /**
     * @test
     */
    public function sellDaysPassedQualityDegreeTwice()
    {
        $items = $this->increaseNDaysAndReturnItems(14);
        $this->assertSame(2, $items[0]->quality);
        $this->assertSame(26, $items[1]->quality);
        $this->assertSame(0, $items[2]->quality);
        $this->assertSame(80, $items[3]->quality);
        $this->assertSame(47, $items[4]->quality);
        $this->assertSame(0, $items[5]->quality);
    }

    /**
     * @test
     */
    public function itemQualityIsNeverNegative()
    {
        $items = $this->increaseNDaysAndReturnItems(40);
        foreach($items as $item) {
            $this->assertSame(true, $item->quality >= 0);
        }
    }

    /**
     * @test
     */
    public function qualityItemNeverGreaterThanFifty()
    {
        $items = $this->increaseNDaysAndReturnItems(50);
        $this->assertSame(true, $items[1]->quality <= 50);
        $this->assertSame(true, $items[4]->quality <= 50);
    }

    /**
     * @test
     */
    public function sulfurasNeverSoldAndNeverDecreaseQuality()
    {
        $items = $this->increaseNDaysAndReturnItems(20);
        $this->assertSame(true, $items[3]->sellIn === 0 && $items[3]->quality === 80);
    }

    /**
     * @test
     */
    public function backStagePassesQuality()
    {
        $items = $this->increaseNDaysAndReturnItems(5);
        $this->assertSame(true, $items[4]->quality === 25);
        $items = $this->increaseNDaysAndReturnItems(5);
        $this->assertSame(true, $items[4]->quality === 35);
        $items = $this->increaseNDaysAndReturnItems(5);
        $this->assertSame(true, $items[4]->quality === 50);
        $items = $this->increaseNDaysAndReturnItems(5);
        $this->assertSame(true, $items[4]->quality === 0);

    }

    /**
     * @test
     */
    public function conjuredDecreaseTwice()
    {
        $items = $this->increaseNDaysAndReturnItems(2);
        $this->assertSame(true, $items[5]->quality === 2);
    }

    /**
     * @return mixed
     */
    private function increaseNDaysAndReturnItems($ndays)
    {
        for($i=0;$i<$ndays;$i++) {
            $this->app->UpdateQuality();
        }
        return $this->app->getItems();
    }
}