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

include_once(__DIR__.'/FirstDominoChosen.php');
include_once(__DIR__.'/NextDominoChosen.php');

include_once(__DIR__.'/../Infrastructure/Domino.php');
use Bga\Games\PyramidoCannonFodder\Infrastructure;

class Actions {
    protected array $decks = [];

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

    public function action_first_domino_chosen(int $quarry_index): void {
        $update_domino = Infrastructure\UpdateDomino::create($this->decks['domino']);
        $get_current_data = GetAllDatas::create($this->database, $this->decks)->set_current_player_id($this->player_id)->set_active_player_id($this->player_id);
        FirstDominoChosen::create($this->gamestate)->set_notifications($this->notifications)->set_player_id($this->player_id)->set_get_current_data($get_current_data)->set_update_domino($update_domino)->set_quarry_index($quarry_index)->execute()->nextState();
    }

    public function action_next_domino_chosen(int $next_index, int $quarry_index): void {
        $update_market = Infrastructure\UpdateMarket::create($this->decks['domino']);
        NextDominoChosen::create($this->gamestate)->set_notifications($this->notifications)->set_update_market($update_market)->set_next_index($next_index)->set_quarry_index($quarry_index)->execute()->nextState();
    }
}
?>
