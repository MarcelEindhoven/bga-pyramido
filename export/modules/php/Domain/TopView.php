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

namespace Bga\Games\PyramidoCannonFodder\Domain;

#[\AllowDynamicProperties]
class TopView
{
    /**
     * Stage 0 means outside the pyramid.
     * Stage 1-4 are the floors in the pyramid.
     * Stage 4 has the special meaning that it is the last placed domino
     */
    const FACTOR_STAGE = 5;
    const FACTOR_HORIZONTAL = 20; // Maximum range coordinates is between 2 and 19
    const FACTOR_VERTICAL = 20;

    /**
     * pyramid location_key => tile
     * Includes resurfacing tiles, no distinction is made between resurfacing and other tiles
     * Each domino has 2 tiles.
     * A resurfacing has 1 tile which always replaces a domino tile.
     * Each tile has 2x2 locations for jewels.
     * Each tile has a rotation
     * Each jewel and each tile in the pyramid is located on a floor (stage 1-4).
     * The horizontal and vertical location of the tile is the location of the first jewel position in the tile.
     * The horizontal and vertical distance between 2 jewels is an integer number.
     */
    public array $tiles = [];
    public array $jewels = [];
    public array $colour_map = [];

    static public function create($tiles): TopView {
        $object = new TopView();
        $object->set_tiles($tiles);
        return $object;
    }

    /**
     * Tiles on higher stages overwrite 4 lower jewel/colour locations
     */
    public function set_tiles($tiles): TopView {
        foreach(range(1, 4) as $i) {
            foreach($this->get_tiles_for_stage($tiles, $i) as $tile) {
                $this->tiles[$this->get_location_key($tile)] = $tile;
                $this->fill_jewels_from_tile($tile);
                $this->fill_colour_map_from_tile($tile);
            }
        }
        return $this;
    }
    protected function fill_jewels_from_tile($tile): void {
        foreach($tile['jewels'] as $jewel_index) {
            $this->add_jewel($tile, $jewel_index);
        }
    }
    protected function add_jewel($tile, $jewel_index): void {
        $this->jewels[] = $this->get_location_key($tile);
    }
    protected function fill_colour_map_from_tile($tile): void {
        $horizontal = $tile['horizontal'];
        $vertical = $tile['vertical'];
        $colour = $tile['colour'];
        $this->colour_map[$this->get_key($horizontal, $vertical)] = $colour;
        $this->colour_map[$this->get_key($horizontal + 1, $vertical)] = $colour;
        $this->colour_map[$this->get_key($horizontal, $vertical + 1)] = $colour;
        $this->colour_map[$this->get_key($horizontal + 1, $vertical + 1)] = $colour;
    }
    public function get_tiles(): array {return $this->tiles;}
    public function get_jewels(): array {return $this->jewels;}
    public function get_colour_map(): array {return $this->colour_map;}

    /**
     * Calculate key that is unique for each possible combination of horizontal and vertical 
     */
    static public function get_location_key($location_specification) {
        return TopView::get_key($location_specification['horizontal']
        , $location_specification['vertical']);
    }
    static public function get_key($horizontal, $vertical) {
        return  $horizontal + TopView::FACTOR_HORIZONTAL * $vertical;
    }

    public function get_tiles_for_stage($tiles, $stage): array {
        return array_filter($tiles, function($tile) use ($stage) {return $stage == $tile['stage'];});
    }
}
