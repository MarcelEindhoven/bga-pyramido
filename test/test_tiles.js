var assert = require('assert');
var sinon = require('sinon');

var sut_module = require('../export/modules/javascript/tiles.js');

describe('Tiles', function () {
    beforeEach(function() {
        dojo = {connect:sinon.spy(), addClass:sinon.spy(), removeClass:sinon.spy(), };
        dependencies = {dojo: dojo, stocks:{'next0': sinon.spy(), 'quarry0': sinon.spy(), 'quarry1': sinon.spy()}};
        sut = new sut_module(dependencies);
        callback_object = {
            tile_placed: sinon.spy(),
        };

    });
    describe('Create tile from specification', function () {
        it('copies the input parameters', function () {
            // Arrange
            tile_specification = {test_parameter: dojo};
            // Act
            tile = sut.create_tile_from(tile_specification);
            // Assert
            assert.equal(tile.test_parameter, tile_specification.test_parameter);
        });
    });
});
