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
class ResurfacingChosenAndPlaced extends \NieuwenhovenGames\BGA\Action {
    static public function create($gamestate): ResurfacingChosenAndPlaced {
        $object = new ResurfacingChosenAndPlaced($gamestate);
        return $object;
    }

    public function set_update_resurfacing($update_resurfacing) : ResurfacingChosenAndPlaced {
        $this->update_resurfacing = $update_resurfacing;
        return $this;
    }

    public function set_player_id($player_id) : ResurfacingChosenAndPlaced {
        $this->player_id = $player_id;
        return $this;
    }

    public function set_tile_specification($tile_specification) : ResurfacingChosenAndPlaced {
        $this->tile_specification = $tile_specification;
        return $this;
    }

    public function execute(): ResurfacingChosenAndPlaced {
        $this->tile_specification['stage'] = $this->get_current_data->get()['current_stage'];

        $this->update_resurfacing->move($this->player_id, $this->tile_specification);

        return $this;
    }

    public function get_transition_name() : string {
        return 'tile_to_place_resurfacing_chosen';
    }
}
