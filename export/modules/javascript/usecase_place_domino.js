define(['dojo/_base/declare'], (declare) => {
    return declare('pyramido.usecase_place_domino', null, {
        /**
         * Dependencies:
         * market
         * domino_factory
         */
        /**
         * Use case:
         * u = usecase_choose_domino(dependencies);
         */
        constructor(dependencies) {
            this.clone(dependencies);

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
        quarry_selected(domino) {
            this.callback_object[this.callback_method](domino);
            Object.values(this.candidate_positions).forEach(candidate_position => {
                candidate_domino = this.domino_factory.create_domino_from(domino);
                candidate_domino.id = domino.id + candidate_position.horizontal + candidate_position.vertical;
                candidate_domino.horizontal = candidate_position.horizontal;
                candidate_domino.vertical = candidate_position.vertical;
                candidate_domino.rotation = candidate_position.rotation;
                this.pyramid.add(candidate_domino);
            });
        },
        placement_selected(domino) {
            this.callback_object[this.callback_method](domino);
        },
        unsubscribe() {
            this.market.unsubscribe();
        },
    });
});
