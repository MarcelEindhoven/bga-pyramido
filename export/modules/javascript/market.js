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
            this.get_stock_keys('quarry').forEach(key => this.make_stock_selectable(key));
        },
        get_stock_keys(prefix) {
            return Object.keys(this.stocks).filter(key => key.startsWith(prefix));
        },
        make_stock_selectable(key) {
            this.dojo.addClass(key, 'selectable')
        },
    });
});
