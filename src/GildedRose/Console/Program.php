<?php

namespace GildedRose\Console;

/**
 * Hi and welcome to team Gilded Rose.
 *
 * As you know, we are a small inn with a prime location in a prominent city
 * ran by a friendly innkeeper named Allison. We also buy and sell only the
 * finest goods. Unfortunately, our goods are constantly degrading in quality
 * as they approach their sell by date. We have a system in place that updates
 * our inventory for us. It was developed by a no-nonsense type named Leeroy,
 * who has moved on to new adventures. Your task is to add the new feature to
 * our system so that we can begin selling a new category of items. First an
 * introduction to our system:
 *
 * - All items have a SellIn value which denotes the number of days we have to sell the item
 * - All items have a Quality value which denotes how valuable the item is
 * - At the end of each day our system lowers both values for every item
 *
 * Pretty simple, right? Well this is where it gets interesting:
 *
 * - Once the sell by date has passed, Quality degrades twice as fast
 * - The Quality of an item is never negative
 * - "Aged Brie" actually increases in Quality the older it gets
 * - The Quality of an item is never more than 50
 * - "Sulfuras", being a legendary item, never has to be sold or decreases in Quality
 * - "Backstage passes", like aged brie, increases in Quality as it's SellIn
 *   value approaches; Quality increases by 2 when there are 10 days or less and
 *   by 3 when there are 5 days or less but Quality drops to 0 after the concert
 *
 * We have recently signed a supplier of conjured items. This requires an
 * update to our system:
 *
 * - "Conjured" items degrade in Quality twice as fast as normal items
 *
 * Feel free to make any changes to the UpdateQuality method and add any new
 * code as long as everything still works correctly. However, do not alter the
 * Item class or Items property as those belong to the goblin in the corner who
 * will insta-rage and one-shot you as he doesn't believe in shared code
 * ownership (you can make the UpdateQuality method and Items property static
 * if you like, we'll cover for you).
 *
 * Just for clarification, an item can never have its Quality increase above
 * 50, however "Sulfuras" is a legendary item and as such its Quality is 80 and
 * it never alters.
 */
const AGEDBRIE_NAME = 'Aged Brie';
const DEXTERITY_NAME = '+5 Dexterity Vest';
const ELIXIR_NAME = 'Elixir of the Mongoose';
const SULFURAS_NAME = 'Sulfuras, Hand of Ragnaros';
const BACKSTAGE_NAME = 'Backstage passes to a TAFKAL80ETC concert';
const CONJURED_NAME = 'Conjured Mana Cake';

class Program
{
    /**
     * @var array
     */
    private $items = array();

    public static function main()
    {
        echo 'OMGHAI!' . PHP_EOL;

        $app = new Program([
            new Item(['name' => DEXTERITY_NAME, 'sellIn' => 10, 'quality' => 20]),
            new Item(['name' => AGEDBRIE_NAME, "sellIn" => 2, 'quality' => 0]),
            new Item(['name' => ELIXIR_NAME, 'sellIn' => 5, 'quality' => 7]),
            new Item(['name' => SULFURAS_NAME, 'sellIn' => 0, 'quality' => 80]),
            new Item(['name' => BACKSTAGE_NAME, 'sellIn'    => 15, 'quality'   => 20]),
            new Item(array('name' => CONJURED_NAME,'sellIn' => 3,'quality' => 6)),
        ]);

        $app->UpdateQuality();

        echo sprintf('%50s - %7s - %7s', 'Name', 'SellIn', 'Quality') . PHP_EOL;
        foreach ($app->items as $item) {
            echo sprintf('%50s - %7d - %7d', $item->name, $item->sellIn, $item->quality) . PHP_EOL;
        }
    }

    public function __construct(array $items)
    {
        $this->items = $items;
    }

    public function UpdateQuality()
    {
        foreach($this->items as &$item) {
            $this->manageQualityItem($item);
            $this->decreaseOneDayIfNotSulfuras($item);
            $this->manageQualityAfterSellIn($item);
        }
    }

    /**
     * @param $item
     */
    private function manageQualityItem($item)
    {
        if ($this->itemNameIs($item, AGEDBRIE_NAME) || $this->itemNameIs($item, BACKSTAGE_NAME)) {
            $this->increaseQualityIfItIsLessThanFifty($item);
            $this->increaseBackStageExtraQualityByDay($item);
        } else {
            $this->decreaseQualityIfNotSulfurasAndGreaterThanZero($item);
        }
    }

    /**
     * @param $item
     */
    private function decreaseOneDayIfNotSulfuras($item)
    {
        if (!$this->itemNameIs($item, SULFURAS_NAME)) {
            $this->decreaseOneDay($item);
        }
    }

    /**
     * @param $item
     */
    private function manageQualityAfterSellIn($item)
    {
        if ($this->daysLeftToSellInItemIs(0, $item)) {
            if ($this->itemNameIs($item, "Aged Brie")) {
                $this->increaseQualityIfItIsLessThanFifty($item);
            } else {
                if ($this->itemNameIs($item, BACKSTAGE_NAME)) {
                    $item->quality = 0;
                } else {
                    $this->decreaseQualityIfNotSulfurasAndGreaterThanZero($item);
                }
            }
        }
    }

    /**
     * @param $item
     */
    private function increaseQualityIfItIsLessThanFifty($item)
    {
        if ($item->quality < 50) {
            $this->increaseQuality($item);
        }
    }

    /**
     * @return array
     */
    public function getItems()
    {
        return $this->items;
    }


    /**
     * @param $item
     */
    private function decreaseQualityIfNotSulfurasAndGreaterThanZero($item)
    {
        if (!$this->itemNameIs($item, SULFURAS_NAME)) {
            $this->decreaseQuality($item);
            $this->decreaseConjuredExtraQuality($item);
        }
    }


    /**
     * @param $item
     */
    private function increaseBackStageExtraQualityByDay($item)
    {
        if ($this->itemNameIs($item, BACKSTAGE_NAME)) {
            if ($this->daysLeftToSellInItemIs(11, $item)) {
                $this->increaseQualityIfItIsLessThanFifty($item);
            }

            if ($this->daysLeftToSellInItemIs(6, $item)) {
                $this->increaseQualityIfItIsLessThanFifty($item);
            }
        }
    }

    /**
     * @param $item
     */
    private function decreaseConjuredExtraQuality($item)
    {
        if($this->itemNameIs($item, CONJURED_NAME)) {
            $this->decreaseQuality($item);
        }
    }

    /**
     * @param $item
     */
    private function increaseQuality($item)
    {
        $item->quality++;
    }

    /**
     * @param $item
     */
    private function decreaseQuality($item)
    {
        if($item->quality > 0) {
            $item->quality--;
        }
    }

    /**
     * @param $item
     */
    private function decreaseOneDay($item)
    {
        $item->sellIn--;
    }

    /**
     * @param $days
     * @param $item
     * @return bool
     */
    private function daysLeftToSellInItemIs($days,$item)
    {
        return $item->sellIn < $days;
    }


    /**
     * @param $item
     * @return bool
     */
    private function itemNameIs($item, $name)
    {
        return $item->name == $name;
    }

}
