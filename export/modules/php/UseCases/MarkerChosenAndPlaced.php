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

include_once(__DIR__.'/../Domain/Colour.php');
use Bga\Games\PyramidoCannonFodder\Domain;

include_once(__DIR__.'/../Infrastructure/Domino.php');
use Bga\Games\PyramidoCannonFodder\Infrastructure;

#[\AllowDynamicProperties]
class MarkerChosenAndPlaced extends \NieuwenhovenGames\BGA\Action {
    static public function create($gamestate): MarkerChosenAndPlaced {
        $object = new MarkerChosenAndPlaced($gamestate);
        return $object;
    }

    public function set_update_marker($update_marker) : MarkerChosenAndPlaced {
        $this->update_marker = $update_marker;
        return $this;
    }

    public function set_player_id($player_id) : MarkerChosenAndPlaced {
        $this->player_id = $player_id;
        return $this;
    }

    public function set_tile_specification($tile_specification) : MarkerChosenAndPlaced {
        $this->tile_specification = $tile_specification;
        return $this;
    }

    public function execute(): MarkerChosenAndPlaced {
        $this->tile_specification['stage'] = 4;

        $tile = $this->get_current_data->get()['tiles'][$this->player_id][Infrastructure\CurrentTiles::calculate_array_index($this->tile_specification)];
        $this->update_marker->move($this->player_id, $tile);

        $marker = $this->update_marker->get_marker($this->player_id, $tile);

        $notification_arguments = $this->get_default_notification_arguments($this->player_id);
        $notification_arguments['marker_specification'] = $marker;
        $notification_arguments['colour'] = Domain\COLOURS[$marker['colour']];
        $this->notifications->notifyAllPlayers('marker_placed', '${player_name} places ${colour} marker', $notification_arguments);

        return $this;
    }
}

#[\AllowDynamicProperties]
class MarkerAutomaticallyChosenAndPlaced extends MarkerChosenAndPlaced {
    static public function create($gamestate): MarkerAutomaticallyChosenAndPlaced {
        $object = new MarkerAutomaticallyChosenAndPlaced($gamestate);
        return $object;
    }

    public function execute(): MarkerAutomaticallyChosenAndPlaced {
        $candidate_tiles_for_marker = $this->get_current_data->get()['candidate_tiles_for_marker'];

        $this->set_tile_specification(end($candidate_tiles_for_marker));

        return parent::execute();
    }

}
