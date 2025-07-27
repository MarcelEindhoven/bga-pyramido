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
include_once(__DIR__.'/TopView.php');

#[\AllowDynamicProperties]
class StageScore
{
    public TopView $stage;
    protected array $colour_per_location_key = [];
    protected array $jewel_location_keys = [];

    static public function create($stage): StageScore {
        $object = new StageScore();
        $object->set_stage($stage);
        return $object;
    }

    public function set_stage($jewel_location_keys, $colour_map): StageScore {
        $this->jewel_location_keys = $jewel_location_keys;
        $this->colour_per_location_key = $colour_map;
        return $this;
    }

    public function get_score($markers): int {
        $score = 0;
        $lowest_score = 20;
        foreach($markers as $marker) {
            $score = $score + $this->get_score_for_marker($marker);
            $lowest_score = min($lowest_score, $this->get_score_for_marker($marker));
        }
        if ($score >0)
            $score = $score + $lowest_score; 
        return $score;
    }
    protected function get_score_for_marker($marker): int {
        $marker_colour = $this->colour_per_location_key[TopView::get_location_key($marker)];
        $marker_colour_location_keys = array_keys(array_filter($this->colour_per_location_key,
            function($colour) use($marker_colour) {
                return $marker_colour == $colour;
            }
        ));
        $marker_area_location_keys = $this->get_area($marker, $marker_colour_location_keys);

        return count(array_intersect($this->jewel_location_keys, $marker_area_location_keys));
    }
    protected function get_area($initial_location, $location_keys): array {
        $area = [];
        $candidates = [$initial_location];
        while ($candidates) {
            $candidate = array_shift($candidates);
            $location_key = TopView::get_location_key($candidate);
            $index = array_search ($location_key, $location_keys);
            if ($index !== false) {
                unset($location_keys[$index]);
                $area[] = $location_key;
                $candidates = array_merge($candidates, $this->get_neighbours($candidate));
            }
        }
        return $area;
    }
    protected function get_neighbours($location): array {
        $horizontal = $location['horizontal'];
        $vertical = $location['vertical'];
        return [
            ['horizontal' => $horizontal, 'vertical' => $vertical + 1],
            ['horizontal' => $horizontal + 1, 'vertical' => $vertical],
            ['horizontal' => $horizontal, 'vertical' => $vertical - 1],
            ['horizontal' => $horizontal - 1, 'vertical' => $vertical],
        ];
    }

    protected function get_key($horizontal, $vertical) {
        $location = ['horizontal' => $horizontal, 'vertical' => $vertical];
        return TopView::get_location_key($location);
    }
}
