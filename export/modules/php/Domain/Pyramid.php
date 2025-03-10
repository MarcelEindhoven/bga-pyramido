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

    public function get_adjacent_positions_first_stage(): array {
        if (sizeof($this->tiles) == 0)
        return $this->get_adjacent_positions_first_stage_initial();
        return $this->get_adjacent_positions_first_stage_with_tiles();
    }

    public function get_adjacent_positions_first_stage_initial(): array {
        $initial0 = ['horizontal' => 10, 'vertical' => 10, 'rotation' => 0];
        $initial1 = ['horizontal' => 10, 'vertical' => 10, 'rotation' => 1];
        $initial2 = ['horizontal' => 10, 'vertical' => 10, 'rotation' => 2];
        $initial3 = ['horizontal' => 10, 'vertical' => 10, 'rotation' => 3];
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

            $occupied[$this->calculate_key($horizontal, $vertical)] = [$horizontal, $vertical];

            $neighbours[$this->calculate_key($horizontal - 2, $vertical)] = [$horizontal - 2, $vertical];
            $neighbours[$this->calculate_key($horizontal + 2, $vertical)] = [$horizontal + 2, $vertical];
            $neighbours[$this->calculate_key($horizontal, $vertical - 2)] = [$horizontal, $vertical - 2];
            $neighbours[$this->calculate_key($horizontal, $vertical + 2)] = [$horizontal, $vertical + 2];
        }
        $allowed_size_vertical = $horizontal_max - $horizontal_min < 8? 10:8;
        $allowed_size_horizontal = $vertical_max - $vertical_min < 8? 10:8;
        for ($i = 0; $i <= 21; $i++) {
            $occupied[$this->calculate_key($i, $vertical_max - $allowed_size_vertical)] = 999;
            $occupied[$this->calculate_key($i, $vertical_min + $allowed_size_vertical)] = 999;
            $occupied[$this->calculate_key($horizontal_max - $allowed_size_horizontal, $i)] = 999;
            $occupied[$this->calculate_key($horizontal_min + $allowed_size_horizontal, $i)] = 999;
        }
        foreach ($neighbours as $position) {
            $horizontal = $position[0];
            $vertical = $position[1];
            $candidate_key = $this->calculate_key($horizontal, $vertical);
            if (!array_key_exists($candidate_key . 0, $candidate_positions) &&
            !array_key_exists($this->calculate_key($horizontal + 2, $vertical), $occupied) &&
            !array_key_exists($candidate_key, $occupied)){
                $candidate_positions[$candidate_key . 0] = ['horizontal' => $horizontal, 'vertical' => $vertical, 'rotation' => 0];
            }
            $candidate_key = $this->calculate_key($horizontal - 2, $vertical);
            if (!array_key_exists($candidate_key . 0, $candidate_positions) &&
            !array_key_exists($candidate_key, $occupied) &&
            !array_key_exists($this->calculate_key($horizontal, $vertical), $occupied)){
                $candidate_positions[$candidate_key . 0] = ['horizontal' => $horizontal - 2, 'vertical' => $vertical, 'rotation' => 0];
            }
            $candidate_key = $this->calculate_key($horizontal, $vertical);
            if (!array_key_exists($candidate_key . 1, $candidate_positions) &&
            !array_key_exists($this->calculate_key($horizontal, $vertical + 2), $occupied) &&
            !array_key_exists($candidate_key, $occupied)){
                $candidate_positions[$candidate_key . 1] = ['horizontal' => $horizontal, 'vertical' => $vertical, 'rotation' => 1];
            }
            $candidate_key = $this->calculate_key($horizontal, $vertical - 2);
            if (!array_key_exists($candidate_key . 1, $candidate_positions) &&
            !array_key_exists($candidate_key, $occupied) &&
            !array_key_exists($this->calculate_key($horizontal, $vertical), $occupied)){
                $candidate_positions[$candidate_key . 1] = ['horizontal' => $horizontal, 'vertical' => $vertical - 2, 'rotation' => 1];
            }
            $candidate_key = $this->calculate_key($horizontal, $vertical);
            if (!array_key_exists($candidate_key . 2, $candidate_positions) &&
            !array_key_exists($this->calculate_key($horizontal - 2, $vertical), $occupied) &&
            !array_key_exists($candidate_key, $occupied)){
                $candidate_positions[$candidate_key . 2] = ['horizontal' => $horizontal, 'vertical' => $vertical, 'rotation' => 2];
            }
            $candidate_key = $this->calculate_key($horizontal + 2, $vertical);
            if (!array_key_exists($candidate_key . 2, $candidate_positions) &&
            !array_key_exists($candidate_key, $occupied) &&
            !array_key_exists($this->calculate_key($horizontal, $vertical), $occupied)){
                $candidate_positions[$candidate_key . 2] = ['horizontal' => $horizontal + 2, 'vertical' => $vertical, 'rotation' => 2];
            }
            $candidate_key = $this->calculate_key($horizontal, $vertical);
            if (!array_key_exists($candidate_key . 3, $candidate_positions) &&
            !array_key_exists($this->calculate_key($horizontal, $vertical - 2), $occupied) &&
            !array_key_exists($candidate_key, $occupied)){
                $candidate_positions[$candidate_key . 3] = ['horizontal' => $horizontal, 'vertical' => $vertical, 'rotation' => 3];
            }
            $candidate_key = $this->calculate_key($horizontal, $vertical + 2);
            if (!array_key_exists($candidate_key . 3, $candidate_positions) &&
            !array_key_exists($candidate_key, $occupied) &&
            !array_key_exists($this->calculate_key($horizontal, $vertical), $occupied)){
                $candidate_positions[$candidate_key . 3] = ['horizontal' => $horizontal, 'vertical' => $vertical + 2, 'rotation' => 3];
            }
        }
        return $candidate_positions;
    }
    protected function calculate_key($horizontal, $vertical) {
        return '' . $horizontal . $vertical;
    }

}
