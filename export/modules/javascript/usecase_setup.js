define(['dojo/_base/declare'], (declare) => {
    return declare('cascadia.usecase_setup', null, {
        /**
         * Use case:
         * u = usecase_setup(dependencies);
         * u.setup(gamedatas);
         * 
         * Dependencies:
         * document
         */
        constructor(dependencies) {
            this.clone(dependencies);
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
                                <tr id="next"></tr>
                            </table>
                        </tr>
                        <tr>
                            <div id="quarry">quarry</div>
                        </tr>
                    </table>
                </div>
            `);
            for (let i = 0; i < 4; i++) {
                this.document.getElementById('next').insertAdjacentHTML('beforeend', `
                    <td >
                    <div id = "next-${i}" style="display: inline-block" class=".single_card">${i}</div>
                    </td>
                `);
            }

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