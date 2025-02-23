define(['dojo/_base/declare'], (declare) => {
    return declare('pyramido.usecase_place_domino', null, {
        /**
         * Dependencies:
         * market
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
        subscribe(callback_object, callback_method) {
            this.callback_object = callback_object;
            this.callback_method = callback_method;
            this.market.subscribe_to_quarry(this, 'quarry_selected');
        },
        quarry_selected(domino) {
            this.callback_object[this.callback_method](domino);
        },
        placement_selected(domino) {
            this.callback_object[this.callback_method](domino);
        },
        unsubscribe() {
            this.market.unsubscribe();
        },
    });
});
