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
 * Game.php
 *
 * This is the main file for your game logic.
 *
 * In this PHP file, you are going to defines the rules of the game.
 */
declare(strict_types=1);

namespace Bga\Games\PyramidoCannonFodder;

require_once(APP_GAMEMODULE_PATH . "module/table/table.game.php");

class Game extends \Table
{
    protected array $decks = [];

    private static array $CARD_TYPES;

    /**
     * Your global variables labels:
     *
     * Here, you can assign labels to global variables you are using for this game. You can use any number of global
     * variables with IDs between 10 and 99. If your game has options (variants), you also have to associate here a
     * label to the corresponding ID in `gameoptions.inc.php`.
     *
     * NOTE: afterward, you can get/set the global variables with `getGameStateValue`, `setGameStateInitialValue` or
     * `setGameStateValue` functions.
     */
    public function __construct()
    {
        parent::__construct();

        $this->initGameStateLabels([
            "my_first_global_variable" => 10,
            "my_second_global_variable" => 11,
            "AI" => 100,
            "my_second_game_variant" => 101,
        ]);        

        self::$CARD_TYPES = [
            1 => [
                "card_name" => clienttranslate('Troll'), // ...
            ],
            2 => [
                "card_name" => clienttranslate('Goblin'), // ...
            ],
            // ...
        ];

        $this->decks['domino'] = self::getNew('module.common.deck');
        $this->decks['domino']->init('domino');

        $this->decks['marker'] = self::getNew('module.common.deck');
        $this->decks['marker']->init('marker');

        $this->decks['resurfacing'] = self::getNew('module.common.deck');
        $this->decks['resurfacing']->init('resurfacing');
    }

    // NieuwenhovenGames\BGA\Database
    public function query(string $query) : void  {
        self::DbQuery($query);
    }
	
    public function getObject(string $query) : array {
        self::trace("getObject {$query}");
        return self::getObjectFromDB($query);
    }

    public function getObjectList(string $query) : array {
        return self::getObjectListFromDB($query);
    }

    public function getCollection(string $query) : array {
        return self::getCollectionFromDb($query);
    }

    /**
     * The framework demands that each action starts with the prefix "act"
     */
    public function action_domino_chosen_and_placed(string $quarry_index, int $horizontal, int $vertical, int $rotation): void {
        $this->initialise();
        $domino_specification = ['horizontal' => $horizontal, 'vertical' => $vertical, 'rotation' => $rotation, ];

        $this->actions->action_domino_chosen_and_placed($quarry_index, $domino_specification);
    }

    public function action_tile_to_place_marker_chosen(int $horizontal, int $vertical, int $rotation): void {
        $this->initialise();
        $tile_specification = ['horizontal' => $horizontal, 'vertical' => $vertical, 'rotation' => $rotation];

        $this->actions->action_tile_to_place_marker_chosen($tile_specification);
    }

    public function action_next_domino_chosen(string $next_index, string $quarry_index): void {
        $this->initialise();

        $this->actions->action_next_domino_chosen($next_index, $quarry_index);
    }

    public function action_tile_to_place_resurfacing_chosen(int $horizontal, int $vertical, int $rotation, int $colour): void {
        $this->initialise();
        $tile_specification = ['horizontal' => $horizontal, 'vertical' => $vertical, 'rotation' => $rotation, 'colour' => $colour];

        $this->actions->action_tile_to_place_resurfacing_chosen($tile_specification);
    }

    protected function initialise() {
        $this->actions = new UseCases\Actions();

        $this->actions->set_gamestate($this->gamestate);
        $this->actions->set_decks($this->decks);

        $this->actions->set_notifications($this);
        $this->actions->set_database($this);
        $this->actions->set_players($this->loadPlayersBasicInfos());

        // Note: the following statement crashes in setup stage
        $this->actions->set_player_id((int)$this->getActivePlayerId());
    }

    /**
     * Player action, example content.
     *
     * In this scenario, each time a player plays a card, this method will be called. This method is called directly
     * by the action trigger on the front side with `bgaPerformAction`.
     *
     * @throws BgaUserException
     */
    public function actPlayCard(int $card_id): void
    {
        // Retrieve the active player ID.
        $player_id = (int)$this->getActivePlayerId();

        // check input values
        $args = $this->argPlayerTurn();
        $playableCardsIds = $args['playableCardsIds'];
        if (!in_array($card_id, $playableCardsIds)) {
            throw new \BgaUserException('Invalid card choice');
        }

        // Add your game logic to play a card here.
        $card_name = self::$CARD_TYPES[$card_id]['card_name'];

        // Notify all players about the card played.
        $this->notifyAllPlayers("cardPlayed", clienttranslate('${player_name} plays ${card_name}'), [
            "player_id" => $player_id,
            "player_name" => $this->getActivePlayerName(),
            "card_name" => $card_name,
            "card_id" => $card_id,
            "i18n" => ['card_name'],
        ]);

        // at the end of the action, move to the next state
        $this->gamestate->nextState("playCard");
    }

    public function actPass(): void
    {
        // Retrieve the active player ID.
        $player_id = (int)$this->getActivePlayerId();

        // Notify all players about the choice to pass.
        $this->notifyAllPlayers("cardPlayed", clienttranslate('${player_name} passes'), [
            "player_id" => $player_id,
            "player_name" => $this->getActivePlayerName(),
        ]);

        // at the end of the action, move to the next state
        $this->gamestate->nextState("pass");
    }

    /**
     * Game state arguments, example content.
     *
     * This method returns some additional information that is very specific to the `playerTurn` game state.
     *
     * @return array
     * @see ./states.inc.php
     */
    public function argPlayerTurn(): array
    {
        // Get some values from the current game situation from the database.

        return [
            "playableCardsIds" => [1, 2],
        ];
    }

    /**
     * Compute and return the current game progression.
     *
     * The number returned must be an integer between 0 and 100.
     *
     * This method is called each time we are in a game state with the "updateGameProgression" property set to true.
     *
     * @return int
     * @see ./states.inc.php
     */
    public function getGameProgression()
    {
        // TODO: compute and return the game progression

        return 0;
    }

    /**
     * Game state actions
     */
    public function stAfterDominoPlaced(): void {
        // Retrieve the active player ID.
        $player_id = (int)$this->getActivePlayerId();

        $this->actions->stAfterDominoPlaced();
    }

    public function stAutomaticallyPlaceMarker(): void {
        $this->initialise();

        $this->actions->stAutomaticallyPlaceMarker();
    }

    public function stAfterTurnFinished(): void {
        $this->initialise();

        $this->actions->stAfterTurnFinished();
    }

    public function stAfterStageFinished(): void {
        $this->initialise();

        $this->actions->stAfterStageFinished($this->getObjectListFromDB( "SELECT `player_id` `id`, `player_name` `name`, `player_score` `score` FROM `player`" ));
    }

    public function stReturnAllMarkers(): void {
        $this->initialise();

        $this->actions->stReturnAllMarkers();
    }

    public function stCheckResurfacing(): void {
        $this->initialise();

        $this->actions->stCheckResurfacing();
    }

    public function stAfterOptionalResurfacing(): void {
        $this->initialise();

        $this->actions->stAfterOptionalResurfacing();
    }

    public function stMarkerOptionallyOnResurfacing(): void {
        $this->initialise();

        $this->actions->stMarkerOptionallyOnResurfacing();
    }

    public function stAISelectAndPlaceDomino(): void {
        $this->initialise();

        $this->actions->stAISelectAndPlaceDomino();
    }

    public function stAISelectAndPlaceMarker(): void {
        $this->initialise();

        $this->actions->stAISelectAndPlaceMarker();
    }

    public function stAIChooseNextDomino(): void {
        $this->initialise();

        $this->actions->stAIChooseNextDomino();
    }

    /**
     * The action method of state `nextPlayer` is called everytime the current game state is set to `nextPlayer`.
     */
    public function stNextPlayer(): void {
        // Retrieve the active player ID.
        $player_id = (int)$this->getActivePlayerId();

        // Give some extra time to the active player when he completed an action
        $this->giveExtraTime($player_id);
        
        $this->activeNextPlayer();

        $this->initialise();

        $this->actions->stNextPlayer($this->getActivePlayerId());
    }

    /**
     * Migrate database.
     *
     * You don't have to care about this until your game has been published on BGA. Once your game is on BGA, this
     * method is called everytime the system detects a game running with your old database scheme. In this case, if you
     * change your database scheme, you just have to apply the needed changes in order to update the game database and
     * allow the game to continue to run with your new version.
     *
     * @param int $from_version
     * @return void
     */
    public function upgradeTableDb($from_version)
    {
//       if ($from_version <= 1404301345)
//       {
//            // ! important ! Use DBPREFIX_<table_name> for all tables
//
//            $sql = "ALTER TABLE DBPREFIX_xxxxxxx ....";
//            $this->applyDbUpgradeToAllDB( $sql );
//       }
//
//       if ($from_version <= 1405061421)
//       {
//            // ! important ! Use DBPREFIX_<table_name> for all tables
//
//            $sql = "CREATE TABLE DBPREFIX_xxxxxxx ....";
//            $this->applyDbUpgradeToAllDB( $sql );
//       }
    }

    /*
     * Gather all information about current game situation (visible by the current player).
     *
     * The method is called each time the game interface is displayed to a player, i.e.:
     *
     * - when the game starts
     * - when a player refreshes the game page (F5)
     */
    protected function getAllDatas()
    {
        $this->trace('getAllDatas');
        $this->trace(phpversion());

        // WARNING: We must only return information visible by the current player.
        $current_player_id = (int) $this->getCurrentPlayerId();

        return UseCases\GetAllDatas::create($this, $this->decks)->set_players($this->loadPlayersBasicInfos())->set_current_player_id($current_player_id)->get();
    }

    /**
     * Returns the game name.
     *
     * IMPORTANT: Please do not modify.
     */
    protected function getGameName()
    {
        return "pyramidocannonfodder";
    }

    /**
     * This method is called only once, when a new game is launched. In this method, you must setup the game
     *  according to the game rules, so that the game is ready to be played.
     */
    protected function setupNewGame($players, $options = [])
    {
        $this->trace('setupNewGame');
        $this->trace(phpversion());

        NewGame\NewGame::create($this->decks)->set_number_ai_players($this->tableOptions->get(100))->setup($players);

        // Set the colors of the players with HTML color code. The default below is red/green/blue/orange/brown. The
        // number of colors defined here must correspond to the maximum number of players allowed for the gams.
        $gameinfos = $this->getGameinfos();
        $default_colors = $gameinfos['player_colors'];

        foreach ($players as $player_id => $player) {
            // Now you can access both $player_id and $player array
            $query_values[] = vsprintf("('%s', '%s', '%s', '%s', '%s')", [
                $player_id,
                array_shift($default_colors),
                $player["player_canal"],
                addslashes($player["player_name"]),
                addslashes($player["player_avatar"]),
            ]);
        }

        // Create players based on generic information.
        //
        // NOTE: You can add extra field on player table in the database (see dbmodel.sql) and initialize
        // additional fields directly here.
        static::DbQuery(
            sprintf(
                "INSERT INTO player (player_id, player_color, player_canal, player_name, player_avatar) VALUES %s",
                implode(",", $query_values)
            )
        );

        $this->reattributeColorsBasedOnPreferences($players, $gameinfos["player_colors"]);
        $this->reloadPlayersBasicInfos();

        // Init global values with their initial values.

        // Dummy content.
        $this->setGameStateInitialValue("my_first_global_variable", 0);

        // Init game statistics.
        //
        // NOTE: statistics used in this file must be defined in your `stats.inc.php` file.

        // Dummy content.
        // $this->initStat("table", "table_teststat1", 0);
        // $this->initStat("player", "player_teststat1", 0);

        // TODO: Setup the initial game situation here.

        // Activate first player once everything has been initialized and ready.
        $this->activeNextPlayer();
    }

    /**
     * This method is called each time it is the turn of a player who has quit the game (= "zombie" player).
     * You can do whatever you want in order to make sure the turn of this player ends appropriately
     * (ex: pass).
     *
     * Important: your zombie code will be called when the player leaves the game. This action is triggered
     * from the main site and propagated to the gameserver from a server, not from a browser.
     * As a consequence, there is no current player associated to this action. In your zombieTurn function,
     * you must _never_ use `getCurrentPlayerId()` or `getCurrentPlayerName()`, otherwise it will fail with a
     * "Not logged" error message.
     *
     * @param array{ type: string, name: string } $state
     * @param int $active_player
     * @return void
     * @throws feException if the zombie mode is not supported at this game state.
     */
    protected function zombieTurn(array $state, int $active_player): void
    {
        $state_name = $state["name"];

        if ($state["type"] === "activeplayer") {
            switch ($state_name) {
                default:
                {
                    $this->gamestate->nextState("zombiePass");
                    break;
                }
            }

            return;
        }

        // Make sure player is in a non-blocking status for role turn.
        if ($state["type"] === "multipleactiveplayer") {
            $this->gamestate->setPlayerNonMultiactive($active_player, '');
            return;
        }

        throw new \feException("Zombie mode not supported at this game state: \"{$state_name}\".");
    }
}
