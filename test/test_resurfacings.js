var assert = require('assert');
var sinon = require('sinon');

var sut_module = require('../export/modules/javascript/resurfacings.js');

class Document {
    getElementById(element_id) {return {insertAdjacentHTML: sinon.spy(),};}
}
class Animation {
    play () {}
}
describe('Resurfacings', function () {
    beforeEach(function() {
        dojo = {style:sinon.spy(), addClass:sinon.spy(), removeClass:sinon.spy(), connect:sinon.spy(), destroy:sinon.spy(), };
        document = new Document();
        game = {get_element:sinon.stub().returns(44), slideToObjectPos:sinon.stub().returns (new Animation ()) ,};
        dependencies = {dojo: dojo, document: document, game:game, };
        sut = new sut_module(dependencies);
        resurfacing_specification = {resurfacing_id: 0, colour: 0, stage: 0, horizontal: 0,vertical: 0,};
        element_id = 'HTML element ID ';
        x = 12;
        y = 33;
        PIXELS_PER_TILE = 60;
        MARGIN_BETWEEN_TOKENS = 10;

    });
    describe('Create resurfacing from specification', function () {
        it('copies the input parameters', function () {
            // Arrange
            resurfacing_specification.test_parameter = dojo;
            // Act
            resurfacing = sut.create_from(resurfacing_specification);
            // Assert
            assert.equal(resurfacing.test_parameter, resurfacing_specification.test_parameter);
        });
    });
    describe('Position within resurfacing window for stage 0', function () {
        it('sets horizontal and vertical for top left colour', function () {
            // Arrange
            resurfacing_specification.colour = 0;
            // Act
            resurfacing = sut.create_from(resurfacing_specification);
            // Assert
            assert.equal(resurfacing.horizontal, 0);
            assert.equal(resurfacing.vertical, 0);
        });
        it('sets horizontal and vertical for top right colour', function () {
            // Arrange
            resurfacing_specification.colour = 1;
            // Act
            resurfacing = sut.create_from(resurfacing_specification);
            // Assert
            assert.equal(resurfacing.horizontal, 2);
            assert.equal(resurfacing.vertical, 0);
        });
        it('sets horizontal and vertical for bottom right colour', function () {
            // Arrange
            resurfacing_specification.colour = 5;
            // Act
            resurfacing = sut.create_from(resurfacing_specification);
            // Assert
            assert.equal(resurfacing.horizontal, 2);
            assert.equal(resurfacing.vertical, 4);
        });
    });
    describe('Paint', function () {
        beforeEach(function() {
            resurfacing = sut.create_from(resurfacing_specification);
        });
        it('calls slideToObjectPos with unique_id', function () {
            // Arrange
            // Act
            resurfacing.paint();
            // Assert
            assert.equal(game.slideToObjectPos.getCall(0).args[0], resurfacing.unique_id);
        });
        it('uses the parameters from move', function () {
            // Arrange
            resurfacing.move_to(element_id, x, y);
            // Act
            resurfacing.paint();
            // Assert
            assert.equal(game.slideToObjectPos.getCall(0).args[1], element_id);
            assert.equal(game.slideToObjectPos.getCall(0).args[2], x);
            assert.equal(game.slideToObjectPos.getCall(0).args[3], y);
        });
    });
    describe('Place', function () {
        beforeEach(function() {
            resurfacing = sut.create_from(resurfacing_specification);
        });
        it('removes from HTML', function () {
            // Arrange
            specification = {resurfacing_id: 7, colour: 99, stage: 1, horizontal: 10,vertical: 12,};
            // Act
            resurfacing.place(specification);
            // Assert
            assert.equal(dojo.destroy.getCall(0).args[0], resurfacing.unique_id);
        });
        it('creates into HTML', function () {
            // Arrange
            specification = {resurfacing_id: 7, colour: 99, stage: 1, horizontal: 10,vertical: 12,};
            // Act
            resurfacing.place(specification);
            // Assert
            assert.equal(dojo.addClass.getCall(0).args[0], resurfacing.unique_id);
        });
    });
});
