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
            this.candidate_markeres = {};
        },
        clone(properties){
            for (var property in properties) {
                this[property] = properties[property];
            }
        },
        set_candidate_positions(candidate_positions) {
            this.candidate_positions = candidate_positions;
        },
        subscribe(callback_object, callback_method) {
            this.callback_object = callback_object;
            this.callback_method = callback_method;
        },
        create_candidate_markeres() {
            Object.values(this.candidate_positions)
            .filter(candidate_position => { if (candidate_position.rotation == this.rotation) return candidate_position;})
                .filter(candidate_position => { if (this.is_toggle_for_position(candidate_position)) return candidate_position;})
                    .forEach(candidate_position => {
                let candidate_marker = this.marker_factory.create_marker_from(this.selected_marker);

                candidate_marker.unique_id = candidate_marker.unique_id + candidate_position.horizontal + candidate_position.vertical;

                candidate_marker.horizontal = candidate_position.horizontal;
                candidate_marker.vertical = candidate_position.vertical;
                candidate_marker.rotation = candidate_position.rotation;

                candidate_marker.stage = 5;

                candidate_marker.subscribe(this, 'placement_selected');

                this.pyramid.add(candidate_marker);
                this.candidate_markeres[candidate_marker.unique_id] = candidate_marker;
            });
        },
        tile_selected(tile) {
            this.callback_object[this.callback_method](tile);
        },
        unsubscribe() {
        },
    });
});
