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

 #[\AllowDynamicProperties]
 class Score {
    function __construct($database) {
        $this->database = $database;
    }

    public function set_notifications($notifications) : Score {
        $this->notifications = $notifications;
        return $this;
    }

    public function set_players($players) : Score {
        $this->players = $players;
        return $this;
    }

    public function add($player_id, $score_increase) : Score {
        $this->database->query("UPDATE `player` SET `player_score` = `player_score` + ".$score_increase." WHERE `player_id` = '".$player_id."'");
        return $this;
    }
}
?>
