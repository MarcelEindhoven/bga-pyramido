<?php
/**
 *------
 * BGA framework: Gregory Isabelli & Emmanuel Colin & BoardGameArena
 * Pyramido implementation : © Marcel van Nieuwenhoven marcel.eindhoven@hotmail.com
 *
 * This code has been produced on the BGA studio platform for use on http://boardgamearena.com.
 * See http://en.boardgamearena.com/#!doc/Studio for more information.
 * -----
 *
 */
declare(strict_types=1);

namespace Bga\Games\Pyramido\UseCases;

include_once(__DIR__.'/../Infrastructure/Domino.php');
include_once(__DIR__.'/../Infrastructure/Marker.php');
include_once(__DIR__.'/../Infrastructure/Resurfacing.php');

include_once(__DIR__.'/../Domain/Pyramid.php');

use Bga\Games\Pyramido\Domain;
use Bga\Games\Pyramido\Infrastructure;

class GetAllDatas {
    protected array $decks = [];
    protected array $next_player_table = [];
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

    public function set_current_player_id($current_player_id): GetAllDatas {
        $this->current_player_id = $current_player_id;
        return $this;
    }

    /**
     * Combine results from database with calculated results from domain
     */
    public function get(): array {
        $players = $this->get_results_from_database();
        $results = ["players" => $players];

        $results['quarry'] = Infrastructure\CurrentMarket::create($this->decks['domino'])->get_market();
        $results['next'] = Infrastructure\CurrentMarket::create($this->decks['domino'])->get_next_market();
        $results['markers'] = Infrastructure\CurrentMarkers::create($this->decks['marker'])->set_players($players)->get();
        $results['resurfacings'] = Infrastructure\CurrentResurfacings::create($this->decks['resurfacing'])->set_players($players)->get();
        $results['placed_resurfacings'] = Infrastructure\CurrentResurfacings::create($this->decks['resurfacing'])->set_players($players)->get_placed_resurfacings();

        $tiles = Infrastructure\CurrentTiles::create($this->decks['domino'])->set_players($players)->get();
        $placed_resurfacings = Infrastructure\CurrentResurfacings::create($this->decks['resurfacing'])->set_players($players)->get_placed_resurfacings();
        $results['tiles'] = [];
        foreach ($tiles as $player_id => $tiles_per_player) {
            $pyramid = new Domain\Pyramid();
            $pyramid->set_tiles($tiles_per_player);
            $pyramid->resurface($placed_resurfacings[$player_id]);
            $results['tiles'][$player_id] = $pyramid->get_tiles();
        }

        if ($this->is_current_player_no_spectator($players)) {
            $pyramid = new Domain\Pyramid();
            $pyramid->set_tiles($results['tiles'][$this->current_player_id]);
            $results['candidate_positions'] = $pyramid->get_candidate_positions();
            $results['current_stage'] = $pyramid->get_stage_next_domino();
            $results['candidate_tiles_for_marker'] = $pyramid->get_candidate_tiles_for_marker($results['markers'][$this->current_player_id]);
            $results['candidate_tiles_for_resurfacing'] = $pyramid->get_candidate_tiles_for_resurfacing($results['markers'][$this->current_player_id]);
        }

        return $results;
    }
    protected function is_current_player_no_spectator($players) {
        return array_key_exists($this->current_player_id, $players);
    }

    protected function get_results_from_database(): array {
        return $this->database->getCollectionFromDb(
            "SELECT `player_id` `id`, `player_name` `name`, `player_score` `score` FROM `player`"
        );
    }
}
?>
