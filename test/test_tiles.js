var assert = require('assert');
var sinon = require('sinon');

var sut_module = require('../export/modules/javascript/tiles.js');

class Document {
    getElementById(element_id) {return {insertAdjacentHTML: sinon.spy(),};}
}
describe('Tiles', function () {
    beforeEach(function() {
        dojo = {style:sinon.spy(), addClass:sinon.spy(), removeClass:sinon.spy(), };
        document = new Document();
        game = {placeOnObjectPos:sinon.spy(),};
        dependencies = {dojo: dojo, document: document, game:game, };
        sut = new sut_module(dependencies);
        tile_specification = {tile_id: 0, stage: 0, horizontal: 0,vertical: 0,};
        element_id = 'HTML element ID ';
        x = 12;
        y = 33;

    });
    describe('Create tile from specification', function () {
        it('copies the input parameters', function () {
            // Arrange
            tile_specification.test_parameter = dojo;
            // Act
            tile = sut.create_tile_from(tile_specification);
            // Assert
            assert.equal(tile.test_parameter, tile_specification.test_parameter);
        });
    });
    describe('Paint', function () {
        beforeEach(function() {
            tile = sut.create_tile_from(tile_specification);
        });
        it('calls placeOnObjectPos with unique_id', function () {
            // Arrange
            // Act
            tile.paint();
            // Assert
            assert.equal(game.placeOnObjectPos.getCall(0).args[0], tile.unique_id);
        });
        it('uses the parameters from move', function () {
            // Arrange
            tile.move_to(element_id, x, y);
            // Act
            tile.paint();
            // Assert
            assert.equal(game.placeOnObjectPos.getCall(0).args[1], element_id);
            assert.equal(game.placeOnObjectPos.getCall(0).args[2], x);
            assert.equal(game.placeOnObjectPos.getCall(0).args[3], y);
        });
    });
});
