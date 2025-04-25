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

#[\AllowDynamicProperties]
class AfterStageFinished extends \NieuwenhovenGames\BGA\Action {
    static public function create($gamestate): AfterStageFinished {
        $object = new AfterStageFinished($gamestate);
        return $object;
    }

    public function set_update_marker($update_marker) : AfterStageFinished {
        $this->update_marker = $update_marker;
        return $this;
    }

    public function execute(): AfterStageFinished {
        // Calculate score
        // Determine next player

        return $this;
    }
}
