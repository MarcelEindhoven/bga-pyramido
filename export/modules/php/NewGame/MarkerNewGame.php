<?php
/**
 *------
 * BGA framework: Gregory Isabelli & Emmanuel Colin & BoardGameArena
 * Pyramido implementation : © Marcel van Nieuwenhoven marcel.eindhoven@hotmail.com
 *
 * This code has been produced on the BGA studio platform for use on http://boardgamearena.com.
 * See http://en.boardgamearena.com/#!doc/Studio for more information.
 * -----
 *
 */
declare(strict_types=1);

namespace Bga\Games\Pyramido\NewGame;

#[\AllowDynamicProperties]
class MarkerNewGame
{
    const SIZE = 6;

    static public function create(): MarkerNewGame {
        $object = new MarkerNewGame();
        return $object;
    }

    public function set_marker_factory($marker_factory) : MarkerNewGame {
        $this->marker_factory = $marker_factory;
        return $this;
    }

    public function set_players($players): MarkerNewGame {
        $this->players = $players;
        return $this;
    }

    public function setup() : MarkerNewGame {
        foreach ($this->players as $player_id => $player) {
            for ($i = 0; $i <= 5; $i++)
                $this->marker_factory->add($i);
            $this->marker_factory->flush($player_id);
        }
        return $this;
    }
}
