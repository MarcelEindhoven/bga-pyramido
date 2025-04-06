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

            this.token_containers = {};
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
            this.setup_markers(gamedatas.markers);
        },
        setup_players(players) {
            Object.values(players).forEach(player => {
                this.document.getElementById('game_play_area').insertAdjacentHTML('beforeend', `
                    <div id="player-table-${player.id}">
                        <div style = "display: inline-block;"><strong>${player.name}</strong></div>
                        <div id="marker-${player.id}" style = "display: inline-block; width: 130px ; height: 200px"></div>
                        <div id="resurface-${player.id}" style = "display: inline-block; width: 170px ; height: 260px"></div>
                        <div id="pyramid-${player.id}" style = "display: inline-block; position: relative; width: 80px ; height: 80px"></div>
                    </div>
                `);

                this.create_canvas('resurface' + '-' + player.id);
                this.create_canvas('pyramid' + '-' + player.id);

                element_id = 'marker' + '-' + player.id;
                this.create_canvas(element_id);
                this.token_containers[element_id].set_pixels_per_tile(60);
                this.token_containers[element_id].set_margin_between_tiles(10);
            });
        },
        setup_markers(markers_per_player) {
            Object.keys(markers_per_player).forEach(player_id => {
                Object.values(markers_per_player[player_id]).forEach(marker_specification => {
                    marker = this.marker_factory.create_from(marker_specification);
                    this.paintables[marker.stage][marker.unique_id] = marker;
                    this.token_containers[(marker.stage > 0 ? 'pyramid-' : 'marker-') + player_id].add(marker);
                });
            });
        },
        setup_pyramid_tiles(tiles_per_player) {
            Object.keys(tiles_per_player).forEach(player_id => {
                Object.values(tiles_per_player[player_id]).forEach(tile_specification => {
                    tile = this.tile_factory.create_tile_from(tile_specification);
                    this.paintables[tile.stage][tile.unique_id] = tile;
                    this.token_containers['pyramid-' + player_id].add(tile);
                });
            });
        },
        create_canvas(element_id) {
            dependencies = {dojo: this.dojo, game: this.game, element_id: element_id};
            this.token_containers[element_id] = new this.canvas_class(dependencies);
        },
    });
});
