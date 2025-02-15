define(['dojo/_base/declare'], (declare) => {
    return declare('pyramido.usecase_choose_domino', null, {
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
        quarry_selected(stock) {
            this.callback_object[this.callback_method](0 + stock.control_name.substring(7));
        },
        unsubscribe() {
            this.market.unsubscribe();
        },
    });
});
