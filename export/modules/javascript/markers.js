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
        get_unique_id(tile_specification){return "marker-" + tile_specification.id;},
        /**
         * specification:
         * id
         * stage
         * horizontal
         * vertical
         */
        create_from(specification) {
            class Marker {
                TILES_PER_ROW = 6;
                PIXELS_PER_TILE = 80;
                PIXELS_PER_MARKER = 40;
                MARGIN_BETWEEN_TOKENS = 10;
                constructor(dependencies) {
                    this.clone(dependencies);
                }
                create_token(specification) {
                    this.clone(specification);
                    this.unique_id = this.get_unique_id(specification);


                    if (this.stage == 0)
                        this.set_position_for_marker_window();
                    else
                        this.adjust_position_within_pyramid();

                    this.create_dojo();
                }
                clone(properties){
                    for (var property in properties) {
                        this[property] = properties[property];
                    }
                }
                place(specification) {
                    this.dojo.destroy(this.unique_id);
                    this.clone(specification);
                    this.adjust_position_within_pyramid();
                    this.create_dojo();
                }
                return(specification) {
                    this.clone(specification);
                    this.set_position_for_marker_window();
                }
                set_position_for_marker_window() {
                    this.horizontal = 2 * (this.colour % 2);
                    this.vertical = 2 * ((this.colour - (this.colour % 2)) / 2);
                }
                move_to(element_id, x, y) {
                    this.element_id = element_id;
                    //this.game.attachToNewParent(this.unique_id, this.element_id);
                    this.x = x;
                    this.y = y;
                }
                /**
                 * Precondition: move_to has been called
                 * During setup, must be called twice!
                 */
                paint() {
                    //console.log(this.unique_id, this.element_id, this.x, this.y);
                    this.game.slideToObjectPos(this.unique_id, this.element_id, this.x, this.y, 0, 0).play();
                }
                get_bounding_box() {
                    return {horizontal_min: this.horizontal - 2, vertical_min: this.vertical - 1, horizontal_max: this.horizontal + 0 - 1, vertical_max: this.vertical + 1 - 1};
                }
                create_dojo() {
                    this.document.getElementById('game_play_area').insertAdjacentHTML('beforeend', `
                        <div id="${this.unique_id}">
                    `);
                    this.dojo.addClass(this.unique_id,'marker40');
                    const id_horizontal = +this.colour % 2;
                    const id_vertical = (+this.colour - id_horizontal) / 2;
                    this.dojo.style(this.unique_id, 'backgroundPosition', '-' + this.PIXELS_PER_MARKER * id_horizontal + 'px -' + this.PIXELS_PER_MARKER * id_vertical + 'px');
                }
                adjust_position_within_pyramid() {
                    this.horizontal = this.horizontal + this.PIXELS_PER_MARKER/this.PIXELS_PER_TILE;
                    this.vertical = this.vertical + this.PIXELS_PER_MARKER/this.PIXELS_PER_TILE;
                }
            }
            result = new Marker({document: this.document, dojo: this.dojo, game: this.game, get_unique_id: this.get_unique_id,});
            result.create_token(specification);
            return result;
        },
        /**
         */
    });
});
