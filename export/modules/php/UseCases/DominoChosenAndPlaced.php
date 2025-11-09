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

#[\AllowDynamicProperties]
class DominoChosenAndPlaced extends \NieuwenhovenGames\BGA\Action {
    static public function create($gamestate): DominoChosenAndPlaced {
        $object = new DominoChosenAndPlaced($gamestate);
        return $object;
    }

    public function set_update_domino($update_domino) : DominoChosenAndPlaced {
        $this->update_domino = $update_domino;
        return $this;
    }

    public function set_player_id($player_id) : DominoChosenAndPlaced {
        $this->player_id = $player_id;
        return $this;
    }

    public function set_quarry_index($quarry_index) : DominoChosenAndPlaced {
        $this->quarry_index = $quarry_index;
        return $this;
    }

    public function set_domino_specification($domino_specification) : DominoChosenAndPlaced {
        $this->domino_specification = $domino_specification;
        return $this;
    }

    public function execute(): DominoChosenAndPlaced {
        // Last-placed domino is first located on highest stage to distinguish it from other dominoes
        $this->domino_specification['stage'] = 4;

        $this->update_domino->move($this->quarry_index, $this->player_id, $this->domino_specification);

        $domino = $this->update_domino->get_domino($this->player_id, $this->domino_specification);
        $this->notifications->notifyAllPlayers('domino_placed', '',
        ['quarry_index' => $this->quarry_index, 
        'player_id' => $this->player_id, 
        'tiles' => [$this->update_domino->get_first_tile_for($domino), $this->update_domino->get_second_tile_for($domino), ],
        ]);

        $this->notifications->notifyPlayer($this->player_id, 'candidate_tiles_for_marker', '',
        ['candidate_tiles_for_marker' => $this->get_current_data->get()['candidate_tiles_for_marker']]);

        return $this;
    }

    public function get_transition_name() : string {
        return 'normal_play';
    }
}
