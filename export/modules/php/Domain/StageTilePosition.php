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

/**
 * Horizontal and vertical coordinate within stage
 */
class StageTilePosition extends \ArrayObject {
    const FACTOR_HORIZONTAL = 100; // Maximum range coordinates is between 0 and 20
    static public function create_from_position($position): StageTilePosition {
        $object = new StageTilePosition($position);
        return $object;
    }
    static public function create_from_coordinates($coordinates): StageTilePosition {
        $object = new StageTilePosition(['horizontal'=> $coordinates[0], 'vertical' => $coordinates[1]]);
        return $object;
    }
    static public function create($horizontal, $vertical): StageTilePosition {
        $object = new StageTilePosition(['horizontal'=> $horizontal, 'vertical' => $vertical]);
        return $object;
    }
    public function __construct($position) {
        foreach ($position as $key => $value)
            $this[$key] = $value;
    }

    public function key(): int {
        return $this['horizontal'] + $this['vertical'] * StageTilePosition::FACTOR_HORIZONTAL;
    }

    public function get_neighbours(): array {
        return [
            StageTilePosition::create($this['horizontal'] + 2, $this['vertical']),
            StageTilePosition::create($this['horizontal'] - 2, $this['vertical']),
            StageTilePosition::create($this['horizontal'], $this['vertical'] + 2),
            StageTilePosition::create($this['horizontal'], $this['vertical'] - 2),
        ];
    }
}
