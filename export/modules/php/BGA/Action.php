<?php
namespace NieuwenhovenGames\BGA;
/**
 * https://boardgamearena.com/doc/Main_game_logic:_yourgamename.game.php#States_functions
 *------
 * BGA implementation : © Marcel van Nieuwenhoven marcel.eindhoven@hotmail.com
 * This code has been produced on the BGA studio platform for use on https://boardgamearena.com.
 * See http://en.doc.boardgamearena.com/Studio for more information.
 *
 */

class Action {
    const DEFAULT_TRANSITION_NAME = '';

    function __construct($gamestate) {
        $this->gamestate = $gamestate;
    }

    public function set_gamestate($gamestate) : Action {
        $this->gamestate = $gamestate;
        return $this;
    }

    public function set_notifications($notifications) : Action {
        $this->notifications = $notifications;
        return $this;
    }
    public function get_default_notification_arguments($player_id): array {
        $current_data = $this->get_current_data->get();
        $player_name = $current_data["players"][$player_id]["name"];
        return             [
            'player_id' => $player_id, 
            'player_name' => $player_name,
        ]
;
    }

    public function set_get_current_data($get_current_data) : Action {
        $this->get_current_data = $get_current_data;
        return $this;
    }

    public function nextState() {
        $transition_name = method_exists($this, 'get_transition_name') ? $this->get_transition_name() : Action::DEFAULT_TRANSITION_NAME;

        $this->gamestate->nextState($transition_name);
    }

    public function change_active_player($player_id) {
        $this->gamestate->changeActivePlayer($player_id);
    }

    public function get_active_player_id() {
        $active_player_list = $this->gamestate->getActivePlayerList();
        return array_shift($active_player_list);
    }
    
}
?>
