var assert = require('assert');
var sinon = require('sinon');

var sut_module = require('../export/modules/javascript/canvas.js');

describe('Canvas class', function () {
    beforeEach(function() {
        id = 'HTML element ID ';
        dojo = {style:sinon.spy(), addClass:sinon.spy(), removeClass:sinon.spy(), };
        game = {placeOnObjectPos:sinon.spy(),};
        dependencies = {dojo: dojo, game:game, element_id: id,};
        sut = new sut_module(dependencies);
        tile = {tile_id: 0, stage: 0, horizontal: 10,vertical: 20, rotation: 0,
            unique_id: 'first ',
            move_to:sinon.spy(),
            paint:sinon.spy(),
            get_bounding_box: function() { return {horizontal_min: this.horizontal - 1, vertical_min: this.vertical - 1, horizontal_max: this.horizontal + 1, vertical_max: this.vertical + 1};}
        };
        other_tile = {tile_id: 0, stage: 0, horizontal: 12,vertical: 17, rotation: 0,
            unique_id: 'second ',
            move_to:sinon.spy(),
            paint:sinon.spy(),
            get_bounding_box: function() { return {horizontal_min: this.horizontal - 1, vertical_min: this.vertical - 1, horizontal_max: this.horizontal + 1, vertical_max: this.vertical + 1};}
        };
        element_id = 'HTML element ID ';
        x = 12;
        y = 33;

    });
    describe('Add paintable', function () {
        function act(tile) {
            sut.add(tile);
        };
        it('moves paintable to itself', function () {
            // Arrange
            // Act
            act(tile);            
            // Assert
            assert.equal(tile.move_to.getCall(0).args[0], id);
        });
        it('uses zero coordinates for first paintable', function () {
            // Arrange
            // Act
            act(tile);            
            // Assert
            assert.equal(tile.move_to.getCall(0).args[1], 0);
            assert.equal(tile.move_to.getCall(0).args[2], 0);
        });
        it('styles itself twice per resize', function () {
            // Arrange
            // Act
            act(tile);            
            // Assert
            assert.equal(dojo.style.getCall(0).args[0], id);
            assert.equal(dojo.style.getCall(1).args[0], id);
            assert.equal(dojo.style.getCall(2).args[0], id);
            assert.equal(dojo.style.getCall(3).args[0], id);
        });
        it('resizes width as well as height', function () {
            // Arrange
            // Act
            act(tile);            
            // Assert
            assert.equal(dojo.style.getCall(0).args[1], 'width');
            assert.equal(dojo.style.getCall(1).args[1], 'height');
            assert.equal(dojo.style.getCall(2).args[1], 'width');
            assert.equal(dojo.style.getCall(3).args[1], 'height');
        });
        it('resizes to one tile', function () {
            // Arrange
            // Act
            act(tile);            
            // Assert
            assert.equal(dojo.style.getCall(2).args[2], '' + sut.DEFAULT_PIXELS_PER_TILE + 'px');
            assert.equal(dojo.style.getCall(3).args[2], '' + sut.DEFAULT_PIXELS_PER_TILE + 'px');
        });
    });
    describe('Remove paintables', function () {
        beforeEach(function() {
            sut.add(tile);
        });
        function act(tile) {
            sut.remove(tile);
        };
        it('no paint when tile is removed', function () {
            // Arrange
            // Act
            act(tile);
            // Assert
            sut.paint();
            sinon.assert.callCount(tile.paint, 0);
        });
    });
    describe('Multiple paintables', function () {
        beforeEach(function() {
            sut.add(tile);
        });
        function act(tile) {
            sut.add(tile);
        };
        it('no additional resize when tile added with same horizontal and vertical', function () {
            // Arrange
            // Act
            act(tile);
            // Assert
            sinon.assert.callCount(dojo.style, 4);
        });
        it('no additional resize means no additional move', function () {
            // Arrange
            // Act
            act(tile);
            // Assert
            sinon.assert.callCount(tile.move_to, 2);
        });
        it('resizes once when adding second tile', function () {
            // Arrange
            // Act
            act(other_tile);
            // Assert
            sinon.assert.callCount(dojo.style, 8);
        });
        it('resizes a half tile per coordinate', function () {
            // Arrange
            // Act
            act(other_tile);            
            // Assert
            assert.equal(dojo.style.getCall(6).args[2], '' + ((12-10+2)/2*sut.DEFAULT_PIXELS_PER_TILE) + 'px');
            assert.equal(dojo.style.getCall(7).args[2], '' + ((20-17+2)/2*sut.DEFAULT_PIXELS_PER_TILE) + 'px');
        });
        it('also moves existing tiles when resizing', function () {
            // Arrange
            // Act
            act(other_tile);
            // Assert
            sinon.assert.callCount(tile.move_to, 3);
        });
        it('moves new tile a half tile per coordinate', function () {
            // Arrange
            // Act
            act(other_tile);            
            // Assert
            assert.equal(other_tile.move_to.getCall(0).args[1], 1 * sut.DEFAULT_PIXELS_PER_TILE);
            assert.equal(other_tile.move_to.getCall(0).args[2], 0);
        });
        it('moves existing tile with original data', function () {
            // Arrange
            // Act
            act(other_tile);            
            // Assert
            assert.equal(tile.move_to.getCall(1).args[1], 0);
            assert.equal(tile.move_to.getCall(1).args[2], 1.5 * sut.DEFAULT_PIXELS_PER_TILE);
        });
    });
    describe('Multiple paintables with margin between tiles', function () {
        function act(tile) {
            sut.add(tile);
        };
        it('takes into account margin between tiles', function () {
            // Arrange
            sut.set_margin_between_tiles(10);
            other_tile.horizontal = tile.horizontal + 2;
            other_tile.vertical = tile.vertical - 4;
            // Act
            act(tile);
            act(other_tile);
            // Assert
            assert.equal(tile.move_to.getCall(1).args[1], 0);
            assert.equal(tile.move_to.getCall(1).args[2], 2 * sut.DEFAULT_PIXELS_PER_TILE + 2 * 10);
            assert.equal(other_tile.move_to.getCall(0).args[1], 1 * sut.DEFAULT_PIXELS_PER_TILE+ 1 * 10);
            assert.equal(other_tile.move_to.getCall(0).args[2], 0);
        });
    });
});
