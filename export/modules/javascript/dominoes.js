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
                PIXELS_PER_TILE = 80;
                constructor(dependencies) {
                    this.clone(dependencies);
                }
                create_token(specification) {
                    this.clone(specification);
                }
                create_canvas_token() {
                    this.unique_id = "domino-" + this.id;
                    this.document.getElementById('game_play_area').insertAdjacentHTML('beforeend', `
                        <div id="${this.unique_id}">
                    `);
                    this.dojo.addClass(this.unique_id, 'domino');
                    const id_horizontal = this.id % this.DOMINOES_PER_ROW;
                    const id_vertical = (this.id-id_horizontal) / this.DOMINOES_PER_ROW;
                    this.dojo.style(this.unique_id, 'backgroundPosition', '-' + this.PIXELS_PER_TILE * id_horizontal * 2 + 'px -' + this.PIXELS_PER_TILE * id_vertical + 'px');
                }
                get_bounding_box() {
                    if (this.rotation == 0) {
                        return {horizontal_min: this.horizontal, vertical_min: this.vertical, horizontal_max: this.horizontal + 4, vertical_max: this.vertical + 2};
                    }
                    if (this.rotation == 1) {
                        return {horizontal_min: this.horizontal, vertical_min: this.vertical, horizontal_max: this.horizontal + 2, vertical_max: this.vertical + 4};
                    }
                    if (this.rotation == 2) {
                        return {horizontal_min: this.horizontal - 2, vertical_min: this.vertical, horizontal_max: this.horizontal + 2, vertical_max: this.vertical + 2};
                    }
                    if (this.rotation == 3) {
                        return {horizontal_min: this.horizontal, vertical_min: this.vertical - 2, horizontal_max: this.horizontal + 2, vertical_max: this.vertical + 2};
                    }
                }
                move_to(element_id, x, y) {
                    this.element_id = element_id;
                    this.x = x;
                    this.y = y;
                }
                paint() {
                    console.log(this.unique_id, this.element_id, this.x, this.y);
                    //this.game.placeOnObjectPos(this.unique_id, this.element_id, this.x, this.y);
                    this.game.slideToObjectPos(this.unique_id, this.element_id, this.x, this.y).play();
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
