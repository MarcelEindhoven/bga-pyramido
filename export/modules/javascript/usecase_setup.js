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
            this.setup_resurfacings(gamedatas.resurfacings);
        },
        setup_players(players) {
            Object.values(players).forEach(player => {
                this.document.getElementById('game_play_area').insertAdjacentHTML('beforeend', `
                    <div id="player-table-${player.id}" style = "display: inline-block;">
                        <div style = "display: inline-block;"><strong>${player.name}</strong></div>
                        <div id="marker-${player.id}" style = "display: inline-block; width: 90px ; height: 140px"></div>
                        <div id="resurfacing-${player.id}" style = "display: inline-block; width: 160px ; height: 240px"></div>
                        <div id="pyramid-${player.id}" style = "display: inline-block; position: relative; width: 80px ; height: 80px"></div>
                    </div>
                `);

                element_id = 'resurfacing' + '-' + player.id;
                this.create_canvas(element_id);
                this.token_containers[element_id].set_pixels_per_tile(60);

                this.create_canvas('pyramid' + '-' + player.id);

                element_id = 'marker' + '-' + player.id;
                this.create_canvas(element_id);
                this.token_containers[element_id].set_pixels_per_tile(40);
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
        setup_resurfacings(resurfacings_per_player) {
            Object.keys(resurfacings_per_player).forEach(player_id => {
                Object.values(resurfacings_per_player[player_id]).forEach(resurfacing_specification => {
                    resurfacing_specification.pixels_per_tile = 60;
                    resurfacing = this.resurfacing_factory.create_from(resurfacing_specification);
                    this.paintables[resurfacing.stage][resurfacing.unique_id] = resurfacing;
                    this.token_containers[(resurfacing.stage > 0 ? 'pyramid-' : 'resurfacing-') + player_id].add(resurfacing);
                });
            });
        },
        setup_pyramid_tiles(tiles_per_player) {
            Object.keys(tiles_per_player).forEach(player_id => {
                const tiles = Object.values(tiles_per_player[player_id]);
                for (stage = 1; stage <= 4; stage++) {
                    Object.values(tiles.filter(tile_specification => {return stage == tile_specification.stage;}))
                    .forEach(tile_specification => {
                        tile = ('resurfacing' == tile_specification.class ? this.resurfacing_factory : this.tile_factory).create_from(tile_specification);
                        this.paintables[tile.stage][tile.unique_id] = tile;
                        this.token_containers['pyramid-' + player_id].add(tile);
                    });
                    }
            });
        },
        create_canvas(element_id) {
            dependencies = {dojo: this.dojo, game: this.game, element_id: element_id};
            this.token_containers[element_id] = new this.canvas_class(dependencies);
            this.token_containers[element_id].set_margin_between_tiles(4);
        },
    });
});
