<?php
namespace Bga\Games\PyramidoCannonFodder\Infrastructure;
/**
 *------
 * Pyramido implementation unit tests : © Marcel van Nieuwenhoven marcel.eindhoven@hotmail.com
 *
 */

include_once(__DIR__.'/../../vendor/autoload.php');
use PHPUnit\Framework\TestCase;

include_once(__DIR__.'/../../export/modules/php/Infrastructure/Domino.php');

include_once(__DIR__.'/../_ide_helper.php');
use Bga\Games\FrameworkInterfaces;

class DominoFactoryTest extends TestCase{
    protected ?DominoFactory $sut = null;
    protected ?FrameworkInterfaces\Deck $mock_cards = null;

    protected function setUp(): void {
        $this->mock_cards = $this->createMock(FrameworkInterfaces\Deck::class);
        $this->sut = DominoFactory::create($this->mock_cards);
    }

    // Test creation of Domino tokens
    public function test_Domino_is_created() {
        // Arrange
        $first_colour = 0;
        $second_colour = 5;
        $expected_definition = array( 'type' => $first_colour, 'type_arg' => $second_colour, 'nbr' => 1);

        $this->mock_cards->expects($this->exactly(1))->method('createCards')->with([$expected_definition]);

        // Act
        $this->sut->add($first_colour, $second_colour);
        $this->sut->flush();
        // Assert
    }

    public function test_shuffle() {
        // Arrange
        $this->mock_cards->expects($this->exactly(1))->method('shuffle')->with('deck');

        // Act
        $this->sut->flush();
        // Assert
    }
}
?>
