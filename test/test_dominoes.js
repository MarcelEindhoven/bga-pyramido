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
        domino_specification = {id: 0, stage: 0, horizontal: 0,vertical: 0,};
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
});
