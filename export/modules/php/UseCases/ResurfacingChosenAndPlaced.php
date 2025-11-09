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

include_once(__DIR__.'/../Domain/Colour.php');
use Bga\Games\Pyramido\Domain;

include_once(__DIR__.'/../Infrastructure/Domino.php');
use Bga\Games\Pyramido\Infrastructure;

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
        $notification_arguments = $this->get_default_notification_arguments($this->player_id);

        $this->tile_specification['stage'] = 4;
        $notification_arguments['removed_tile'] = $this->get_current_data->get()['tiles'][$this->player_id][Infrastructure\CurrentTiles::calculate_array_index($this->tile_specification)];

        // $this->tile_specification['stage'] = $this->get_current_data->get()['current_stage'];
        $notification_arguments['removed_resurfacings'] = $this->update_resurfacing->get_both_unplaced($this->player_id, $this->tile_specification);

        $this->update_resurfacing->move_to_pyramid($this->player_id, $this->tile_specification);

        // notify
        $notification_arguments['colour'] = Domain\COLOURS[$this->tile_specification['colour']];
        $notification_arguments['added_tile'] = $this->get_current_data->get()['tiles'][$this->player_id][Infrastructure\CurrentTiles::calculate_array_index($this->tile_specification)];
        $this->notifications->notifyAllPlayers('resurface', '${player_name} resurfaces with a ${colour} tile', $notification_arguments);

        return $this;
    }

    public function get_transition_name() : string {
        return 'tile_to_place_resurfacing_chosen';
    }
}
