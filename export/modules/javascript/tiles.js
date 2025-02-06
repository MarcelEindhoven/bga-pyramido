define(['dojo/_base/declare'], (declare) => {
    return declare('pyramido.tiles', null, {
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
         * tile_id
         * stage
         * horizontal
         * vertical
         */
        create_tile_from(specification) {
            class Tile {
                constructor(dependencies) {
                    this.clone(dependencies);
                }
                create_token(specification) {
                    this.clone(specification);
                    this.unique_id = "tile-" + this.tile_id;
                    this.document.getElementById('game_play_area').insertAdjacentHTML('beforeend', `
                        <div id="${this.unique_id}">
                    `);
                    this.dojo.addClass(this.unique_id,'tile');
                    const id_horizontal = this.tile_id % 20;
                    const id_vertical = (this.tile_id-id_horizontal) / 20;
                    this.dojo.style(this.unique_id, 'backgroundPosition', '-' + 80 * id_horizontal + 'px -' + 80 * id_vertical + 'px');
                }
                clone(properties){
                    for (var property in properties) {
                        this[property] = properties[property];
                    }
                }
                move_to(element_id, x, y) {
                    this.element_id = element_id;
                    this.x = x;
                    this.y = y;
                }
                /**
                 * Precondition: move_to has been called
                 */
                paint() {
                    this.game.placeOnObjectPos(this.unique_id, this.element_id, this.x, this.y);
                }
            }
            result = new Tile({document: this.document, dojo: this.dojo, game: this.game,});
            result.create_token(specification);
            return result;
        },
        /**
         */
    });
});
