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

namespace Bga\Games\PyramidoCannonFodder\NewGame;

#[\AllowDynamicProperties]
class ResurfacingNewGame
{
    const SIZE = 3;

    static public function create(): ResurfacingNewGame {
        $object = new ResurfacingNewGame();
        return $object;
    }

    public function set_resurfacing_factory($resurfacing_factory) : ResurfacingNewGame {
        $this->resurfacing_factory = $resurfacing_factory;
        return $this;
    }

    public function set_players($players): ResurfacingNewGame {
        $this->players = $players;
        return $this;
    }

    public function setup() : ResurfacingNewGame {
        foreach ($this->players as $player_id => $player) {
            for ($i = 0; $i < ResurfacingNewGame::SIZE; $i++)
                $this->resurfacing_factory->add($i * 2, $i * 2 + 1);
            $this->resurfacing_factory->flush($player_id);
        }
        return $this;
    }
}
