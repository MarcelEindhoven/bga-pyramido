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
     * colour first tile, colour second tile, position first jewel, position second jewel
        [, ],
     */
    const DOMINO_SPECIFICATION =[
        [0, 0,1, 6],
        [1, 0,0, 5],
        [1, 0,0, 1],
        [1, 0,0, 3],
        [2, 0,0, 6],
        [2, 0,1, 7],
        [2, 0,1, 6],
        [2, 0,0, 7],
        [2, 0,0, 1],
        [2, 0,1, 2],

        [3, 0,0, 6],
        [0, 0,0, 7],
        [3, 0,1, 7],
        [3, 0,1, 4],
        [3, 0,0, 1],
        [3, 0,0, 3],
        [4, 0,0, 6],
        [4, 0,1, 7],
        [4, 0,1, 2],
        [4, 0,0, 3],

        [4, 0,0, 1],
        [5, 0,0, 1],
        [0, 0,2, 6],
        [5, 0,1, 2],
        [5, 0,0, 3],
        [5, 0,0, 2],
        [1, 1,2, 6],
        [1, 1,3, 7],
        [1, 1,1, 6],
        [1, 1,0, 7],

        [2, 1,0, 6],
        [2, 1,1, 7],
        [2, 1,1, 6],
        [0, 0,3, 7],
        [2, 1,0, 7],
        [2, 1,0, 1],
        [2, 1,0, 3],
        [3, 1,0, 6],
        [3, 1,1, 7],
        [3, 1,0, 7],

        [3, 1,0, 1],
        [3, 1,1, 2],
        [4, 1,0, 6],
        [4, 1,1, 7],
        [0, 0,0, 6],
        [4, 1,0, 1],
        [4, 1,1, 2],
        [5, 1,0, 6],
        [5, 1,0, 1],
        [5, 1,0, 3],

        [2, 2,1, 6],
        [2, 2,0, 7],
        [2, 2,2, 6],
        [3, 2,0, 6],
        [3, 2,1, 7],
        [1, 0,0, 6],
        [3, 2,1, 6],
        [3, 2,0, 1],
        [3, 2,0, 3],
        [4, 2,0, 6],

        [4, 2,1, 7],
        [4, 2,0, 1],
        [4, 2,0, 3],
        [5, 2,1, 7],
        [5, 2,0, 1],
        [5, 2,1, 2],
        [1, 0,1, 7],
        [3, 3,1, 6],
        [3, 3,0, 7],
        [4, 3,0, 6],

        [4, 3,1, 7],
        [4, 3,0, 7],
        [4, 3,0, 1],
        [5, 3,1, 7],
        [5, 3,0, 1],
        [5, 3,1, 2],
        [5, 3,0, 3],
        [1, 0,1, 6],
        [4, 4,1, 6],
        [5, 4,0, 6],

        [5, 4,1, 7],
        [5, 4,1, 6],
        [5, 4,0, 1],
        [5, 4,1, 3],
        [5, 5,0, 7],
        [4, 4,4, 7],
        [5, 5,4, 7],
        [2, 3,0, 7],
        [1, 0,0, 7],
        [1, 3,3, 5],

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
            $this->domino_factory->add($i
        , DominoNewGame::DOMINO_SPECIFICATION[$i][0]
        , DominoNewGame::DOMINO_SPECIFICATION[$i][1]
        , DominoNewGame::DOMINO_SPECIFICATION[$i][2]
        , DominoNewGame::DOMINO_SPECIFICATION[$i][3]);
        $this->domino_factory->flush();
        return $this;
    }
}
