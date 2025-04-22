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
class AfterStageFinished extends \NieuwenhovenGames\BGA\Action {
    static public function create($gamestate): AfterStageFinished {
        $object = new AfterStageFinished($gamestate);
        return $object;
    }

    public function set_update_domino($update_domino) : AfterStageFinished {
        $this->update_domino = $update_domino;
        return $this;
    }

    public function set_player_id($player_id) : AfterStageFinished {
        $this->player_id = $player_id;
        return $this;
    }

    public function execute(): AfterStageFinished {

        return $this;
    }
}
