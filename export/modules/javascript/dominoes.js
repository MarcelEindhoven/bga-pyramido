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
         */
        create_domino_from(specification) {
            class Domino {
                constructor(dependencies) {
                    this.clone(dependencies);
                }
                create_token(specification) {
                    this.clone(specification);
                }
                clone(properties){
                    for (var property in properties) {
                        this[property] = properties[property];
                    }
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
