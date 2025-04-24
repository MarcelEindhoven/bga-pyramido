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
 * states.inc.php
 *
 * PyramidoCannonFodder game states description
 *
 */

/*
   Game state machine is a tool used to facilitate game developpement by doing common stuff that can be set up
   in a very easy way from this configuration file.

   Please check the BGA Studio presentation about game state to understand this, and associated documentation.

   Summary:

   States types:
   _ activeplayer: in this type of state, we expect some action from the active player.
   _ multipleactiveplayer: in this type of state, we expect some action from multiple players (the active players)
   _ game: this is an intermediary state where we don't expect any actions from players. Your game logic must decide what is the next game state.
   _ manager: special type for initial and final state

   Arguments of game states:
   _ name: the name of the GameState, in order you can recognize it on your own code.
   _ description: the description of the current game state is always displayed in the action status bar on
                  the top of the game. Most of the time this is useless for game state with "game" type.
   _ descriptionmyturn: the description of the current game state when it's your turn.
   _ type: defines the type of game states (activeplayer / multipleactiveplayer / game / manager)
   _ action: name of the method to call when this game state become the current game state. Usually, the
             action method is prefixed by "st" (ex: "stMyGameStateName").
   _ possibleactions: array that specify possible player actions on this step. It allows you to use "checkAction"
                      method on both client side (Javacript: this.checkAction) and server side (PHP: $this->checkAction).
   _ transitions: the transitions are the possible paths to go from a game state to another. You must name
                  transitions in order to use transition names in "nextState" PHP method, and use IDs to
                  specify the next game state for each transition.
   _ args: name of the method to call to retrieve arguments for this gamestate. Arguments are sent to the
           client side to be used on "onEnteringState" or to set arguments in the gamestate description.
   _ updateGameProgression: when specified, the game progression is updated (=> call to your getGameProgression
                            method).
*/

//    !! It is not a good idea to modify this file when a game is running !!


$machinestates = [

    // The initial state. Please do not modify.

    1 => array(
        "name" => "gameSetup",
        "description" => "",
        "type" => "manager",
        "action" => "stGameSetup",
        "transitions" => ["" => 10]
    ),

    // Note: ID=2 => your first state

    10 => [
        "name" => "selectAndPlaceQuarry",
        "description" => clienttranslate('${actplayer} must place a domino'),
        "descriptionmyturn" => clienttranslate('${you} must place a domino'),
        "type" => "activeplayer",
        "args" => "argPlayerTurn",
        "possibleactions" => [
            // these actions are called from the front with bgaPerformAction, and matched to the function on the game.php file
            "action_domino_chosen_and_placed", 
        ],
        "transitions" => ["" => 11,]
    ],
    11 => [
        "name" => "afterDominoPlaced",
        "description" => '',
        "type" => "game",
        "action" => "stAfterDominoPlaced",
        "transitions" => ["no_candidate_tile" => 30, "single_candidate_tile" => 12, "double_candidate_tile" => 20,]
    ],
    12 => [
        "name" => "automaticallyPlaceMarker",
        "description" => '',
        "type" => "game",
        "action" => "stAutomaticallyPlaceMarker",
        "transitions" => ["" => 30,]
    ],
    20 => [
        "name" => "selectMarkerTile",
        "description" => clienttranslate('${actplayer} must select tile to place marker'),
        "descriptionmyturn" => clienttranslate('${you} must select tile to place marker'),
        "type" => "activeplayer",
        "args" => "argPlayerTurn",
        "possibleactions" => [
            "action_tile_to_place_marker_chosen", 
        ],
        "transitions" => ["" => 21,]
    ],
    21 => [
        "name" => "afterMarkerPlaced",
        "description" => '',
        "type" => "game",
        "action" => "stAfterMarkerPlaced",
        "transitions" => ["" => 30,]
    ],
    30 => [
        "name" => "selectNextDomino",
        "description" => clienttranslate('${actplayer} must select next domino'),
        "descriptionmyturn" => clienttranslate('${you} must select next domino'),
        "type" => "activeplayer",
        "args" => "argPlayerTurn",
        "possibleactions" => [
            // these actions are called from the front with bgaPerformAction, and matched to the function on the game.php file
            "action_next_domino_chosen", 
        ],
        "transitions" => ["" => 31,]
    ],
    31 => [
        "name" => "afterTurnFinished",
        "description" => '',
        "type" => "game",
        "action" => "stAfterTurnFinished",
        "transitions" => ["stage_finished" => 32, "stage_not_finished" => 35,]
    ],
    32 => [
        "name" => "afterStageFinished",
        "description" => '',
        "type" => "game",
        "action" => "stAfterStageFinished",
        "transitions" => ["not_finished_playing" => 35, "finished_playing" => 99]
    ],
    35 => [
        "name" => "nextPlayer",
        "description" => 'Choosing next player',
        "type" => "game",
        "action" => "stNextPlayer",
        "updateGameProgression" => true,
        "possibleactions" => array("player_playing", "ai_playing", "finished_playing"),
        "transitions" => ["player_playing" => 10, "ai_playing" => 50, "finished_playing" => 99]
    ],
    50 => [
        "name" => "selectAndPlaceDominoAI",
        "description" => 'AI selects and places domino',
        "type" => "game",
        "action" => "stAISelectAndPlaceDomino",
        "transitions" => ["" => 60, ]
    ],
    60 => [
        "name" => "selectAndPlaceMarkerAI",
        "description" => 'AI selects and places marker',
        "type" => "game",
        "action" => "stAISelectAndPlaceMarker",
        "transitions" => ["" => 70, ]
    ],
    70 => [
        "name" => "chooseNextDominoAI",
        "description" => 'AI chooses next domino',
        "type" => "game",
        "action" => "stAIChooseNextDomino",
        "transitions" => ["" => 31, ]
    ],
    // Final state.
    // Please do not modify (and do not overload action/args methods).
    99 => [
        "name" => "gameEnd",
        "description" => clienttranslate("End of game"),
        "type" => "manager",
        "action" => "stGameEnd",
        "args" => "argGameEnd"
    ],

];



