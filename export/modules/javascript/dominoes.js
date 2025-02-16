define(['dojo/_base/declare'], (declare) => {
    return declare('pyramido.dominoes', null, {
        /**
         * Dependencies:
         * dojo
         * document
         * game
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
        /**
         * specification:
         * domino_id
         * stage
         * horizontal
         * vertical
         * rotation
         */
        create_domino_from(specification) {
            class Domino {
                constructor(dependencies) {
                    this.clone(dependencies);
                }
                create_token(specification) {
                    this.clone(specification);
                    this.calculate_bounding_box();
                }
                calculate_bounding_box() {
                    if (this.rotation == 0) {
                        this.bounding_box = {horizontal_min: this.horizontal, vertical_min: this.vertical, horizontal_max: this.horizontal + 4, vertical_max: this.vertical + 2};
                    }
                    if (this.rotation == 1) {
                        this.bounding_box = {horizontal_min: this.horizontal, vertical_min: this.vertical, horizontal_max: this.horizontal + 2, vertical_max: this.vertical + 4};
                    }
                    if (this.rotation == 2) {
                        this.bounding_box = {horizontal_min: this.horizontal - 2, vertical_min: this.vertical, horizontal_max: this.horizontal + 2, vertical_max: this.vertical + 2};
                    }
                    if (this.rotation == 3) {
                        this.bounding_box = {horizontal_min: this.horizontal, vertical_min: this.vertical - 2, horizontal_max: this.horizontal + 2, vertical_max: this.vertical + 2};
                    }
                }
                clone(properties){
                    for (var property in properties) {
                        this[property] = properties[property];
                    }
                }
                get_bounding_box() {
                    return this.bounding_box;
                }
            }
            result = new Domino({document: this.document, dojo: this.dojo, game: this.game,});
            result.create_token(specification);
            return result;
        },
        /**
         */
    });
});
