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

namespace Bga\Games\Pyramido\Setup;

class Market
{
    static public function create($deck_domino): Market {
        $object = new Market();
        $object->deck_domino = $deck_domino;
        return $object;
    }

    public function setup($players) : Market{
        return $this;
    }
}
