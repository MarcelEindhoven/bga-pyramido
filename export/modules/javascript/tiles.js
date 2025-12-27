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
        get_unique_id(tile_specification){return "tile-" + tile_specification.tile_id;},
        /**
         * specification:
         * tile_id
         * stage
         * horizontal
         * vertical
         */
        create_from(specification) {
            class Tile {
                TILES_PER_ROW = 20;
                PIXELS_PER_TILE = 80;
                constructor(dependencies) {
                    this.clone(dependencies);
                }
                create_token(specification) {
                    this.clone(specification);
                    this.unique_id = this.get_unique_id(specification);

                    // Create shadow first to be underneath
                    this.document.getElementById('game_play_area').insertAdjacentHTML('beforeend', `
                        <div id="${this.unique_id}_shadow">
                    `);
                    this.dojo.addClass(this.unique_id + '_shadow', 'shadow');

                    this.document.getElementById('game_play_area').insertAdjacentHTML('beforeend', `
                        <div id="${this.unique_id}">
                    `);
                    this.dojo.addClass(this.unique_id,'tile');

                    this.set_stage(this.stage);
                    const id_horizontal = this.tile_id % this.TILES_PER_ROW;
                    const id_vertical = (this.tile_id-id_horizontal) / this.TILES_PER_ROW;

                    this.dojo.style(this.unique_id, 'backgroundPosition', '-' + this.PIXELS_PER_TILE * id_horizontal + 'px -' + this.PIXELS_PER_TILE * id_vertical + 'px');

                    this.dojo.connect(this.game.get_element(this.unique_id), 'onclick', this, 'tile_selected');
                }
                clone(properties){
                    for (var property in properties) {
                        this[property] = properties[property];
                    }
                }
                subscribe(callback_object, callback_method) {
                    this.callback_object = callback_object;
                    this.callback_method = callback_method;
                    this.dojo.addClass(this.unique_id, 'selectable');
                }
                unsubscribe() {
                    this.dojo.removeClass(this.unique_id, 'selectable');
                    delete this.callback_object;
                    delete this.callback_method;
                }
                set_stage(stage) {
                    this.stage = stage;
                    let tooltip = 'undefined';
                    switch(this.stage) {
                        case 0: tooltip = '4x5 or 5x4'; break;
                        case 1: tooltip = 'Tile in 4x5 or 5x4 grid'; break;
                        case 2: tooltip = 'Tile in 3x4 or 4x3 grid'; break;
                        case 3: tooltip = 'Tile in 2x3 or 3x2 grid'; break;
                        case 4: tooltip = 'Latest placed domino'; break;
                        default: tooltip = 'undefined'; break;
                    }
                    this.game.addTooltip(this.unique_id, _(tooltip), '');

                }
                tile_selected(tile) {
                    console.log('tile_selected');
                    if ('callback_object' in this)
                        this.callback_object[this.callback_method](tile);
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
                    // console.log(this.unique_id, this.element_id, this.x, this.y);
                    //this.game.placeOnObjectPos(this.unique_id, this.element_id, this.x, this.y);
                    if (this.rotation_class) {
                        this.dojo.removeClass(this.unique_id, this.rotation_class);
                    }

                    this.game.slideToObjectPos(this.unique_id + '_shadow', this.element_id, this.x, this.y).play();
                    this.game.slideToObjectPos(this.unique_id, this.element_id, this.x, this.y).play();

                    this.rotation_class = 'rotate' + this.rotation;
                    this.dojo.addClass(this.unique_id, this.rotation_class);
                }
                get_bounding_box() {
                    return {horizontal_min: this.horizontal - 2, vertical_min: this.vertical - 1, horizontal_max: this.horizontal + 0, vertical_max: this.vertical + 1};
                }
                destroy_canvas_token() {
                    this.dojo.destroy(this.unique_id);
                    this.dojo.destroy(this.unique_id + '_shadow');
                }
            }
            result = new Tile({document: this.document, dojo: this.dojo, game: this.game, get_unique_id: this.get_unique_id,});
            result.create_token(specification);
            return result;
        },
        /**
         */
    });
});
