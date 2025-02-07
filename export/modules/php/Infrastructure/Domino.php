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
        for ($i=1; $i <= MarketSetup::MARKET_SIZE; $i++) 
            $this->deck->pickCardForLocation('deck', 'market', $i);
        return $this;
    }

    public function setup_next() : MarketSetup {
        for ($i=1; $i <= MarketSetup::MARKET_SIZE + 1; $i++) 
            $this->deck->pickCardForLocation('deck', 'next', $i);
        return $this;
    }
}

#[\AllowDynamicProperties]
class UpdateDomino extends CurrentMarket
{
    static public function create($deck_domino): UpdateDomino {
        $object = new UpdateDomino();
        $object->set_deck($deck_domino);
        return $object;
    }
    public function move($quarry_index, $player_id, $stage_index, $horizontal, $vertical, $rotation): UpdateDomino {
        $this->deck->moveAllCardsInLocation(CurrentMarket::LOCATION_MARKET, $player_id, $quarry_index
        , $stage_index
        + CurrentTiles::FACTOR_STAGE * $horizontal
        + CurrentTiles::FACTOR_STAGE * CurrentTiles::FACTOR_HORIZONTAL * $vertical
        + CurrentTiles::FACTOR_STAGE * CurrentTiles::FACTOR_HORIZONTAL * CurrentTiles::FACTOR_VERTICAL * $rotation);

        return $this;
    }
}

#[\AllowDynamicProperties]
class CurrentTiles
{
    const FACTOR_STAGE = 5;
    const FACTOR_HORIZONTAL = 20;
    const FACTOR_VERTICAL = 20;
    const FACTOR_ROTATION = 4;

    static public function create($deck_domino): CurrentTiles {
        $object = new CurrentTiles();
        $object->set_deck_domino($deck_domino);
        return $object;
    }

    public function set_deck_domino($deck_domino): CurrentTiles {
        $this->deck_domino = $deck_domino;

        return $this;
    }

    public function set_players($players): CurrentTiles {
        $this->players = $players;

        return $this;
    }

    public function get(): array {
        $tiles_per_player = [];
        foreach ($this->players as $player_id => $player)
            $tiles_per_player[strval($player_id)] = $this->get_tiles_for($player_id);

        return $tiles_per_player;
    }
    public function get_tiles_for($player_id) {
        $tiles_per_stage = [];
        $dominoes = $this->deck_domino->getCardsInLocation(strval($player_id));
        foreach ($dominoes as $domino) {
            $stage = $domino['location_arg'] % CurrentTiles::FACTOR_STAGE;
            if (!array_key_exists($stage, $tiles_per_stage))
                $tiles_per_stage[$stage] = [];
            $tiles_per_stage[$stage][] = $this->get_first_tile_for($domino);
            $tiles_per_stage[$stage][] = $this->get_second_tile_for($domino);
        }
        return $tiles_per_stage;
    }
    protected function get_first_tile_for($domino) {
        $tile = $this->get_tile_common($domino);

        $tile['tile_id'] = ($domino['id'] - 1) * 2;

        return $tile;
    }
    protected function get_second_tile_for($domino) {
        $tile = $this->get_tile_common($domino);
        $tile['tile_id'] = ($domino['id'] - 1) * 2 + 1;

        if ($tile['rotation'] == 0)
            $tile['horizontal'] = $tile['horizontal'] + 2;

        if ($tile['rotation'] == 1)
            $tile['vertical'] = $tile['vertical'] + 2;

        if ($tile['rotation'] == 2)
            $tile['horizontal'] = $tile['horizontal'] - 2;

        if ($tile['rotation'] == 3)
            $tile['vertical'] = $tile['vertical'] - 2;

        return $tile;
    }
    protected function get_tile_common($domino) {
        $tile = ['id' => '1', 'colour' => 0, 'stage' => 1, 'horizontal' => 10, 'vertical' => 10, 'rotation' => 0];

        $location_arg = 0 + $domino['location_arg'];
        $tile['stage'] = $location_arg % CurrentTiles::FACTOR_STAGE;

        $remaining = intdiv($location_arg, CurrentTiles::FACTOR_STAGE);
        $tile['horizontal'] = $remaining % CurrentTiles::FACTOR_HORIZONTAL;

        $remaining = intdiv($remaining, CurrentTiles::FACTOR_HORIZONTAL);
        $tile['vertical'] = $remaining % CurrentTiles::FACTOR_VERTICAL;

        $remaining = intdiv($remaining, CurrentTiles::FACTOR_VERTICAL);
        $tile['rotation'] = $remaining % CurrentTiles::FACTOR_ROTATION;

        return $tile;

    }
}

#[\AllowDynamicProperties]
class CurrentMarket
{
    const LOCATION_MARKET = 'market';

    static public function create($deck_domino): CurrentMarket {
        $object = new CurrentMarket();
        $object->set_deck($deck_domino);
        return $object;
    }

    public function set_deck($deck) {
        $this->deck = $deck;
    }

    public function get_market() : array {
        return $this->get_market_entries(CurrentMarket::LOCATION_MARKET);
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
