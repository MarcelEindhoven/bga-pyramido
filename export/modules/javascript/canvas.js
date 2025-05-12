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
        DEFAULT_PIXELS_PER_TILE: 80,

        constructor(dependencies) {
            this.clone(dependencies);
            this.paintables = {};

            this.x_minimum = 5000000;
            this.x_maximum = 0;
            this.y_minimum = 5000000;
            this.y_maximum = 0;

            this.pixels_per_tile = this.DEFAULT_PIXELS_PER_TILE;
            this.margin_between_tiles = 0;
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
         * get_bounding_box
         */
        set_margin_between_tiles(margin) {this.margin_between_tiles = margin;},
        set_pixels_per_tile(pixels_per_tile) {this.pixels_per_tile = pixels_per_tile;},
        add(paintable) {
            const bounding_box = paintable.get_bounding_box();

            this.resize_if_needed(this.getAbsoluteCoordinates(bounding_box.horizontal_min, bounding_box.vertical_min));
            this.resize_if_needed(this.getAbsoluteCoordinates(bounding_box.horizontal_max, bounding_box.vertical_max));

            this.relocate(paintable);

            this.paintables[paintable.unique_id] = paintable;
        },
        remove(paintable) {
            delete this.paintables[paintable.unique_id];
        },
        get(unique_id) {return this.paintables[unique_id];},
        is_present(unique_id) {return unique_id in this.paintables;},
        paint: function() {
            Object.values(this.paintables).forEach(paintable => {
                // console.log(paintable.unique_id);
                paintable.paint();
            });
        },
        resize_if_needed(location) {
            const [x, y] = location;
            if ( this.is_resize_needed(x, y) ) {
                this.adjust_boundary(x, y);
                this.resize_element();
                this.relocate_paintables();
            }
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
        resize_element() {
            this.dojo.style(this.element_id, 'width', '' + this.x_maximum - this.x_minimum + 'px');
            this.dojo.style(this.element_id, 'height', '' + this.y_maximum - this.y_minimum + 'px');
        },
        relocate_paintables() {
            for (index in this.paintables)
                this.relocate(this.paintables[index]);
        },
        relocate(paintable) {
            const bounding_box = paintable.get_bounding_box();
            const [x, y] = this.getAbsoluteCoordinates(bounding_box.horizontal_min, bounding_box.vertical_min);
            paintable.move_to(this.element_id, x - this.x_minimum, y - this.y_minimum);
        },
        getAbsoluteCoordinates(horizontal, vertical) {
            const x = horizontal * (this.pixels_per_tile + this.margin_between_tiles)/ 2;
            const y = vertical * (this.pixels_per_tile + this.margin_between_tiles) / 2;
            return [x, y];
        },
        /**
         */
    });
});
