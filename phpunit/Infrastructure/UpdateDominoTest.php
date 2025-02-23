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

class UpdateDominoTest extends TestCase{
    protected ?UpdateDomino $sut = null;
    protected ?FrameworkInterfaces\Deck $mock_cards = null;

    protected string $player_id = '77';
    protected string $quarry_index = 'quarry-2';
    protected int $stage_index = 4;
    protected int $horizontal = 19;
    protected int $vertical = 18;
    protected int $rotation = 3;

    protected function setUp(): void {
        $this->mock_cards = $this->createMock(FrameworkInterfaces\Deck::class);
        $this->sut = UpdateDomino::create($this->mock_cards);
    }

    public function test_move() {
        // Arrange
        $this->mock_cards->expects($this->exactly(1))->method('moveAllCardsInLocation')
        ->with('quarry', $this->player_id, 2
        , $this->stage_index + 5 * $this->horizontal + 5*20* $this->vertical + 5*20*20* $this->rotation);

        // Act
        $this->sut->move($this->quarry_index, $this->player_id, $this->stage_index, $this->horizontal, $this->vertical, $this->rotation);
        // Assert
    }
}
?>
