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
    public array $tile_positions = [];
    public array $occupied_positions = [];
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
            $this->occupied_positions[$tile->key()] = $tile;
        }
        return $this;
    }
    public function get_candidate_dominoes(): array {
        $candidates = [];
        foreach ($this->create_candidate_dominoes() as $domino2d) {
            if ($this->can_domino_be_placed($domino2d)) {
                $candidates = array_merge($candidates, $domino2d->create_dominoes_with_rotation($this->stage));
            }
        }
        return $candidates;
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
     * Because a domino always consists of 2 tiles, the space available for dominoes within a rectangle must be an even number of tiles
     */
    public function are_empty_spaces_inevitable($positions_candidate_domino): bool {
        $tile_positions = $this->tile_positions;
        $tile_positions[$positions_candidate_domino[0]->key()] = $positions_candidate_domino[0];
        $tile_positions[$positions_candidate_domino[1]->key()] = $positions_candidate_domino[1];
        $include_candidate_domino = $this->get_with_additional_positions($tile_positions);
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
    protected function occupy($position): StageTilePositions {
        $tile = StageTilePosition::create($position[0], $position[1]);
        $this->occupied_positions[$tile->key()] = $tile;

        return $this;
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

    protected function get_with_additional_positions($tile_positions): FirstStageTilePositions {
        return FirstStageTilePositions::create_and_fill($tile_positions);
    }
    public function create_candidate_dominoes(): array {
        if (empty($this->tile_positions)) {
            return $this->create_initial_candidate_domino();
        }
        return $this->create_multiple_candidate_dominoes();
    }
    public function create_initial_candidate_domino(): array {
        $domino_horizontal = DominoHorizontalVertical::create_from_coordinates([10, 10], [12, 10]);
        $domino_vertical = DominoHorizontalVertical::create_from_coordinates([10, 10], [10, 12]);
        return [$domino_horizontal->key() => $domino_horizontal,
                $domino_vertical->key() => $domino_vertical];
    }
    public function create_multiple_candidate_dominoes(): array {
        $candidates = [];
        foreach ($this->tile_positions as $tile_position) {
            $candidates +=  $this->create_candidate_dominoes_for_tile($tile_position);
        }
        return $candidates;

    }
    public function create_candidate_dominoes_for_tile($tile_position): array {
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
}

class BoundedStageTilePositions extends StageTilePositions {
    public array $bounding_box_first_stage;

    static public function create_and_fill($stage, $tile_positions, $bounding_box_first_stage): BoundedStageTilePositions {
        $object = new BoundedStageTilePositions($tile_positions);
        $object->set_bounding_box_first_stage($bounding_box_first_stage);
        $object->set_stage($stage);
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
    protected function get_with_additional_positions($tile_positions): BoundedStageTilePositions {
        return BoundedStageTilePositions::create_and_fill($this->stage, $tile_positions, $this->bounding_box_first_stage);
    }
    public function create_candidate_dominoes(): array {
        [$horizontal_min, $vertical_min, $horizontal_max, $vertical_max] = $this->bounding_box_first_stage;
        $stage = $this->stage;
        // Fill candidate array from bounding box
        $candidates = [];
        for ($v = $vertical_min + $stage - 1; $v <= $vertical_max - $stage + 1; $v = $v +2)
            for ($h = $horizontal_min + $stage - 1; $h <= $horizontal_max - $stage - 1; $h = $h +2)
                $candidates[] = DominoHorizontalVertical::create_from_coordinates([$h, $v], [$h + 2, $v]);
        for ($v = $vertical_min + $stage - 1; $v <= $vertical_max - $stage - 1; $v = $v +2)
            for ($h = $horizontal_min + $stage - 1; $h <= $horizontal_max - $stage + 1; $h = $h +2)
                $candidates[] = DominoHorizontalVertical::create_from_coordinates([$h, $v], [$h, $v + 2]);
        return $candidates;
    }
    public function create_vertical_candidate_dominoes(): array {
        [$horizontal_min, $vertical_min, $horizontal_max, $vertical_max] = $this->bounding_box_first_stage;
        $stage = $this->stage;
        // Fill candidate array from bounding box
        $candidates = [];
        return $candidates;
    }
}
