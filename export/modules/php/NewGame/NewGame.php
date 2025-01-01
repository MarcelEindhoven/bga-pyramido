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

namespace Bga\Games\PyramidoCannonFodder\NewGame;

require_once("DominoNewGame.php");
include_once(__DIR__.'/../Infrastructure/Domino.php');
use Bga\Games\PyramidoCannonFodder\Infrastructure;

#[\AllowDynamicProperties]
class NewGame
{
    protected array $decks = [];
    protected ?Infrastructure\DominoFactory $domino_factory = null;

    static public function create($decks): NewGame {
        $object = new NewGame();
        $object->set_decks($decks);
        return $object;
    }

    public function set_decks($decks) : NewGame {
        $this->decks = $decks;
        $this->set_domino_factory(Infrastructure\DominoFactory::create($this->decks['domino']));
        return $this;
    }

    public function set_domino_factory($domino_factory) : NewGame {
        $this->domino_factory = $domino_factory;
        return $this;
    }

    public function setup() : NewGame{
        $this->setup_domino();
        return $this;
    }

    public function setup_domino() : NewGame{
        DominoNewGame::create($this->decks['domino'])->set_domino_factory($this->domino_factory)->setup();
        return $this;
    }
}
