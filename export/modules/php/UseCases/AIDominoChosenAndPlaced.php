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

include_once(__DIR__.'/DominoChosenAndPlaced.php');

#[\AllowDynamicProperties]
class AIDominoChosenAndPlaced extends DominoChosenAndPlaced {
    static public function create($gamestate): AIDominoChosenAndPlaced {
        $object = new AIDominoChosenAndPlaced($gamestate);
        return $object;
    }

    public function execute(): AIDominoChosenAndPlaced {
        parent::set_quarry_index('quarry-1');
        $horizontal = 10;
        $vertical = 10;
        $rotation = 0;
        $domino_specification = ['horizontal' => $horizontal, 'vertical' => $vertical, 'rotation' => $rotation, ];
        $domino_specification['stage'] = 1;

        parent::set_domino_specification($domino_specification);
        parent::execute();

        return $this;
    }
}
