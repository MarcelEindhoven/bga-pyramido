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

/**
 * Array of StageTilePosition
 */
class StageDomino extends \ArrayObject {
    static public function create_from_coordinates($coordinates1, $coordinates2): StageDomino {
        $object = new StageDomino([
            StageTilePosition::create_from_coordinates($coordinates1),
            StageTilePosition::create_from_coordinates($coordinates2)
        ]);
        return $object;
    }
    public function __construct($positions) {
        foreach ($positions as $key => $value)
            $this[] = $value;
    }
}

/**
 * Collection of tile positions that helps to determine if a domino can be placed
 */
class StageTilePositions
{
    /**
     * location_key => tile
     * Each domino has 2 tiles.
     * A resurfacing has 1 tile which always replaces a domino tile.
     * Each tile has 2x2 locations for jewels.
     * Each jewel and each tile in the pyramid is located on a floor (stage 1-4).
     * The horizontal and vertical location of the tile is the location of the first jewel in the tile.
     * The horizontal and vertical distance between 2 jewels is an integer number.
     */
    public array $tile_positions = [];
    public array $occupied_positions = [];

    static public function create($tile_positions): StageTilePositions {
        $object = new StageTilePositions($tile_positions);
        return $object;
    }
    public function __construct(array $tile_positions) {
        $this->set_tile_positions($tile_positions);
    }
    /**
     * Precondition: key of each tile == get_location_key(tile)
     */
    public function set_tile_positions($tile_positions): StageTilePositions {
        foreach($tile_positions as $tile_position) {
            $tile = StageTilePosition::create_from_position($tile_position);
            $this->tile_positions[$tile->key()] = $tile;
            $this->occupied_positions[$tile->key()] = $tile;
        }
        return $this;
    }
    /**
     * Candidate is an array of 2 positions with horizontal and vertical coordinates
     */
    public function can_domino_be_placed($candidate): bool {
        if ($this->is_position_occupied($candidate[0]))
            return false;
        if ($this->is_position_occupied($candidate[1]))
            return false;
        return ! $this->are_empty_spaces_inevitable($candidate);
    }
    public function is_position_occupied($position): bool {
        return  array_key_exists(StageTilePosition::create_from_position($position)->key(), $this->occupied_positions);
    }
    /**
     * Because a domino always consists of 2 tiles, the space available for dominoes must be an even number of tiles
     */
    public function are_empty_spaces_inevitable($positions_candidate_domino): bool {
        $include_candidate_domino = $this->get_with_additional_positions($positions_candidate_domino);
        foreach ($positions_candidate_domino as $position) {
            foreach (StageTilePosition::create_from_position($position)->get_neighbours() as $neighbour) {
                $free_contiguous_area = $include_candidate_domino->get_free_contiguous_area($neighbour);
                if (count($free_contiguous_area) % 2 != 0)
                    return true;
            }
        }
        return false;
    }
    /**
     * Returns array of StageTilePosition, can be empty
     */
    public function get_free_contiguous_area($first_free_position): array {
        $area = [];
        $candidates = [StageTilePosition::create_from_position($first_free_position)];
        $occupied_new = $this->occupied_positions;
        while ($candidates) {
            $candidate = array_shift($candidates);
            // print_r($candidate);
            if (!array_key_exists($candidate->key(), $occupied_new)) {
                $area[] = $candidate;
                // Use "+" when the keys have a ono-to-one relation with the values and array_merge when the values are used
                $candidates = array_merge($candidates, $candidate->get_neighbours());
                $occupied_new[$candidate->key()] = $candidate;
                // print_r($candidates);
            }
        }
        return $area;
    }
}

class FirstStageTilePositions extends StageTilePositions {
    static public function create_and_fill($tile_positions): FirstStageTilePositions {
        $object = new FirstStageTilePositions($tile_positions);
        $object->create_border_positions();
        return $object;
    }
    public function create_border_positions(): FirstStageTilePositions {
        [$horizontal_min, $vertical_min, $horizontal_max, $vertical_max] = $this->get_bounding_box();

        $allowed_size_vertical = $horizontal_max - $horizontal_min < 8? 10:8;
        $allowed_size_horizontal = $vertical_max - $vertical_min < 8? 10:8;
        for ($i = 0; $i <= 21; $i = $i + 2) {
            $this->occupy([$i, $vertical_max - $allowed_size_vertical]);
            $this->occupy([$i, $vertical_min + $allowed_size_vertical]);
            $this->occupy([$horizontal_max - $allowed_size_horizontal, $i]);
            $this->occupy([$horizontal_min + $allowed_size_horizontal, $i]);
        }
        // If bounding box within 4x4, disable corners
        $this->occupy([$horizontal_max - 8, $vertical_max - 8]);
        $this->occupy([$horizontal_max - 8, $vertical_min + 8]);
        $this->occupy([$horizontal_min + 8, $vertical_max - 8]);
        $this->occupy([$horizontal_min + 8, $vertical_min + 8]);

        return $this;
    }
    protected function occupy($position): FirstStageTilePositions {
        $tile = StageTilePosition::create($position[0], $position[1]);
        $this->occupied_positions[$tile->key()] = $tile;

        return $this;
    }
    public function get_bounding_box(): array {
        //print("get_bounding_box\n");
        // print_r($tiles);
        $horizontal_min = 10;
        $horizontal_max = 10;
        $vertical_min = 10;
        $vertical_max = 10;
        foreach ($this->tile_positions as $tile_position) {
            $horizontal = $tile_position['horizontal'];
            $vertical = $tile_position['vertical'];
            if ($horizontal > $horizontal_max) $horizontal_max = $horizontal;
            if ($horizontal < $horizontal_min) $horizontal_min = $horizontal;
            if ($vertical > $vertical_max) $vertical_max = $vertical;
            if ($vertical < $vertical_min) $vertical_min = $vertical;
        }
        return [$horizontal_min, $vertical_min, $horizontal_max, $vertical_max];
    }

    protected function get_with_additional_positions($positions_candidate_domino): FirstStageTilePositions {
        return FirstStageTilePositions::create_and_fill($positions_candidate_domino + $this->tile_positions);
    }
}

class HigherStageTilePositions extends StageTilePositions {
    public array $bounding_box_first_stage;
    public int $stage;

    static public function create_and_fill($stage, $tile_positions, $bounding_box_first_stage): HigherStageTilePositions {
        $object = new HigherStageTilePositions($tile_positions);
        $object->set_bounding_box_first_stage($bounding_box_first_stage);
        $object->set_stage($stage);
        $object->create_border_positions();
        return $object;
    }
    public function set_bounding_box_first_stage($bounding_box): HigherStageTilePositions {
        $this->bounding_box_first_stage = $bounding_box;
        return $this;
    }    
    public function set_stage($stage): HigherStageTilePositions {
        $this->stage = $stage;
        return $this;
    }
    public function create_border_positions(): HigherStageTilePositions {
        $min_horizontal = $this->bounding_box_first_stage[0] + ($this->stage - 3);
        $max_horizontal = $this->bounding_box_first_stage[2] - ($this->stage - 3);
        $min_vertical = $this->bounding_box_first_stage[1] + ($this->stage - 3);
        $max_vertical = $this->bounding_box_first_stage[3] - ($this->stage - 3);

        for($horizontal = $min_horizontal; $horizontal <= $max_horizontal; $horizontal +=2) {
            $this->occupied_positions[StageTilePosition::create($horizontal, $min_vertical)->key()] = StageTilePosition::create($horizontal, $min_vertical);
            $this->occupied_positions[StageTilePosition::create($horizontal, $max_vertical)->key()] = StageTilePosition::create($horizontal, $max_vertical);
        }
        for($vertical = $min_vertical + 2; $vertical <= $max_vertical -2; $vertical +=2) {
            $this->occupied_positions[StageTilePosition::create($min_horizontal, $vertical)->key()] = StageTilePosition::create($min_horizontal, $vertical);
            $this->occupied_positions[StageTilePosition::create($max_horizontal, $vertical)->key()] = StageTilePosition::create($max_horizontal, $vertical);
        }
        return $this;
    }
    protected function get_with_additional_positions($positions_candidate_domino): HigherStageTilePositions {
        return HigherStageTilePositions::create_and_fill($this->stage, $positions_candidate_domino + $this->tile_positions, $this->bounding_box_first_stage);
    }
    public function create_horizontal_candidate_dominoes(): array {
        [$horizontal_min, $vertical_min, $horizontal_max, $vertical_max] = $this->bounding_box_first_stage;
        $stage = $this->stage;
        // Fill candidate array from bounding box
        $candidates = [];
        for ($v = $vertical_min + $stage - 1; $v <= $vertical_max - $stage + 1; $v = $v +2)
            for ($h = $horizontal_min + $stage - 1; $h <= $horizontal_max - $stage - 1; $h = $h +2)
                $candidates[] = StageDomino::create_from_coordinates([$h, $v], [$h + 2, $v]);
        return $candidates;
    }
    public function create_vertical_candidate_dominoes(): array {
        [$horizontal_min, $vertical_min, $horizontal_max, $vertical_max] = $this->bounding_box_first_stage;
        $stage = $this->stage;
        // Fill candidate array from bounding box
        $candidates = [];
        for ($v = $vertical_min + $stage - 1; $v <= $vertical_max - $stage - 1; $v = $v +2)
            for ($h = $horizontal_min + $stage - 1; $h <= $horizontal_max - $stage + 1; $h = $h +2)
                $candidates[] = StageDomino::create_from_coordinates([$h, $v], [$h, $v + 2]);
        return $candidates;
    }
}
