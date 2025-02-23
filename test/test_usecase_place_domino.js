var assert = require('assert');
var sinon = require('sinon');

var sut_module = require('../export/modules/javascript/usecase_place_domino.js');

describe('Use case choose first domino', function () {
    beforeEach(function() {
        market = {subscribe_to_quarry: sinon.spy(), unsubscribe: sinon.spy(),};
        sut = new sut_module({market: market});
        stock = {control_name: "quarry-2"};
    });
    describe('Subscribe', function () {
        beforeEach(function() {
            callback_object = {
                domino_selected: sinon.spy(),
            };
        });
        function act() {
            sut.subscribe(callback_object, 'domino_selected');
        };
        it('subscribes to the market', function () {
            // Arrange
            
            // Act
            act();
            // Assert
            sinon.assert.callCount(market.subscribe_to_quarry, 1);
            assert.equal(market.subscribe_to_quarry.getCall(0).args[0], sut);
            assert.equal(market.subscribe_to_quarry.getCall(0).args[1], 'quarry_selected');
        });
    });
    describe('Quarry selected', function () {
        beforeEach(function() {
            callback_object = {
                domino_selected: sinon.spy(),
            };
            sut.subscribe(callback_object, 'domino_selected');
        });
        function act() {
            domino = 'domino';
            sut.quarry_selected(domino);
        };
        it('calls subscriber', function () {
            // Arrange
            // Act
            act();
            // Assert
            sinon.assert.callCount(callback_object.domino_selected, 1);
        });
        it('calls subscriber with domino', function () {
            // Arrange
            // Act
            act();
            // Assert
            assert.equal(callback_object.domino_selected.getCall(0).args[0], domino);
        });
    });
});
