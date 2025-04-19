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

include_once(__DIR__.'/GetAllDatas.php');

include_once(__DIR__.'/AIDominoChosenAndPlaced.php');
include_once(__DIR__.'/AINextDominoChosen.php');

include_once(__DIR__.'/DominoChosenAndPlaced.php');
include_once(__DIR__.'/FirstDominoChosen.php');
include_once(__DIR__.'/MarkerChosenAndPlaced.php');
include_once(__DIR__.'/NextDominoChosen.php');
include_once(__DIR__.'/AfterTurnFinished.php');
include_once(__DIR__.'/AfterDominoPlaced.php');

include_once(__DIR__.'/../Infrastructure/Domino.php');
include_once(__DIR__.'/../Infrastructure/Marker.php');
use Bga\Games\PyramidoCannonFodder\Infrastructure;

class Actions {
    protected array $decks = [];
    protected array $players = [];

    static public function create(): Actions {
        $object = new Actions();
        return $object;
    }

    public function set_gamestate($gamestate) : Actions {
        $this->gamestate = $gamestate;
        return $this;
    }

    public function set_decks($decks) : Actions {
        $this->decks = $decks;
        return $this;
    }

    public function set_players($players) : Actions {
        $this->players = $players;
        return $this;
    }

    public function set_notifications($notifications) : Actions {
        $this->notifications = $notifications;
        return $this;
    }

    public function set_database($database) : Actions {
        $this->database = $database;
        return $this;
    }

    /**
     * Current player ID is not known during game setup
     */
    public function set_player_id($player_id) : Actions {
        $this->player_id = $player_id;
        return $this;
    }

    public function stNextPlayer($active_player_id) {
        $this->player_id = $active_player_id;

        $get_current_data = GetAllDatas::create($this->database, $this->decks)->set_players($this->players)->set_current_player_id($this->player_id)->set_active_player_id($this->player_id);
        NextPlayer::create($this->gamestate)->set_notifications($this->notifications)->set_player_id($active_player_id)->set_deck($this->decks['domino'])->set_get_current_data($get_current_data)->execute()->nextState();
    }

    public function stAISelectAndPlaceDomino(): void {
        $update_domino = Infrastructure\UpdateDomino::create($this->decks['domino']);
        $get_current_data = GetAllDatas::create($this->database, $this->decks)->set_players($this->players)->set_current_player_id($this->player_id)->set_active_player_id($this->player_id);
        AIDominoChosenAndPlaced::create($this->gamestate)->set_notifications($this->notifications)->set_player_id($this->player_id)->set_update_domino($update_domino)->set_get_current_data($get_current_data)
        ->execute()->nextState();
    }

    public function action_tile_to_place_marker_chosen(array $tile_specification): void {
        $update_marker = Infrastructure\UpdateMarker::create($this->decks['marker']);
        $get_current_data = GetAllDatas::create($this->database, $this->decks)->set_players($this->players)->set_current_player_id($this->player_id)->set_active_player_id($this->player_id);
        MarkerChosenAndPlaced::create($this->gamestate)->set_notifications($this->notifications)->set_player_id($this->player_id)->set_update_marker($update_marker)->set_get_current_data($get_current_data)->set_tile_specification($tile_specification)
        ->execute()->nextState();
    }

    public function stAutomaticallyPlaceMarker(): void {
        $update_marker = Infrastructure\UpdateMarker::create($this->decks['marker']);
        $get_current_data = GetAllDatas::create($this->database, $this->decks)->set_players($this->players)->set_current_player_id($this->player_id)->set_active_player_id($this->player_id);
        MarkerAutomaticallyChosenAndPlaced::create($this->gamestate)->set_notifications($this->notifications)->set_player_id($this->player_id)->set_update_marker($update_marker)->set_get_current_data($get_current_data)->set_tile_specification($tile_specification)
        ->execute()->nextState();
    }

    public function action_domino_chosen_and_placed(string $quarry_index, array $domino_specification): void {
        $update_domino = Infrastructure\UpdateDomino::create($this->decks['domino']);
        $domino_specification['stage'] = 1;
        $get_current_data = GetAllDatas::create($this->database, $this->decks)->set_players($this->players)->set_current_player_id($this->player_id)->set_active_player_id($this->player_id);
        DominoChosenAndPlaced::create($this->gamestate)->set_notifications($this->notifications)->set_player_id($this->player_id)->set_update_domino($update_domino)->set_get_current_data($get_current_data)
        ->set_quarry_index($quarry_index)->set_domino_specification($domino_specification)->execute()->nextState();
    }

    public function action_next_domino_chosen(string $next_index, string $quarry_index): void {
        $update_market = Infrastructure\UpdateMarket::create($this->decks['domino']);
        NextDominoChosen::create($this->gamestate)->set_notifications($this->notifications)->set_update_market($update_market)->set_next_index($next_index)->set_quarry_index($quarry_index)->execute()->nextState();
    }

    public function stAIChooseNextDomino(): void {
        $update_market = Infrastructure\UpdateMarket::create($this->decks['domino']);
        AINextDominoChosen::create($this->gamestate)->set_notifications($this->notifications)->set_update_market($update_market)->execute()->nextState();
    }

    public function stAfterTurnFinished(): void {
        $update_domino = Infrastructure\UpdateDomino::create($this->decks['domino']);
        $get_current_data = GetAllDatas::create($this->database, $this->decks)->set_players($this->players)->set_current_player_id($this->player_id)->set_active_player_id($this->player_id);
        AfterTurnFinished::create($this->gamestate)->set_notifications($this->notifications)->set_player_id($this->player_id)->set_update_domino($update_domino)->set_get_current_data($get_current_data)->execute()->nextState();
    }

    public function stAfterDominoPlaced(): void {
        $get_current_data = GetAllDatas::create($this->database, $this->decks)->set_players($this->players)->set_current_player_id($this->player_id)->set_active_player_id($this->player_id);
        AfterDominoPlaced::create($this->gamestate)->set_notifications($this->notifications)->set_player_id($this->player_id)->set_get_current_data($get_current_data)->execute()->nextState();
    }
}
?>
