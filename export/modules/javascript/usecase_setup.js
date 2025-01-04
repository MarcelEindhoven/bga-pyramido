define(['dojo/_base/declare'], (declare) => {
    return declare('cascadia.usecase_setup', null, {
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

            this.cardwidth = 100;
            this.cardheight = 50;
            this.image_items_per_row = 15;

            this.stocks =[];
        },
        clone(properties){
            for (var property in properties) {
                this[property] = properties[property];
            }
        },
        setup(gamedatas) {
            this.setup_market_structure();
            this.setup_original(gamedatas);
        },
        setup_market_structure() {
            this.document.getElementById('game_play_area').insertAdjacentHTML('beforeend', `
                <div id="market">
                    <table>
                        <tr>
                            <table>
                                <tr><td>.............................</td><td>.............................</td><td>.............................</td><td>.............................</td></tr>
                                <tr id="next"></tr>
                            </table>
                        </tr>
                        <tr>
                            <table>
                                <tr><td>..........................................</td><td>.............................</td><td>.............................................</td></tr>
                                <tr id="quarry"></tr>
                            </table>
                        </tr>
                    </table>
                </div>
            `);
            for (let i = 0; i < 4; i++) {
                this.setup_market_element('next', i);
                this.stocks['next-' + i].addToStockWithId(i, i);
            }
            for (let i = 0; i < 3; i++) {
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
                    hand.addItemType(card_type_id, card_type_id, this.gamethemeurl+'img/' + 'tiles.png', card_type_id);
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
                        <div>Player zone content goes here</div>
                    </div>
                `);
            });
        },
    });
});
