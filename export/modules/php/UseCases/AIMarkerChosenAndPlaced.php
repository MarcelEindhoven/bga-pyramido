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

include_once(__DIR__.'/MarkerChosenAndPlaced.php');

#[\AllowDynamicProperties]
class AIMarkerChosenAndPlaced extends MarkerChosenAndPlaced {
    static public function create($gamestate): AIMarkerChosenAndPlaced {
        $object = new AIMarkerChosenAndPlaced($gamestate);
        return $object;
    }

    public function execute(): AIMarkerChosenAndPlaced {
        $candidate_tiles_for_marker = $this->get_current_data->get()['candidate_tiles_for_marker'];
        if (count($candidate_tiles_for_marker) >0) {
            $this->set_tile_specification(end($candidate_tiles_for_marker));
            parent::execute();
        }
        

        return $this;
    }
 
    protected function get_domino_specification(): array {
        $candidate_positions = array_filter($this->get_current_data->get()['candidate_positions'],
        function (array $candidate_position) {
            return 
            ($candidate_position['horizontal'] >= 10) && 
            ($candidate_position['horizontal'] % 4 == 2) && 
            ($candidate_position['vertical'] >= 10) && 
            ($candidate_position['rotation'] == 0);
        });

        return array_shift($candidate_positions);
    }
}
