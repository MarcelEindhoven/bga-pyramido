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

class UpdateMarketTest extends TestCase{
    protected ?UpdateMarket $sut = null;
    protected ?FrameworkInterfaces\Deck $mock_cards = null;

    protected int $next_index = 2;
    protected int $quarry_index = 1;

    protected function setUp(): void {
        $this->mock_cards = $this->createMock(FrameworkInterfaces\Deck::class);
        $this->sut = UpdateMarket::create($this->mock_cards);
    }

    public function test_move() {
        // Arrange
        $this->mock_cards->expects($this->exactly(1))->method('moveAllCardsInLocation')
        ->with('next', 'quarry', $this->next_index, $this->quarry_index);

        // Act
        $this->sut->move($this->next_index, $this->quarry_index);
        // Assert
    }

    public function test_refill() {
        // Arrange
        $this->mock_cards->expects($this->exactly(1))->method('pickCardForLocation')
        ->with('deck', 'next', $this->next_index);

        // Act
        $this->sut->refill($this->next_index);
        // Assert
    }
}
?>
