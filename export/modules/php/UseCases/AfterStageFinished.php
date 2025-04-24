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

include_once(__DIR__.'/../Domain/Pyramid.php');
use Bga\Games\PyramidoCannonFodder\Domain;

#[\AllowDynamicProperties]
class AfterStageFinished extends \NieuwenhovenGames\BGA\Action {
    static public function create($gamestate): AfterStageFinished {
        $object = new AfterStageFinished($gamestate);
        return $object;
    }

    public function set_update_marker($update_marker) : AfterStageFinished {
        $this->update_marker = $update_marker;
        return $this;
    }

    public function set_player_id($player_id) : AfterStageFinished {
        $this->player_id = $player_id;
        return $this;
    }

    public function execute(): AfterStageFinished {

        return $this;
    }
    public function get_transition_name() : string {
        $tiles = $this->get_current_data->get()['tiles'];
        foreach ($tiles as $player_id => $tiles_per_player) {
            $pyramid = Domain\Pyramid::create($tiles_per_player);
            if (count($pyramid->get_tiles_for_stage(4)) > 0)
                return 'finished_playing';
        }
        return 'not_finished_playing';
    }
}
