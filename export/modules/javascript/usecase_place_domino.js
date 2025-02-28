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
            this.rotation = 0;
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
        },
        quarry_selected(domino) {
            console.log (this.candidate_positions);
            Object.values(this.candidate_positions)
            .filter(candidate_position => { if (candidate_position.rotation == this.rotation) return candidate_position;})
                .forEach(candidate_position => {
                candidate_domino = this.domino_factory.create_domino_from(domino);

                candidate_domino.unique_id = domino.unique_id + candidate_position.horizontal + candidate_position.vertical;
                candidate_domino.horizontal = candidate_position.horizontal;
                candidate_domino.vertical = candidate_position.vertical;
                candidate_domino.rotation = candidate_position.rotation;

                candidate_domino.create_canvas_token();
                candidate_domino.subscribe(this, 'placement_selected');

                this.pyramid.add(candidate_domino);
            });
            this.ui.paint();
        },
        placement_selected(domino) {
            this.callback_object[this.callback_method](domino);
        },
        unsubscribe() {
            this.market.unsubscribe();
        },
    });
});
