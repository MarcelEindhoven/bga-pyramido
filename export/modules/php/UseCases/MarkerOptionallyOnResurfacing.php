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
class MarkerOptionallyOnResurfacing extends \NieuwenhovenGames\BGA\Action {
    static public function create($gamestate): MarkerOptionallyOnResurfacing {
        $object = new MarkerOptionallyOnResurfacing($gamestate);
        return $object;
    }

    public function set_update_marker($update_marker) : MarkerOptionallyOnResurfacing {
        $this->update_marker = $update_marker;
        return $this;
    }

    public function set_player_id($player_id) : MarkerOptionallyOnResurfacing {
        $this->player_id = $player_id;
        return $this;
    }

    public function set_tile_specification($tile_specification) : MarkerOptionallyOnResurfacing {
        $this->tile_specification = $tile_specification;
        return $this;
    }

    public function execute(): MarkerOptionallyOnResurfacing {
        foreach(array_filter($this->get_current_data->get()['placed_resurfacings'][$this->player_id]
                    , function(array $resurfacing) {return 4 == $resurfacing['stage'];})
                as $tile) {
            $this->update_marker->move($this->player_id, $tile);
            $marker = $this->update_marker->get_marker($this->player_id, $tile);
            $this->notifications->notifyAllPlayers('marker_placed', 'marker_placed',
            ['player_id' => $this->player_id, 
            'marker_specification' => $marker,
            ]);
        }

        return $this;
    }
}
