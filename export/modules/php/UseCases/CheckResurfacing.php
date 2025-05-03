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
class CheckResurfacing extends \NieuwenhovenGames\BGA\Action {
    static public function create($gamestate): CheckResurfacing {
        $object = new CheckResurfacing($gamestate);
        return $object;
    }

    public function set_player_id($player_id) : CheckResurfacing {
        $this->player_id = $player_id;
        return $this;
    }

    public function execute(): CheckResurfacing {

        $this->notifications->notifyPlayer($this->player_id, 'candidate_tiles_for_resurfacing', '',
        ['candidate_tiles_for_resurfacing' => $this->are_resurfacing_tiles_left() ? $this->get_current_data->get()['candidate_tiles_for_resurfacing']:[]]);

        return $this;
    }

    protected function are_resurfacing_tiles_left() {
        return count($this->get_current_data->get()['resurfacings'][$this->player_id]) > 0;
    }

    public function get_transition_name() : string {
        if ($this->are_resurfacing_tiles_left())
            return 'candidate_tiles_for_resurfacing';
        return 'no_candidate_tiles_for_resurfacing';
    }
}
