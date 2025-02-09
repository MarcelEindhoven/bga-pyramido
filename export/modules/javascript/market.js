define(['dojo/_base/declare'], (declare) => {
    return declare('pyramido.market', null, {
        /**
         * Dependencies:
         * dojo
         * stocks
         */
        /**
         * Use case creation:
         * Permanently subscribes to each stock but ignores selections until client subscribes
         */
        constructor(dependencies) {
            this.clone(dependencies);
            Object.values(this.stocks).forEach(stock => {
                this.dojo.connect(stock, 'onChangeSelection', this, 'domino_selected');
            });
        },
        clone(properties){
            for (var property in properties) {
                this[property] = properties[property];
            }
        },
        /**
         * Support single subscription
         */
        subscribe_to_next(callback_object, callback_method) {
            this.subscribe(callback_object, callback_method, 'next');
        },
        subscribe_to_quarry(callback_object, callback_method) {
            this.subscribe(callback_object, callback_method, 'quarry');
        },
        subscribe(callback_object, callback_method, category) {
            this.callback_object = callback_object;
            this.callback_method = callback_method;
            this.category_subscription = category;
            this.get_stock_keys(category).forEach(key => this.make_stock_selectable(key));
        },
        /**
         * Notify subscriber when domino selected
         */
        domino_selected(selected_element_id) {
            if (this.callback_object) {
                this.callback_object[this.callback_method](this.stocks[selected_element_id]);
            }
        },
        unsubscribe() {
            this.get_stock_keys(this.category_subscription).forEach(key => this.make_stock_unselectable(key));
            delete this.callback_object;
            delete this.callback_method;
            delete this.category_subscription;
        },
        /**
         * Private methods
         */
        get_stock_keys(prefix) {
            return Object.keys(this.stocks).filter(key => key.startsWith(prefix));
        },
        make_stock_selectable(key) {
            this.dojo.addClass(key, 'selectable')
        },
        make_stock_unselectable(key) {
            this.dojo.removeClass(key, 'selectable')
        },
    });
});
