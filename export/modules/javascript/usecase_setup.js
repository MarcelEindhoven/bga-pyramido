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
         */

        constructor(dependencies) {
            this.clone(dependencies);
            console.log(dependencies);

            this.tile_containers = {};
            this.paintables = {0: {}, 1: {}, 2: {}, 3: {}, 4: {}, };
        },
        clone(properties){
            for (var property in properties) {
                this[property] = properties[property];
            }
        },
        setup(gamedatas) {
            setup_dependencies = {dojo: this.dojo, document: this.document, stock_class: this.stock_class, game: this.game, domino_factory: this.domino_factory, };
            this.market.setup(setup_dependencies, gamedatas);
            this.setup_players(gamedatas.players);
            this.setup_pyramid_tiles(gamedatas.tiles);
        },
        setup_players(players) {
            Object.values(players).forEach(player => {
                this.document.getElementById('game_play_area').insertAdjacentHTML('beforeend', `
                    <div id="player-table-${player.id}">
                        <div style = "display: inline-block;"><strong>${player.name}</strong></div>
                        <div id="replacement-${player.id}" style = "display: inline-block; width: 80px ; height: 80px"></div>
                        <div id="pyramid-${player.id}" style = "display: inline-block; position: relative; width: 80px ; height: 80px"></div>
                    </div>
                `);
                element_id = 'pyramid-' + player.id;
                dependencies = {dojo: this.dojo, game: this.game, element_id: element_id};
                this.tile_containers[element_id] = new this.canvas_class(dependencies);
            });
        },
        setup_pyramid_tiles(tiles_per_player) {
            Object.keys(tiles_per_player).forEach(player_id => {
                Object.values(tiles_per_player[player_id]).forEach(tile_specification => {
                    tile = this.tile_factory.create_tile_from(tile_specification);
                    this.paintables[tile.stage][tile.tile_id] = tile;
                    this.tile_containers['pyramid-' + player_id].add(tile);
                });
            });
        },
    });
});
