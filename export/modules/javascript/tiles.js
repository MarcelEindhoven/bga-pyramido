define(['dojo/_base/declare'], (declare) => {
    return declare('pyramido.tiles', null, {
        /**
         * Dependencies:
         * dojo
         */
        /**
         * Use case creation:
         */
        constructor(dependencies) {
            this.clone(dependencies);
        },
        clone(properties){
            for (var property in properties) {
                this[property] = properties[property];
            }
        },
        create_tile_from(tile_specification) {
            class tile {
                constructor(dependencies) {
                    this.clone(dependencies);
                }
                clone(properties){
                    for (var property in properties) {
                        this[property] = properties[property];
                    }
                }        
            }
            return new tile(tile_specification);
        },
        /**
         */
    });
});
