define(['dojo/_base/declare'], (declare) => {
    return declare('pyramido.canvas', null, {
        /**
         * Dependencies:
         * dojo
         * document
         * game
         * element_id
         */
        /**
         * Use case creation:
         */
        PIXELS_PER_TILE: 80,

        constructor(dependencies) {
            this.clone(dependencies);
            this.paintables = [];

            this.x_minimum = 5000000;
            this.x_maximum = 0;
            this.y_minimum = 5000000;
            this.y_maximum = 0;
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
        add(paintable) {
            const [x, y] = this.getAbsoluteCoordinates(paintable.horizontal, paintable.vertical);
            if ( this.is_resize_needed(x, y) ) {
                this.adjust_boundary(x, y);
                this.resize_element();
                this.relocate_paintables();
            }

            this.relocate(paintable);

            this.paintables.push(Object.assign({}, paintable));
        },
        resize_element() {
            this.dojo.style(this.element_id, 'width', '' + this.x_maximum - this.x_minimum + this.PIXELS_PER_TILE + 'px');
            this.dojo.style(this.element_id, 'height', '' + this.y_maximum - this.y_minimum + this.PIXELS_PER_TILE + 'px');
        },
        is_resize_needed(new_x, new_y) {
            return (new_y < this.y_minimum) || (new_y > this.y_maximum) || (new_x < this.x_minimum) || (new_x > this.x_maximum);
        },
        adjust_boundary(new_x, new_y) {
            if (new_x > this.x_maximum) {
                this.x_maximum = new_x;
            }
            if (new_x < this.x_minimum) {
                this.x_minimum = new_x;                    
            }
            if (new_y > this.y_maximum) {
                this.y_maximum = new_y;
            }
            if (new_y < this.y_minimum) {
                this.y_minimum = new_y;
            }
        },
        relocate_paintables() {
            for (index in this.paintables)
                this.relocate(this.paintables[index]);
        },
        relocate(paintable) {
            const y_centre = (this.y_maximum + this.y_minimum)/2;
            const x_centre = (this.x_maximum + this.x_minimum)/2;
            const [x, y] = this.getAbsoluteCoordinates(paintable.horizontal, paintable.vertical);
            paintable.move_to(this.element_id, x - x_centre, y - y_centre);
        },
        getAbsoluteCoordinates(horizontal, vertical) {
            x = horizontal * this.PIXELS_PER_TILE / 2;
            y = vertical * this.PIXELS_PER_TILE / 2;
            return [x, y];
        },
        /**
         */
    });
});
