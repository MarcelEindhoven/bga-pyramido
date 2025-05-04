define(['dojo/_base/declare'], (declare) => {
    return declare('pyramido.usecase_choose_resurfacing', null, {
        /**
         * Dependencies:
         * container
         */
        /**
         * Use case:
         * u = usecase_choose_resurfacing(dependencies);
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
            Object.values(this.container.paintables).forEach(paintable => {
                paintable.subscribe(this.callback_object, this.callback_method);
            });
        },
        unsubscribe() {
            Object.values(this.container.paintables).forEach(paintable => {
                paintable.unsubscribe(this.callback_object, this.callback_method);
            });
        },
    });
});
