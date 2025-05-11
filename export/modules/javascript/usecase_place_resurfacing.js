define(['dojo/_base/declare'], (declare) => {
    return declare('pyramido.usecase_place_resurfacing', null, {
        /**
         * Dependencies:
         * ui
         * resurfacing_factory
         * pyramid
         */
        /**
         * Use case:
         * u = usecase_choose_resurfacing(dependencies);
         */
        constructor(dependencies) {
            this.clone(dependencies);
            this.candidate_tiles_for_resurfacing = {};
            this.candidate_resurfacinges = {};
            this.rotation = 0;
        },
        clone(properties){
            for (var property in properties) {
                this[property] = properties[property];
            }
        },
        set_candidate_tiles_for_resurfacing(candidate_tiles_for_resurfacing) {
            this.candidate_tiles_for_resurfacing = candidate_tiles_for_resurfacing;
        },
        subscribe(callback_object, callback_method) {
            this.callback_object = callback_object;
            this.callback_method = callback_method;
        },
        rotate() {
            this.rotation = this.rotation + 1;
            if (this.rotation >3) this.rotation = 0;
            if (this.selected_resurfacing) {
                this.destroy_candidate_resurfacinges();
                this.create_candidate_resurfacinges();
                this.ui.paint();
            }
        },
        resurfacing_selected(resurfacing) {
            this.destroy_candidate_resurfacinges();
            this.selected_resurfacing = resurfacing;
            console.log(resurfacing);
            this.create_candidate_resurfacinges();
            this.ui.paint();
        },
        create_candidate_resurfacinges() {
            Object.values(this.candidate_tiles_for_resurfacing)
            .forEach(candidate_position => {
                let specification = Object.assign({}, this.selected_resurfacing)
                specification.id = 100 + candidate_position.horizontal + candidate_position.vertical * 20;

                specification.horizontal = candidate_position.horizontal;
                specification.vertical = candidate_position.vertical;
                specification.rotation = this.rotation;

                specification.stage = 5;

                let candidate_resurfacing = this.resurfacing_factory.create_from(specification);
                candidate_resurfacing.subscribe(this, 'placement_selected');

                this.pyramid.add(candidate_resurfacing);
                this.candidate_resurfacinges[candidate_resurfacing.unique_id] = candidate_resurfacing;
            });
        },
        destroy_candidate_resurfacinges() {
            Object.values(this.candidate_resurfacinges).forEach(candidate_resurfacing => {
                this.pyramid.remove(candidate_resurfacing);
                candidate_resurfacing.destroy_canvas_token();
                delete this.candidate_resurfacinges[candidate_resurfacing.unique_id];
            });
        },
        placement_selected(resurfacing) {
            this.callback_object[this.callback_method](resurfacing);
        },
        unsubscribe() {
            this.destroy_candidate_resurfacinges();
        },
    });
});
