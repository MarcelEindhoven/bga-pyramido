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
    const OFFSETS = [
        ['horizontal' => 0, 'vertical' => 0],
        ['horizontal' => 0, 'vertical' => 1],
        ['horizontal' => 1, 'vertical' => 0],
        ['horizontal' => 1, 'vertical' => 1],
    ];
    const INDEX_AFTER_ROTATION = [
        [0, 1, 2, 3],
        [2, 0, 3, 1],
        [3, 2, 1, 0],
        [1, 3, 0, 2]
    ];

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
    public function get_tiles(): array {return $this->tiles;}
    public function get_jewels(): array {return $this->jewels;}
    public function get_colour_map(): array {return $this->colour_map;}

    protected function fill_jewels_from_tile($tile): void {
        $this->hide_lower_stage_jewels($tile);
        foreach($tile['jewels'] as $jewel_index) {
            $this->add_jewel($tile, $jewel_index);
        }
    }
    protected function add_jewel($tile, $jewel_index): void {
        $this->jewels[] = $this->get_location_key(
            $this->add(
                $tile, 
                $this->get_offset($this->get_rotated_jewel_index($jewel_index, $tile['rotation']))));
    }
    protected function hide_lower_stage_jewels($tile): void {
        foreach(range(0, 3) as $jewel_index) {
            $location_to_be_hidden = $this->add(
                $tile, 
                $this->get_offset($jewel_index));
            if (($key = array_search($this->get_location_key($location_to_be_hidden), $this->jewels)) !== false)
                unset($this->jewels[$key]);
        }
    }
    protected function get_offset($jewel_index): array {
        return TopView::OFFSETS[$jewel_index];
    }
    protected function get_rotated_jewel_index($jewel_index, $rotation): int {
        return TopView::INDEX_AFTER_ROTATION[$rotation][$jewel_index];
    }
    protected function add($location1, $location2): array {
        return [
            'horizontal' => $location1['horizontal'] + $location2['horizontal'],
            'vertical' => $location1['vertical'] + $location2['vertical'],
        ];

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
    static public function get_location($key): array {
        return [
            'horizontal' => $key % TopView::FACTOR_HORIZONTAL,
            'vertical' => intdiv($key, TopView::FACTOR_HORIZONTAL),
        ];
    }

    public function get_tiles_for_stage($tiles, $stage): array {
        return array_filter($tiles, function($tile) use ($stage) {return $stage == $tile['stage'];});
    }
}
