define(['dojo/_base/declare'], (declare) => {
    return declare('pyramido.usecase_place_marker', null, {
        /**
         * Dependencies:
         * market
         * marker_factory
         * pyramid
         */
        /**
         * Use case:
         * u = usecase_choose_marker(dependencies);
         */
        constructor(dependencies) {
            this.clone(dependencies);
            this.candidate_tiles = {};
        },
        clone(properties){
            for (var property in properties) {
                this[property] = properties[property];
            }
        },
        set_candidate_tile_specifications(candidate_tile_specifications) {
            Object.values(candidate_tile_specifications).forEach(candidate_tile_specification => {
                console.log(candidate_tile_specification);
                this.candidate_tiles[candidate_tile_specification.unique_id] = this.pyramid.get(candidate_tile_specification.unique_id);
                console.log(this.candidate_tiles[candidate_tile_specification.unique_id]);
            });
        },
        subscribe(callback_object, callback_method) {
            this.callback_object = callback_object;
            this.callback_method = callback_method;
            Object.values(this.candidate_tiles).forEach(candidate_tile => {
                candidate_tile.subscribe(callback_object, callback_method);
            });
        },
        tile_selected(tile) {
            this.callback_object[this.callback_method](tile);
        },
        unsubscribe() {
            Object.values(this.candidate_tiles).forEach(candidate_tile => {
                candidate_tile.unsubscribe(this.callback_object, this.callback_method);
            });
        },
    });
});
