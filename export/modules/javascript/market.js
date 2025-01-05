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
        subscribe_to_quarry(callback_object, callback_method) {
            this.callback_object = callback_object;
            this.callback_method = callback_method;
            this.get_stock_keys('quarry').forEach(key => this.make_stock_selectable(key));
        },
        /**
         * Terminate subscription after subscriber is notified
         */
        domino_selected(selected_element_id) {
            if (this.callback_object) {
                this.get_stock_keys('quarry').forEach(key => this.make_stock_unselectable(key));
                this.call_subscriber_and_terminate_subscription(selected_element_id);
            }
        },
        /**
         * Private methods
         */
        call_subscriber_and_terminate_subscription(selected_element_id) {
            this.callback_object[this.callback_method](this.stocks[selected_element_id]);
            delete this.callback_object;
            delete this.callback_method;
        },
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
