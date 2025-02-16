var assert = require('assert');
var sinon = require('sinon');

var sut_module = require('../export/modules/javascript/dominoes.js');

class Document {
    getElementById(element_id) {return {insertAdjacentHTML: sinon.spy(),};}
}
class Animation {
    play () {}
}
describe('Dominoes', function () {
    beforeEach(function() {
        dojo = {style:sinon.spy(), addClass:sinon.spy(), removeClass:sinon.spy(), };
        document = new Document();
        game = {slideToObjectPos:sinon.stub().returns (new Animation ()) ,};
        dependencies = {dojo: dojo, document: document, game:game, };
        sut = new sut_module(dependencies);
        domino_specification = {id: 0, stage: 0, horizontal: 10,vertical: 12, rotation: 0};
        element_id = 'HTML element ID ';
        x = 12;
        y = 33;

    });
    describe('Create domino from specification', function () {
        it('copies the input parameters', function () {
            // Arrange
            domino_specification.test_parameter = dojo;
            // Act
            domino = sut.create_domino_from(domino_specification);
            // Assert
            assert.equal(domino.test_parameter, domino_specification.test_parameter);
        });
    });
    describe('Bounding box', function () {
        it('returns bounding box for rotation 0', function () {
            // Arrange
            domino = sut.create_domino_from(domino_specification);
            // Act
            bounding_box = domino.get_bounding_box();
            // Assert
            assert.equal(bounding_box.horizontal_min, 10);
            assert.equal(bounding_box.vertical_min, 12);
            assert.equal(bounding_box.horizontal_max, 14);
            assert.equal(bounding_box.vertical_max, 14);
        });
    });
});
