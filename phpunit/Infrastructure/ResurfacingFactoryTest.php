<?php
namespace Bga\Games\Pyramido\Infrastructure;
/**
 *------
 * Pyramido implementation unit tests : © Marcel van Nieuwenhoven marcel.eindhoven@hotmail.com
 *
 */

include_once(__DIR__.'/../../vendor/autoload.php');
use PHPUnit\Framework\TestCase;

include_once(__DIR__.'/../../export/modules/php/Infrastructure/Resurfacing.php');

include_once(__DIR__.'/../_ide_helper.php');
use Bga\Games\FrameworkInterfaces;

class ResurfacingFactoryTest extends TestCase{
    protected ?ResurfacingFactory $sut = null;
    protected ?FrameworkInterfaces\Deck $mock_cards = null;

    protected function setUp(): void {
        $this->mock_cards = $this->createMock(FrameworkInterfaces\Deck::class);
        $this->sut = ResurfacingFactory::create($this->mock_cards);
    }

    // Test creation of Resurfacing tokens
    public function test_Resurfacing_is_created() {
        // Arrange
        $player_id = 7;
        $first_colour = 1;
        $second_colour = 5;
        $expected_definition = array( 'type' => $first_colour, 'type_arg' => $second_colour, 'nbr' => 1);

        $this->mock_cards->expects($this->exactly(1))->method('createCards')->with([$expected_definition], '7', 0);

        // Act
        $this->sut->add($first_colour, $second_colour);
        $this->sut->flush($player_id);
        // Assert
    }
}
?>
