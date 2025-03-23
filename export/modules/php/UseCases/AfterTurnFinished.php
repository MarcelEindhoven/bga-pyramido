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
        $domino_stage_4 = end(
            array_filter($this->update_domino->get_dominoes($this->player_id)
            , function($domino) {return 4 == $domino['stage'];})
        );
        $this->update_domino->move_stage($this->player_id, $domino_stage_4, $current_stage);

        return $this;
    }
}
