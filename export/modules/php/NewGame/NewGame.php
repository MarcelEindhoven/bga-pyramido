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
require_once("MarkerNewGame.php");
require_once("ResurfacingNewGame.php");

include_once(__DIR__.'/../Infrastructure/Domino.php');
include_once(__DIR__.'/../Infrastructure/Marker.php');
include_once(__DIR__.'/../Infrastructure/Resurfacing.php');
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
        $this->set_marker_factory(Infrastructure\MarkerFactory::create($this->decks['marker']));
        $this->set_resurfacing_factory(Infrastructure\ResurfacingFactory::create($this->decks['resurfacing']));
        return $this;
    }

    public function set_domino_factory($domino_factory) : NewGame {
        $this->domino_factory = $domino_factory;
        return $this;
    }

    public function set_marker_factory($marker_factory) : NewGame {
        $this->marker_factory = $marker_factory;
        return $this;
    }

    public function set_resurfacing_factory($resurfacing_factory) : NewGame {
        $this->resurfacing_factory = $resurfacing_factory;
        return $this;
    }

    public function set_number_ai_players(int $number_ai_players) : NewGame {
        $this->number_ai_players = $number_ai_players;
        return $this;
    }

    public function setup(&$players): NewGame {
        $keys = array_keys($players);
        // Assign player slots to AI by overwriting the player name
        for ($AI_index = 0; $AI_index < $this->number_ai_players; $AI_index++) {
            // Interleave human players with AI players
            $this->skipFirstKeyIfPossible($keys, $this->number_ai_players - $AI_index);
            $this->assignPlayerAsAI($players[$keys[array_key_first($keys)]], $AI_index + 1);
            unset($keys[array_key_first($keys)]);
        }

        $this->setup_domino();
        $this->setup_market();
        $this->setup_marker($players);
        $this->setup_resurfacing($players);

        return $this;
    }
    protected function skipFirstKeyIfPossible(& $keys, $remaining_AI) {
        if ($remaining_AI < count($keys)) {
            // First in the remaining list is a human player
            unset($keys[array_key_first($keys)]);
        }
    }
    protected function assignPlayerAsAI(& $player, $AI_sequence_number) {
        $player['player_name'] = 'AI_' . ($AI_sequence_number);
    }

    public function setup_market() : NewGame{
        Infrastructure\MarketSetup::create($this->decks['domino'])->setup_market()->setup_next();
        return $this;
    }

    public function setup_domino() : NewGame{
        DominoNewGame::create()->set_domino_factory($this->domino_factory)->setup();
        return $this;
    }

    public function setup_marker($players) : NewGame{
        MarkerNewGame::create()->set_players($players)->set_marker_factory($this->marker_factory)->setup();
        return $this;
    }

    public function setup_resurfacing($players) : NewGame{
        ResurfacingNewGame::create()->set_players($players)->set_resurfacing_factory($this->resurfacing_factory)->setup();
        return $this;
    }
}
