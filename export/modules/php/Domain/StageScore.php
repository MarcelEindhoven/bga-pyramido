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
class StageScore
{
    public TopView $stage;

    static public function create($stage): StageScore {
        $object = new StageScore();
        $object->set_stage($stage);
        return $object;
    }

    /**
     * Precondition: key of each tile == get_location_key(tile)
     */
    public function set_stage($stage): StageScore {
        $this->stage = $stage;
        return $this;
    }
    public function get_score($markers): int {
        foreach($markers as $marker) {
            return 2 * $this->get_score_for_marker($marker);
        }
        return 0;
    }
    protected function get_score_for_marker($marker): int {
        return count($this->stage->jewels);
    }
}
