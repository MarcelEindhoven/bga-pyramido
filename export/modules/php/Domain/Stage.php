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

include_once(__DIR__.'/DominoHorizontalVertical.php');
include_once(__DIR__.'/StageTilePosition.php');

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
    public array $tile_positions = []; // location_key => StageTilePosition
    public int $stage = 1;

    static public function create($tile_positions): StageTilePositions {
        $object = new StageTilePositions($tile_positions);
        return $object;
    }
    public function __construct(array $tile_positions) {
        $this->set_tile_positions($tile_positions);
    }
    /**
     * Stored as key -> StageTilePosition
     */
    public function set_tile_positions($tile_positions): StageTilePositions {
        foreach($tile_positions as $tile_position) {
            $tile = StageTilePosition::create_from_position($tile_position);
            $this->tile_positions[$tile->key()] = $tile;
        }
        return $this;
    }

    /**
     * Return dominoes including rotation
     */
    public function get_candidate_dominoes(): array {
        $candidates = [];
        foreach ($this->create_candidate_dominoes() as $domino2d) {
            $candidates = array_merge($candidates, $domino2d->create_dominoes_with_rotation($this->stage));
        }
        return $candidates;
    }
    /**
     * Returns array of DominoHorizontalVertical that can be placed without problems
     */
    public function create_candidate_dominoes(): array {
        if (empty($this->tile_positions)) {
            return $this->create_initial_candidate_dominoes();
        }
        return $this->create_multiple_candidate_dominoes();
    }
    /**
     * Returns array of all DominoHorizontalVertical that can be placed without problems
     */
    public function create_multiple_candidate_dominoes(): array {
        return array_filter($this->create_all_possible_dominoes(), function($domino) {
            return $this->is_domino_approved($domino);
        });
    }
    /**
     * Returns true if domino can be placed and does not cause empty spaces
     */
    public function is_domino_approved($domino): bool {
        if ($this->does_domino_overlap($domino))
            return false;
        return $this->get_with_additional_positions($domino)->is_valid();
    }
    /**
     * Candidate is an array of 2 positions with horizontal and vertical coordinates
     */
    public function does_domino_overlap($candidate): bool {
        foreach ($candidate as $position) {
            if ($this->is_position_occupied($position))
                return true;
        }
        return false;
    }
    /**
     * Returns true if the position is occupied by an existing tile
     */
    public function is_position_occupied($position): bool {
        return  array_key_exists(StageTilePosition::create_from_position($position)->key(), $this->tile_positions);
    }

    /**
     * Return object of the same type, with additional tiles
     */
    protected function get_with_additional_positions($additional_tile_positions): StageTilePositions {
        $tile_positions = $this->tile_positions;
        foreach ( $additional_tile_positions as $additional_tile_position )
            $tile_positions[$additional_tile_position->key()] = $additional_tile_position;

        return $this->get_with_positions($tile_positions);
    }
}

class FirstStageTilePositions extends StageTilePositions {
    static public function create_and_fill($tile_positions): FirstStageTilePositions {
        $object = new FirstStageTilePositions($tile_positions);
        return $object;
    }

    protected function get_with_positions($tile_positions): FirstStageTilePositions {
        return FirstStageTilePositions::create_and_fill($tile_positions);
    }

    public function create_initial_candidate_dominoes(): array {
        $domino_horizontal = DominoHorizontalVertical::create_from_coordinates([10, 10], [12, 10]);
        $domino_vertical = DominoHorizontalVertical::create_from_coordinates([10, 10], [10, 12]);
        return [$domino_horizontal->key() => $domino_horizontal,
                $domino_vertical->key() => $domino_vertical];
    }

    /**
     * Returns array of all DominoHorizontalVertical that touch existing tiles
     */
    public function create_all_possible_dominoes(): array {
        $candidates = [];
        foreach ($this->tile_positions as $tile_position) {
            foreach ($this->create_touching_dominoes($tile_position) as $key => $domino) {
                // Avoid duplicates, could also have been achieved by "+"?
                $candidates[$key] = $domino;
            }
        }
        return $candidates;
    }
    public function create_touching_dominoes($tile_position): array {
        $horizontal = $tile_position['horizontal'];
        $vertical = $tile_position['vertical'];
        $candidates = [];
        foreach ([[-2, 2], [0, 2], [-4, 0], [2, 0], [-2, -2], [0, -2]] as $delta) {
            $domino_horizontal = DominoHorizontalVertical::create_from_coordinates(
                [$horizontal + $delta[0], $vertical + $delta[1]],
                [$horizontal + $delta[0] + 2, $vertical + $delta[1]]);
            $domino_vertical = DominoHorizontalVertical::create_from_coordinates(
                    [$horizontal + $delta[1], $vertical + $delta[0]],
                    [$horizontal + $delta[1], $vertical + $delta[0] + 2]);
            $candidates[$domino_horizontal->key()] = $domino_horizontal;
            $candidates[$domino_vertical->key()] = $domino_vertical;
        }
        return $candidates;
    }

    /**
     * Returns true if a valid 5x4 or 4x5 box is possible
     */
    public function is_valid(): bool {
        foreach ($this->get_all_bounding_boxes() as $bounding_box) {
            if (BoundedStageTilePositions::create_and_fill(1, $this->tile_positions, $bounding_box)->is_valid())
                return true;
        }
        return false;
    }
    /**
     * Get all possible future bounding boxes as [$horizontal_min, $vertical_min, $horizontal_max, $vertical_max]
     */
    public function get_all_bounding_boxes(): array {
        $bounding_boxes = [];
        [$horizontal_min, $vertical_min, $horizontal_max, $vertical_max] = $this->get_bounding_box();
        // 5x4
        for ($hmin = $horizontal_max - 8; $hmin <= $horizontal_min; $hmin = $hmin +2)
            for ($vmin = $vertical_max - 6; $vmin <= $vertical_min; $vmin = $vmin +2)
                $bounding_boxes[] = [$hmin, $vmin, $hmin + 8, $vmin + 6];
        // 4x5
        for ($hmin = $horizontal_max - 6; $hmin <= $horizontal_min; $hmin = $hmin +2)
            for ($vmin = $vertical_max - 8; $vmin <= $vertical_min; $vmin = $vmin +2)
                $bounding_boxes[] = [$hmin, $vmin, $hmin + 6, $vmin + 8];
        return $bounding_boxes;
    }
    /**
     * bounding box as [$horizontal_min, $vertical_min, $horizontal_max, $vertical_max]
     * Coordinates from top left tile until and including bottom right tile
     */
    public function get_bounding_box(): array {
        $horizontal_min = 20;
        $horizontal_max = 0;
        $vertical_min = 20;
        $vertical_max = 0;
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
}

class BoundedStageTilePositions extends StageTilePositions {
    public array $bounding_box_first_stage;
    /** Tiles plus border positions */
    public array $occupied_positions = [];

    static public function create_and_fill($stage, $tile_positions, $bounding_box_first_stage): BoundedStageTilePositions {
        $object = new BoundedStageTilePositions($tile_positions);
        $object->set_bounding_box_first_stage($bounding_box_first_stage);
        $object->set_stage($stage);
        $object->occupied_positions = $object->tile_positions;
        $object->create_border_positions();
        return $object;
    }
    public function set_bounding_box_first_stage($bounding_box): BoundedStageTilePositions {
        $this->bounding_box_first_stage = $bounding_box;
        return $this;
    }    
    public function set_stage($stage): BoundedStageTilePositions {
        $this->stage = $stage;
        return $this;
    }
    public function create_border_positions(): BoundedStageTilePositions {
        $min_horizontal = $this->bounding_box_first_stage[0] + ($this->stage - 3);
        $max_horizontal = $this->bounding_box_first_stage[2] - ($this->stage - 3);
        $min_vertical = $this->bounding_box_first_stage[1] + ($this->stage - 3);
        $max_vertical = $this->bounding_box_first_stage[3] - ($this->stage - 3);

        for($horizontal = $min_horizontal; $horizontal <= $max_horizontal; $horizontal +=2) {
            $this->occupy([$horizontal, $min_vertical]);
            $this->occupy([$horizontal, $max_vertical]);
        }
        for($vertical = $min_vertical + 2; $vertical <= $max_vertical -2; $vertical +=2) {
            $this->occupy([$min_horizontal, $vertical]);
            $this->occupy([$max_horizontal, $vertical]);
        }
        return $this;
    }
    protected function occupy($position): StageTilePositions {
        $tile = StageTilePosition::create($position[0], $position[1]);
        $this->occupied_positions[$tile->key()] = $tile;

        return $this;
    }

    protected function get_with_positions($tile_positions): BoundedStageTilePositions {
        return BoundedStageTilePositions::create_and_fill($this->stage, $tile_positions, $this->bounding_box_first_stage);
    }

    public function create_initial_candidate_dominoes(): array {
        return $this->create_all_possible_dominoes();
    }
    /**
     * Returns array of all DominoHorizontalVertical that fit within the stage
     */
    public function create_all_possible_dominoes(): array {
        [$horizontal_min, $vertical_min, $horizontal_max, $vertical_max] = $this->get_bounding_box();
        // Fill candidate array from bounding box
        $candidates = [];
        for ($v = $vertical_min; $v <= $vertical_max; $v = $v +2)
            for ($h = $horizontal_min; $h < $horizontal_max; $h = $h +2)
                $candidates[] = DominoHorizontalVertical::create_from_coordinates([$h, $v], [$h + 2, $v]);
        for ($v = $vertical_min; $v < $vertical_max; $v = $v +2)
            for ($h = $horizontal_min; $h <= $horizontal_max; $h = $h +2)
                $candidates[] = DominoHorizontalVertical::create_from_coordinates([$h, $v], [$h, $v + 2]);
        return $candidates;
    }

    public function is_valid(): bool {
        [$horizontal_min, $vertical_min, $horizontal_max, $vertical_max] = $this->get_bounding_box();
        for($horizontal = $horizontal_min; $horizontal <= $horizontal_max; $horizontal +=2) {
            for($vertical = $vertical_min; $vertical <= $vertical_max; $vertical +=2) {
                if ($this->are_empty_spaces_inevitable_for_position(StageTilePosition::create($horizontal, $vertical)))
                    return false;
            }
        }
        return true;
    }
    /**
     * Get bounding box from possible tile top left until and including possible tile right bottom
     */
    public function get_bounding_box(): array {
        return [
            $this->bounding_box_first_stage[0] + ($this->stage - 1),
            $this->bounding_box_first_stage[1] + ($this->stage - 1),
            $this->bounding_box_first_stage[2] - ($this->stage - 1),
            $this->bounding_box_first_stage[3] - ($this->stage - 1),
        ];
    }
    /**
     * Check positions of a position on the provided stage tile set
     * and return true if any position has a free contiguous area with odd size.
     */
    public function are_empty_spaces_inevitable_for_position($position): bool {
        $free_contiguous_area = $this->get_free_contiguous_area($position);
        return (count($free_contiguous_area) % 2 != 0);
    }
    /**
     * Returns array of StageTilePosition, can be empty
     */
    public function get_free_contiguous_area($first_free_position): array {
        $area = [];
        $candidates = [StageTilePosition::create_from_position($first_free_position)];
        $occupied = $this->occupied_positions;
        while ($candidates) {
            $candidate = array_shift($candidates);
            // print_r($candidate);
            if (!array_key_exists($candidate->key(), $occupied)) {
                $area[] = $candidate;
                // Use "+" when the keys have a ono-to-one relation with the values and array_merge when the values are used
                $candidates = array_merge($candidates, $candidate->get_neighbours());
                $occupied[$candidate->key()] = $candidate;
                // print_r($candidates);
            }
        }
        return $area;
    }
}
