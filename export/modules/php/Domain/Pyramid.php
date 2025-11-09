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

include_once(__DIR__.'/../Infrastructure/Domino.php');
use Bga\Games\Pyramido\Infrastructure;

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

    public function get_tiles_for_stage($stage): array {
        return array_filter($this->tiles, function($tile) use ($stage) {return $stage == $tile['stage'];});
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
        return $this->get_adjacent_positions_first_stage();
    }

    public function get_candidate_positions_stage($stage): array {
        $candidates = $this->get_possible_positions($stage);
        $occupied = $this->get_occupied_array($stage);
        $this->create_border($occupied, $stage);
        return $this->combine($candidates, $occupied, $stage);
    }

    protected function get_possible_positions($stage): array {
        // Calculate bounding box stage 1
        [$horizontal_min, $horizontal_max, $vertical_min, $vertical_max] = $this->get_bounding_box_first_stage();
        // Fill candidate array from bounding box
        $candidates = [];
        for ($h = $horizontal_min + $stage -1; $h <= $horizontal_max - $stage + 1; $h = $h +2)
            for ($v = $vertical_min + $stage -1; $v <= $vertical_max - $stage + 1; $v = $v +2)
                $candidates[$this->calculate_key_horizontal_vertical([$h, $v])] = [$h, $v];
        return $candidates;
    }
    protected function create_border(& $occupied, $stage) {
        [$horizontal_min, $horizontal_max, $vertical_min, $vertical_max] = $this->get_bounding_box_first_stage();
        for ($h = $horizontal_min + $stage - 3; $h <= $horizontal_max - $stage + 3; $h = $h +2) {
            $v = $vertical_min + $stage - 3;
            $occupied[$this->calculate_key_horizontal_vertical([$h, $v])] = [$h, $v];
            $v = $vertical_max - $stage + 3;
            $occupied[$this->calculate_key_horizontal_vertical([$h, $v])] = [$h, $v];
        }
        for ($v = $vertical_min + $stage - 3; $v <= $vertical_max - $stage + 3; $v = $v +2) {
            $h = $horizontal_min + $stage - 3;
            $occupied[$this->calculate_key_horizontal_vertical([$h, $v])] = [$h, $v];
            $h = $horizontal_max - $stage + 3;
            $occupied[$this->calculate_key_horizontal_vertical([$h, $v])] = [$h, $v];
        }
    }
    public function get_occupied_array($stage): array {
        return $this->get_occupied_array_for_tiles($this->get_tiles_stage($stage));
    }
    public function get_occupied_array_for_tiles($tiles): array {
        $occupied = [];
        foreach ($tiles as $tile) {
            $horizontal = $tile['horizontal'];
            $vertical = $tile['vertical'];

            $occupied[$this->calculate_key_horizontal_vertical([$horizontal, $vertical])] = [$horizontal, $vertical];
        }
        return $occupied;
    }
    public function get_bounding_box_first_stage() {
        return $this->get_bounding_box($this->get_tiles_stage(1));
    }
    public function get_bounding_box($tiles): array {
        //print("get_bounding_box\n");
        // print_r($tiles);
        $horizontal_min = 10;
        $horizontal_max = 10;
        $vertical_min = 10;
        $vertical_max = 10;
        foreach ($tiles as $tile) {
            $horizontal = $tile['horizontal'];
            $vertical = $tile['vertical'];
            if ($horizontal > $horizontal_max) $horizontal_max = $horizontal;
            if ($horizontal < $horizontal_min) $horizontal_min = $horizontal;
            if ($vertical > $vertical_max) $vertical_max = $vertical;
            if ($vertical < $vertical_min) $vertical_min = $vertical;
        }
        return [$horizontal_min, $horizontal_max, $vertical_min, $vertical_max];
    }
    protected function get_tiles_stage($stage) {
        return array_filter($this->tiles, function($tile) use ($stage) {return $stage == $tile['stage'];});
    }

    public function get_adjacent_positions_first_stage(): array {
        if (sizeof($this->tiles) == 0)
            return $this->get_adjacent_positions_first_stage_initial();
        return $this->get_adjacent_positions_first_stage_with_tiles();
    }

    static public function get_adjacent_positions_first_stage_initial(): array {
        $initial0 = ['stage' => 1, 'horizontal' => 10, 'vertical' => 10, 'rotation' => 0];
        $initial1 = ['stage' => 1, 'horizontal' => 10, 'vertical' => 10, 'rotation' => 1];
        $initial2 = ['stage' => 1, 'horizontal' => 10, 'vertical' => 10, 'rotation' => 2];
        $initial3 = ['stage' => 1, 'horizontal' => 10, 'vertical' => 10, 'rotation' => 3];
        return [$initial0, $initial1, $initial2, $initial3];
    }

    public function get_adjacent_positions_first_stage_with_tiles(): array {
        $candidates = [];
        foreach ($this->tiles as $tile) {
            $horizontal = $tile['horizontal'];
            $vertical = $tile['vertical'];

            $candidates[$this->calculate_key_horizontal_vertical([$horizontal - 2, $vertical])] = [$horizontal - 2, $vertical];
            $candidates[$this->calculate_key_horizontal_vertical([$horizontal + 2, $vertical])] = [$horizontal + 2, $vertical];
            $candidates[$this->calculate_key_horizontal_vertical([$horizontal, $vertical - 2])] = [$horizontal, $vertical - 2];
            $candidates[$this->calculate_key_horizontal_vertical([$horizontal, $vertical + 2])] = [$horizontal, $vertical + 2];
        }

        return $this->combine ($candidates, $this->get_occupied_including_forbidden_spaces($this->tiles), 1);
    }
    public function get_occupied_including_forbidden_spaces($tiles): array {
        $occupied = $this->get_occupied_array_for_tiles($tiles);
        [$horizontal_min, $horizontal_max, $vertical_min, $vertical_max] = $this->get_bounding_box($tiles);
        $allowed_size_vertical = $horizontal_max - $horizontal_min < 8? 10:8;
        $allowed_size_horizontal = $vertical_max - $vertical_min < 8? 10:8;
        for ($i = 0; $i <= 21; $i = $i + 2) {
            $occupied[$this->calculate_key_horizontal_vertical([$i, $vertical_max - $allowed_size_vertical])] = 999;
            $occupied[$this->calculate_key_horizontal_vertical([$i, $vertical_min + $allowed_size_vertical])] = 999;
            $occupied[$this->calculate_key_horizontal_vertical([$horizontal_max - $allowed_size_horizontal, $i])] = 999;
            $occupied[$this->calculate_key_horizontal_vertical([$horizontal_min + $allowed_size_horizontal, $i])] = 999;
        }
        // If bounding box within 4x4, disable corners
        $occupied[$this->calculate_key_horizontal_vertical([$horizontal_max - 8, $vertical_max - 8])] = 999;
        $occupied[$this->calculate_key_horizontal_vertical([$horizontal_max - 8, $vertical_min + 8])] = 999;
        $occupied[$this->calculate_key_horizontal_vertical([$horizontal_min + 8, $vertical_max - 8])] = 999;
        $occupied[$this->calculate_key_horizontal_vertical([$horizontal_min + 8, $vertical_min + 8])] = 999;

        return $occupied;
    }
    public function combine($candidates, $occupied, $stage): array {
        $candidate_positions = [];
        foreach ($candidates as $position) {
            for ($rotation = 0; $rotation <= 3; $rotation++) {
                $offset_second_tile = $this->get_offset_second_tile($rotation);
                $candidate = $position;
                $candidate[2] = $rotation;
                if ($this->is_candidate_position_free($occupied, $candidate, $rotation))
                    $this->register_candidate_position($candidate_positions, $candidate, $stage);
                $candidate[0] = $candidate[0] - $offset_second_tile[0];
                $candidate[1] = $candidate[1] - $offset_second_tile[1];
                if ($this->is_candidate_position_free($occupied, $candidate, $rotation))
                    $this->register_candidate_position($candidate_positions, $candidate, $stage);
            }
        }
        return $candidate_positions;
    }
    protected function register_candidate_position(& $candidate_positions, $position, $stage) {
        if (array_key_exists($this->calculate_key_horizontal_vertical_rotation($position), $candidate_positions)) return;
        $candidate_positions[$this->calculate_key_horizontal_vertical_rotation($position)] = ['horizontal' => $position[0], 'vertical' => $position[1], 'rotation' => $position[2], 'stage' => $stage, ];
    }

    public function is_candidate_position_free($occupied, $position, $rotation) {
        if (array_key_exists($this->calculate_key_horizontal_vertical($position), $occupied)) return false;
        if (array_key_exists($this->calculate_key_horizontal_vertical($this->get_position_second_tile($position, $rotation)), $occupied)) return false;
        $new_occupied = $occupied + [
            $this->calculate_key_horizontal_vertical($position) => 999,
            $this->calculate_key_horizontal_vertical($this->get_position_second_tile($position, $rotation)) => 999,
        ];
        //print_r($position);
        //print_r(array_keys($new_occupied));

        if (! self::can_neighbour_areas_be_filled_with_dominoes($new_occupied, $position)) {
            return false;
        }
        if (! self::can_neighbour_areas_be_filled_with_dominoes($new_occupied, $this->get_position_second_tile($position, $rotation))) return false;
        return true;
    }
    public function can_neighbour_areas_be_filled_with_dominoes($occupied, $position): bool {
        foreach (self::get_neighbours($position) as $neighbour) {
            if (!self::can_area_be_filled_with_dominoes($occupied, $neighbour)) return false;
        }
        return true;
    }
    public function can_area_be_filled_with_dominoes($occupied, $first_free_position): bool {
        if ((count($this->get_free_contiguous_area($occupied, $first_free_position)) % 2) != 0) {
            // print_r($first_free_position);
            // print_r(array_keys($occupied));
            // print_r($this->get_free_contiguous_area($occupied, $first_free_position));
            // print(count($this->get_free_contiguous_area($occupied, $first_free_position)));
        }
        return (count($this->get_free_contiguous_area($occupied, $first_free_position)) % 2) == 0;
    }
    public function get_free_contiguous_area($occupied, $first_free_position): array {
        $area = [];
        $candidates = [$first_free_position];
        $occupied_new = $occupied;
        while ($candidates) {
            $candidate = array_shift($candidates);
            // print_r($candidate);
            $location_key = $this->calculate_key_horizontal_vertical($candidate);
            if (!array_key_exists($location_key, $occupied_new)) {
                $area[] = $candidate;
                // Use "+" when the keys are used and array_merge when the values are used
                $candidates = array_merge($candidates, $this->get_neighbours($candidate));
                $occupied_new[$location_key] = $candidate;
                // print_r($candidates);
            }
        }
        return $area;
    }
    static public function get_neighbours($position): array {
        return [
            [$position[0] + 2, $position[1]],
            [$position[0] - 2, $position[1]],
            [$position[0], $position[1] + 2],
            [$position[0], $position[1] - 2],
        ];
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

    /**
     * Calculate key that is unique for each possible combination of horizontal and vertical and rotation
     */
    protected function calculate_key_horizontal_vertical_rotation($position) {
        return $this->calculate_key_horizontal_vertical($position) . $position[2];
    }
    /**
     * Calculate key that is unique for each possible combination of horizontal and vertical
     * BUG: position 11, 1 results in the same key as 1, 11, fortunately this combination cannot happen
     */
    static public function calculate_key_horizontal_vertical($position) {
        return '' . $position[0] . $position[1];
    }

}
