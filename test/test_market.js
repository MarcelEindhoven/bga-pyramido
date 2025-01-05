var assert = require('assert');
var sinon = require('sinon');

var sut_module = require('../export/modules/javascript/market.js');

describe('Market', function () {
    beforeEach(function() {
        dojo = {connect:sinon.spy(), addClass:sinon.spy(), };
        dependencies = {dojo: dojo, stocks:{'next0': sinon.spy(), 'quarry0': sinon.spy(), 'quarry1': sinon.spy()}};
        sut = new sut_module(dependencies);
        callback_object = {
            tile_placed: sinon.spy(),
        };

    });
    describe('Constructor', function () {
        it('subscribes to dojo from the start', function () {
            // Arrange
            // Act
            // Assert
            sinon.assert.callCount(dojo.connect, Object.keys(dependencies.stocks).length);
        });
        it('connects to dojo with the correct parameters', function () {
            // Arrange
            // Act
            // Assert
            assert.equal(dojo.connect.getCall(0).args[0], dependencies.stocks['next0']);
            assert.equal(dojo.connect.getCall(0).args[1], 'onChangeSelection');
            assert.equal(dojo.connect.getCall(0).args[2], sut);
            assert.equal(dojo.connect.getCall(0).args[3], 'domino_selected');
        });
    });
    describe('Selectable', function () {
        beforeEach(function() {
            callback_object = {
                quarry_selected: sinon.spy(),
            };
        });
        it('makes quarry dominoes selectable when subscribed to quarry', function () {
            // Arrange
            // Act
            sut.subscribe_to_quarry(callback_object, 'quarry_selected');
            // Assert
            sinon.assert.callCount(dojo.addClass, 2);
        });
        it('uses the correct arguments for addClass', function () {
            // Arrange
            // Act
            sut.subscribe_to_quarry(callback_object, 'quarry_selected');
            // Assert
            assert.equal(dojo.addClass.getCall(1).args[0], 'quarry1');
            assert.equal(dojo.addClass.getCall(1).args[1], 'selectable');
        });
    });
});
