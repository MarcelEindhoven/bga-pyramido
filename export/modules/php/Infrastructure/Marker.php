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
class MarkerFactory
{
    protected array $definitions = [];

    static public function create($deck_domino): MarkerFactory {
        $object = new MarkerFactory();
        $object->set_deck($deck_domino);
        return $object;
    }

    public function set_deck($deck) {
        $this->deck = $deck;
    }

    public function add($colour) {
        $this->definitions[] = array( 'type' => $colour, 'type_arg' => 0, 'nbr' => 1);
    }
    public function flush($player_id) {
        $this->deck->createCards($this->definitions, '' . $player_id, 0);
        $this->definitions = [];
    }
}

#[\AllowDynamicProperties]
class MarkerSetup
{
    const SIZE = 6;
    static public function create($deck_domino): MarkerSetup {
        $object = new MarkerSetup();
        $object->set_deck($deck_domino);
        return $object;
    }

    public function set_deck($deck) : MarkerSetup {
        $this->deck = $deck;
        return $this;
    }

    public function set_players($players): MarkerSetup {
        $this->players = $players;

        return $this;
    }

    public function setup() : MarkerSetup {
        for ($i=1; $i <= MarkerSetup::SIZE; $i++) 
            $this->deck->pickCardForLocation('deck', 'quarry', $i);
        return $this;
    }
}
