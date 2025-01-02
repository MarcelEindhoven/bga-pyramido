<?php
/**
 *------
 * BGA framework: Gregory Isabelli & Emmanuel Colin & BoardGameArena
 * PyramidoCannonFodder implementation : © Marcel van Nieuwenhoven marcel.eindhoven@hotmail.com
 *
 * This code has been produced on the BGA studio platform for use on http://boardgamearena.com.
 * See http://en.boardgamearena.com/#!doc/Studio for more information.
 * -----
 *
 */
declare(strict_types=1);

namespace Bga\Games\PyramidoCannonFodder\Infrastructure;

#[\AllowDynamicProperties]
class DominoFactory
{
    protected array $definitions = [];

    static public function create($deck_domino): DominoFactory {
        $object = new DominoFactory();
        $object->set_deck($deck_domino);
        return $object;
    }

    public function set_deck($deck) {
        $this->deck = $deck;
    }

    public function add($first_colour, $second_colour) {
        $this->definitions[] = array( 'type' => $first_colour, 'type_arg' => $second_colour, 'nbr' => 1);
    }
    public function flush() {
        $this->deck->createCards($this->definitions);
        $this->deck->shuffle('deck');
        $this->definitions = [];
    }
}

#[\AllowDynamicProperties]
class MarketSetup
{
    const MARKET_SIZE = 3;
    static public function create($deck_domino): MarketSetup {
        $object = new MarketSetup();
        $object->set_deck($deck_domino);
        return $object;
    }

    public function set_deck($deck) : MarketSetup {
        $this->deck = $deck;
        return $this;
    }

    public function setup_market() : MarketSetup {
        for ($i=0; $i < MarketSetup::MARKET_SIZE; $i++) 
            $this->deck->pickCardForLocation('deck', 'market', $i);
        return $this;
    }

    public function setup_next() : MarketSetup {
        for ($i=0; $i < MarketSetup::MARKET_SIZE + 1; $i++) 
            $this->deck->pickCardForLocation('deck', 'next', $i);
        return $this;
    }
}

#[\AllowDynamicProperties]
class CurrentMarket
{
    static public function create($deck_domino): CurrentMarket {
        $object = new CurrentMarket();
        $object->set_deck($deck_domino);
        return $object;
    }

    public function set_deck($deck) {
        $this->deck = $deck;
    }

    public function get_market() : array {
        return $this->get_market_entries('market');
    }

    public function get_next_market() : array {
        return $this->get_market_entries('next');
    }

    public function get_market_entries($category) : array {
        $dominoes = [];
        $cards = $this->deck->getCardsInLocation($category);
        foreach ($cards as $card) {
            $dominoes[] = ['id' => $card['id'], 'tiles' => [['colour' => $card['type']], ['colour' => $card['type_arg']]]];
        }
        return $dominoes;
    }
}
