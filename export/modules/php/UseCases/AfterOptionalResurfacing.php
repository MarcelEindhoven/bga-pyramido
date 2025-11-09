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

include_once(__DIR__.'/../Infrastructure/Domino.php');
use Bga\Games\Pyramido\Infrastructure;

#[\AllowDynamicProperties]
class AfterOptionalResurfacing extends \NieuwenhovenGames\BGA\Action {
    static public function create($gamestate): AfterOptionalResurfacing {
        $object = new AfterOptionalResurfacing($gamestate);
        return $object;
    }

    public function set_update_domino($update_domino) : AfterOptionalResurfacing {
        $this->update_domino = $update_domino;
        return $this;
    }

    public function set_update_resurfacing($update_resurfacing) : AfterOptionalResurfacing {
        $this->update_resurfacing = $update_resurfacing;
        return $this;
    }

    public function set_player_id($player_id) : AfterOptionalResurfacing {
        $this->player_id = $player_id;
        return $this;
    }

    public function execute(): AfterOptionalResurfacing {
        $current_stage = $this->get_current_data->get()['current_stage'];
        if ($current_stage >4) $current_stage = 4;

        $dominoes_last_placed = array_filter($this->update_domino->get_dominoes($this->player_id)
            , function(array $domino) {return 4 == $domino['stage'];});
        $domino_last_placed = end($dominoes_last_placed);

        $this->update_domino->move_stage($this->player_id, $domino_last_placed, $current_stage);
        $domino_last_placed['stage'] = $current_stage;


        foreach(array_filter($this->get_current_data->get()['placed_resurfacings'][$this->player_id]
                    , function(array $resurfacing) {return 4 == $resurfacing['stage'];})
                as $just_placed_resurfacing)
            $this->update_resurfacing->move_stage($this->player_id, $just_placed_resurfacing, $current_stage);


        $domino = $this->update_domino->get_domino($this->player_id, $domino_last_placed);

        $updated_tiles = $this->get_current_data->get()['tiles'][$this->player_id];

        $notification_arguments = $this->get_default_notification_arguments($this->player_id);
        $notification_arguments['tiles'] = [
            $updated_tiles[Infrastructure\CurrentTiles::calculate_array_index($this->update_domino->get_first_tile_for($domino))],
            $updated_tiles[Infrastructure\CurrentTiles::calculate_array_index($this->update_domino->get_second_tile_for($domino))],
        ];
        $this->notifications->notifyAllPlayers('tiles_new_stage', '', $notification_arguments);

        return $this;
    }
}
