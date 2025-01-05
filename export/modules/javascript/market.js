define(['dojo/_base/declare'], (declare) => {
    return declare('pyramido.market', null, {
        /**
         * Use case:
         * 
         * Dependencies:
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
        subscribe_to_quarry(callback_object, callback_method) {
            this.callback_object = callback_object;
            this.callback_method = callback_method;
            this.get_stock_keys('quarry').forEach(key => this.make_stock_selectable(key));
        },
        domino_selected(selected_element_id) {
            if (this.callback_object)
                this.callback_object[this.callback_method](this.stocks[selected_element_id]);
        },
        get_stock_keys(prefix) {
            return Object.keys(this.stocks).filter(key => key.startsWith(prefix));
        },
        make_stock_selectable(key) {
            this.dojo.addClass(key, 'selectable')
        },
    });
});
