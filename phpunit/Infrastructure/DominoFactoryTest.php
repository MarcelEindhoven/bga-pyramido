<?php
namespace Bga\Games\Pyramido\Infrastructure;
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
        $order_index = 7;
        $first_colour = 1;
        $second_colour = 5;
        $first_jewel = 1;
        $second_jewel = 3;
        $expected_definition = array( 'type' => $order_index, 'type_arg' => $first_colour + 6 * $second_colour + 6*6 * $first_jewel + 6*6*8 * $second_jewel, 'nbr' => 1);

        $this->mock_cards->expects($this->exactly(1))->method('createCards')->with([$expected_definition]);

        // Act
        $this->sut->add($order_index, $first_colour, $second_colour, $first_jewel, $second_jewel);
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
