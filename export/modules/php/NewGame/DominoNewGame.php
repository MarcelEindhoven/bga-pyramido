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
class DominoNewGame
{
    /**
     * colour first tile, colour second tile
     */
    const DOMINO_SPECIFICATION =[
        [0, 5],
        [1, 4],
        [2, 3],
        [3, 2],
        [4, 1],
        [5, 0],
        [4, 4],
        [3, 5],
        [2, 2],
        [1, 5],
        [0, 5],
        [1, 4],
        [2, 3],
        [3, 2],
        [4, 1],
        [5, 0],
        [4, 4],
        [3, 5],
        [2, 2],
        [1, 5],
        [0, 5],
        [1, 4],
        [2, 3],
        [3, 2],
        [4, 1],
        [5, 0],
        [4, 4],
        [3, 5],
        [2, 2],
        [1, 5],
        [0, 5],
        [1, 4],
        [2, 3],
        [3, 2],
        [4, 1],
        [5, 0],
        [4, 4],
        [3, 5],
        [2, 2],
        [1, 5],
        [0, 5],
        [1, 4],
        [2, 3],
        [3, 2],
        [4, 1],
        [5, 0],
        [4, 4],
        [3, 5],
        [2, 2],
        [1, 5],
        [0, 5],
        [1, 4],
        [2, 3],
        [3, 2],
        [4, 1],
        [5, 0],
        [4, 4],
        [3, 5],
        [2, 2],
        [1, 5],
        [0, 5],
        [1, 4],
        [2, 3],
        [3, 2],
        [4, 1],
        [5, 0],
        [4, 4],
        [3, 5],
        [2, 2],
        [1, 5],
        [0, 5],
        [1, 4],
        [2, 3],
        [3, 2],
        [4, 1],
        [5, 0],
        [4, 4],
        [3, 5],
        [2, 2],
        [1, 5],
        [0, 5],
        [1, 4],
        [2, 3],
        [3, 2],
        [4, 1],
        [5, 0],
        [4, 4],
        [3, 5],
        [2, 2],
        [1, 5],
    ];
    static public function create(): DominoNewGame {
        $object = new DominoNewGame();
        return $object;
    }

    public function set_domino_factory($domino_factory) : DominoNewGame {
        $this->domino_factory = $domino_factory;
        return $this;
    }

    public function setup() : DominoNewGame {
        foreach (DominoNewGame::DOMINO_SPECIFICATION as $specification) {
            $this->domino_factory->add($specification[0], $specification[1]);
        }
        $this->domino_factory->flush();
        return $this;
    }
}
