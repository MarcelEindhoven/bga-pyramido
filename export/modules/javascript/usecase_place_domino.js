define(['dojo/_base/declare'], (declare) => {
    return declare('pyramido.usecase_place_domino', null, {
        /**
         * Dependencies:
         * market
         * domino_factory
         * pyramid
         */
        /**
         * Use case:
         * u = usecase_choose_domino(dependencies);
         */
        constructor(dependencies) {
            this.clone(dependencies);
            this.candidate_positions = {};
            this.candidate_dominoes = {};
            this.rotation = 0;
            this.is_toggled = false;
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
            this.market.subscribe_to_quarry(this, 'quarry_selected');
        },
        rotate() {
            this.rotation = this.rotation + 1;
            if (this.rotation >3) this.rotation = 0;
            if (this.selected_domino) {
                this.destroy_candidate_dominoes();
                this.create_candidate_dominoes();
                this.ui.paint();
            }
        },
        toggle_positions() {
            this.destroy_candidate_dominoes();
            this.is_toggled = !this.is_toggled;
            this.create_candidate_dominoes();
            this.ui.paint();
        },
        quarry_selected(domino) {
            this.destroy_candidate_dominoes();
            this.selected_domino = domino;
            this.create_candidate_dominoes();
            this.ui.paint();
        },
        is_toggle_for_value(value) {
            if (this.is_toggled) return value % 4 < 2;
            return value % 4>= 2;
        },
        is_toggle_for_position(candidate_position) {
            if (candidate_position.rotation % 2 == 1)
                return this.is_toggle_for_value(candidate_position.vertical);
            return this.is_toggle_for_value(candidate_position.horizontal);
        },
        create_candidate_dominoes() {
            Object.values(this.candidate_positions)
            .filter(candidate_position => { if (candidate_position.rotation == this.rotation) return candidate_position;})
                .filter(candidate_position => { if (this.is_toggle_for_position(candidate_position)) return candidate_position;})
                    .forEach(candidate_position => {
                candidate_domino = this.domino_factory.create_domino_from(this.selected_domino);

                candidate_domino.unique_id = candidate_domino.unique_id + candidate_position.horizontal + candidate_position.vertical;

                candidate_domino.horizontal = candidate_position.horizontal;
                candidate_domino.vertical = candidate_position.vertical;
                candidate_domino.rotation = candidate_position.rotation;

                candidate_domino.stage = 5;

                candidate_domino.subscribe(this, 'placement_selected');

                this.pyramid.add(candidate_domino);
                this.candidate_dominoes[candidate_domino.unique_id] = candidate_domino;
            });
        },
        destroy_candidate_dominoes() {
            Object.values(this.candidate_dominoes).forEach(candidate_domino => {
                this.pyramid.remove(candidate_domino);
                candidate_domino.destroy_canvas_token();
                delete this.candidate_dominoes[candidate_domino.unique_id];
            });
        },
        placement_selected(domino) {
            this.callback_object[this.callback_method](domino);
        },
        unsubscribe() {
            this.destroy_candidate_dominoes();
            this.market.unsubscribe();
        },
    });
});
