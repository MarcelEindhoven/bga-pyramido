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

include_once(__DIR__.'/Marker.php');

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
class UpdateMarker extends CurrentMarkers
{
    static public function create($deck): UpdateMarker {
        $object = new UpdateMarker();
        $object->set_deck($deck);
        return $object;
    }

    public function move($player_id, $domino_specification): UpdateMarker {
        $this->deck->moveAllCardsInLocation(explode('-', $quarry_index)[0], strval($player_id), (int)explode('-', $quarry_index)[1]
        , $this->calculate_location_argument($domino_specification));
        return $this;
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
        $markers_per_player = [];
        foreach ($this->players as $player_id => $player)
            $markers_per_player[strval($player_id)] = $this->get_markers_for($player_id);

        return $markers_per_player;
    }

    public function get_markers_for($player_id) {
        $markers_per_player = [];
        $markers = $this->deck->getCardsInLocation(strval($player_id));
        foreach ($markers as $marker_specification) {
            $markers_per_player[] = $this->get_first_marker_for($marker_specification);
        }
        return $markers_per_player;
    }

    public function get_first_marker_for($marker_specification) {
        $marker = ['id' => '1', 'colour' => 0, 'stage' => 0, 'horizontal' => 10, 'vertical' => 10, 'rotation' => 0];

        $marker['colour'] = $marker_specification['type'];
        $marker['id'] = $marker_specification['id'];

        CurrentTiles::convert_location_argument($marker, 0 + $marker_specification['location_arg']);
        return $marker;
    }
}

        