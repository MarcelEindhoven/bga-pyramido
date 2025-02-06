var assert = require('assert');
var sinon = require('sinon');

var sut_module = require('../export/modules/javascript/canvas.js');

class Tile {
    getElementById(element_id) {return {insertAdjacentHTML: sinon.spy(),};}
}
describe('Canvas class', function () {
    beforeEach(function() {
        id = 'HTML element ID ';
        dojo = {style:sinon.spy(), addClass:sinon.spy(), removeClass:sinon.spy(), };
        game = {placeOnObjectPos:sinon.spy(),};
        dependencies = {dojo: dojo, game:game, element_id: id,};
        sut = new sut_module(dependencies);
        tile = {tile_id: 0, stage: 0, horizontal: 10,vertical: 20,
            move_to:sinon.spy(),
        };
        element_id = 'HTML element ID ';
        x = 12;
        y = 33;

    });
    describe('Add paintables', function () {
        it('moves paintable to itself', function () {
            // Arrange
            // Act
            sut.add(tile);
            // Assert
            assert.equal(tile.move_to.getCall(0).args[0], id);
        });
        it('uses zero coordinates for first paintable', function () {
            // Arrange
            // Act
            sut.add(tile);
            // Assert
            assert.equal(tile.move_to.getCall(0).args[1], 0);
            assert.equal(tile.move_to.getCall(0).args[2], 0);
        });
        it('styles itself twice', function () {
            // Arrange
            // Act
            sut.add(tile);
            // Assert
            assert.equal(dojo.style.getCall(0).args[0], id);
            assert.equal(dojo.style.getCall(1).args[0], id);
        });
        it('resizes width as well as height', function () {
            // Arrange
            // Act
            sut.add(tile);
            // Assert
            assert.equal(dojo.style.getCall(0).args[1], 'width');
            assert.equal(dojo.style.getCall(1).args[1], 'height');
        });
        it('resizes to one tile', function () {
            // Arrange
            // Act
            sut.add(tile);
            // Assert
            assert.equal(dojo.style.getCall(0).args[2], '' + sut.PIXELS_PER_TILE + 'px');
            assert.equal(dojo.style.getCall(1).args[2], '' + sut.PIXELS_PER_TILE + 'px');
        });
        it('no additional resize when tile added with same horizontal and vertical', function () {
            // Arrange
            // Act
            sut.add(tile);
            sut.add(tile);
            // Assert
            sinon.assert.callCount(dojo.style, 2);
        });
        it('no additional resize means no additional move', function () {
            // Arrange
            // Act
            sut.add(tile);
            sut.add(tile);
            // Assert
            sinon.assert.callCount(tile.move_to, 2);
        });
    });
});
