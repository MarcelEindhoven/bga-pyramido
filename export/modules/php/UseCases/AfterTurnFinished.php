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
use Bga\Games\Pyramido\Domain;

#[\AllowDynamicProperties]
class AfterTurnFinished extends \NieuwenhovenGames\BGA\Action {
    static public function create($gamestate): AfterTurnFinished {
        $object = new AfterTurnFinished($gamestate);
        return $object;
    }

    public function execute(): AfterTurnFinished {
        return $this;
    }

    public function get_transition_name() : string {
        $tiles = $this->get_current_data->get()['tiles'];
        $stages = [];
        foreach ($tiles as $player_id => $tiles_per_player) {
            $pyramid = Domain\Pyramid::create($tiles_per_player);
            $stages[] = $pyramid->get_stage_next_domino();
            if (count($pyramid->get_tiles_for_stage($pyramid->get_stage_next_domino())) > 0)
                return 'stage_not_finished';
        }
        if (count(array_unique($stages)) > 1)
            return 'stage_not_finished';
        return 'stage_finished';
    }
}
