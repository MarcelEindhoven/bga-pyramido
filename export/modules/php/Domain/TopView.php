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
     * The horizontal and vertical location of the tile is the location of the first jewel in the tile.
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
     * Precondition: key of each lower tile has lower value
     */
    public function set_tiles($tiles): TopView {
        foreach(range(1, 4) as $i) {
            foreach($this->get_tiles_for_stage($tiles, $i) as $tile) {
                $this->tiles[$this->get_location_key($tile)] = $tile;
                $horizontal = $tile['horizontal'];
                $vertical = $tile['vertical'];
                $colour = $tile['colour'];
                $this->colour_map[$this->get_key($horizontal, $vertical)] = $colour;
                $this->colour_map[$this->get_key($horizontal + 1, $vertical)] = $colour;
                $this->colour_map[$this->get_key($horizontal, $vertical + 1)] = $colour;
                $this->colour_map[$this->get_key($horizontal + 1, $vertical + 1)] = $colour;
            }
        }
        return $this;
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

    public function get_candidate_tiles_for_marker($markers): array {
        $tiles_last_placed_with_jewels =  array_filter($this->get_last_placed_tiles(), function($tile) {
            return count($tile['jewels']) > 0;
        });
        $unplaced_markers =  array_filter($markers, function($marker) {return 0 == $marker['stage'];});

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
        $occupied = [];
        foreach ($this->get_tiles_stage($stage) as $tile) {
            $horizontal = $tile['horizontal'];
            $vertical = $tile['vertical'];

            $occupied[$this->calculate_key_horizontal_vertical([$horizontal, $vertical])] = [$horizontal, $vertical];
        }
        return $occupied;
    }
    public function get_bounding_box_first_stage() {
        $horizontal_min = 10;
        $horizontal_max = 10;
        $vertical_min = 10;
        $vertical_max = 10;
        foreach ($this->get_tiles_stage(1) as $tile) {
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
        $occupied = $this->get_occupied_array(1);
        [$horizontal_min, $horizontal_max, $vertical_min, $vertical_max] = $this->get_bounding_box_first_stage();
        $allowed_size_vertical = $horizontal_max - $horizontal_min < 8? 10:8;
        $allowed_size_horizontal = $vertical_max - $vertical_min < 8? 10:8;
        foreach ($this->tiles as $tile) {
            $horizontal = $tile['horizontal'];
            $vertical = $tile['vertical'];

            $candidates[$this->calculate_key_horizontal_vertical([$horizontal - 2, $vertical])] = [$horizontal - 2, $vertical];
            $candidates[$this->calculate_key_horizontal_vertical([$horizontal + 2, $vertical])] = [$horizontal + 2, $vertical];
            $candidates[$this->calculate_key_horizontal_vertical([$horizontal, $vertical - 2])] = [$horizontal, $vertical - 2];
            $candidates[$this->calculate_key_horizontal_vertical([$horizontal, $vertical + 2])] = [$horizontal, $vertical + 2];
        }
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

        return $this->combine ($candidates, $occupied, 1);
    }
    protected function combine($candidates, $occupied, $stage): array {
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

    protected function is_candidate_position_free($occupied, $position, $rotation) {
        if (array_key_exists($this->calculate_key_horizontal_vertical($position), $occupied)) return false;
        return !array_key_exists($this->calculate_key_horizontal_vertical($this->get_position_second_tile($position, $rotation)), $occupied);
    }
    protected function get_position_second_tile($position_first_tile, $rotation) : array {
        $offset_second_tile = $this->get_offset_second_tile($rotation);
        return [$position_first_tile[0] + $offset_second_tile[0], $position_first_tile[1] + $offset_second_tile[1]];
    }
    protected function get_offset_second_tile($rotation) : array {
        if ($rotation == 0) return [2,0];
        if ($rotation == 1) return [0,2];
        if ($rotation == 2) return [-2,0];
        return [0, -2];
    }

    protected function calculate_key_horizontal_vertical_rotation($position) {
        return $this->calculate_key_horizontal_vertical($position) . $position[2];
    }

    protected function calculate_key_horizontal_vertical($position) {
        return '' . $position[0] . $position[1];
    }

}
