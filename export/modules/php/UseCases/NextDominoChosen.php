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
class NextDominoChosen extends \NieuwenhovenGames\BGA\Action {
    static public function create($gamestate): NextDominoChosen {
        $object = new NextDominoChosen($gamestate);
        return $object;
    }

    public function set_update_market($update_market) : NextDominoChosen {
        $this->update_market = $update_market;
        return $this;
    }

    public function set_next_index($next_index) : NextDominoChosen {
        $this->next_index = $next_index;
        return $this;
    }

    public function set_quarry_index($quarry_index) : NextDominoChosen {
        $this->quarry_index = $quarry_index;
        return $this;
    }

    public function execute(): NextDominoChosen {
        $this->update_market->move($this->next_index, $this->quarry_index);

        $this->update_market->refill($this->next_index);

        $next_domino = $this->update_market->get_market_entries('next')[$this->next_index];

        $this->notifications->notifyAllPlayers('next_domino_chosen', 'next_domino_chosen',
        ['next_index' => $this->next_index, 
        'quarry_index' => $this->quarry_index, 
        'next_domino' => $next_domino,
        ]);
    return $this;
    }
}
