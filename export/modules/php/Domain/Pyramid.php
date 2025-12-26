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

namespace Bga\Games\Pyramido\Domain;

include_once(__DIR__.'/Stage.php');

#[\AllowDynamicProperties]
class Pyramid
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
     * location_key => tile
     * Each domino has 2 tiles.
     * A resurfacing has 1 tile which always replaces a domino tile.
     * Each tile has 2x2 locations for jewels.
     * Each jewel and each tile in the pyramid is located on a floor (stage 1-4).
     * The horizontal and vertical location of the tile is the location of the first jewel in the tile.
     * The horizontal and vertical distance between 2 jewels is an integer number.
     */
    public array $tiles = [];

    static public function create($tiles): Pyramid {
        $object = new Pyramid();
        $object->set_tiles($tiles);
        return $object;
    }

    /**
     * Precondition: key of each tile == get_location_key(tile)
     */
    public function set_tiles($tiles): Pyramid {
        $this->tiles = $tiles;
        return $this;
    }
    public function get_tiles(): array {return $this->tiles;}

    public function resurface($placed_resurfacings): Pyramid {
        foreach ($placed_resurfacings as $placed_resurfacing)
            $this->replace_tile($placed_resurfacing);
        return $this;
    }
    protected function replace_tile($placed_resurfacing) {
        $this->tiles[$this->get_location_key($placed_resurfacing)] = $placed_resurfacing;
    }

    /**
     * Calculate key that is unique for each possible combination of stage and horizontal and vertical
     */
    static public function get_location_key($pyramid_object_specification) {
        return  $pyramid_object_specification['stage']
        + Pyramid::FACTOR_STAGE * $pyramid_object_specification['horizontal']
        + Pyramid::FACTOR_STAGE * Pyramid::FACTOR_HORIZONTAL * $pyramid_object_specification['vertical'];
    }

    public function get_stage_next_domino(): int {
        if ((count($this->get_tiles_for_stage(4)) > 0) && (count($this->get_tiles_for_stage(3)) > 4))
            return 5;
        if (count($this->get_tiles_for_stage(3)) > 4)
            return 4;
        if (count($this->get_tiles_for_stage(2)) > 10)
            return 3;
        if (count($this->get_tiles_for_stage(1)) > 18)
            return 2;
        return 1;
    }

    public function get_candidate_tiles_for_marker($markers): array {
        $tiles_last_placed_with_jewels =  array_filter($this->get_last_placed_tiles(), function($tile) {
            return count($tile['jewels']) > 0;
        });
        $unplaced_markers = array_filter($markers, function($marker) {return 0 == $marker['stage'];});

        return array_filter($tiles_last_placed_with_jewels, function($tile) use ($unplaced_markers){
            return $this->is_tile_candidate_for_marker($tile, $unplaced_markers);
        });
    }
    protected function is_tile_candidate_for_marker($tile, $unplaced_markers): bool {
        foreach ($unplaced_markers as $marker) {
            if (($marker['colour'] == $tile['colour'])) return true;
        }
        return false;
    }

    public function get_candidate_tiles_for_resurfacing($markers): array {
        return array_filter($this->get_last_placed_tiles(), function($tile) use ($markers){
            foreach ($markers as $marker) {
                if (($marker['horizontal'] == $tile['horizontal']) && ($marker['vertical'] == $tile['vertical'])) return false;
            }
            return true;
        });
    }

    public function get_last_placed_tiles(): array {
        return array_filter($this->tiles, function($tile) {return 4 == $tile['stage'];});
    }

    public function get_candidate_positions(): array {
        $stage = $this->get_stage_next_domino();
        if ($stage > 1) return $this->get_candidate_positions_stage($stage);
        return FirstStageTilePositions::create_and_fill(
            $this->get_tiles_stage(1)
            )->get_candidate_dominoes();
    }

    public function get_candidate_positions_stage($stage): array {
        $bounding_box = FirstStageTilePositions::create_and_fill(
            $this->get_tiles_stage(1)
            )->get_bounding_box();
        return BoundedStageTilePositions::create_and_fill(
            $stage, 
            $this->get_tiles_stage($stage),
            $bounding_box)->get_candidate_dominoes();
    }

    public function get_tiles_for_stage($stage): array {
        return array_filter($this->tiles, function($tile) use ($stage) {return $stage == $tile['stage'];});
    }

    protected function get_tiles_stage($stage) {
        return array_filter($this->tiles, function($tile) use ($stage) {return $stage == $tile['stage'];});
    }

    protected function get_position_second_tile($position_first_tile, $rotation) : array {
        $offset_second_tile = $this->get_offset_second_tile($rotation);
        return [$position_first_tile[0] + $offset_second_tile[0], $position_first_tile[1] + $offset_second_tile[1]];
    }
    static protected function get_offset_second_tile($rotation) : array {
        if ($rotation == 0) return [2,0];
        if ($rotation == 1) return [0,2];
        if ($rotation == 2) return [-2,0];
        return [0, -2];
    }

}
