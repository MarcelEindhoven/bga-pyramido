<?php
/**
 *------
 * BGA framework: Gregory Isabelli & Emmanuel Colin & BoardGameArena
 * Pyramido implementation : © Marcel van Nieuwenhoven marcel.eindhoven@hotmail.com
 *
 * This code has been produced on the BGA studio platform for use on http://boardgamearena.com.
 * See http://en.boardgamearena.com/#!doc/Studio for more information.
 * -----
 *
 */
declare(strict_types=1);

namespace Bga\Games\Pyramido\UseCases;

include_once(__DIR__.'/../BGA/Action.php');

include_once(__DIR__.'/../Domain/Pyramid.php');
use Bga\Games\Pyramido\Domain;

#[\AllowDynamicProperties]
class ReturnAllMarkers extends \NieuwenhovenGames\BGA\Action {
    static public function create($gamestate): ReturnAllMarkers {
        $object = new ReturnAllMarkers($gamestate);
        return $object;
    }

    public function set_update_marker($update_marker) : ReturnAllMarkers {
        $this->update_marker = $update_marker;
        return $this;
    }

    public function execute(): ReturnAllMarkers {
        $markers = $this->get_current_data->get()['markers'];
        foreach ($markers as $player_id => $markers_per_player)
            $this->update_marker->return_all_markers($player_id);

        $markers = $this->get_current_data->get()['markers'];
        $this->notifications->notifyAllPlayers('return_all_markers', '', ['markers' => $markers]);

        return $this;
    }

    public function get_transition_name() : string {
        $tiles = $this->get_current_data->get()['tiles'];
        foreach ($tiles as $player_id => $tiles_per_player) {
            $pyramid = Domain\Pyramid::create($tiles_per_player);
            if (count($pyramid->get_tiles_for_stage(4)) < 2)
                return 'not_finished_playing';
        }
        return 'finished_playing';
    }
}
