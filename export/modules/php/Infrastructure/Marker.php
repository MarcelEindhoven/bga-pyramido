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

namespace Bga\Games\PyramidoCannonFodder\Infrastructure;

#[\AllowDynamicProperties]
class MarkerFactory
{
    protected array $definitions = [];

    static public function create($deck): MarkerFactory {
        $object = new MarkerFactory();
        $object->set_deck($deck);
        return $object;
    }

    public function set_deck($deck) {
        $this->deck = $deck;
    }

    public function add($colour) {
        $this->definitions[] = array( 'type' => $colour, 'type_arg' => 0, 'nbr' => 1);
    }
    public function flush($player_id) {
        $this->deck->createCards($this->definitions, '' . $player_id, 0);
        $this->definitions = [];
    }
}

#[\AllowDynamicProperties]
class CurrentMarkers
{
    const FACTOR_STAGE = 5;
    const FACTOR_HORIZONTAL = 20;
    const FACTOR_VERTICAL = 20;
    const FACTOR_ROTATION = 4;

    static public function create($deck): CurrentMarkers {
        $object = new CurrentMarkers();
        $object->set_deck($deck);
        return $object;
    }

    public function set_deck($deck): CurrentMarkers {
        $this->deck = $deck;

        return $this;
    }

    public function set_players($players): CurrentMarkers {
        $this->players = $players;

        return $this;
    }

    public function get_marker($player_id, $marker_specification) {
        $marker_array =  $this->deck->getCardsInLocation(strval($player_id), $this->calculate_location_argument($marker_specification));
        return end($marker_array);
    }

    protected function calculate_location_argument($marker_specification) {
        return  $marker_specification['stage']
        + CurrentMarkers::FACTOR_STAGE * $marker_specification['horizontal']
        + CurrentMarkers::FACTOR_STAGE * CurrentMarkers::FACTOR_HORIZONTAL * $marker_specification['vertical']
        + CurrentMarkers::FACTOR_STAGE * CurrentMarkers::FACTOR_HORIZONTAL * CurrentMarkers::FACTOR_VERTICAL * $marker_specification['rotation'];
    }

    public function get(): array {
        $tiles_per_player = [];
        foreach ($this->players as $player_id => $player)
            $tiles_per_player[strval($player_id)] = $this->get_tiles_for($player_id);

        return $tiles_per_player;
    }

    public function get_tiles_for($player_id) {
        $tiles_per_player = [];
        $dominoes = $this->deck->getCardsInLocation(strval($player_id));
        foreach ($dominoes as $domino) {
            $tiles_per_player[] = $this->get_first_tile_for($domino);
        }
        return $tiles_per_player;
    }

    public function get_first_tile_for($domino) {
        $tile = ['id' => '1', 'colour' => 0, 'stage' => 1, 'horizontal' => 10, 'vertical' => 10, 'rotation' => 0];

        $tile['colour'] = $domino['type'];

        return $tile;
    }
}

        