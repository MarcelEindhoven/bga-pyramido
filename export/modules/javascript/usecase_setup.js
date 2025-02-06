define(['dojo/_base/declare'], (declare) => {
    return declare('pyramido.usecase_setup', null, {
        /**
         * Use case:
         * u = usecase_setup(dependencies);
         * u.setup(gamedatas);
         * 
         * Dependencies:
         * document
         * game
         * stock_class
         */
        constructor(dependencies) {
            this.clone(dependencies);

            this.cardwidth = 160;
            this.cardheight = 80;
            this.image_items_per_row = 10;

            this.stocks = {};
        },
        clone(properties){
            for (var property in properties) {
                this[property] = properties[property];
            }
        },
        setup(gamedatas) {
            this.setup_market_structure();
            this.setup_original(gamedatas);
            this.display_tiles(gamedatas);
        },
        setup_market_structure() {
            this.document.getElementById('game_play_area').insertAdjacentHTML('beforeend', `
                <div id="market">
                    <table style="justify-content:center;">
                        <tr>
                            <table>
                                <tr><td>.......................................</td><td>.......................................</td><td>.......................................</td><td>.......................................</td></tr>
                                <tr id="next"></tr>
                            </table>
                        </tr>
                        <tr style="background-color:powderblue;">
                            <table>
                                <tr><td>..........................................</td><td>.......................................</td><td>.............................................</td></tr>
                                <tr id="quarry"></tr>
                            </table>
                        </tr>
                    </table>
                </div>
            `);
            for (let i = 1; i <= 4; i++) {
                this.setup_market_element('next', i);
                this.stocks['next-' + i].addToStockWithId(i, i);
            }
            for (let i = 1; i <= 3; i++) {
                this.setup_market_element('quarry', i);
                this.stocks['quarry-' + i].addToStockWithId(10+i, 10+i);
            }

        },
        setup_market_element(category, index) {
            let element_id = category + '-'+ index;
            this.document.getElementById(category).insertAdjacentHTML('beforeend', `
                <td class=".single_card">
                <div id = "${category}-${index}" style="display: inline-block" ></div>
                </td>
            `);
            hand = new this.stock_class();
            hand.create(this.game, this.game.get_element(element_id), this.cardwidth, this.cardheight);
            hand.image_items_per_row = this.image_items_per_row;
            for (let row = 0; row < 90/hand.image_items_per_row; row++) {
                for (let i = 0; i < hand.image_items_per_row; i++) {
                    let card_type_id = this.get_card_type_id(row, i);
                    hand.addItemType(card_type_id, card_type_id, this.gamethemeurl+'img/' + 'dominoesx80.png', card_type_id);
                }
            }
            this.stocks[element_id] = hand;
        },
        get_card_type_id(row, i) {
            return row * 90/this.image_items_per_row + i;
        },
        setup_original(gamedatas) {
            // Example to add a div on the game area
            this.document.getElementById('game_play_area').insertAdjacentHTML('beforeend', `
                <div id="player-tables"></div>
            `);
            
            // Setting up player boards
            Object.values(gamedatas.players).forEach(player => {
                // example of setting up players boards
                /*
                this.getPlayerPanelElement(player.id).insertAdjacentHTML('beforeend', `
                    <div id="player-counter-${player.id}">A player counter</div>
                `);
                */

                // example of adding a div for each player
                this.document.getElementById('player-tables').insertAdjacentHTML('beforeend', `
                    <div id="player-table-${player.id}">
                        <strong>${player.name}</strong>
                        <div id="pyramid-${player.id}" style = "display: inline-block; width: 80px ; height: 80px"></div>
                    </div>
                `);
                console.log(`pyramid-${player.id}`);
                Object.values(gamedatas.tiles[player.id]).forEach(tiles_per_stage => {
                    console.log(tiles_per_stage);
                    Object.values(tiles_per_stage).forEach(tile => {
                        tile.player_id = player.id;console.log(player);console.log(this);console.log(tile);this.display_tile(tile);
                        
                    });
                });
            });
    },
        display_tiles(gamedatas) {
            Object.values(gamedatas.players).forEach(player => {
                Object.values(gamedatas.tiles[player.id]).forEach(tiles_per_stage => {
                    console.log(tiles_per_stage);
                    Object.values(tiles_per_stage).forEach(tile => {
                        this.game.placeOnObjectPos(unique_id, "pyramid-" + tile.player_id, 0, 0);
                    });
                });
            });
        },
        display_tile(tile) {
            unique_id = "tile-" + tile.tile_id;
            this.document.getElementById('game_play_area').insertAdjacentHTML('beforeend', `
                <div id="${unique_id}">
            `);
            this.dojo.addClass(unique_id,'tile');
            id_horizontal = tile.tile_id % 20;
            id_vertical = (tile.tile_id-id_horizontal) / 20;
            this.dojo.style(unique_id, 'backgroundPosition', '-' + 80 * id_horizontal + 'px -' + 80 * id_vertical + 'px');
            console.log ("pyramid-" + tile.player_id);
            //this.game.slideToObjectPos(unique_id, "pyramid-" + tile.player_id, 0, 0).play();

        },
    });
});
