var assert = require('assert');
var sinon = require('sinon');

var sut_module = require('../export/modules/javascript/markers.js');

class Document {
    getElementById(element_id) {return {insertAdjacentHTML: sinon.spy(),};}
}
class Animation {
    play () {}
}
describe('Markers', function () {
    beforeEach(function() {
        dojo = {style:sinon.spy(), addClass:sinon.spy(), removeClass:sinon.spy(), };
        document = new Document();
        game = {slideToObjectPos:sinon.stub().returns (new Animation ()) ,};
        dependencies = {dojo: dojo, document: document, game:game, };
        sut = new sut_module(dependencies);
        marker_specification = {marker_id: 0, colour: 0, stage: 0, horizontal: 0,vertical: 0,};
        element_id = 'HTML element ID ';
        x = 12;
        y = 33;
        PIXELS_PER_TILE = 60;
        MARGIN_BETWEEN_TOKENS = 10;

    });
    describe('Create marker from specification', function () {
        it('copies the input parameters', function () {
            // Arrange
            marker_specification.test_parameter = dojo;
            // Act
            marker = sut.create_from(marker_specification);
            // Assert
            assert.equal(marker.test_parameter, marker_specification.test_parameter);
        });
    });
    describe('Position within marker window for stage 0', function () {
        it('sets horizontal and vertical for colour 1', function () {
            // Arrange
            marker_specification.colour = 1;
            // Act
            marker = sut.create_from(marker_specification);
            // Assert
            assert.equal(marker.horizontal, 0);
            assert.equal(marker.vertical, 0);
        });
        it('sets horizontal and vertical for colour 2', function () {
            // Arrange
            marker_specification.colour = 2;
            // Act
            marker = sut.create_from(marker_specification);
            // Assert
            assert.equal(marker.horizontal, 2);
            assert.equal(marker.vertical, 0);
        });
        it('sets horizontal and vertical for colour 6', function () {
            // Arrange
            marker_specification.colour = 6;
            // Act
            marker = sut.create_from(marker_specification);
            // Assert
            assert.equal(marker.horizontal, 2);
            assert.equal(marker.vertical, 4);
        });
    });
    describe('Paint', function () {
        beforeEach(function() {
            marker = sut.create_from(marker_specification);
        });
        it('calls slideToObjectPos with unique_id', function () {
            // Arrange
            // Act
            marker.paint();
            // Assert
            assert.equal(game.slideToObjectPos.getCall(0).args[0], marker.unique_id);
        });
        it('uses the parameters from move', function () {
            // Arrange
            marker.move_to(element_id, x, y);
            // Act
            marker.paint();
            // Assert
            assert.equal(game.slideToObjectPos.getCall(0).args[1], element_id);
            assert.equal(game.slideToObjectPos.getCall(0).args[2], x);
            assert.equal(game.slideToObjectPos.getCall(0).args[3], y);
        });
    });
});
