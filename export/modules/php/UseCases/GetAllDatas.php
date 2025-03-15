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

include_once(__DIR__.'/../Infrastructure/Domino.php');
include_once(__DIR__.'/../Infrastructure/Marker.php');

include_once(__DIR__.'/../Domain/Pyramid.php');

use Bga\Games\PyramidoCannonFodder\Domain;
use Bga\Games\PyramidoCannonFodder\Infrastructure;

class GetAllDatas {
    protected array $decks = [];
    protected array $players = [];
    /**
     * 
     */
    static public function create($database,  $decks): GetAllDatas {
        $object = new GetAllDatas();
        $object->set_decks($decks)->set_database($database);
        return $object;
    }

    public function set_database($database) : GetAllDatas {
        $this->database = $database;
        return $this;
    }

    public function set_decks($decks): GetAllDatas {
        $this->decks = $decks;
        return $this;
    }

    public function set_players($players): GetAllDatas {
        $this->players = $players;

        return $this;
    }

    public function set_active_player_id($active_player_id): GetAllDatas {
        $this->active_player_id = $active_player_id;
        return $this;
    }

    public function set_current_player_id($current_player_id): GetAllDatas {
        $this->current_player_id = $current_player_id;
        return $this;
    }

    /**
     * Combine results from database with calculated results from domain
     */
    public function get(): array {
        $results = $this->get_results_from_database();
        $results['quarry'] = Infrastructure\CurrentMarket::create($this->decks['domino'])->get_market();
        $results['next'] = Infrastructure\CurrentMarket::create($this->decks['domino'])->get_next_market();
        $results['tiles'] = Infrastructure\CurrentTiles::create($this->decks['domino'])->set_players($this->players)->get();
        $results['markers'] = Infrastructure\CurrentMarkers::create($this->decks['marker'])->set_players($this->players)->get();

        $pyramid = Domain\Pyramid::create($results['tiles'][$this->current_player_id]);
        $pyramid = new Domain\Pyramid();
        $pyramid->set_tiles($results['tiles'][$this->current_player_id]);
        $pyramid->get_adjacent_positions_first_stage();
        $results['candidate_positions'] = Domain\Pyramid::create($results['tiles'][$this->current_player_id])->get_adjacent_positions_first_stage();

        return $results;
    }

    protected function get_results_from_database(): array {
        $result = [];
        $result["players"] = $this->database->getCollectionFromDb(
            "SELECT `player_id` `id`, `player_name` `name`, `player_score` `score` FROM `player`"
        );
        return $result;
    }
}
?>
