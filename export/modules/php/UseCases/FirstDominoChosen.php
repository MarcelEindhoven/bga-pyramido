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
class FirstDominoChosen extends \NieuwenhovenGames\BGA\Action {
    static public function create($gamestate): FirstDominoChosen {
        $object = new FirstDominoChosen($gamestate);
        return $object;
    }

    public function set_player_id($player_id) : FirstDominoChosen {
        $this->player_id = $player_id;
        return $this;
    }

    public function set_update_domino($update_domino) : FirstDominoChosen {
        $this->update_domino = $update_domino;
        return $this;
    }

    public function set_quarry_index($quarry_index) : FirstDominoChosen {
        $this->quarry_index = $quarry_index;
        return $this;
    }

    public function execute(): FirstDominoChosen {
        $this->update_domino->move($this->quarry_index, $this->player_id, 1, 10, 10, 0);

        $domino = $this->update_domino->get_domino($this->player_id, 1, 10, 10, 0);
        $this->notifications->notifyAllPlayers('domino_placed', 'domino_placed',
        ['quarry_index' => $this->quarry_index, 
        'player_id' => $this->player_id, 
        'tiles' => [$this->update_domino->get_first_tile_for($domino), $this->update_domino->get_second_tile_for($domino), ],
        ]);

        return $this;
    }
}
