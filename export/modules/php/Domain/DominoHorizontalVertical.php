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

include_once(__DIR__.'/StageTilePosition.php');

/**
 * Array of 2 StageTilePosition, converts coordinates into StageTilePositions into 2 dominoes
 */
class DominoHorizontalVertical extends \ArrayObject {
    /**
     * First coordinates must be smaller than second, so to the left or to the top
     */
    static public function create_from_coordinates($coordinates1, $coordinates2): DominoHorizontalVertical {
        if ($coordinates1[0] < $coordinates2[0] || $coordinates1[1] < $coordinates2[1]) {
            $smaller = $coordinates1;
            $larger = $coordinates2;
        } else {
            $smaller = $coordinates2;
            $larger = $coordinates1;
        }
        $object = new DominoHorizontalVertical([
            StageTilePosition::create_from_coordinates($smaller),
            StageTilePosition::create_from_coordinates($larger)
        ]);
        return $object;
    }
    public function __construct($positions) {
        foreach ($positions as $key => $value)
            $this[] = $value;
    }

    public function key(): int {
        return $this[0]->key() + $this[1]->key() * StageTilePosition::FACTOR_HORIZONTAL * StageTilePosition::FACTOR_HORIZONTAL;
    }

    public function create_dominoes_with_rotation($stage): array {
        return $this[0]['horizontal'] === $this[1]['horizontal']
            ? $this->create_vertical_dominoes($stage)
            : $this->create_horizontal_dominoes($stage);
    }

    public function create_horizontal_dominoes($stage): array {
        return [['horizontal' => $this[0]['horizontal'], 'vertical' => $this[0]['vertical'], 'rotation' => 0, 'stage' => $stage, ],
                ['horizontal' => $this[1]['horizontal'], 'vertical' => $this[1]['vertical'], 'rotation' => 2, 'stage' => $stage,]
    ];
    }

    public function create_vertical_dominoes($stage): array {
        return [['horizontal' => $this[0]['horizontal'], 'vertical' => $this[0]['vertical'], 'rotation' => 1, 'stage' => $stage, ],
                ['horizontal' => $this[1]['horizontal'], 'vertical' => $this[1]['vertical'], 'rotation' => 3, 'stage' => $stage,]
    ];
    }
}
