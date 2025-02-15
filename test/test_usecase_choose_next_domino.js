var assert = require('assert');
var sinon = require('sinon');

var sut_module = require('../export/modules/javascript/usecase_choose_next_domino.js');

describe('Use case choose next domino', function () {
    beforeEach(function() {
        market = {subscribe_to_next: sinon.spy(), get_missing_index: sinon.stub().returns(1), unsubscribe: sinon.spy(),};
        sut = new sut_module({market: market});
        stock = {control_name: "next-2"};
    });
    describe('Subscribe', function () {
        beforeEach(function() {
            callback_object = {
                next_domino_selected: sinon.spy(),
            };
        });
        function act_default() {
            sut.subscribe(callback_object, 'next_domino_selected');
        };
        it('subscribes to the market', function () {
            // Arrange
            
            // Act
            act_default();
            // Assert
            sinon.assert.callCount(market.subscribe_to_next, 1);
            assert.equal(market.subscribe_to_next.getCall(0).args[0], sut);
            assert.equal(market.subscribe_to_next.getCall(0).args[1], 'next_selected');
        });
    });
    describe('next_selected', function () {
        beforeEach(function() {
            callback_object = {
                next_domino_selected: sinon.spy(),
            };
        });
        function arrange_default() {
            sut.subscribe(callback_object, 'next_domino_selected');
        };
        function act_default() {
            sut.next_selected(stock);
        };
        it('calls subscriber with next index', function () {
            // Arrange
            arrange_default();
            // Act
            act_default();
            // Assert
            sinon.assert.callCount(callback_object.next_domino_selected, 1);
            assert.equal(callback_object.next_domino_selected.getCall(0).args[0], 2);
        });
    });
    describe('Stop subscription', function () {
        function act_default() {
            sut.unsubscribe();
        };
        it('calls subscriber with next index', function () {
            // Arrange
            // Act
            act_default();
            // Assert
            sinon.assert.callCount(market.unsubscribe, 1);
        });
    });
});
