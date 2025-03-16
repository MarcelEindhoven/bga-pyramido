define(['dojo/_base/declare'], (declare) => {
    return declare('pyramido.markers', null, {
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
         * marker_id
         * stage
         * horizontal
         * vertical
         */
        create_from(specification) {
            class Marker {
                TILES_PER_ROW = 6;
                PIXELS_PER_TILE = 60;
                MARGIN_BETWEEN_TOKENS = 10;
                constructor(dependencies) {
                    this.clone(dependencies);
                }
                create_token(specification) {
                    this.clone(specification);
                    this.set_position_for_marker_window();

                    this.unique_id = "marker-" + this.id;
                    this.document.getElementById('game_play_area').insertAdjacentHTML('beforeend', `
                        <div id="${this.unique_id}">
                    `);
                    this.dojo.addClass(this.unique_id,'marker');
                    const id_horizontal = this.colour;
                    this.dojo.style(this.unique_id, 'backgroundPosition', '-' + this.PIXELS_PER_TILE * id_horizontal + 'px -0px');
                }
                clone(properties){
                    for (var property in properties) {
                        this[property] = properties[property];
                    }
                }
                set_position_for_marker_window() {
                    this.horizontal = 2 * ((this.colour - 1) % 2);
                    this.vertical = 2 * ((this.colour - 1 - ((this.colour - 1) % 2)) / 2);
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
            result = new Marker({document: this.document, dojo: this.dojo, game: this.game,});
            result.create_token(specification);
            return result;
        },
        /**
         */
    });
});
