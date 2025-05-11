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

include_once(__DIR__.'/../Infrastructure/Domino.php');
use Bga\Games\PyramidoCannonFodder\Infrastructure;

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

    public function set_player_id($player_id) : AfterOptionalResurfacing {
        $this->player_id = $player_id;
        return $this;
    }

    public function execute(): AfterOptionalResurfacing {
        $current_stage = $this->get_current_data->get()['current_stage'];

        $dominoes_last_placed = array_filter($this->update_domino->get_dominoes($this->player_id)
            , function(array $domino) {return 4 == $domino['stage'];});
        $domino_last_placed = end($dominoes_last_placed);

        // Retrieve stage 4 tiles from Pyramid

        $this->update_domino->move_stage($this->player_id, $domino_last_placed, $current_stage);
        $domino_last_placed['stage'] = $current_stage;

        $domino = $this->update_domino->get_domino($this->player_id, $domino_last_placed);
        $updated_tiles = $this->get_current_data->get()['tiles'][$this->player_id];
        $this->notifications->notifyAllPlayers('tiles_new_stage', '',
        [
        'player_id' => $this->player_id, 
        'tiles' => [
            $updated_tiles[Infrastructure\CurrentTiles::calculate_array_index($this->update_domino->get_first_tile_for($domino))],
            $updated_tiles[Infrastructure\CurrentTiles::calculate_array_index($this->update_domino->get_second_tile_for($domino))],
        ]]);

        return $this;
    }
}
