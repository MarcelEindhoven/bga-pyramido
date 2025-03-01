/**
 *------
 * BGA framework: Gregory Isabelli & Emmanuel Colin & BoardGameArena
 * PyramidoCannonFodder implementation : © Marcel van Nieuwenhoven marcel.eindhoven@hotmail.com
 *
 * This code has been produced on the BGA studio platform for use on http://boardgamearena.com.
 * See http://en.boardgamearena.com/#!doc/Studio for more information.
 * -----
 *
 * pyramidocannonfodder.js
 *
 * PyramidoCannonFodder user interface script
 * 
 * In this file, you are describing the logic of your user interface, in Javascript language.
 *
 */

define([
    "dojo","dojo/_base/declare",
    g_gamethemeurl + 'modules/javascript/market.js',
    g_gamethemeurl + 'modules/javascript/canvas.js',
    g_gamethemeurl + 'modules/javascript/dominoes.js',
    g_gamethemeurl + 'modules/javascript/tiles.js',
    g_gamethemeurl + 'modules/javascript/usecase_setup.js',
    g_gamethemeurl + 'modules/javascript/usecase_choose_domino.js',
    g_gamethemeurl + 'modules/javascript/usecase_place_domino.js',
    g_gamethemeurl + 'modules/javascript/usecase_choose_next_domino.js',
    "ebg/core/gamegui",
    "ebg/counter",
    "ebg/stock",
],
function (dojo, declare, market, canvas, dominoes, tiles, usecase_setup, usecase_choose_domino, usecase_place_domino, usecase_choose_next_domino) {
    return declare("bgagame.pyramidocannonfodder", ebg.core.gamegui, {
        constructor: function(){
            console.log('pyramidocannonfodder constructor');
              
            // Here, you can init the global variables of your user interface
            // Example:
            // this.myGlobalValue = 0;

        },
        
        /*
            setup:
            
            This method must set up the game user interface according to current game situation specified
            in parameters.
            
            The method is called each time the game interface is displayed to a player, ie:
            _ when the game starts
            _ when a player refreshes the game page (F5)
            
            "gamedatas" argument contains all datas retrieved by your "getAllDatas" PHP method.
        */
        
        setup: function( gamedatas )
        {
            console.log( "Starting game setup" );
            console.log(gamedatas);

            this.tile_factory = new tiles({
                game: this,
                document: document,
                dojo: dojo, 
            });
            this.domino_factory = new dominoes({
                game: this,
                document: document,
                dojo: dojo, 
            });
            this.market = new market({dojo: dojo, document: document, game: this, stock_class: ebg.stock, gamethemeurl: g_gamethemeurl, domino_factory: this.domino_factory,});
            this.usecase_setup = new usecase_setup({
                game: this,
                document: document,
                market: this.market,
                dojo: dojo, 
                tile_factory: this.tile_factory,
                canvas_class: canvas,
                stock_class: ebg.stock, gamethemeurl: g_gamethemeurl, domino_factory: this.domino_factory,
            });
            this.usecase_setup.setup(gamedatas);
            this.tile_containers = this.usecase_setup.tile_containers;

            // Setup game notifications to handle (see "setupNotifications" method below)
            this.setupNotifications();

            this.paint();
            //this.experiment(gamedatas);
            this.paint();

            console.log( "Ending game setup" );
        },
        paint: function() {
            console.log("paint" );
            Object.values(this.tile_containers).forEach(tile_container => {
                    tile_container.paint();
            });
            Object.values(this.tile_containers).forEach(tile_container => {
                tile_container.paint();
        });
    },
        experiment: function( gamedatas ) {
            this.placeOnObjectPos('tile-18', 'quarry-2', 0, 0);
            this.slideToObjectPos('tile-18', 'pyramid-2371153', -20, 0).play();
            this.slideToObjectPos('tile-19', 'pyramid-2371153', 20, 0).play();
        },
        /**
         * Generic functions
         */
        get_element: function(html_id) {return $(html_id);},

        ///////////////////////////////////////////////////
        //// Game & client states
        
        // onEnteringState: this method is called each time we are entering into a new game state.
        //                  You can use this method to perform some user interface changes at this moment.
        //
        onEnteringState: function( stateName, args )
        {
            console.log( 'Entering state: '+stateName, args );
            if( this.isCurrentPlayerActive() ) {
                switch( stateName )
                {
                case 'selectFirstDomino':
                case 'selectAndPlaceQuarry':
                    this.usecase_place_domino = new usecase_place_domino({ui: this, market: this.market, pyramid: this.tile_containers['pyramid-' + this.player_id], domino_factory: this.domino_factory});
                    this.usecase_place_domino.set_candidate_positions(this.gamedatas.candidate_positions);
                    this.usecase_place_domino.subscribe(this, 'domino_placed');

                    this.usecase_choose_domino = new usecase_choose_domino({market: this.market});
                    this.usecase_choose_domino.subscribe(this.usecase_place_domino, 'quarry_selected');
                    break;
                case 'selectNextDomino':
                    this.usecase_choose_next_domino = new usecase_choose_next_domino({market: this.market});
                    this.usecase_choose_next_domino.subscribe(this, 'next_domino_chosen');
                    break;
                
                case 'dummy':
                    break;
                }
            }
        },
        rotate() {
            this.usecase_place_domino.rotate();
        },
        first_domino_chosen(domino) {
            console.log( "first_domino_chosen" );
            console.log(domino);
            this.call('first_domino_chosen', {quarry_index: domino.element_id});
            this.usecase_choose_domino.unsubscribe();
        },
        domino_placed(domino) {
            console.log(domino);
            this.call('domino_chosen_and_placed', {
                quarry_index: this.usecase_place_domino.selected_domino.element_id,
                horizontal: domino.horizontal,
                vertical: domino.vertical,
                rotation: domino.rotation,
            });
            this.usecase_choose_domino.unsubscribe();
            this.usecase_place_domino.unsubscribe();
        },
        next_domino_chosen(domino) {
            console.log( "next_domino_chosen" );
            console.log( domino);
            console.log(this.usecase_choose_next_domino.quarry_missing_element);
            this.call('next_domino_chosen', {next_index: domino.element_id, quarry_index: this.usecase_choose_next_domino.quarry_missing_element});
            this.usecase_choose_next_domino.unsubscribe();
        },
        call: function(action, args, handler) {
            console.log(action);
            console.log(args);
            this.bgaPerformAction('action_' + action, args);
        },

        // onLeavingState: this method is called each time we are leaving a game state.
        //                 You can use this method to perform some user interface changes at this moment.
        //
        onLeavingState: function( stateName )
        {
            console.log( 'Leaving state: '+stateName );
            
            switch( stateName )
            {
            
            /* Example:
            
            case 'myGameState':
            
                // Hide the HTML block we are displaying only during this game state
                dojo.style( 'my_html_block_id', 'display', 'none' );
                
                break;
           */
           
           
            case 'dummy':
                break;
            }               
        }, 

        // onUpdateActionButtons: in this method you can manage "action buttons" that are displayed in the
        //                        action status bar (ie: the HTML links in the status bar).
        //        
        onUpdateActionButtons: function( stateName, args )
        {
            console.log( 'onUpdateActionButtons: '+stateName, args );
                      
            if( this.isCurrentPlayerActive() )
            {            
                switch( stateName )
                {
                    case 'selectFirstDomino':
                    case 'selectAndPlaceQuarry':
                            this.addActionButton('Rotate', _('Rotate'), () => this.rotate(), null, null, 'gray'); 
                        break;
                    case 'playerTurn':
                    const playableCardsIds = args.playableCardsIds; // returned by the argPlayerTurn

                    // Add test action buttons in the action status bar, simulating a card click:
                    playableCardsIds.forEach(
                        cardId => this.addActionButton(`actPlayCard${cardId}-btn`, _('Play card with id ${card_id}').replace('${card_id}', cardId), () => this.onCardClick(cardId))
                    ); 

                    this.addActionButton('actPass-btn', _('Pass'), () => this.bgaPerformAction("actPass"), null, null, 'gray'); 
                    break;
                }
            }
        },        

        ///////////////////////////////////////////////////
        //// Utility methods
        
        /*
        
            Here, you can defines some utility methods that you can use everywhere in your javascript
            script.
        
        */


        ///////////////////////////////////////////////////
        //// Player's action
        
        /*
        
            Here, you are defining methods to handle player's action (ex: results of mouse click on 
            game objects).
            
            Most of the time, these methods:
            _ check the action is possible at this game state.
            _ make a call to the game server
        
        */
        
        // Example:
        
        onCardClick: function( card_id )
        {
            console.log( 'onCardClick', card_id );

            this.bgaPerformAction("actPlayCard", { 
                card_id,
            }).then(() =>  {                
                // What to do after the server call if it succeeded
                // (most of the time, nothing, as the game will react to notifs / change of state instead)
            });        
        },    

        
        ///////////////////////////////////////////////////
        //// Reaction to cometD notifications

        /*
            setupNotifications:
            
            In this method, you associate each of your game notifications with your local method to handle it.
            
            Note: game notification names correspond to "notifyAllPlayers" and "notifyPlayer" calls in
                  your pyramidocannonfodder.game.php file.
        
        */
        setupNotifications: function()
        {
            console.log( 'notifications subscriptions setup' );

            dojo.subscribe( 'domino_placed', this, "notify_domino_placed" );
            this.notifqueue.setSynchronous( 'domino_placed', 300 );

            dojo.subscribe( 'next_domino_chosen', this, "notify_next_domino_chosen" );
            this.notifqueue.setSynchronous( 'next_domino_chosen', 300 );
        },
        
        notify_next_domino_chosen: function( notif )
        {
            console.log( 'notify_next_domino_chosen' );
            console.log( notif );

            next_index = notif.args.next_index;
            quarry_index = notif.args.quarry_index;

            this.market.move(next_index, quarry_index);
            this.market.fill(next_index, notif.args.next_domino);
        },
        
        notify_domino_placed: function( notif )
        {
            console.log( 'notify_domino_placed' );
            console.log( notif );
            this.market.delete(notif.args.quarry_index);

            Object.values(notif.args.tiles).forEach(tile_specification => {
                let tile = this.tile_factory.create_tile_from(tile_specification);
                this.tile_containers['pyramid-' + notif.args.player_id].add(tile);
            });
            this.paint();
            this.paint();
            // Note: notif.args contains the arguments specified during you "notifyAllPlayers" / "notifyPlayer" PHP call
            
            // TODO: play the card in the user interface.
        },    
        // TODO: from this point and below, you can write your game notifications handling methods
        
        /*
        Example:
        
        
        */
   });             
});
