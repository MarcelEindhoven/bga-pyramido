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
include_once(__DIR__.'/../Domain/StageScore.php');
include_once(__DIR__.'/../Domain/TopView.php');
use Bga\Games\PyramidoCannonFodder\Domain;

#[\AllowDynamicProperties]
class AfterStageFinished extends \NieuwenhovenGames\BGA\Action {
    static public function create($gamestate): AfterStageFinished {
        $object = new AfterStageFinished($gamestate);
        return $object;
    }

    public function set_database($database) : AfterStageFinished {
        $this->database = $database;
        return $this;
    }

    public function execute(): AfterStageFinished {
        // Calculate score
        foreach ($this->get_current_data->get()['tiles'] as $player_id => $tiles_per_player) {
            $top_view = Domain\TopView::create($tiles_per_player);
            $markers = $this->get_current_data->get()['markers'][$player_id];
            $placed_markers = array_filter($markers, function($marker) {return 0 != $marker['stage'];});
            $this->process_stage_score(
                $player_id,
                Domain\StageScore::create($top_view->get_jewels(), $top_view->get_colour_map())->get_score_details($placed_markers)
            );
        }

        return $this;
    }
    protected function process_stage_score($player_id, $score_details): void {
        $score = $score_details['score_increase'];
        $this->database->DbQuery( "UPDATE `player` SET `player_score` = `player_score` + ".$score." WHERE `player_id` = '".$player_id."'" );
        $this->notifications->notifyAllPlayers('score_details', '',
        [
            'player_id' => $player_id, 
            'score_details' => $score_details
        ]);
    
    }
}
