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

class GetAllDatas {
    protected array $decks = [];
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

        return $results;
    }

    protected function get_results_from_database(): array {
        $result = [];
        $result["players"] = $this->database->getCollectionFromDb(
            "SELECT `player_id` `id`, `player_score` `score` FROM `player`"
        );
        return $result;
    }
}
?>
