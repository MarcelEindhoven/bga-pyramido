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
                DOMINOES_PER_ROW = 10;
                DEFAULT_PIXELS_PER_TILE = 80;
                constructor(dependencies) {
                    this.clone(dependencies);
                    this.pixels_per_tile = this.DEFAULT_PIXELS_PER_TILE;
                }
                create_token(specification) {
                    this.clone(specification);
                    this.unique_id = 'domino-' + this.id;
                }
                subscribe(callback_object, callback_method) {
                    this.create_canvas_token();
                    this.callback_object = callback_object;
                    this.callback_method = callback_method;

                    this.small_pixels_per_tile = this.pixels_per_tile;

                    this.dojo.addClass(this.unique_id, 'selectable');
                    this.dojo.connect(this.game.get_element(this.unique_id), 'onclick', this, 'domino_selected');
                    this.dojo.connect(this.game.get_element(this.unique_id), 'onmouseover', this, 'onmouseover');
                    this.dojo.connect(this.game.get_element(this.unique_id), 'onmouseout', this, 'onmouseout');
                }
                create_canvas_token() {
                    this.document.getElementById('game_play_area').insertAdjacentHTML('beforeend', `
                        <div id="${this.unique_id}">
                    `);
                    this.style_canvas_token();
                }
                destroy_canvas_token() {
                    this.dojo.destroy(this.unique_id);
                }
                onmouseover(selected_element_id) {
                    console.log(this.unique_id, this.element_id);
                    this.dojo.removeClass(this.unique_id, 'domino' + this.pixels_per_tile);
                    this.small_pixels_per_tile = this.pixels_per_tile;
                    this.pixels_per_tile = this.DEFAULT_PIXELS_PER_TILE;
                    this.style_canvas_token();
                    this.paint();
                }
                onmouseout(selected_element_id) {
                    console.log(this.unique_id, this.element_id);
                    this.dojo.removeClass(this.unique_id, 'domino' + this.pixels_per_tile);
                    this.pixels_per_tile = this.small_pixels_per_tile;
                    this.style_canvas_token();
                    this.paint();
                }
                style_canvas_token() {
                    this.dojo.addClass(this.unique_id, 'domino' + this.pixels_per_tile);
                    const id_horizontal = this.id % this.DOMINOES_PER_ROW;
                    const id_vertical = (this.id-id_horizontal) / this.DOMINOES_PER_ROW;
                    this.dojo.style(this.unique_id, 'backgroundPosition', '-' + this.pixels_per_tile * id_horizontal * 2 + 'px -' + this.pixels_per_tile * id_vertical + 'px');
                }
                domino_selected(selected_element_id) {
                    this.callback_object[this.callback_method](this);
                }
                get_bounding_box() {
                    if (this.rotation == 0) {
                        return {horizontal_min: this.horizontal - 2, vertical_min: this.vertical - 1, horizontal_max: this.horizontal + 2, vertical_max: this.vertical + 1};
                    }
                    if (this.rotation == 1) {
                        return {horizontal_min: this.horizontal - 2, vertical_min: this.vertical - 1, horizontal_max: this.horizontal + 0, vertical_max: this.vertical + 3};
                    }
                    if (this.rotation == 2) {
                        return {horizontal_min: this.horizontal - 4, vertical_min: this.vertical - 1, horizontal_max: this.horizontal + 0, vertical_max: this.vertical + 1};
                    }
                    if (this.rotation == 3) {
                        return {horizontal_min: this.horizontal - 2, vertical_min: this.vertical - 3, horizontal_max: this.horizontal + 0, vertical_max: this.vertical + 1};
                    }
                }
                move_to(element_id, x, y, canvas_pixels_per_tile) {
                    this.element_id = element_id;
                    this.x = x;
                    this.y = y;
                    this.canvas_pixels_per_tile = canvas_pixels_per_tile;
                }
                paint() {
                    //console.log(this.unique_id, this.element_id, this.x, this.y);
                    //this.game.placeOnObjectPos(this.unique_id, this.element_id, this.x, this.y);
                    const x = this.x + ((this.rotation % 2 == 0) ?
                        this.canvas_pixels_per_tile - this.pixels_per_tile:
                        (this.canvas_pixels_per_tile - this.pixels_per_tile)/2);
                    const y = this.y + ((this.rotation % 2 == 1) ?
                        this.canvas_pixels_per_tile - this.pixels_per_tile:
                        (this.canvas_pixels_per_tile - this.pixels_per_tile)/2);

                    this.game.slideToObjectPos(this.unique_id, this.element_id, x, y).play();
                    if (this.rotation_class)
                        this.dojo.removeClass(this.unique_id, this.rotation_class);
                    this.rotation_class = 'domino_rotate' + this.rotation;
                    this.dojo.addClass(this.unique_id, this.rotation_class);
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
