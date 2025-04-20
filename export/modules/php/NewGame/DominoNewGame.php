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
        [, ],
     */
    const DOMINO_SPECIFICATION =[
        [4, 4],
        [1, 4],
        [1, 4],
        [1, 4],
        [0, 4],
        [0, 4],
        [0, 4],
        [0, 4],
        [0, 4],
        [0, 4],

        [5, 4],
        [4, 4],
        [5, 4],
        [5, 4],
        [5, 4],
        [5, 4],
        [2, 4],
        [2, 4],
        [2, 4],
        [2, 4],

        [2, 4],
        [3, 4],
        [4, 4],
        [3, 4],
        [3, 4],
        [3, 4],
        [1, 1],
        [1, 1],
        [1, 1],
        [1, 1],

        [0, 1],
        [0, 1],
        [0, 1],
        [4, 4],
        [0, 1],
        [0, 1],
        [0, 1],
        [5, 1],
        [5, 1],
        [5, 1],

        [5, 1],
        [5, 1],
        [2, 1],
        [2, 4],
        [4, 1],
        [2, 1],
        [2, 1],
        [3, 1],
        [3, 1],
        [3, 1],

        [0, 0],
        [0, 0],
        [0, 0],
        [5, 0],
        [5, 0],
        [1, 4],
        [5, 0],
        [5, 0],
        [5, 0],
        [2, 0],

        [2, 0],
        [2, 0],
        [2, 0],
        [3, 0],
        [3, 0],
        [3, 0],
        [1, 4],
        [5, 5],
        [5, 5],
        [2, 5],

        [2, 5],
        [2, 5],
        [2, 5],
        [3, 5],
        [3, 5],
        [3, 5],
        [3, 5],
        [1, 4],
        [2, 2],
        [3, 2],

        [3, 2],
        [3, 2],
        [3, 2],
        [3, 2],
        [3, 3],
        [2, 2],
        [3, 3],
        [0, 5],
        [1, 4],
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
        for ($i = 0; $i < count(DominoNewGame::DOMINO_SPECIFICATION); $i++) 
            $this->domino_factory->add($i, DominoNewGame::DOMINO_SPECIFICATION[$i][0], DominoNewGame::DOMINO_SPECIFICATION[$i][1]);
        $this->domino_factory->flush();
        return $this;
    }
}
