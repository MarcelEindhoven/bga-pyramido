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

    public function set_marker_specification($marker_specification) : MarkerChosenAndPlaced {
        $this->marker_specification = $marker_specification;
        return $this;
    }

    public function execute(): MarkerChosenAndPlaced {
        // Last-placed marker is first located on highest stage to distinguish it from other markeres
        $this->marker_specification['stage'] = 4;

        $this->update_marker->move($this->player_id, $this->marker_specification);

        $marker = $this->update_marker->get_marker($this->player_id, $this->marker_specification);
        $this->notifications->notifyAllPlayers('marker_placed', 'marker_placed',
        ['player_id' => $this->player_id, 
        'marker_specification' => $marker,
        ]);

        return $this;
    }
}
