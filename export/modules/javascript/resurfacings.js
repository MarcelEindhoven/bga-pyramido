define(['dojo/_base/declare'], (declare) => {
    return declare('pyramido.resurfacings', null, {
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
        get_unique_id(specification){return "resurfacing-" + specification.id;},
        /**
         * specification:
         * id
         * stage
         * horizontal
         * vertical
         */
        create_from(specification) {
            class Resurfacing {
                TILES_PER_ROW = 2;
                DEFAULT_PIXELS_PER_TILE = 100;
                SIZE_TILE_IN_COORDINATES = 2;
                constructor(dependencies) {
                    this.clone(dependencies);
                    this.pixels_per_tile = this.DEFAULT_PIXELS_PER_TILE;
                }
                create_token(specification) {
                    this.clone(specification);
                    this.unique_id = this.get_unique_id(specification);

                    if (this.stage == 0)
                        this.set_position_for_resurfacing_window();

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
                    this.create_dojo();
                }
                return(specification) {
                    this.clone(specification);
                    this.set_position_for_resurfacing_window();
                }
                set_position_for_resurfacing_window() {
                    this.horizontal = this.SIZE_TILE_IN_COORDINATES * (this.colour % this.TILES_PER_ROW);
                    this.vertical = this.SIZE_TILE_IN_COORDINATES * ((this.colour - (this.colour % this.TILES_PER_ROW)) / this.TILES_PER_ROW);
                }
                move_to(element_id, x, y, element_pixels_per_tile) {
                    this.element_id = element_id;
                    this.centre_within_tile(x, y, element_pixels_per_tile);
                }
                centre_within_tile(x, y, element_pixels_per_tile) {
                    this.x = x + (element_pixels_per_tile - this.pixels_per_tile)/2;
                    this.y = y + (element_pixels_per_tile - this.pixels_per_tile)/2;
                }
                /**
                 * Precondition: move_to has been called
                 * During setup, must be called twice!
                 */
                paint() {
                    // console.log(this.unique_id, this.element_id, this.x, this.y, this.rotation);
                    if (this.rotation_class)
                        this.dojo.removeClass(this.unique_id, this.rotation_class);

                    this.game.slideToObjectPos(this.unique_id, this.element_id, this.x, this.y, 0, 0).play();

                    this.rotation_class = 'rotate' + this.rotation;
                    this.dojo.addClass(this.unique_id, this.rotation_class);
                }
                get_bounding_box() {
                    return {horizontal_min: this.horizontal - 2, vertical_min: this.vertical - 1, horizontal_max: this.horizontal + 0, vertical_max: this.vertical + 1};
                }
                create_dojo() {
                    this.document.getElementById('game_play_area').insertAdjacentHTML('beforeend', `
                        <div id="${this.unique_id}">
                    `);
                    const upside = +this.colour % 2;
                    const side = upside == 0 ? 'downside' : 'upside';
                    console.log('colour = '+ +this.colour + ' binary = ' + upside + ' side = '+ side);
                    this.game.addTooltip(this.unique_id, _('Resurfacing ' + side), '');

                    this.dojo.addClass(this.unique_id,'resurfacing' + this.pixels_per_tile);
                    const id_horizontal = +this.colour % 2;
                    const id_vertical = (+this.colour - id_horizontal) / 2;
                    this.dojo.style(this.unique_id, 'backgroundPosition', '-' + this.pixels_per_tile * id_horizontal + 'px -' + this.pixels_per_tile * id_vertical + 'px');

                    this.dojo.connect(this.game.get_element(this.unique_id), 'onclick', this, 'tile_selected');
                }
                subscribe(callback_object, callback_method) {
                    this.callback_object = callback_object;
                    this.callback_method = callback_method;
                    this.dojo.addClass(this.unique_id, 'selectable');
                }
                unsubscribe() {
                    console.log(this.unique_id);
                    this.dojo.removeClass(this.unique_id, 'selectable');
                    delete this.callback_object;
                    delete this.callback_method;
                }
                tile_selected(tile) {
                    console.log(tile);
                    if ('callback_object' in this)
                        this.callback_object[this.callback_method](this);
                }
                destroy_canvas_token() {
                    this.dojo.destroy(this.unique_id);
                }
            }
            result = new Resurfacing({document: this.document, dojo: this.dojo, game: this.game, get_unique_id: this.get_unique_id,});
            result.create_token(specification);
            return result;
        },
        /**
         */
    });
});
