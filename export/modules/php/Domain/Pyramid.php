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
class Pyramid
{
    protected array $tiles = [];

    static public function create($tiles): Pyramid {
        $object = new Pyramid();
        $object->set_tiles($tiles);
        return $object;
    }

    public function set_tiles($tiles): Pyramid {
        $this->tiles = $tiles;
        return $this;
    }

    public function get_stage_next_domino(): int {
        if (count($this->get_tiles_for_stage(4)) > 0)
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
        $tiles_stage_4 =  array_filter($this->tiles, function($tile) {return 4 == $tile['stage'];});
        $tiles_stage_4_with_jewels =  array_filter($tiles_stage_4, function($tile) {return count($tile['jewels']) > 0;});
        $markers_stage_0 =  array_filter($markers, function($marker) {return 0 == $marker['stage'];});
        return array_filter($tiles_stage_4_with_jewels, function($tile) use ($markers_stage_0){
            foreach ($markers_stage_0 as $marker) {
                if (($marker['colour'] == $tile['colour'])) return true;
            }
            return false;
        });
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
        $occupied = [];
        $neighbours = [];
        $candidate_positions = [];
        $horizontal_min = 10;
        $horizontal_max = 10;
        $vertical_min = 10;
        $vertical_max = 10;
        foreach ($this->tiles as $tile) {
            $horizontal = $tile['horizontal'];
            $vertical = $tile['vertical'];
            if ($horizontal > $horizontal_max) $horizontal_max = $horizontal;
            if ($horizontal < $horizontal_min) $horizontal_min = $horizontal;
            if ($vertical > $vertical_max) $vertical_max = $vertical;
            if ($vertical < $vertical_min) $vertical_min = $vertical;

            $occupied[$this->calculate_key_horizontal_vertical([$horizontal, $vertical])] = [$horizontal, $vertical];

            $neighbours[$this->calculate_key_horizontal_vertical([$horizontal - 2, $vertical])] = [$horizontal - 2, $vertical];
            $neighbours[$this->calculate_key_horizontal_vertical([$horizontal + 2, $vertical])] = [$horizontal + 2, $vertical];
            $neighbours[$this->calculate_key_horizontal_vertical([$horizontal, $vertical - 2])] = [$horizontal, $vertical - 2];
            $neighbours[$this->calculate_key_horizontal_vertical([$horizontal, $vertical + 2])] = [$horizontal, $vertical + 2];
        }
        $allowed_size_vertical = $horizontal_max - $horizontal_min < 8? 10:8;
        $allowed_size_horizontal = $vertical_max - $vertical_min < 8? 10:8;
        for ($i = 0; $i <= 21; $i++) {
            $occupied[$this->calculate_key_horizontal_vertical([$i, $vertical_max - $allowed_size_vertical])] = 999;
            $occupied[$this->calculate_key_horizontal_vertical([$i, $vertical_min + $allowed_size_vertical])] = 999;
            $occupied[$this->calculate_key_horizontal_vertical([$horizontal_max - $allowed_size_horizontal, $i])] = 999;
            $occupied[$this->calculate_key_horizontal_vertical([$horizontal_min + $allowed_size_horizontal, $i])] = 999;
        }
        foreach ($neighbours as $position) {
            for ($rotation = 0; $rotation <= 3; $rotation++) {
                $offset_second_tile = $this->get_offset_second_tile($rotation);
                $candidate = $position;
                $candidate[2] = $rotation;
                if ($this->is_candidate_position_free($occupied, $candidate, $rotation))
                    $this->register_candidate_position($candidate_positions, $candidate, $rotation);
                $candidate[0] = $candidate[0] - $offset_second_tile[0];
                $candidate[1] = $candidate[1] - $offset_second_tile[1];
                if ($this->is_candidate_position_free($occupied, $candidate, $rotation))
                    $this->register_candidate_position($candidate_positions, $candidate, $rotation);
            }
        }
        return $candidate_positions;
    }
    protected function register_candidate_position(& $candidate_positions, $position) {
        if (array_key_exists($this->calculate_key_horizontal_vertical_rotation($position), $candidate_positions)) return;
        $candidate_positions[$this->calculate_key_horizontal_vertical_rotation($position)] = ['horizontal' => $position[0], 'vertical' => $position[1], 'rotation' => $position[2], 'stage' => 1, ];
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
