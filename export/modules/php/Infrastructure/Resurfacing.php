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

include_once(__DIR__.'/Resurfacing.php');

#[\AllowDynamicProperties]
class ResurfacingFactory
{
    protected array $definitions = [];

    static public function create($deck): ResurfacingFactory {
        $object = new ResurfacingFactory();
        $object->set_deck($deck);
        return $object;
    }

    public function set_deck($deck) {
        $this->deck = $deck;
    }

    public function add($first_colour, $second_colour) {
        $this->definitions[] = array( 'type' => $first_colour, 'type_arg' => $second_colour, 'nbr' => 1);
    }
    public function flush($player_id) {
        $this->deck->createCards($this->definitions, '' . $player_id, 0);
        $this->definitions = [];
    }
}

#[\AllowDynamicProperties]
class UpdateResurfacing extends CurrentResurfacings
{
    static public function create($deck): UpdateResurfacing {
        $object = new UpdateResurfacing();
        $object->set_deck($deck);
        return $object;
    }

    public function move($player_id, $tile): UpdateResurfacing {
        $cards = $this->deck->getCardsInLocation(strval($player_id), 0);
        $colour = $tile['colour'];
        $this->deck->moveCard(current(array_filter($cards, function($card) use($colour){return $colour == $card['type'];}))['id']
        , strval($player_id), $this->calculate_location_argument($tile));

        return $this;
    }

    public function return_all_markers($player_id): UpdateResurfacing {
        $cards = $this->deck->getCardsInLocation(strval($player_id));
        foreach ($cards as $card) {
            $this->deck->moveCard($card['id'], strval($player_id), 0);
        }
        return $this;
    }
}

#[\AllowDynamicProperties]
class CurrentResurfacings
{
    const FACTOR_STAGE = 5;
    const FACTOR_HORIZONTAL = 20;
    const FACTOR_VERTICAL = 20;
    const FACTOR_ROTATION = 4;

    static public function create($deck): CurrentResurfacings {
        $object = new CurrentResurfacings();
        $object->set_deck($deck);
        return $object;
    }

    public function set_deck($deck): CurrentResurfacings {
        $this->deck = $deck;

        return $this;
    }

    public function set_players($players): CurrentResurfacings {
        $this->players = $players;

        return $this;
    }

    public function get_marker($player_id, $marker_specification) {
        $marker_array =  $this->deck->getCardsInLocation(strval($player_id), $this->calculate_location_argument($marker_specification));
        return $this->get_marker_for(end($marker_array));
    }

    public function calculate_location_argument($marker_or_tile_specification) {
        return  $marker_or_tile_specification['stage']
        + CurrentResurfacings::FACTOR_STAGE * $marker_or_tile_specification['horizontal']
        + CurrentResurfacings::FACTOR_STAGE * CurrentResurfacings::FACTOR_HORIZONTAL * $marker_or_tile_specification['vertical']
        + CurrentResurfacings::FACTOR_STAGE * CurrentResurfacings::FACTOR_HORIZONTAL * CurrentResurfacings::FACTOR_VERTICAL * $marker_or_tile_specification['rotation'];
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
            $markers_per_player[$marker_specification['type']] = $this->get_marker_for($marker_specification);
        }
        return $markers_per_player;
    }

    public function get_marker_for($marker_specification) {
        $marker = ['id' => '1', 'colour' => 0, 'stage' => 0, 'horizontal' => 10, 'vertical' => 10, 'rotation' => 0];

        $marker['colour'] = $marker_specification['type'];
        $marker['id'] = $marker_specification['id'];

        CurrentTiles::convert_location_argument($marker, 0 + $marker_specification['location_arg']);
        return $marker;
    }
}

        