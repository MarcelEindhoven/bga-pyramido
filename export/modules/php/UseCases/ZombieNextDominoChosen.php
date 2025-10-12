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

namespace Bga\Games\PyramidoCannonFodder\UseCases;

include_once(__DIR__.'/../BGA/Action.php');

#[\AllowDynamicProperties]
class ZombieNextDominoChosen extends NextDominoChosen {
    static public function create($gamestate): ZombieNextDominoChosen {
        $object = new ZombieNextDominoChosen($gamestate);
        return $object;
    }

    public function execute(): ZombieNextDominoChosen {
        parent::set_next_index('next-2');
        parent::set_quarry_index('quarry-2');
        parent::execute();

        return $this;
    }
}
