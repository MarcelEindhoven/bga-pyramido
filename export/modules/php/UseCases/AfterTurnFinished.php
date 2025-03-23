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
class AfterTurnFinished extends \NieuwenhovenGames\BGA\Action {
    static public function create($gamestate): AfterTurnFinished {
        $object = new AfterTurnFinished($gamestate);
        return $object;
    }

    public function set_update_domino($update_domino) : AfterTurnFinished {
        $this->update_domino = $update_domino;
        return $this;
    }

    public function set_player_id($player_id) : AfterTurnFinished {
        $this->player_id = $player_id;
        return $this;
    }

    public function execute(): AfterTurnFinished {
        $current_stage = $this->get_current_data->get()['current_stage'];

        $dominoes_stage_4 = array_filter($this->update_domino->get_dominoes($this->player_id)
            , function(array $domino) {return 4 == $domino['stage'];});
        $domino_stage_4 = end($dominoes_stage_4);

        $this->update_domino->move_stage($this->player_id, $domino_stage_4, $current_stage);
        $domino_stage_4['stage'] = $current_stage;

        $domino = $this->update_domino->get_domino($this->player_id, $domino_stage_4);
        $this->notifications->notifyAllPlayers('domino_new_stage', '',
        [
        'player_id' => $this->player_id, 
        'tiles' => [$this->update_domino->get_first_tile_for($domino), $this->update_domino->get_second_tile_for($domino), ],
        ]);

        return $this;
    }
}
