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
        return $this->get_adjacent_positions_first_stage_initial();
    }

    public function get_adjacent_positions_first_stage_initial(): array {
        $initial0 = ['horizontal' => 10, 'vertical' => 10, 'rotation' => 0];
        $initial1 = ['horizontal' => 10, 'vertical' => 10, 'rotation' => 1];
        $initial2 = ['horizontal' => 10, 'vertical' => 10, 'rotation' => 2];
        $initial3 = ['horizontal' => 10, 'vertical' => 10, 'rotation' => 3];
        return [$initial0, $initial1, $initial2, $initial3];
    }

}
