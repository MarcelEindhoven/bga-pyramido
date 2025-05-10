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
        $tile['side'] = ($colour % 2);
        $type = $colour - $tile['side'];
        $this->deck->moveCard(current(array_filter($cards, function($card) use($type){return $type == $card['type'];}))['id']
        , strval($player_id), $this->calculate_location_argument($tile));

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
    const FACTOR_SIDE = 2;

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

    public function get_resurfacing($player_id, $resurfacing_specification) {
        $resurfacing_array =  $this->deck->getCardsInLocation(strval($player_id), $this->calculate_location_argument($resurfacing_specification));
        return $this->get_resurfacings_for(end($resurfacing_array));
    }

    public function calculate_location_argument($resurfacing_or_tile_specification) {
        return  $resurfacing_or_tile_specification['stage']
        + CurrentResurfacings::FACTOR_STAGE * $resurfacing_or_tile_specification['horizontal']
        + CurrentResurfacings::FACTOR_STAGE * CurrentResurfacings::FACTOR_HORIZONTAL * $resurfacing_or_tile_specification['vertical']
        + CurrentResurfacings::FACTOR_STAGE * CurrentResurfacings::FACTOR_HORIZONTAL * CurrentResurfacings::FACTOR_VERTICAL * $resurfacing_or_tile_specification['rotation'];
        + CurrentResurfacings::FACTOR_STAGE * CurrentResurfacings::FACTOR_HORIZONTAL * CurrentResurfacings::FACTOR_VERTICAL * CurrentResurfacings::FACTOR_ROTATION * ($resurfacing_or_tile_specification['colour'] % 2);
    }

    public function get_placed_resurfacings(): array {
        $resurfacings_per_player = [];
        foreach ($this->players as $player_id => $player)
            $resurfacings_per_player[strval($player_id)] = $this->get_placed_resurfacings_for($player_id);

        return $resurfacings_per_player;
    }

    public function get_placed_resurfacings_for($player_id) {
        $resurfacings_per_player = [];
        $resurfacings = array_filter($this->deck->getCardsInLocation(strval($player_id)),
            function($card) {return 0 != $card['location_arg'];});
        foreach ($resurfacings as $resurfacing_specification) {
            $resurfacings_per_player[] = $this->get_placed_resurfacing($resurfacing_specification);
        }
        return $resurfacings_per_player;
    }
    protected function get_placed_resurfacing($resurfacing_specification): array {
        $resurfacing = ['id' => '1', 'class' => 'resurfacing', 'colour' => 0, 'stage' => 0];

        $resurfacing['id'] = $resurfacing_specification['id'];
        $resurfacing['side'] = (int) ($resurfacing_specification['location_arg'] / (CurrentResurfacings::FACTOR_STAGE * CurrentResurfacings::FACTOR_HORIZONTAL * CurrentResurfacings::FACTOR_VERTICAL * CurrentResurfacings::FACTOR_ROTATION));
        $resurfacing['colour'] = $resurfacing['side'] ? $resurfacing_specification['type_arg'] : $resurfacing_specification['type'];
        CurrentTiles::convert_location_argument($resurfacing, $resurfacing_specification['location_arg'] % (CurrentResurfacings::FACTOR_STAGE * CurrentResurfacings::FACTOR_HORIZONTAL * CurrentResurfacings::FACTOR_VERTICAL * CurrentResurfacings::FACTOR_ROTATION));

        return $resurfacing;
    }

    public function get(): array {
        $resurfacings_per_player = [];
        foreach ($this->players as $player_id => $player)
            $resurfacings_per_player[strval($player_id)] = $this->get_resurfacings_for($player_id);

        return $resurfacings_per_player;
    }

    public function get_resurfacings_for($player_id) {
        $resurfacings_per_player = [];
        $resurfacings = $this->deck->getCardsInLocation(strval($player_id), 0);
        foreach ($resurfacings as $resurfacing_specification) {
            $resurfacings_per_player[$resurfacing_specification['type']] = $this->get_resurfacing_first($resurfacing_specification);
            $resurfacings_per_player[$resurfacing_specification['type_arg']] = $this->get_resurfacing_second($resurfacing_specification);
        }
        return $resurfacings_per_player;
    }

    public function get_resurfacing_first($resurfacing_specification) {
        $resurfacing = ['id' => '1', 'colour' => 0, 'stage' => 0];

        $resurfacing['colour'] = $resurfacing_specification['type'];
        $resurfacing['id'] = $resurfacing_specification['id'];

        return $resurfacing;
    }

    public function get_resurfacing_second($resurfacing_specification) {
        $resurfacing = ['id' => '1', 'colour' => 0, 'stage' => 0];

        $resurfacing['colour'] = $resurfacing_specification['type_arg'];
        $resurfacing['id'] = $resurfacing_specification['id'] + 100;

        return $resurfacing;
    }
}

        