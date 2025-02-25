var assert = require('assert');
var sinon = require('sinon');

var sut_module = require('../export/modules/javascript/usecase_choose_domino.js');

describe('Use case choose first domino', function () {
    beforeEach(function() {
        market = {subscribe_to_quarry: sinon.spy(), unsubscribe: sinon.spy(),};
        sut = new sut_module({market: market});
        stock = {control_name: "quarry-2"};
    });
    describe('Subscribe', function () {
        beforeEach(function() {
            callback_object = {
                first_domino_selected: sinon.spy(),
            };
        });
        function act() {
            sut.subscribe(callback_object, 'first_domino_selected');
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
    describe('quarry_selected', function () {
        beforeEach(function() {
            callback_object = {
                first_domino_selected: sinon.spy(),
            };
        });
        function arrange() {
            sut.subscribe(callback_object, 'first_domino_selected');
        };
        function act() {
            domino = 'domino';
            sut.quarry_selected(domino);
        };
        it('calls subscriber with domino', function () {
            // Arrange
            arrange();
            // Act
            act();
            // Assert
            sinon.assert.callCount(callback_object.first_domino_selected, 1);
            assert.equal(callback_object.first_domino_selected.getCall(0).args[0], domino);
        });
    });
    describe('Stop subscription', function () {
        function act() {
            sut.unsubscribe();
        };
        it('calls subscriber with quarry index', function () {
            // Arrange
            // Act
            act();
            // Assert
            sinon.assert.callCount(market.unsubscribe, 1);
        });
    });
});
