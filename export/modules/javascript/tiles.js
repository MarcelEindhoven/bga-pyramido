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
                TILES_PER_ROW = 20;
                PIXELS_PER_TILE = 80;
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
                    const id_horizontal = this.tile_id % this.TILES_PER_ROW;
                    const id_vertical = (this.tile_id-id_horizontal) / this.TILES_PER_ROW;
                    this.dojo.style(this.unique_id, 'backgroundPosition', '-' + this.PIXELS_PER_TILE * id_horizontal + 'px -' + this.PIXELS_PER_TILE * id_vertical + 'px');
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
                 * During setup, must be called twice!
                 */
                paint() {
                    console.log(this.unique_id, this.element_id, this.x, this.y);
                    //this.game.placeOnObjectPos(this.unique_id, this.element_id, this.x, this.y);
                    this.game.slideToObjectPos(this.unique_id, this.element_id, this.x, this.y).play();
                }
                get_bounding_box() {
                    return {horizontal_min: this.horizontal - 2, vertical_min: this.vertical - 1, horizontal_max: this.horizontal + 0, vertical_max: this.vertical + 1};
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
