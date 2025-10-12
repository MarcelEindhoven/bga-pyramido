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
class ZombieDominoChosenAndPlaced extends DominoChosenAndPlaced {
    static public function create($gamestate): ZombieDominoChosenAndPlaced {
        $object = new ZombieDominoChosenAndPlaced($gamestate);
        return $object;
    }

    public function execute(): ZombieDominoChosenAndPlaced {
        parent::set_quarry_index('quarry-2');

        parent::set_domino_specification($this->get_domino_specification());

        parent::execute();

        return $this;
    }

    protected function get_domino_specification(): array {
        $candidate_positions = array_filter($this->get_current_data->get()['candidate_positions'],
        function (array $candidate_position) {
            $stage = $candidate_position['stage'];
            if ($stage % 2 == 1)
                return 
                ($candidate_position['horizontal'] >= 10) && 
                ($candidate_position['horizontal'] % 4 == 3 - $stage) && 
                ($candidate_position['vertical'] >= 10) && 
                ($candidate_position['rotation'] == 0);
            else
                return 
                ($candidate_position['horizontal'] >= 10) && 
                ($candidate_position['vertical'] % 4 == 5 - $stage) && 
                ($candidate_position['vertical'] >= 10) && 
                ($candidate_position['rotation'] == 1);
        });

        return array_shift($candidate_positions);
    }
}
