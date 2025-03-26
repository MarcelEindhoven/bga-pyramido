var assert = require('assert');
var sinon = require('sinon');

var sut_module = require('../export/modules/javascript/tiles.js');

class Document {
    getElementById(element_id) {return {insertAdjacentHTML: sinon.spy(),};}
}
class Animation {
    play () {}
}
describe('Tiles', function () {
    beforeEach(function() {
        dojo = {style:sinon.spy(), addClass:sinon.spy(), removeClass:sinon.spy(), connect:sinon.spy(), };
        document = new Document();
        game = {get_element:sinon.stub().returns(44), slideToObjectPos:sinon.stub().returns (new Animation ()) ,};
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
        it('creates predictable ID', function () {
            // Arrange
            tile_specification.tile_id = 179;
            // Act
            tile = sut.create_tile_from(tile_specification);
            // Assert
            assert.equal(tile.unique_id, "tile-179");
        });
        it('connects with unique ID', function () {
            // Arrange
            tile_specification.tile_id = 179;
            // Act
            tile = sut.create_tile_from(tile_specification);
            // Assert
            sinon.assert.callCount(dojo.connect, 1);
            assert.equal(dojo.connect.getCall(0).args[0], 44);
            assert.equal(dojo.connect.getCall(0).args[1], 'onclick');
        });
    });
    describe('Unique ID', function () {
        it('creates predictable ID', function () {
            // Arrange
            tile_specification.tile_id = 179;
            // Act
            id = sut.get_unique_id(tile_specification);
            // Assert
            assert.equal(id, "tile-179");
        });
    });
    describe('Subscribe', function () {
        beforeEach(function() {
            callback_object = {
                tile_selected: sinon.spy(),
            };
        });
        function act() {
            tile.subscribe(callback_object, 'tile_selected');
        };
        it('makes selectable', function () {
            // Arrange
            tile_specification.tile_id = 179;            
            tile = sut.create_tile_from(tile_specification);
            // Act
            act();
            // Assert
            sinon.assert.callCount(dojo.addClass, 1 + 1);
            assert.equal(dojo.addClass.getCall(1).args[0], "tile-179");
            assert.equal(dojo.addClass.getCall(1).args[1], "selectable");
        });
    });
    describe('Stop subscription', function () {
        beforeEach(function() {
            tile_specification.tile_id = 179;            
            tile = sut.create_tile_from(tile_specification);
        });
        function act() {
            tile.unsubscribe();
        };
        it('makes unselectable in UI', function () {
            // Arrange
            // Act
            act();
            // Assert
            sinon.assert.callCount(dojo.removeClass, 1);
            assert.equal(dojo.removeClass.getCall(0).args[0], "tile-179");
            assert.equal(dojo.removeClass.getCall(0).args[1], "selectable");
        });
    });
    describe('Paint', function () {
        beforeEach(function() {
            tile = sut.create_tile_from(tile_specification);
        });
        it('calls slideToObjectPos with unique_id', function () {
            // Arrange
            // Act
            tile.paint();
            // Assert
            assert.equal(game.slideToObjectPos.getCall(0).args[0], tile.unique_id);
        });
        it('uses the parameters from move', function () {
            // Arrange
            tile.move_to(element_id, x, y);
            // Act
            tile.paint();
            // Assert
            assert.equal(game.slideToObjectPos.getCall(0).args[1], element_id);
            assert.equal(game.slideToObjectPos.getCall(0).args[2], x);
            assert.equal(game.slideToObjectPos.getCall(0).args[3], y);
        });
    });
});
