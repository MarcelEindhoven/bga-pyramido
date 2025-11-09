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
include_once(__DIR__.'/../Domain/Colour.php');
use Bga\Games\Pyramido\Domain;

include_once(__DIR__.'/../Infrastructure/Domino.php');
use Bga\Games\Pyramido\Infrastructure;

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
        $notification_arguments = $this->get_default_notification_arguments($this->player_id);
        foreach($this->get_current_data->get()['candidate_tiles_for_marker']
                as $tile) {
            $this->update_marker->move($this->player_id, $tile);
            $marker = $this->update_marker->get_marker($this->player_id, $tile);
            $notification_arguments['marker_specification'] = $marker;
            $notification_arguments['colour'] = Domain\COLOURS[$marker['colour']];
            $this->notifications->notifyAllPlayers('marker_placed', '${player_name} places ${colour} marker on resurfacing tile', $notification_arguments);
        }

        return $this;
    }
}
