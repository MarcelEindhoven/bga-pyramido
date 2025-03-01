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
        foreach ($this->tiles as $tile) {
            $horizontal = $tile['horizontal'];
            $vertical = $tile['vertical'];
            $occupied[$this->calculate_key($horizontal, $vertical)] = [$horizontal, $vertical];

            $neighbours[$this->calculate_key($horizontal - 2, $vertical)] = [$horizontal - 2, $vertical];
            $neighbours[$this->calculate_key($horizontal + 2, $vertical)] = [$horizontal + 2, $vertical];
            $neighbours[$this->calculate_key($horizontal, $vertical - 2)] = [$horizontal, $vertical - 2];
            $neighbours[$this->calculate_key($horizontal, $vertical + 2)] = [$horizontal, $vertical + 2];
        }
        foreach ($neighbours as $position) {
            $horizontal = $position[0];
            $vertical = $position[1];
            if (!array_key_exists($this->calculate_key($horizontal, $vertical) . 0, $candidate_positions) &&
            !array_key_exists($this->calculate_key($horizontal + 2, $vertical), $occupied) &&
            !array_key_exists($this->calculate_key($horizontal, $vertical), $occupied)){
                $candidate_positions[$this->calculate_key($horizontal, $vertical) . 0] = ['horizontal' => $horizontal, 'vertical' => $vertical, 'rotation' => 0];
            }
            if (!array_key_exists($this->calculate_key($horizontal - 2, $vertical) . 0, $candidate_positions) &&
            !array_key_exists($this->calculate_key($horizontal - 2, $vertical), $occupied) &&
            !array_key_exists($this->calculate_key($horizontal, $vertical), $occupied)){
                $candidate_positions[$this->calculate_key($horizontal - 2, $vertical) . 0] = ['horizontal' => $horizontal - 2, 'vertical' => $vertical, 'rotation' => 0];
            }
        }
        return $candidate_positions;
    }
    protected function calculate_key($horizontal, $vertical) {
        return '' . $horizontal . $vertical;
    }

}
