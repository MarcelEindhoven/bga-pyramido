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
class NextPlayer extends \NieuwenhovenGames\BGA\Action {
    protected array $ais = [];

    public static function create($gamestate) : NextPlayer {
        return new NextPlayer($gamestate);
    }

    public function set_deck($deck) : NextPlayer {
        $this->deck = $deck;
        return $this;
    }

    public function set_player_id($player_id) : NextPlayer {
        $this->player_id = $player_id;
        return $this;
    }

    public function execute() : NextPlayer {
        return $this;
    }

    public function get_transition_name() : string {
        if (0 == $this->deck->countCardInLocation('deck'))
            return 'finished_playing';
        if (substr($this->get_current_data->get()['players'][$this->player_id]['name'], 0, 3) === 'AI_')
            return 'ai_playing';
        return 'player_playing';
    }
}
?>
