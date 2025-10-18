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
        $least_stage_score = 999;
        $current_data = $this->get_current_data->get();
        $player_id = $this->database->getActivePlayerId();
        $stage_start_player_id = $this->database->getPlayerAfter($player_id);
        $stage = $current_data['current_stage'] - 1;

        $default_notification_arguments = $this->get_default_notification_arguments($stage_start_player_id);
        $this->notifications->notifyAllPlayers(
            'current_stage_start_player',
            'Current stage start player is ${player_name}',
            $default_notification_arguments
        );

        $player_after_stage_starter_player_id = $this->database->getPlayerAfter($stage_start_player_id);
        $player_id = $player_after_stage_starter_player_id;
        do {
            $tiles_per_player = $current_data['tiles'][$player_id];
            $top_view = Domain\TopView::create($tiles_per_player);
            $markers = $current_data['markers'][$player_id];
            $placed_markers = array_filter($markers, function($marker) {return 0 != $marker['stage'];});
            $score_details = Domain\StageScore::create($top_view->get_jewels(), $top_view->get_colour_map())->get_score_details($placed_markers);
            $this->process_stage_score(
                $player_id,
                $score_details,
                $placed_markers,
                $top_view->get_jewels(),
                $top_view->get_colour_map()
            );
            $score_increase = $score_details['score_increase'];
            if ($score_increase < $least_stage_score) {
                $least_stage_score = $score_increase;
                $player_id_least_stage_score = $player_id;
            }
            $this->database->initStat("player", "score_stage_" . $stage, $score_increase, $player_id);
            $player_id = $this->database->getPlayerAfter($player_id);
        }
        while ($player_id != $player_after_stage_starter_player_id);

        $default_notification_arguments = $this->get_default_notification_arguments($player_id_least_stage_score);
        $this->notifications->notifyAllPlayers(
            'least_stage_score',
            'Player with least stage score is ${player_name}',
            $default_notification_arguments
        );
        $this->change_active_player($this->database->getPlayerBefore($player_id_least_stage_score));

        return $this;
    }
    protected function process_stage_score($player_id, $score_details, $placed_markers, $jewels, $colour_map): void {
        $score_increase = $score_details['score_increase'];
        $this->database->DbQuery( "UPDATE `player` SET `player_score` = `player_score` + ".$score_increase." WHERE `player_id` = '".$player_id."'" );

        $notification_arguments = $this->get_default_notification_arguments($player_id);
        $notification_arguments['score_details'] = $score_details;
        $notification_arguments['placed_markers'] = $placed_markers;
        $notification_arguments['jewels'] = $jewels;
        $notification_arguments['colour_map'] = $colour_map;

        $this->notifications->notifyAllPlayers(
            'score_details',
            'Stage score ${player_name} is ' . $score_increase,
            $notification_arguments
        );
    
    }
}
