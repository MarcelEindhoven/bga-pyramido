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
class AfterDominoPlaced extends \NieuwenhovenGames\BGA\Action {
    static public function create($gamestate): AfterDominoPlaced {
        $object = new AfterDominoPlaced($gamestate);
        return $object;
    }

    public function set_player_id($player_id) : AfterDominoPlaced {
        $this->player_id = $player_id;
        return $this;
    }

    public function execute(): AfterDominoPlaced {

        return $this;
    }

    public function get_transition_name() : string {
        $candidate_tiles_for_marker = $this->get_current_data->get()['candidate_tiles_for_marker'];

        if (count($candidate_tiles_for_marker) == 0)
            return 'no_candidate_tile';
        if (count($candidate_tiles_for_marker) == 1)
            return 'single_candidate_tile';
        return 'double_candidate_tile';
    }
}
